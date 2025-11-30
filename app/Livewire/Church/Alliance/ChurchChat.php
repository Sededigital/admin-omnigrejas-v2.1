<?php

namespace App\Livewire\Church\Alliance;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use App\Helpers\SupabaseHelper;
use Livewire\Attributes\Layout;
use App\Models\Chats\IgrejaChat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Chats\IgrejaChatMensagem;
use App\Models\Chats\IgrejaChatParticipante;

#[Title('Chat da Igreja| Omnigrejas')]
#[Layout('components.layouts.app')]
class ChurchChat extends Component
{
    use WithFileUploads;

    // Propriedades para gerenciar chats
    public $chats = [];
    public $chatAtivo = null;
    public $mensagens = [];
    public $novaMensagem = '';
    public $mostrarModalCriarChat = false;
    public $nomeChat = '';
    public $descricaoChat = '';
    public $visibilidadeChat = 'publico'; // publico ou privado

    // Propriedades para mídia
    public $arquivoAudio;
    public $arquivoAnexo;
    public $gravandoAudio = false;
    public $mediaRecorder;
    public $audioChunks = [];

    // Propriedades para participantes
    public $participantes = [];
    public $isAdminGrupo = false;
    public $adminsChat = [];


    // Propriedades para validação
    public $rules = [
        'novaMensagem' => 'nullable|string|max:50000',
        'nomeChat' => 'required|string|max:255',
        'descricaoChat' => 'nullable|string|max:500',
        'visibilidadeChat' => 'required|in:publico,privado',
        'arquivoAudio' => 'nullable|file|mimes:mp3,wav,ogg|max:10240', // 10MB
        'arquivoAnexo' => 'nullable|file|max:20480', // 20MB
    ];

    public function mount()
    {
        $this->carregarChats();
    }

    public function carregarChats()
    {
        // Carregar todos os chats da igreja do usuário atual, excluindo aqueles dos quais ele saiu
        $user = Auth::user();
        $igrejaId = $user ? $user->getIgrejaId() : null;

        if ($user && $igrejaId) {
            $this->chats = IgrejaChat::where('igreja_id', $igrejaId)
                ->whereDoesntHave('participantes', function($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->where('status', 'removido');
                })
                ->with(['criador', 'mensagens' => function($query) {
                    $query->latest()->limit(1);
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();
        }
    }

    public function selecionarChat($chatId)
    {
        $this->chatAtivo = $chatId;
        $this->carregarMensagens();
        $this->carregarParticipantes();
        $this->carregarAdminsChat();
        $this->verificarAdminGrupo();

        // Disparar evento para scroll automático
        $this->dispatch('scroll-to-bottom', ['containerId' => 'churchChatMessages']);
    }

    public function carregarAdminsChat()
    {
        if ($this->chatAtivo) {
            $this->adminsChat = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('is_admin', true)
                ->where('status', 'ativo')
                ->with('user')
                ->get()
                ->map(function($participante) {
                    return [
                        'id' => $participante->user->id,
                        'name' => $participante->user->name,
                        'photo_url' => $participante->user->photo_url ? Storage::disk('supabase')->url($participante->user->photo_url) : null,
                    ];
                })
                ->toArray();
        }
    }

    public function carregarMensagens()
    {
        if ($this->chatAtivo) {
            $this->mensagens = IgrejaChatMensagem::where('chat_id', $this->chatAtivo)
                ->with('autor')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();

            // Marcar mensagens como lidas para o usuário atual
            $userId = Auth::id();
            $mensagensNaoLidas = IgrejaChatMensagem::where('chat_id', $this->chatAtivo)->get();

            foreach ($mensagensNaoLidas as $mensagem) {
                $lidaPor = $mensagem->lida_por ?? [];
                if (!in_array($userId, $lidaPor)) {
                    $lidaPor[] = $userId;
                    $mensagem->update(['lida_por' => $lidaPor]);
                }
            }
        }
    }


    public function carregarParticipantes()
    {
        if ($this->chatAtivo) {
            $this->participantes = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->with('user')
                ->orderBy('is_admin', 'desc')
                ->orderBy('data_entrada', 'asc')
                ->get()
                ->toArray();
        }
    }

    public function verificarAdminGrupo()
    {
        $user = Auth::user();
        if ($user && $this->chatAtivo) {
            $participante = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('user_id', $user->id)
                ->first();

            $this->isAdminGrupo = $participante ? $participante->is_admin : false;
        } else {
            $this->isAdminGrupo = false;
        }
    }

    public function criarChat()
    {
        try {
            // Log para debug
            Log::info('=== MÉTODO CRIAR CHAT CHAMADO ===', [
                'nomeChat' => $this->nomeChat,
                'visibilidadeChat' => $this->visibilidadeChat,
                'user_id' => Auth::id(),
                'timestamp' => now()
            ]);

            $this->validate([
                'nomeChat' => 'required|string|max:255',
                'descricaoChat' => 'nullable|string|max:5000',
                'visibilidadeChat' => 'required|in:publico,privado',
            ]);

            $user = Auth::user();
            if (!$user) {
                $this->addError('autorizacao', 'Usuário não autenticado.');
                return;
            }

            $igrejaId = $user->getIgrejaId();
            if (!$igrejaId) {
                $this->addError('autorizacao', 'Você não está associado a uma igreja.');
                return;
            }

            Log::info('Dados validados, iniciando transação', [
                'igreja_id' => $igrejaId,
                'user_id' => $user->id
            ]);

            DB::transaction(function () use ($user, $igrejaId) {
                // Criar novo chat
                $chat = IgrejaChat::create([
                    'id' => (string) Str::uuid(),
                    'igreja_id' => $igrejaId,
                    'nome' => trim($this->nomeChat),
                    'descricao' => trim($this->descricaoChat),
                    'criado_por' => $user->id,
                    'visibilidade' => $this->visibilidadeChat,
                ]);

                Log::info('Chat criado', ['chat_id' => $chat->id]);

                // Adicionar criador como participante e admin
                IgrejaChatParticipante::create([
                    'chat_id' => $chat->id,
                    'user_id' => $user->id,
                    'is_admin' => true,
                    'added_by' => $user->id,
                ]);

                Log::info('Criador adicionado como participante');

                // Se for público, adicionar automaticamente todos os membros da igreja
                if ($this->visibilidadeChat === 'publico') {
                    $this->adicionarTodosMembrosIgreja($chat->id, $user->id);
                    Log::info('Membros adicionados ao chat público');
                }

                $this->chatAtivo = $chat->id;
            });

            Log::info('Transação concluída, fechando modal');

            $this->fecharModalCriarChat();
            $this->carregarChats();
            $this->selecionarChat($this->chatAtivo);

            // Garantir fechamento do modal
            $this->dispatch('close-criar-chat-modal');

            $this->dispatch('toast', ['type' => 'success', 'message' => 'Chat criado com sucesso!']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao criar chat', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Erro ao criar chat', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao criar chat: ' . $e->getMessage()]);


        }
    }

    private function adicionarTodosMembrosIgreja($chatId, $addedBy)
    {
        $igrejaId = Auth::user()->getIgrejaId();
        $membros = IgrejaMembro::where('igreja_id', $igrejaId)
            ->where('status', 'ativo')
            ->get();

        foreach ($membros as $membro) {
            // Pular o criador, já foi adicionado
            if ($membro->user_id === $addedBy) continue;

            IgrejaChatParticipante::create([
                'chat_id' => $chatId,
                'user_id' => $membro->user_id,
                'is_admin' => false,
                'added_by' => $addedBy,
            ]);
        }
    }

    public function enviarMensagem()
    {

        if (!$this->chatAtivo) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Verificar se usuário é participante do chat
        $participante = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->first();

        if (!$participante) {
            $this->addError('participacao', 'Você não é participante deste chat.');
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
            'chat_id' => $this->chatAtivo,
            'autor_id' => $user->id,
            'tipo_mensagem' => 'texto',
            'conteudo' => trim($this->novaMensagem),
            'lida_por' => [$user->id],
        ];

        IgrejaChatMensagem::create($dadosMensagem);

        $this->novaMensagem = '';
        $this->carregarMensagens();

        $this->dispatch('scroll-to-bottom', ['containerId' => 'churchChatMessages']);
    }

    public function enviarAudio()
    {
        $this->validateOnly('arquivoAudio');

        if (!$this->chatAtivo || !$this->arquivoAudio) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        try {
            // Upload para Supabase usando o Helper
            $path = SupabaseHelper::fazerUploadChat($this->arquivoAudio, $this->chatAtivo, 'audio');

            $dadosMensagem = [
                'chat_id' => $this->chatAtivo,
                'autor_id' => $user->id,
                'tipo_mensagem' => 'audio',
                'anexo_url' => $path,
                'anexo_nome' => $this->arquivoAudio->getClientOriginalName(),
                'anexo_tamanho' => $this->arquivoAudio->getSize(),
                'anexo_tipo' => $this->arquivoAudio->getMimeType(),
                'lida_por' => [$user->id],
            ];

            IgrejaChatMensagem::create($dadosMensagem);

            $this->arquivoAudio = null;
            $this->carregarMensagens();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'churchChatMessages']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Áudio enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar áudio: ' . $e->getMessage()]);
        }
    }

    public function enviarArquivo()
    {
        $this->validateOnly('arquivoAnexo');

        if (!$this->chatAtivo || !$this->arquivoAnexo) {
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

            // Upload para Supabase usando o Helper
            $path = SupabaseHelper::fazerUploadChat($this->arquivoAnexo, $this->chatAtivo, 'arquivo');

            $dadosMensagem = [
                'chat_id' => $this->chatAtivo,
                'autor_id' => $user->id,
                'tipo_mensagem' => $tipoArquivo,
                'anexo_url' => $path,
                'anexo_nome' => $this->arquivoAnexo->getClientOriginalName(),
                'anexo_tamanho' => $this->arquivoAnexo->getSize(),
                'anexo_tipo' => $this->arquivoAnexo->getMimeType(),
                'lida_por' => [$user->id],
            ];

            IgrejaChatMensagem::create($dadosMensagem);

            $this->arquivoAnexo = null;
            $this->carregarMensagens();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'churchChatMessages']);
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

        $mensagem = IgrejaChatMensagem::find($mensagemId);
        if (!$mensagem) {
            return;
        }

        // Verificar permissões: autor da mensagem ou admin do grupo
        $podeDeletar = $mensagem->autor_id === $user->id || $this->isAdminGrupo;

        if (!$podeDeletar) {
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
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Mensagem deletada com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao deletar mensagem: ' . $e->getMessage()]);
        }
    }

    public function sairDoChat()
    {
        $user = Auth::user();
        if (!$user) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro: Usuário não autenticado.']);
            return;
        }

        if (!$this->chatAtivo) {
            $this->dispatch('toast', ['type' => 'warning', 'message' => 'Nenhum chat selecionado.']);
            return;
        }


        try {
            // Verificar se o usuário é participante do chat
            $participante = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('user_id', $user->id)
                ->where('status', 'ativo')
                ->first();

            if (!$participante) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Você não é mais participante deste chat ou já saiu anteriormente.'
                ]);
                return;
            }

            // Verificar se é o último admin do chat
            if ($participante->is_admin) {
                $outrosAdmins = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                    ->where('is_admin', true)
                    ->where('user_id', '!=', $user->id)
                    ->where('status', 'ativo')
                    ->count();


                if ($outrosAdmins === 0) {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => '❌ Você é o último administrador deste chat. Antes de sair,
                        transfira a administração para outro membro através das configurações do chat.'
                    ]);

                    return;
                }
            }

            // Obter nome do chat para mensagem mais informativa
            $chat = IgrejaChat::find($this->chatAtivo);
            $nomeChat = $chat ? $chat->nome : 'Chat';

            // Remover o usuário do chat
            $participante->update(['status' => 'removido']);

            // Fechar o chat ativo
            $this->chatAtivo = null;

            // Recarregar a lista de chats
            $this->carregarChats();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => "✅ Você saiu do chat '{$nomeChat}' com sucesso! O chat foi removido da sua lista."
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao sair do chat', [
                'user_id' => $user->id,
                'chat_id' => $this->chatAtivo,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => '❌ Erro ao sair do chat. Tente novamente ou entre em contato com o suporte.'
            ]);
        }
    }


    public function fecharModalCriarChat()
    {
        $this->mostrarModalCriarChat = false;
        $this->nomeChat = '';
        $this->descricaoChat = '';
        $this->visibilidadeChat = 'publico';
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
        if (!$this->chatAtivo) {
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

            // Upload para Supabase usando o Helper
            $path = SupabaseHelper::fazerUploadChat($uploadedFile, $this->chatAtivo, 'audio');

            $dadosMensagem = [
                'chat_id' => $this->chatAtivo,
                'autor_id' => $user->id,
                'tipo_mensagem' => 'audio',
                'anexo_url' => $path,
                'anexo_nome' => $fileName,
                'anexo_tamanho' => strlen($audioData),
                'anexo_tipo' => 'audio/webm',
                'lida_por' => [$user->id],
            ];

            IgrejaChatMensagem::create($dadosMensagem);

            // Limpar arquivo temporário
            unlink($tempPath);

            $this->carregarMensagens();
            $this->dispatch('scroll-to-bottom', ['containerId' => 'churchChatMessages']);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Áudio enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar áudio: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // MÉTODOS PARA API AJAX (EDIÇÃO DE CHAT)
    // ==========================================

    public function editarChat()
    {
        $user = Auth::user();
        if (!$user || !$this->chatAtivo) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Verificar se é admin do chat
        $participante = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->first();

        if (!$participante) {
            return response()->json(['error' => 'Apenas administradores podem editar o chat'], 403);
        }

        $validated = request()->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:500',
        ]);

        try {
            $chat = IgrejaChat::find($this->chatAtivo);
            if (!$chat) {
                return response()->json(['error' => 'Chat não encontrado'], 404);
            }

            $chat->update([
                'nome' => trim($validated['nome']),
                'descricao' => trim($validated['descricao']),
            ]);

            Log::info('Chat editado', [
                'chat_id' => $this->chatAtivo,
                'user_id' => $user->id,
                'nome' => $validated['nome']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Chat editado com sucesso',
                'chat' => $chat
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao editar chat', [
                'chat_id' => $this->chatAtivo,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Erro ao editar chat'], 500);
        }
    }

    public function listarAdmins()
    {
        $user = Auth::user();
        if (!$user || !$this->chatAtivo) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        try {
            
            $admins = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('is_admin', true)
                ->where('status', 'ativo')
                ->with('user')
                ->get()
                ->map(function($participante) {
                    return [
                        'id' => $participante->user->id,
                        'name' => $participante->user->name,
                        'photo_url' => $participante->user->photo_url ? Storage::disk('supabase')->url($participante->user->photo_url) : null,
                    ];
                });

            return response()->json($admins);

        } catch (\Exception $e) {
            Log::error('Erro ao listar admins', [
                'chat_id' => $this->chatAtivo,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Erro ao carregar administradores'], 500);
        }
    }

    public function adicionarAdmin()
    {
        $user = Auth::user();
        if (!$user || !$this->chatAtivo) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Verificar se é admin do chat
        $participante = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->first();

        if (!$participante) {
            return response()->json(['error' => 'Apenas administradores podem gerenciar admins'], 403);
        }

        $validated = request()->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Verificar se o usuário já é participante
            $participanteExistente = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('user_id', $validated['user_id'])
                ->first();

            if ($participanteExistente) {
                // Se já existe, apenas tornar admin
                $participanteExistente->update(['is_admin' => true]);
            } else {
                // Se não existe, criar participação como admin
                IgrejaChatParticipante::create([
                    'chat_id' => $this->chatAtivo,
                    'user_id' => $validated['user_id'],
                    'is_admin' => true,
                    'added_by' => $user->id,
                ]);
            }

            Log::info('Admin adicionado ao chat', [
                'chat_id' => $this->chatAtivo,
                'user_id' => $validated['user_id'],
                'added_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Administrador adicionado com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar admin', [
                'chat_id' => $this->chatAtivo,
                'user_id' => $validated['user_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Erro ao adicionar administrador'], 500);
        }
    }

    public function removerAdmin()
    {
        $user = Auth::user();
        if (!$user || !$this->chatAtivo) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Verificar se é admin do chat
        $participante = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->first();

        if (!$participante) {
            return response()->json(['error' => 'Apenas administradores podem gerenciar admins'], 403);
        }

        $validated = request()->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            // Verificar se há mais de um admin
            $totalAdmins = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('is_admin', true)
                ->where('status', 'ativo')
                ->count();

            if ($totalAdmins <= 1) {
                return response()->json(['error' => 'Não é possível remover o último administrador'], 400);
            }

            // Remover status de admin
            $participanteParaRemover = IgrejaChatParticipante::where('chat_id', $this->chatAtivo)
                ->where('user_id', $validated['user_id'])
                ->first();

            if ($participanteParaRemover) {
                $participanteParaRemover->update(['is_admin' => false]);
            }

            Log::info('Admin removido do chat', [
                'chat_id' => $this->chatAtivo,
                'user_id' => $validated['user_id'],
                'removed_by' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Administrador removido com sucesso'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao remover admin', [
                'chat_id' => $this->chatAtivo,
                'user_id' => $validated['user_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Erro ao remover administrador'], 500);
        }
    }

    public function listarMembrosDisponiveis()
    {
        $user = Auth::user();
        if (!$user || !$this->chatAtivo) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        try {
            $igrejaId = $user->getIgrejaId();

            // Buscar membros da igreja que não são admins do chat
            $membrosDisponiveis = IgrejaMembro::where('igreja_id', $igrejaId)
                ->where('status', 'ativo')
                ->whereNotIn('user_id', function($query) {
                    $query->select('user_id')
                          ->from('igreja_chat_participantes')
                          ->where('chat_id', $this->chatAtivo)
                          ->where('is_admin', true);
                })
                ->with('user')
                ->get()
                ->map(function($membro) {
                    return [
                        'id' => $membro->user->id,
                        'name' => $membro->user->name,
                    ];
                });

            return response()->json($membrosDisponiveis);

        } catch (\Exception $e) {
            Log::error('Erro ao listar membros disponíveis', [
                'chat_id' => $this->chatAtivo,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Erro ao carregar membros'], 500);
        }
    }

    public function render()
    {
        return view('church.alliance.church-chat', [
            'isAdmin' => Auth::user()->isIgrejaAdmin(),
            'isAdminGrupo' => $this->isAdminGrupo,
            'participantes' => $this->participantes,
            'adminsChat' => $this->adminsChat,
        ]);
    }
}
