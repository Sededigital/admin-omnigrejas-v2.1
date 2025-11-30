<?php

namespace App\Livewire\Church\Alliance;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Helpers\SupabaseHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use App\Models\Chats\MensagemPrivada;

class PrivateChat extends Component
{
    use WithFileUploads;

    // Propriedades para gerenciar conversas privadas
    public $conversas = [];
    public $conversaAtiva = null;
    public $mensagens = [];
    public $novaMensagem = '';
    public $usuarioDestino = null;

    // Propriedades para nova conversa
    public $mostrarModalNovaConversa = false;
    public $membrosDisponiveis = [];
    public $termoBuscaMembro = '';
    public $paginaMembros = 1;
    public $carregandoMembros = false;
    public $todosMembrosCarregados = false;

    // Propriedades para mídia
    public $arquivoAudio;
    public $arquivoAnexo;
    public $gravandoAudio = false;
    public $mediaRecorder;
    public $audioChunks = [];

    // Propriedades para validação
    public $rules = [
        'novaMensagem' => 'nullable|string|max:50000',
        'arquivoAudio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240', // 10MB
        'arquivoAnexo' => 'nullable|file|max:20480', // 20MB
    ];

    public function mount()
    {
        $this->carregarConversas();
    }

    public function carregarConversas()
    {
        $user = Auth::user();
        if (!$user) return;

        // Buscar todas as conversas privadas do usuário (como remetente ou destinatário)
        // Excluindo mensagens que foram limpadas pelo usuário atual
        $mensagens = MensagemPrivada::where(function($query) use ($user) {
            $query->where('remetente_id', $user->id)
                  ->orWhere('destinatario_id', $user->id);
        })
        ->where(function($query) use ($user) {
            // Não mostrar mensagens que foram limpadas pelo usuário atual
            $query->where(function($q) use ($user) {
                $q->where('remetente_id', $user->id)
                  ->where('limpada_por_remetente', false);
            })
            ->orWhere(function($q) use ($user) {
                $q->where('destinatario_id', $user->id)
                  ->where('limpada_por_destinatario', false);
            });
        })
        ->with(['remetente', 'destinatario'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Agrupar por conversa (combinação de usuários)
        $conversasAgrupadas = [];
        foreach ($mensagens as $mensagem) {
            $outroUsuario = $mensagem->remetente_id === $user->id
                ? $mensagem->destinatario
                : $mensagem->remetente;

            $conversaKey = $outroUsuario->id;

            if (!isset($conversasAgrupadas[$conversaKey])) {
                $conversasAgrupadas[$conversaKey] = [
                    'usuario' => $outroUsuario,
                    'ultima_mensagem' => $mensagem,
                    'nao_lidas' => 0
                ];
            }

            // Contar mensagens não lidas
            if ($mensagem->destinatario_id === $user->id && !in_array($user->id, $mensagem->lida_por ?? [])) {
                $conversasAgrupadas[$conversaKey]['nao_lidas']++;
            }
        }

        $this->conversas = array_values($conversasAgrupadas);
    }

    public function selecionarConversa($usuarioId)
    {
        $this->conversaAtiva = $usuarioId;
        $this->usuarioDestino = User::find($usuarioId);
        $this->carregarMensagens();

        // Disparar evento para scroll automático
        $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
    }

    public function carregarMensagens()
    {
        if (!$this->conversaAtiva) return;

        $user = Auth::user();
        $this->mensagens = MensagemPrivada::where(function($query) use ($user) {
            $query->where(function($q) use ($user) {
                $q->where('remetente_id', $user->id)
                  ->where('destinatario_id', $this->conversaAtiva);
            })->orWhere(function($q) use ($user) {
                $q->where('remetente_id', $this->conversaAtiva)
                  ->where('destinatario_id', $user->id);
            });
        })
        ->with(['remetente', 'destinatario'])
        ->orderBy('created_at', 'asc')
        ->get()
        ->toArray();

        // Marcar mensagens como lidas
        $userId = Auth::id();
        $mensagensNaoLidas = MensagemPrivada::where('remetente_id', $this->conversaAtiva)
            ->where('destinatario_id', $userId)
            ->get();

        foreach ($mensagensNaoLidas as $mensagem) {
            $lidaPor = $mensagem->lida_por ?? [];
            if (!in_array($userId, $lidaPor)) {
                $lidaPor[] = $userId;
                $mensagem->update(['lida_por' => $lidaPor]);
            }
        }
    }

    public function enviarMensagem()
    {
        if (!$this->conversaAtiva) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Verificar se há arquivo anexado
        if ($this->arquivoAnexo) {
            $this->enviarArquivo();
            return;
        }

        // Verificar se há áudio anexado
        if ($this->arquivoAudio) {
            $this->enviarAudio();
            return;
        }

        // Se não há anexos, enviar mensagem de texto
        if (trim($this->novaMensagem) === '') {
            return; // Não enviar mensagem vazia
        }

        $dadosMensagem = [
            'id' => (string) Str::uuid(),
            'remetente_id' => $user->id,
            'destinatario_id' => $this->conversaAtiva,
            'tipo_mensagem' => 'texto',
            'conteudo' => trim($this->novaMensagem),
            'lida_por' => [$user->id],
        ];

        MensagemPrivada::create($dadosMensagem);

        $this->novaMensagem = '';
        $this->carregarMensagens();
        $this->carregarConversas();

        $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
    }

    public function enviarAudio()
    {
        $this->validateOnly('arquivoAudio');

        if (!$this->conversaAtiva || !$this->arquivoAudio) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        try {
            // Upload para Supabase usando o Helper para mensagens privadas
            $path = SupabaseHelper::fazerUploadMensagemPrivada($this->arquivoAudio, $user->id, $this->conversaAtiva, 'audio');

            $dadosMensagem = [
                'id' => (string) Str::uuid(),
                'remetente_id' => $user->id,
                'destinatario_id' => $this->conversaAtiva,
                'tipo_mensagem' => 'audio',
                'anexo_url' => $path,
                'anexo_nome' => $this->arquivoAudio->getClientOriginalName(),
                'anexo_tamanho' => $this->arquivoAudio->getSize(),
                'anexo_tipo' => $this->arquivoAudio->getMimeType(),
                'lida_por' => [$user->id],
            ];

            MensagemPrivada::create($dadosMensagem);

            $this->arquivoAudio = null;
            $this->carregarMensagens();
            $this->carregarConversas();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Áudio enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar áudio: ' . $e->getMessage()]);
        }
    }

    public function enviarArquivo()
    {
        $this->validateOnly('arquivoAnexo');

        if (!$this->conversaAtiva || !$this->arquivoAnexo) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        try {
            // Determinar tipo baseado na extensão
            $extensao = strtolower($this->arquivoAnexo->getClientOriginalExtension());
            $tipoArquivo = $this->determinarTipoArquivo($extensao);

            // Upload para Supabase usando o Helper para mensagens privadas
            $path = SupabaseHelper::fazerUploadMensagemPrivada($this->arquivoAnexo, $user->id, $this->conversaAtiva, 'arquivo');

            $dadosMensagem = [
                'id' => (string) Str::uuid(),
                'remetente_id' => $user->id,
                'destinatario_id' => $this->conversaAtiva,
                'tipo_mensagem' => $tipoArquivo,
                'anexo_url' => $path,
                'anexo_nome' => $this->arquivoAnexo->getClientOriginalName(),
                'anexo_tamanho' => $this->arquivoAnexo->getSize(),
                'anexo_tipo' => $this->arquivoAnexo->getMimeType(),
                'lida_por' => [$user->id],
            ];

            MensagemPrivada::create($dadosMensagem);

            $this->arquivoAnexo = null;
            $this->carregarMensagens();
            $this->carregarConversas();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Arquivo enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()]);
        }
    }

    private function determinarTipoArquivo($extensao)
    {
        $imagens = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videos = ['mp4', 'avi', 'mov', 'wmv', 'flv'];

        if (in_array($extensao, $imagens)) {
            return 'imagem';
        } elseif (in_array($extensao, $videos)) {
            return 'video';
        } else {
            return 'arquivo';
        }
    }

    public function deletarMensagem($mensagemId)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $mensagem = MensagemPrivada::find($mensagemId);
        if (!$mensagem) {
            return;
        }

        // Verificar permissões: apenas o autor pode deletar
        if ($mensagem->remetente_id !== $user->id) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Você não tem permissão para deletar esta mensagem.']);
            return;
        }

        try {
            // Se tem anexo, deletar do Supabase usando o Helper
            if ($mensagem->anexo_url) {
                SupabaseHelper::removerArquivo($mensagem->anexo_url);
            }

            $mensagem->delete();

            $this->carregarMensagens();
            $this->carregarConversas();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Mensagem deletada com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao deletar mensagem: ' . $e->getMessage()]);
        }
    }

    public function iniciarGravacaoAudio()
    {
        // Este método será chamado via JavaScript para iniciar a gravação
        // A implementação real será feita no frontend com MediaRecorder API
        $this->dispatch('iniciar-gravacao-audio');
    }

    public function pararGravacaoAudio()
    {
        // Método para parar a gravação (será chamado pelo JavaScript)
        $this->dispatch('parar-gravacao-audio');
    }

    public function receberAudioGravado($audioBlob, $fileName = null)
    {
        if (!$this->conversaAtiva) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        try {
            // Converter o blob base64 para arquivo
            $audioData = base64_decode($audioBlob);
            $fileName = $fileName ?: 'gravacao_audio_' . time() . '.webm';

            // Criar arquivo temporário
            $tempPath = tempnam(sys_get_temp_dir(), 'audio_');
            file_put_contents($tempPath, $audioData);

            // Criar UploadedFile
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $fileName,
                'audio/webm',
                null,
                true
            );

            // Upload para Supabase usando o Helper para mensagens privadas
            $path = SupabaseHelper::fazerUploadMensagemPrivada($uploadedFile, $user->id, $this->conversaAtiva, 'audio');

            $dadosMensagem = [
                'id' => (string) Str::uuid(),
                'remetente_id' => $user->id,
                'destinatario_id' => $this->conversaAtiva,
                'tipo_mensagem' => 'audio',
                'anexo_url' => $path,
                'anexo_nome' => $fileName,
                'anexo_tamanho' => strlen($audioData),
                'anexo_tipo' => 'audio/webm',
                'lida_por' => [$user->id],
            ];

            MensagemPrivada::create($dadosMensagem);

            // Limpar arquivo temporário
            unlink($tempPath);

            $this->carregarMensagens();
            $this->carregarConversas();
            $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Áudio enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar áudio: ' . $e->getMessage()]);
        }
    }

    public function limparConversa()
    {
        $user = Auth::user();
        if (!$user || !$this->conversaAtiva) {
            return;
        }

        try {
            // Buscar todas as mensagens da conversa
            $mensagens = MensagemPrivada::where(function($query) use ($user) {
                $query->where(function($q) use ($user) {
                    $q->where('remetente_id', $user->id)
                      ->where('destinatario_id', $this->conversaAtiva);
                })->orWhere(function($q) use ($user) {
                    $q->where('remetente_id', $this->conversaAtiva)
                      ->where('destinatario_id', $user->id);
                });
            })->get();

            // Marcar como limpada para o usuário atual
            foreach ($mensagens as $mensagem) {
                if ($mensagem->remetente_id === $user->id) {
                    $mensagem->update(['limpada_por_remetente' => true]);
                } elseif ($mensagem->destinatario_id === $user->id) {
                    $mensagem->update(['limpada_por_destinatario' => true]);
                }
            }

            // Fechar a conversa ativa
            $this->conversaAtiva = null;
            $this->usuarioDestino = null;

            // Recarregar conversas
            $this->carregarConversas();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'Conversa limpa com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao limpar conversa: ' . $e->getMessage()]);
        }
    }

    public function abrirModalNovaConversa()
    {

        $this->mostrarModalNovaConversa = true;
        $this->termoBuscaMembro = '';
        $this->paginaMembros = 1;
        $this->todosMembrosCarregados = false;
        $this->carregarMembrosDisponiveis();
    }

    public function fecharModalNovaConversa()
    {
        $this->mostrarModalNovaConversa = false;
        $this->termoBuscaMembro = '';
        $this->membrosDisponiveis = [];
    }

    public function updatedTermoBuscaMembro()
    {
        $this->paginaMembros = 1;
        $this->todosMembrosCarregados = false;
        $this->carregarMembrosDisponiveis();
    }

    public function carregarMembrosDisponiveis()
    {
        $user = Auth::user();
        if (!$user) return;

        $perPage = 5; // Mostrar 5 membros por página

        // Buscar membros da mesma igreja, excluindo o usuário atual
        $query = IgrejaMembro::where('igreja_membros.igreja_id', $user->getIgrejaId())
            ->where('igreja_membros.user_id', '!=', $user->id)
            ->where('igreja_membros.status', 'ativo')
            ->whereNull('igreja_membros.deleted_at')
            ->with('user');

        // Aplicar filtro de busca se houver termo
        if (!empty($this->termoBuscaMembro)) {
            $termo = strtolower($this->termoBuscaMembro);
            $query->where(function($q) use ($termo) {
                $q->whereRaw('LOWER(users.name) LIKE ?', ['%' . $termo . '%'])
                  ->orWhereRaw('LOWER(users.email) LIKE ?', ['%' . $termo . '%']);
            });
        }

        // Aplicar paginação
        $membros = $query->join('users', 'igreja_membros.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('igreja_membros.*')
            ->paginate($perPage, ['*'], 'page', $this->paginaMembros);

        // Verificar se há mais páginas
        $this->todosMembrosCarregados = !$membros->hasMorePages();

        // Mapear os resultados
        $membrosMapeados = $membros->map(function($membro) {
            return [
                'id' => $membro->user->id,
                'name' => $membro->user->name,
                'email' => $membro->user->email,
                'photo_url' => $membro->user->photo_url,
            ];
        })->toArray();

        // Se for a primeira página, substituir a lista; senão, adicionar aos existentes
        if ($this->paginaMembros == 1) {
            $this->membrosDisponiveis = $membrosMapeados;
        } else {
            $this->membrosDisponiveis = array_merge($this->membrosDisponiveis, $membrosMapeados);
        }
    }

    public function carregarMaisMembros()
    {
        if ($this->todosMembrosCarregados || $this->carregandoMembros) {
            return;
        }

        $this->carregandoMembros = true;
        $this->paginaMembros++;

        try {
            $this->carregarMembrosDisponiveis();
        } finally {
            $this->carregandoMembros = false;
        }
    }

    public function fecharConversa()
    {
        $this->conversaAtiva = null;
        $this->usuarioDestino = null;
    }

    public function iniciarConversaCom($usuarioId)
    {
        // Fechar modal
        $this->fecharModalNovaConversa();

        // Iniciar conversa com o usuário selecionado
        $this->selecionarConversa($usuarioId);

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Conversa iniciada!']);
    }

    public function render()
    {
        return view('church.alliance.private-chat', [
            'usuarioAtual' => Auth::user(),
            'usuarioDestino' => $this->usuarioDestino,
        ]);
    }
}
