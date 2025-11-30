<?php

namespace App\Livewire\Church\Alliance;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use App\Models\AliancaMensagem;
use App\Models\Comunidade\AliancaComunidadeMensagem;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use App\Models\Chats\MensagemPrivada;
use App\Models\Igrejas\IgrejaAlianca;
use App\Helpers\SupabaseHelper;

#[Title('Comunidade dos Líderes Religiosos')]
#[Layout('components.layouts.app')]
class Community extends Component
{
    use WithFileUploads;
    // Propriedades para Chat Público
    public $aliancaId;
    public $tipoChat = 'lideres'; // 'lideres' ou 'comunidade'
    public $novaMensagemChat = '';
    public $mensagens = [];
    public $lideresOnline = [];
    public $membroAtual;

    // Propriedades para Mensagens Privadas (Apenas uma aba)
    public $conversas = [];
    public $conversaAtiva = null;
    public $mensagensPrivadas = [];
    public $novaMensagemPrivada = '';
    public $usuariosDisponiveis = [];
    public $usuarioSelecionado = null;

    // Propriedades para a aba Privadas (com 2 para compatibilidade com view)
    public $conversas2 = [];
    public $conversaAtiva2 = null;
    public $mensagensPrivadas2 = [];
    public $novaMensagemPrivada2 = '';

    // Propriedades gerais
    public $alianca;
    public $abaAtiva = 'chat'; // 'chat', 'private', 'reunion'
    public $minhasAliancas = [];

    // Propriedades para Reuniões
    public $reuniaoSelecionada = null;
    public $filtroReunioes = 'todas'; // 'todas', 'minhas', 'hoje', 'semana', 'mes'
    public $proximasReunioes = [];
    public $todasReunioes = [];
    public $todasReunioesData = []; // Para armazenar os dados das reuniões
    public $currentPage = 1;
    public $perPage = 15;
    public $totalReunioes = 0;

    // Propriedades para Modal de Reunião
    public $isEditing = false;
    public $titulo = '';
    public $descricao = '';
    public $tipo = 'reuniao';
    public $data_agendamento = '';
    public $hora_inicio = '';
    public $hora_fim = '';
    public $local = '';
    public $modalidade = 'presencial';
    public $link_reuniao = '';
    public $responsavel_id = '';
    public $convidado_id = '';
    public $observacoes = '';
    public $aliancaSelecionada = ''; // Nova propriedade para aliança selecionada no modal
    public $lideresDisponiveis = [];
    public $membrosDisponiveis = [];
    public $aliancasDisponiveis = []; // Lista de alianças disponíveis para seleção

    // Propriedades para Modal de Cancelamento
    public $reuniaoParaCancelar = null;
    public $senhaCancelamento = '';
    public $confirmacaoOmnigrejas = '';
    public $confirmacaoTitulo = '';

    // Propriedades para mídia
    public $arquivoAudio;
    public $arquivoAnexo;
    public $gravandoAudio = false;
    public $mediaRecorder;
    public $audioChunks = [];

    // Propriedades para mídia da aliança (chat público)
    public $arquivoAudioAlianca;
    public $arquivoAnexoAlianca;

    // Propriedades para validação de mídia
    public $rules = [
        'novaMensagemPrivada2' => 'nullable|string|max:50000',
        'arquivoAudio' => 'nullable|file|mimes:mp3,wav,ogg,webm,video/webm|max:10240', // 10MB (incluindo video/webm)
        'arquivoAnexo' => 'nullable|file|max:20480', // 20MB
        'arquivoAudioAlianca' => 'nullable|file|mimes:mp3,wav,ogg,aac,m4a,webm,video/webm|max:25600', // 25MB para alianças (incluindo video/webm)
        'arquivoAnexoAlianca' => 'nullable|file|max:51200', // 50MB para alianças
    ];

    public function mount($aliancaId = null)
    {
        $this->aliancaId = $aliancaId;

        // Sempre carregar o membro atual primeiro
        $this->carregarMembroAtual();

        // Sempre carregar as alianças disponíveis
        $this->carregarMinhasAliancas();

        // Sempre carregar usuários disponíveis (independente de aliança)
        $this->carregarUsuariosDisponiveis();

        // Sempre carregar conversas privadas (independente de aliança)
        $this->carregarConversasPrivadas();
        $this->carregarConversasPrivadas2();

        if ($this->aliancaId) {
            $this->carregarAlianca();
            $this->carregarMensagens();
            $this->carregarLideresOnline();
        }

        // Sempre carregar reuniões
        $this->carregarReunioes();

        // Atualizar status dos agendamentos automaticamente
        $this->atualizarStatusAgendamentos();
    }

    /**
     * Atualiza o status dos agendamentos baseado na data/hora atual
     */
    protected function atualizarStatusAgendamentos()
    {
        try {
            // Usar o método do modelo para atualizar status automaticamente
            $atualizados = \App\Models\Eventos\Agendamento::atualizarStatusAutomaticamente();

            if ($atualizados > 0) {

                // \Illuminate\Support\Facades\Log::info("Status de {$atualizados} agendamentos atualizados automaticamente para 'realizado'");
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao atualizar status dos agendamentos: ' . $e->getMessage());
        }
    }

    protected function carregarAlianca()
    {
        if (!$this->membroAtual) {
            $this->alianca = null;
            return;
        }

        $this->alianca = IgrejaAlianca::where('alianca_id', $this->aliancaId)
                                        ->where('igreja_id', Auth::user()->getIgrejaId())
                                        ->where('status', 'ativo')
                                        ->first();
    }

    protected function carregarMembroAtual()
    {
        // Buscar membro ativo do usuário logado
        $this->membroAtual = IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro') // ✅ Garantir que seja líder
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar a comunidade.');
        }

        // Se há aliança selecionada, verificar se o membro pertence a uma igreja da aliança
        if ($this->aliancaId) {
           
            $pertenceAlianca = IgrejaAlianca::where('alianca_id', $this->aliancaId)
                ->where('igreja_id', Auth::user()->getIgrejaId())
                ->where('status', 'ativo')
                ->exists();


            if (!$pertenceAlianca) {
                abort(403, 'Acesso negado. Você não pertence a esta aliança.');
            }
        }
    }

    // ========================================
    // CHAT PÚBLICO DOS LÍDERES
    // ========================================

    protected function carregarMensagens()
    {
        if (!$this->aliancaId) {
            $this->mensagens = [];
            return;
        }

        if ($this->tipoChat === 'lideres') {
            $this->carregarMensagensLideres();
        } elseif ($this->tipoChat === 'comunidade') {
            $this->carregarMensagensComunidade();
        }
    }

    protected function carregarMensagensLideres()
    {
        // Buscar todos os membros líderes da aliança para marcar leituras
        $membrosLideres = IgrejaMembro::whereHas('igreja', function($query) {
                $query->whereHas('aliancas', function($subQuery) {
                    $subQuery->where('alianca_id', $this->aliancaId)
                             ->where('igreja_aliancas.status', 'ativo'); // Especificar tabela
                });
            })
            ->where('cargo', '!=', 'membro')
            ->where('igreja_membros.status', 'ativo') // Especificar tabela
            ->get();

        // Buscar mensagens com foco nas não lidas primeiro
        $mensagensNaoLidas = AliancaMensagem::daAlianca($this->aliancaId)
            ->naoLidasPor($this->membroAtual->id ?? 0)
            ->with(['remetente.user', 'leituras'])
            ->orderBy('created_at', 'asc')
            ->get();

        $mensagensLidas = AliancaMensagem::daAlianca($this->aliancaId)
            ->whereHas('leituras', function($query) {
                $query->where('membro_id', $this->membroAtual->id ?? 0);
            })
            ->with(['remetente.user', 'leituras'])
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limitar histórico para performance
            ->get()
            ->reverse(); // Inverter para ordem cronológica

        // Combinar mensagens (não lidas primeiro, depois lidas)
        $todasMensagens = $mensagensLidas->merge($mensagensNaoLidas);

        $this->mensagens = $todasMensagens->map(function ($mensagem) use ($membrosLideres) {
                // Marcar como lida para todos os líderes (não apenas o atual)
                foreach ($membrosLideres as $lider) {
                    if (!$mensagem->foiLidaPor($lider)) {
                        $mensagem->marcarComoLida($lider);
                    }
                }

                return $mensagem;
            })
            ->unique('id') // Remover duplicatas
            ->sortBy('created_at') // Ordenar cronologicamente
            ->values()
            ->toArray();
    }

    protected function carregarMensagensComunidade()
    {
        // Buscar todos os membros da aliança para marcar leituras
        $membrosAlianca = IgrejaMembro::whereHas('igreja', function($query) {
                $query->whereHas('aliancas', function($subQuery) {
                    $subQuery->where('alianca_id', $this->aliancaId)
                             ->where('igreja_aliancas.status', 'ativo');
                });
            })
            ->where('igreja_membros.status', 'ativo')
            ->get();

        // Buscar mensagens com foco nas não lidas primeiro
        $mensagensNaoLidas = AliancaComunidadeMensagem::daAlianca($this->aliancaId)
            ->naoLidasPor($this->membroAtual->id ?? 0)
            ->with(['remetente.user', 'leituras'])
            ->orderBy('created_at', 'asc')
            ->get();

        $mensagensLidas = AliancaComunidadeMensagem::daAlianca($this->aliancaId)
            ->whereHas('leituras', function($query) {
                $query->where('membro_id', $this->membroAtual->id ?? 0);
            })
            ->with(['remetente.user', 'leituras'])
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limitar histórico para performance
            ->get()
            ->reverse(); // Inverter para ordem cronológica

        // Combinar mensagens (não lidas primeiro, depois lidas)
        $todasMensagens = $mensagensLidas->merge($mensagensNaoLidas);

        $this->mensagens = $todasMensagens->map(function ($mensagem) use ($membrosAlianca) {
                // Marcar como lida para todos os membros da aliança
                foreach ($membrosAlianca as $membro) {
                    if (!$mensagem->foiLidaPor($membro)) {
                        $mensagem->marcarComoLida($membro);
                    }
                }

                return $mensagem;
            })
            ->unique('id') // Remover duplicatas
            ->sortBy('created_at') // Ordenar cronologicamente
            ->values()
            ->toArray();
    }

    protected function carregarLideresOnline()
    {
        // Buscar TODOS os líderes de TODAS as igrejas que fazem parte da aliança
        $this->lideresOnline = IgrejaMembro::whereHas('igreja', function($query) {
                $query->whereHas('aliancas', function($subQuery) {
                    $subQuery->where('alianca_id', $this->aliancaId)
                             ->where('igreja_aliancas.status', 'ativo'); // Especificar tabela
                });
            })
            ->where('cargo', '!=', 'membro')
            ->where('igreja_membros.status', 'ativo') // Especificar tabela
            ->with('user')
            ->get()
            ->toArray();
    }

    public function enviarMensagemChat()
    {   

        
        // Verificar se uma aliança está selecionada
        if (!$this->aliancaId) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Selecione uma aliança primeiro.'
            ]);
            return;
        }

        // Verificar se há arquivo anexado
        if ($this->arquivoAnexoAlianca) {
            $this->enviarArquivoAlianca();
            return;
        }

        // Verificar se há áudio anexado
        if ($this->arquivoAudioAlianca) {
            $this->enviarAudioAlianca();
            return;
        }

        // Se não há anexos, validar mensagem de texto
        if (trim($this->novaMensagemChat) === '') {
            return; // Não enviar mensagem vazia
        }

        $this->validate([
            'novaMensagemChat' => 'required|string|max:1000',
        ]);

        if (!$this->membroAtual) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Membro não encontrado.'
            ]);
            return;
        }

        // Determinar qual modelo usar baseado no tipo de chat
        if ($this->tipoChat === 'lideres') {
            $dadosMensagem = [
                'uuid' => Str::uuid(),
                'alianca_id' => $this->aliancaId,
                'remetente_id' => $this->membroAtual->id,
                'tipo_mensagem' => 'texto',
                'mensagem' => trim($this->novaMensagemChat),
            ];
            $mensagem = AliancaMensagem::create($dadosMensagem);
        } elseif ($this->tipoChat === 'comunidade') {
            $dadosMensagem = [
                'uuid' => Str::uuid(),
                'alianca_id' => $this->aliancaId,
                'remetente_id' => $this->membroAtual->id,
                'tipo_mensagem' => 'texto',
                'mensagem' => trim($this->novaMensagemChat),
            ];
            $mensagem = AliancaComunidadeMensagem::create($dadosMensagem);
        }

        $this->novaMensagemChat = '';
        $this->carregarMensagens();

        // Disparar evento para scroll automático após enviar mensagem
        $this->dispatch('scroll-to-bottom', containerId: 'chatMessages');

        // Emitir evento para outros usuários
        $this->dispatch('mensagem-enviada', $mensagem->id);
    }

    public function marcarMensagensComoLidas()
    {
        $mensagensNaoLidas = AliancaMensagem::daAlianca($this->aliancaId)
            ->naoLidasPor($this->membroAtual->id)
            ->get();

        foreach ($mensagensNaoLidas as $mensagem) {
            $mensagem->marcarComoLida($this->membroAtual);
        }
    }

    // ========================================
    // MENSAGENS PRIVADAS
    // ========================================



    protected function carregarUsuariosDisponiveis()
    {
        // Buscar apenas líderes da mesma aliança do usuário atual
        if ($this->membroAtual && Auth::user()->getIgrejaId()) {
            // Buscar alianças ativas da igreja do usuário
            $aliancasUsuario = \App\Models\Igrejas\IgrejaAlianca::where('igreja_id', Auth::user()->getIgrejaId())
                ->where('status', 'ativo')
                ->pluck('alianca_id')
                ->toArray();

            //** Log::info("Alianças do usuário atual: " . implode(', ', $aliancasUsuario));

            if (!empty($aliancasUsuario)) {
                // Buscar líderes de todas as igrejas que participam das mesmas alianças
                $query = IgrejaMembro::whereHas('igreja', function($query) use ($aliancasUsuario) {
                        $query->whereHas('aliancas', function($subQuery) use ($aliancasUsuario) {
                            $subQuery->whereIn('igreja_aliancas.alianca_id', $aliancasUsuario)
                                     ->where('igreja_aliancas.status', 'ativo');
                        });
                    })
                    ->whereIn('cargo', ['admin', 'pastor', 'ministro', 'membro', 'diacono', 'obreiro']) // ✅ Apenas admin, pastor e ministro, membro, diacono, obreiro
                    ->where('igreja_membros.status', 'ativo') // Especificar tabela
                    ->where('user_id', '!=', Auth::id()); // Excluir o próprio usuário

                $count = $query->count();
                
                //** Log::info("Líderes da mesma aliança encontrados: " . $count);

                $this->usuariosDisponiveis = $query->with('user', 'igreja')->get()->toArray();

            } else {
                //** Log::info("Usuário não participa de nenhuma aliança ativa");
                $this->usuariosDisponiveis = [];
            }
        } else {
            //** Log::info("Membro atual não encontrado ou sem igreja");
            $this->usuariosDisponiveis = [];
        }

        //** Log::info("Total de usuários disponíveis: " . count($this->usuariosDisponiveis));
    }

    protected function carregarConversasPrivadas()
    {
        // Buscar TODAS as conversas do usuário atual (simplificado)
        $conversasQuery = MensagemPrivada::where(function($query) {
                $query->where('remetente_id', Auth::id())
                      ->orWhere('destinatario_id', Auth::id());
            })
            ->with(['remetente', 'destinatario'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($mensagem) {
                $outroUsuarioId = $mensagem->remetente_id === Auth::id()
                    ? $mensagem->destinatario_id
                    : $mensagem->remetente_id;
                return $outroUsuarioId;
            });

        $this->conversas = $conversasQuery->map(function($mensagens, $usuarioId) {
            $ultimaMensagem = $mensagens->first();
            $outroUsuario = $ultimaMensagem->remetente_id === Auth::id()
                ? $ultimaMensagem->destinatario
                : $ultimaMensagem->remetente;

            $naoLidas = $mensagens->where('destinatario_id', Auth::id())
                                   ->filter(function($mensagem) {
                                       return $mensagem->lida_por === null ||
                                              !in_array(Auth::id(), $mensagem->lida_por ?: []);
                                   })
                                   ->count();

            return [
                'usuario_id' => $usuarioId,
                'usuario' => $outroUsuario,
                'ultima_mensagem' => $ultimaMensagem,
                'nao_lidas' => $naoLidas,
                'ultima_atividade' => $ultimaMensagem->created_at,
            ];
        })->sortByDesc('ultima_atividade')->values()->toArray();
    }


    protected function carregarMinhasAliancas()
    {
        if (!$this->membroAtual) {
            $this->minhasAliancas = [];
            return;
        }

        $this->minhasAliancas = IgrejaAlianca::where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'ativo')
            ->with(['alianca.categoria'])
            ->get()
            ->map(function($participacao) {
                // Adicionar dados da aliança para compatibilidade
                $participacao->nome = $participacao->alianca->nome;
                $participacao->descricao = $participacao->alianca->descricao;
                $participacao->categoria = $participacao->alianca->categoria;
                return $participacao;
            });
    }


    protected function carregarMensagensPrivadas()
    {
        if (!$this->conversaAtiva) return;

        $this->mensagensPrivadas = MensagemPrivada::where(function($query) {
                $query->where(function($q) {
                    $q->where('remetente_id', Auth::id())
                      ->where('destinatario_id', $this->conversaAtiva);
                })->orWhere(function($q) {
                    $q->where('remetente_id', $this->conversaAtiva)
                      ->where('destinatario_id', Auth::id());
                });
            })
            ->with(['remetente', 'destinatario'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    protected function marcarMensagensPrivadasComoLidas()
    {
        $mensagens = MensagemPrivada::where('remetente_id', $this->conversaAtiva)
            ->where('destinatario_id', Auth::id())
            ->where(function($query) {
                $query->whereNull('lida_por')
                      ->orWhereRaw("NOT (lida_por::jsonb @> ?::jsonb)", [json_encode([Auth::id()])]);
            })
            ->get();

        foreach ($mensagens as $mensagem) {
            $lidaPor = json_decode($mensagem->lida_por, true) ?: [];
            if (!in_array(Auth::id(), $lidaPor)) {
                $lidaPor[] = Auth::id();
                $mensagem->update(['lida_por' => json_encode($lidaPor)]);
            }
        }
    }

    protected function carregarConversasPrivadas2()
    {
        // Buscar TODAS as conversas do usuário atual (simplificado)
        $conversasQuery = MensagemPrivada::where(function($query) {
                $query->where('remetente_id', Auth::id())
                      ->orWhere('destinatario_id', Auth::id());
            })
            ->with(['remetente', 'destinatario'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($mensagem) {
                $outroUsuarioId = $mensagem->remetente_id === Auth::id()
                    ? $mensagem->destinatario_id
                    : $mensagem->remetente_id;
                return $outroUsuarioId;
            });

        $this->conversas2 = $conversasQuery->map(function($mensagens, $usuarioId) {
            $ultimaMensagem = $mensagens->first();
            $outroUsuario = $ultimaMensagem->remetente_id === Auth::id()
                ? $ultimaMensagem->destinatario
                : $ultimaMensagem->remetente;

            $naoLidas = $mensagens->where('destinatario_id', Auth::id())
                                  ->filter(function($mensagem) {
                                      return $mensagem->lida_por === null ||
                                             !in_array(Auth::id(), $mensagem->lida_por ?: []);
                                  })
                                  ->count();

            return [
                'usuario_id' => $usuarioId,
                'usuario' => $outroUsuario,
                'ultima_mensagem' => $ultimaMensagem,
                'nao_lidas' => $naoLidas,
                'ultima_atividade' => $ultimaMensagem->created_at,
            ];
        })->sortByDesc('ultima_atividade')->values()->toArray();
    }

    protected function carregarMensagensPrivadas2()
    {
        if (!$this->conversaAtiva2) return;

        $this->mensagensPrivadas2 = MensagemPrivada::where(function($query) {
                $query->where(function($q) {
                    $q->where('remetente_id', Auth::id())
                      ->where('destinatario_id', $this->conversaAtiva2);
                })->orWhere(function($q) {
                    $q->where('remetente_id', $this->conversaAtiva2)
                      ->where('destinatario_id', Auth::id());
                });
            })
            ->with(['remetente', 'destinatario'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    protected function marcarMensagensPrivadas2ComoLidas()
    {
        $mensagens = MensagemPrivada::where('remetente_id', $this->conversaAtiva2)
            ->where('destinatario_id', Auth::id())
            ->where(function($query) {
                $query->whereNull('lida_por')
                      ->orWhereRaw("NOT (lida_por::jsonb @> ?::jsonb)", [json_encode([Auth::id()])]);
            })
            ->get();

        foreach ($mensagens as $mensagem) {
            $lidaPor = json_decode($mensagem->lida_por, true) ?: [];
            if (!in_array(Auth::id(), $lidaPor)) {
                $lidaPor[] = Auth::id();
                $mensagem->update(['lida_por' => json_encode($lidaPor)]);
            }
        }
    }


    public function iniciarConversa($usuarioId)
    {
        $this->conversaAtiva = $usuarioId;
        $this->carregarMensagensPrivadas();
    }

    public function selecionarConversa($usuarioId)
    {
        $this->conversaAtiva = $usuarioId;
        $this->carregarMensagensPrivadas();
        $this->marcarMensagensPrivadasComoLidas();
    }

    public function selecionarConversa2($usuarioId)
    {
        $this->conversaAtiva2 = $usuarioId;
        $this->carregarMensagensPrivadas2();
        $this->marcarMensagensPrivadas2ComoLidas();

        // Disparar evento para scroll automático
        $this->dispatch('scroll-to-bottom', containerId: 'privateChatMessages');
    }

    public function enviarMensagemPrivada()
    {
        $this->validate([
            'novaMensagemPrivada' => 'required|string|max:1000',
            'conversaAtiva' => 'required',
        ]);

        MensagemPrivada::create([
            'id' => Str::uuid(),
            'remetente_id' => Auth::id(),
            'destinatario_id' => $this->conversaAtiva,
            'conteudo' => trim($this->novaMensagemPrivada),
            'lida' => false,
        ]);

        $this->novaMensagemPrivada = '';
        $this->carregarMensagensPrivadas();
        $this->carregarConversasPrivadas();

        $this->dispatch('mensagem-privada-enviada');
    }

    public function enviarMensagemPrivada2()
    {
        if (!$this->conversaAtiva2) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Verificar se há arquivo anexado
        if ($this->arquivoAnexo) {
            $this->enviarArquivoPrivado();
            return;
        }

        // Verificar se há áudio anexado
        if ($this->arquivoAudio) {
            $this->enviarAudioPrivado();
            return;
        }

        // Se não há anexos, enviar mensagem de texto
        if (trim($this->novaMensagemPrivada2) === '') {
            return; // Não enviar mensagem vazia
        }

        $dadosMensagem = [
            'id' => (string) Str::uuid(),
            'remetente_id' => $user->id,
            'destinatario_id' => $this->conversaAtiva2,
            'tipo_mensagem' => 'texto',
            'conteudo' => trim($this->novaMensagemPrivada2),
            'lida_por' => [$user->id],
        ];

        MensagemPrivada::create($dadosMensagem);

        $this->novaMensagemPrivada2 = '';
        $this->carregarMensagensPrivadas2();
        $this->carregarConversasPrivadas2();

        // Disparar evento para scroll automático após enviar mensagem
        $this->dispatch('scroll-to-bottom', containerId: 'privateChatMessages');

        $this->dispatch('mensagem-privada-enviada');
    }

    public function enviarArquivoPrivado()
    {
        $this->validateOnly('arquivoAnexo');

        if (!$this->conversaAtiva2 || !$this->arquivoAnexo) {
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
            $path = SupabaseHelper::fazerUploadMensagemPrivada($this->arquivoAnexo, $user->id, $this->conversaAtiva2, 'arquivo');

            $dadosMensagem = [
                'id' => (string) Str::uuid(),
                'remetente_id' => $user->id,
                'destinatario_id' => $this->conversaAtiva2,
                'tipo_mensagem' => $tipoArquivo,
                'conteudo' => trim($this->novaMensagemPrivada2) ?: null,
                'anexo_url' => $path,
                'anexo_nome' => $this->arquivoAnexo->getClientOriginalName(),
                'anexo_tamanho' => $this->arquivoAnexo->getSize(),
                'anexo_tipo' => $this->arquivoAnexo->getMimeType(),
                'lida_por' => [$user->id],
            ];

            MensagemPrivada::create($dadosMensagem);

            $this->arquivoAnexo = null;
            $this->novaMensagemPrivada2 = '';
            $this->carregarMensagensPrivadas2();
            $this->carregarConversasPrivadas2();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
            $this->dispatch('mensagem-privada-enviada');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Arquivo enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()]);
        }
    }

    public function enviarArquivoAlianca()
    {
        $this->validateOnly('arquivoAnexoAlianca');

        if (!$this->aliancaId || !$this->arquivoAnexoAlianca) {
            return;
        }

        $user = Auth::user();
        if (!$user || !$this->membroAtual) {
            return;
        }

        try {
            // Determinar tipo baseado na extensão
            $extensao = strtolower($this->arquivoAnexoAlianca->getClientOriginalExtension());
            $tipoArquivo = $this->determinarTipoArquivo($extensao);

            // Upload para Supabase usando o Helper para alianças
            $path = SupabaseHelper::fazerUploadAlianca($this->arquivoAnexoAlianca, $this->aliancaId, 'arquivo');

            // Criar anexo para a mensagem
            $anexo = [
                'url' => $path,
                'nome' => $this->arquivoAnexoAlianca->getClientOriginalName(),
                'tamanho' => $this->arquivoAnexoAlianca->getSize(),
                'tipo' => $this->arquivoAnexoAlianca->getMimeType(),
                'tipo_arquivo' => $tipoArquivo
            ];

            // Determinar qual modelo usar baseado no tipo de chat
            if ($this->tipoChat === 'lideres') {
                AliancaMensagem::create([
                    'uuid' => Str::uuid(),
                    'alianca_id' => $this->aliancaId,
                    'remetente_id' => $this->membroAtual->id,
                    'tipo_mensagem' => $tipoArquivo,
                    'mensagem' => trim($this->novaMensagemChat) ?: null,
                    'anexos' => [$anexo],
                ]);
            } elseif ($this->tipoChat === 'comunidade') {
                AliancaComunidadeMensagem::create([
                    'uuid' => Str::uuid(),
                    'alianca_id' => $this->aliancaId,
                    'remetente_id' => $this->membroAtual->id,
                    'tipo_mensagem' => $tipoArquivo,
                    'mensagem' => trim($this->novaMensagemChat) ?: null,
                    'anexos' => [$anexo],
                ]);
            }

            $this->arquivoAnexoAlianca = null;
            $this->novaMensagemChat = '';
            $this->carregarMensagens();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'chatMessages']);
            $this->dispatch('mensagem-enviada');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Arquivo enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar arquivo: ' . $e->getMessage()]);
        }
    }

    public function enviarAudioAlianca()
    {
        $this->validateOnly('arquivoAudioAlianca');

        if (!$this->aliancaId || !$this->arquivoAudioAlianca) {
            return;
        }

        $user = Auth::user();
        if (!$user || !$this->membroAtual) {
            return;
        }

        try {
            // Upload para Supabase usando o Helper para alianças
            $path = SupabaseHelper::fazerUploadAlianca($this->arquivoAudioAlianca, $this->aliancaId, 'audio');

            // Criar anexo para a mensagem
            $anexo = [
                'url' => $path,
                'nome' => $this->arquivoAudioAlianca->getClientOriginalName(),
                'tamanho' => $this->arquivoAudioAlianca->getSize(),
                'tipo' => $this->arquivoAudioAlianca->getMimeType(),
                'tipo_arquivo' => 'audio'
            ];

            // Determinar qual modelo usar baseado no tipo de chat
            if ($this->tipoChat === 'lideres') {
                AliancaMensagem::create([
                    'uuid' => Str::uuid(),
                    'alianca_id' => $this->aliancaId,
                    'remetente_id' => $this->membroAtual->id,
                    'tipo_mensagem' => 'audio',
                    'mensagem' => trim($this->novaMensagemChat) ?: null,
                    'anexos' => [$anexo],
                ]);
            } elseif ($this->tipoChat === 'comunidade') {
                AliancaComunidadeMensagem::create([
                    'uuid' => Str::uuid(),
                    'alianca_id' => $this->aliancaId,
                    'remetente_id' => $this->membroAtual->id,
                    'tipo_mensagem' => 'audio',
                    'mensagem' => trim($this->novaMensagemChat) ?: null,
                    'anexos' => [$anexo],
                ]);
            }

            $this->arquivoAudioAlianca = null;
            $this->novaMensagemChat = '';
            $this->carregarMensagens();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'chatMessages']);
            $this->dispatch('mensagem-enviada');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Áudio enviado com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao enviar áudio: ' . $e->getMessage()]);
        }
    }

    public function enviarAudioPrivado()
    {
        $this->validateOnly('arquivoAudio');

        if (!$this->conversaAtiva2 || !$this->arquivoAudio) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        try {
            // Upload para Supabase usando o Helper para mensagens privadas
            $path = SupabaseHelper::fazerUploadMensagemPrivada($this->arquivoAudio, $user->id, $this->conversaAtiva2, 'audio');

            $dadosMensagem = [
                'id' => (string) Str::uuid(),
                'remetente_id' => $user->id,
                'destinatario_id' => $this->conversaAtiva2,
                'tipo_mensagem' => 'audio',
                'conteudo' => trim($this->novaMensagemPrivada2) ?: null,
                'anexo_url' => $path,
                'anexo_nome' => $this->arquivoAudio->getClientOriginalName(),
                'anexo_tamanho' => $this->arquivoAudio->getSize(),
                'anexo_tipo' => $this->arquivoAudio->getMimeType(),
                'lida_por' => [$user->id],
            ];

            MensagemPrivada::create($dadosMensagem);

            // Limpar campos
            $this->novaMensagemPrivada2 = '';
            $this->arquivoAudio = null;

            $this->carregarMensagensPrivadas2();
            $this->carregarConversasPrivadas2();

            $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
            $this->dispatch('mensagem-privada-enviada');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Áudio enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar áudio privado: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao enviar áudio.'
            ]);
        }
    }

    public function receberAudioGravado($audioBase64, $fileName = null)
    {
        if (!$this->conversaAtiva2) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        try {
            // Converter o blob base64 para arquivo
            $audioData = base64_decode($audioBase64);
            $fileName = $fileName ?: 'gravacao_audio_' . time() . '.webm';

            // Criar arquivo temporário
            $tempPath = tempnam(sys_get_temp_dir(), 'audio_');
            file_put_contents($tempPath, $audioData);

            // Determinar MIME type correto - alguns navegadores usam 'video/webm' para WebM com áudio
            $mimeType = 'audio/webm';
            // Verificar se o arquivo é realmente um WebM de áudio (não vídeo)
            if (strpos($fileName, '.webm') !== false) {
                // WebM pode conter áudio ou vídeo, mas neste contexto é áudio
                $mimeType = 'audio/webm';
            }

            // Criar UploadedFile
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $fileName,
                $mimeType,
                null,
                true
            );

            // Upload para Supabase usando o Helper para mensagens privadas
            $path = SupabaseHelper::fazerUploadMensagemPrivada($uploadedFile, $user->id, $this->conversaAtiva2, 'audio');

            $dadosMensagem = [
                'id' => (string) Str::uuid(),
                'remetente_id' => $user->id,
                'destinatario_id' => $this->conversaAtiva2,
                'tipo_mensagem' => 'audio',
                'conteudo' => null,
                'anexo_url' => $path,
                'anexo_nome' => $fileName,
                'anexo_tamanho' => strlen($audioData),
                'anexo_tipo' => $mimeType,
                'lida_por' => [$user->id],
            ];

            MensagemPrivada::create($dadosMensagem);

            // Limpar arquivo temporário
            unlink($tempPath);

            $this->carregarMensagensPrivadas2();
            $this->carregarConversasPrivadas2();
            $this->dispatch('scroll-to-bottom', ['containerId' => 'privateChatMessages']);
            $this->dispatch('mensagem-privada-enviada');
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Áudio enviado com sucesso!']);

        } catch (\Exception $e) {
            Log::error('Erro ao processar áudio gravado: ' . $e->getMessage());
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Erro ao processar áudio gravado.']);
        }
    }


    public function receberAudioGravadoAlianca($audioBase64, $fileName = null)
    {
        // Validações básicas
        if (!$this->aliancaId) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Aliança não selecionada.'
            ]);
            return;
        }

        if (!$this->membroAtual) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Membro não encontrado.'
            ]);
            return;
        }

        try {
            // Converter o blob base64 para arquivo
            $audioData = base64_decode($audioBase64);
            $fileName = $fileName ?: 'gravacao_audio_' . time() . '.webm';

            // Criar arquivo temporário
            $tempPath = tempnam(sys_get_temp_dir(), 'audio_');
            file_put_contents($tempPath, $audioData);

            // Determinar MIME type correto
            $mimeType = 'audio/webm';

            // Criar UploadedFile
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $fileName,
                $mimeType,
                null,
                true
            );

            // Upload para Supabase usando o Helper para alianças
            $path = SupabaseHelper::fazerUploadAlianca($uploadedFile, $this->aliancaId, 'audio');

            // Preparar dados da mensagem
            $dadosMensagem = [
                'uuid' => Str::uuid(),
                'alianca_id' => $this->aliancaId,
                'remetente_id' => $this->membroAtual->id,
                'tipo_mensagem' => 'audio',
                'mensagem' => null,
                'anexos' => [
                    [
                        'url' => $path,
                        'nome' => $fileName,
                        'tamanho' => strlen($audioData),
                        'tipo' => $mimeType,
                        'tipo_arquivo' => 'audio'
                    ]
                ]
            ];

            // Criar mensagem baseada no tipo de chat
            if ($this->tipoChat === 'lideres') {
                AliancaMensagem::create($dadosMensagem);
            } elseif ($this->tipoChat === 'comunidade') {
                AliancaComunidadeMensagem::create($dadosMensagem);
            } else {
                throw new \Exception('Tipo de chat inválido');
            }

            // Limpar arquivo temporário
            unlink($tempPath);

            // Recarregar mensagens e atualizar UI
            $this->carregarMensagens();
            $this->dispatch('scroll-to-bottom', ['containerId' => 'chatMessages']);
            $this->dispatch('mensagem-enviada');
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Áudio enviado com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar áudio gravado da aliança: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao processar áudio gravado.'
            ]);
        }
    }



    public function openModal()
    {
        // Método para abrir o modal - seguindo padrão do members
        $this->usuarioSelecionado = null; // Limpar seleção anterior
    }

    public function iniciarConversa2()
    {
        $this->validate([
            'usuarioSelecionado' => 'required',
        ], [
            'usuarioSelecionado.required' => 'Selecione um membro para iniciar a conversa.'
        ]);

        $this->conversaAtiva2 = $this->usuarioSelecionado;
        $this->usuarioSelecionado = null; // Limpar seleção
        $this->carregarMensagensPrivadas2();

        // Ativar a aba privada após iniciar conversa
        $this->dispatch('ativar-aba-privada');

        // Também recarregar conversas para atualizar a lista
        $this->carregarConversasPrivadas2();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Conversa iniciada com sucesso!'
        ]);
    }


    public function selecionarAlianca($aliancaId, $tipoChat = 'lideres')
    {
        $this->aliancaId = $aliancaId;
        $this->tipoChat = $tipoChat;

        // Carregar dados da aliança selecionada
        $this->carregarAlianca();
        $this->carregarMensagens();
        $this->carregarLideresOnline();
        $this->carregarConversasPrivadas();
        $this->carregarConversasPrivadas2();
        $this->carregarUsuariosDisponiveis();

        // Carregar reuniões da aliança selecionada
        $this->carregarReunioes();

        // Scroll para mensagens não lidas
        $this->dispatch('scroll-to-unread');

        // Disparar evento para scroll automático das mensagens da aliança
        $this->dispatch('scroll-to-bottom', containerId: 'chatMessages');
    }

    // ========================================
    // MÉTODOS DE CONTAGEM
    // ========================================

    public function getMensagensNaoLidasCount(): int
    {
        return AliancaMensagem::daAlianca($this->aliancaId)
            ->naoLidasPor($this->membroAtual->id ?? 0)
            ->count();
    }

    public function getMensagensPrivadasNaoLidasTotal(): int
    {
        return MensagemPrivada::where('destinatario_id', Auth::id())
            ->where(function($query) {
                $query->whereNull('lida_por')
                      ->orWhereRaw("NOT (lida_por::jsonb @> ?::jsonb)", [json_encode([Auth::id()])]);
            })
            ->whereHas('remetente', function($query) {
                $query->whereHas('membros', function($subQuery) {
                    $subQuery->whereHas('igreja', function($igrejaQuery) {
                        $igrejaQuery->whereHas('aliancas', function($aliancaQuery) {
                            $aliancaQuery->where('alianca_id', $this->aliancaId)
                                        ->where('igreja_aliancas.status', 'ativo'); // Especificar tabela
                        });
                    })
                    ->where('cargo', '!=', 'membro')
                    ->where('igreja_membros.status', 'ativo'); // Especificar tabela
                });
            })
            ->count();
    }

    public function getMensagensPrivadas2NaoLidasTotal(): int
    {
        return MensagemPrivada::where('destinatario_id', Auth::id())
            ->where(function($query) {
                $query->whereNull('lida_por')
                      ->orWhereRaw("NOT (lida_por::jsonb @> ?::jsonb)", [json_encode([Auth::id()])]);
            })
            ->where(function($query) {
                // Remetente deve ser membro da aliança
                $query->whereHas('remetente', function($subQuery) {
                    $subQuery->whereHas('membros', function($membroQuery) {
                        $membroQuery->whereHas('igreja', function($igrejaQuery) {
                            $igrejaQuery->whereHas('aliancas', function($aliancaQuery) {
                                $aliancaQuery->where('alianca_id', $this->aliancaId)
                                            ->where('igreja_aliancas.status', 'ativo');
                            });
                        })
                        ->where('igreja_membros.status', 'ativo');
                    });
                });
            })
            ->count();
    }

    public function getTotalMensagens(): int
    {
        return AliancaMensagem::daAlianca($this->aliancaId)->count();
    }

    public function getLideresCount(): int
    {
        return count($this->lideresOnline);
    }

    // ========================================
    // EVENTOS
    // ========================================

    #[On('mensagem-enviada')]
    public function atualizarMensagens()
    {
        $this->carregarMensagens();
    }

    #[On('mensagem-privada-enviada')]
    public function atualizarConversasPrivadas()
    {
        $this->carregarConversasPrivadas();
        $this->carregarConversasPrivadas2();
        if ($this->conversaAtiva) {
            $this->carregarMensagensPrivadas();
        }
        if ($this->conversaAtiva2) {
            $this->carregarMensagensPrivadas2();
        }
    }


    public function mudarAba($aba)
    {
        $this->abaAtiva = $aba;
    }

    // ========================================
    // MÉTODOS PARA REUNIÕES
    // ========================================

    protected function carregarReunioes()
    {
        // Carregar reuniões próximas (próximos 7 dias)
        $this->carregarProximasReunioes();

        // Carregar todas as reuniões com filtros
        $this->carregarTodasReunioes();
    }

    protected function carregarProximasReunioes()
    {
        $query = \App\Models\Eventos\Agendamento::with(['organizador', 'responsavel', 'convidado', 'igreja', 'alianca'])
            ->where(function($q) {
                // Reuniões da aliança atual (todos os membros da aliança podem ver)
                if ($this->aliancaId) {
                    $q->where('alianca_id', $this->aliancaId);
                }
                // Reuniões das alianças do usuário (se não há aliança específica selecionada)
                elseif ($this->membroAtual && Auth::user()->getIgrejaId()) {
                    $q->whereHas('alianca', function($aliancaQuery) {
                        $aliancaQuery->whereHas('participacoes', function($participacaoQuery) {
                            $participacaoQuery->where('igreja_id', Auth::user()->getIgrejaId())
                                             ->where('status', 'ativo');
                        });
                    })->orWhere('igreja_id', Auth::user()->getIgrejaId());
                }
            })
            ->where('data_agendamento', '>=', now()->toDateString())
            ->where('data_agendamento', '<=', now()->addDays(7)->toDateString())
            ->where('status', '!=', 'cancelado')
            ->orderBy('data_agendamento', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->limit(10);

        $this->proximasReunioes = $query->get();
    }

    protected function carregarTodasReunioes()
    {
        $query = \App\Models\Eventos\Agendamento::with(['organizador', 'responsavel', 'convidado', 'igreja', 'alianca'])
            ->where(function($q) {
                // Reuniões da aliança atual (todos os membros da aliança podem ver)
                if ($this->aliancaId) {
                    $q->where('alianca_id', $this->aliancaId);
                }
                // Reuniões das alianças do usuário (se não há aliança específica selecionada)
                elseif ($this->membroAtual && Auth::user()->getIgrejaId()) {
                    $q->whereHas('alianca', function($aliancaQuery) {
                        $aliancaQuery->whereHas('participacoes', function($participacaoQuery) {
                            $participacaoQuery->where('igreja_id', Auth::user()->getIgrejaId())
                                             ->where('status', 'ativo');
                        });
                    })->orWhere('igreja_id', Auth::user()->getIgrejaId());
                }
            });

        // Aplicar filtros
        switch ($this->filtroReunioes) {
            case 'minhas':
                $query->where('organizador_id', Auth::id());
                break;
            case 'hoje':
                $query->where('data_agendamento', now()->toDateString());
                break;
            case 'semana':
                $query->whereBetween('data_agendamento', [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString()
                ]);
                break;
            case 'mes':
                $query->whereYear('data_agendamento', now()->year)
                      ->whereMonth('data_agendamento', now()->month);
                break;
        }

        $query->orderBy('data_agendamento', 'desc')
              ->orderBy('hora_inicio', 'desc');

        // Usar paginação manual para compatibilidade com Livewire
        $total = $query->count();
        $offset = ($this->currentPage - 1) * $this->perPage;

        $this->todasReunioesData = $query->skip($offset)->take($this->perPage)->get()->toArray();
        $this->totalReunioes = $total;
    }

    protected function carregarAliancasDisponiveis()
    {
        if (!$this->membroAtual || !Auth::user()->getIgrejaId()) {
            $this->aliancasDisponiveis = collect();
            return;
        }

        try {
            // Método 1: Buscar alianças criadas pelo usuário
            $aliancasCriadas = \App\Models\Igrejas\AliancaIgreja::where('created_by', Auth::id())
                ->where('ativa', true)
                ->get();

            // Método 2: Buscar alianças através da tabela pivot igreja_aliancas
            $aliancasParticipadas = \App\Models\Igrejas\AliancaIgreja::join('igreja_aliancas', 'aliancas_igrejas.id', '=', 'igreja_aliancas.alianca_id')
                ->where('igreja_aliancas.igreja_id', Auth::user()->getIgrejaId())
                ->where('igreja_aliancas.status', 'ativo')
                ->where('aliancas_igrejas.ativa', true)
                ->select('aliancas_igrejas.*')
                ->get();

            // Combinar e remover duplicatas
            $todasAliancas = $aliancasCriadas->merge($aliancasParticipadas)->unique('id');

            $this->aliancasDisponiveis = $todasAliancas->sortBy('nome')->map(function($alianca) {
                return [
                    'id' => $alianca->id,
                    'nome' => $alianca->nome,
                    'sigla' => $alianca->sigla,
                    'status' => $alianca->status,
                    'tipo' => $alianca->created_by === Auth::id() ? 'criada' : 'participante'
                ];
            })->values();

            // Debug - remover depois
            // \Illuminate\Support\Facades\Log::info('Alianças carregadas para usuário ' . Auth::id(), [
            //     'total' => count($this->aliancasDisponiveis),
            //     'criadas' => $aliancasCriadas->count(),
            //     'participadas' => $aliancasParticipadas->count(),
            //     'igreja_id' => Auth::user()->getIgrejaId()
            // ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao carregar alianças: ' . $e->getMessage());
            $this->aliancasDisponiveis = collect();
        }
    }

    protected function carregarLideresDisponiveis()
    {
        $aliancaId = $this->aliancaSelecionada ?: $this->aliancaId;

        if ($aliancaId) {
            // Buscar participação da igreja na aliança
            $igrejaAlianca = \App\Models\Igrejas\IgrejaAlianca::where('alianca_id', $aliancaId)
                ->where('igreja_id', Auth::user()->getIgrejaId())
                ->where('status', 'ativo')
                ->first();

            if ($igrejaAlianca) {
                // Carregar líderes da aliança usando a tabela alianca_lideres
                $this->lideresDisponiveis = \App\Models\Igrejas\AliancaLider::where('igreja_alianca_id', $igrejaAlianca->id)
                    ->where('ativo', true)
                    ->with(['membro.user', 'membro.igreja'])
                    ->get();

                // \Illuminate\Support\Facades\Log::info('Líderes da aliança carregados', [
                //     'alianca_id' => $aliancaId,
                //     'igreja_alianca_id' => $igrejaAlianca->id,
                //     'total_lideres' => count($this->lideresDisponiveis)
                // ]);
            } else {
                $this->lideresDisponiveis = collect();
                \Illuminate\Support\Facades\Log::warning('Participação da igreja na aliança não encontrada', [
                    'alianca_id' => $aliancaId,
                    'igreja_id' => Auth::user()->getIgrejaId()
                ]);
            }
        } else {
            $this->lideresDisponiveis = collect();
        }
    }

    protected function carregarMembrosDisponiveis()
    {
        $aliancaId = $this->aliancaSelecionada ?: $this->aliancaId;

        if ($aliancaId) {
            // Carregar TODOS os membros da aliança selecionada (de todas as igrejas participantes)
            $this->membrosDisponiveis = \App\Models\Igrejas\IgrejaMembro::join('igreja_aliancas', 'igreja_membros.igreja_id', '=', 'igreja_aliancas.igreja_id')
                ->where('igreja_aliancas.alianca_id', $aliancaId)
                ->where('igreja_aliancas.status', 'ativo')
                ->where('igreja_membros.status', 'ativo')
                ->with('user', 'igreja')
                ->select('igreja_membros.*')
                ->get();

            // \Illuminate\Support\Facades\Log::info('Membros carregados para aliança ' . $aliancaId, [
            //     'total' => count($this->membrosDisponiveis),
            //     'alianca_selecionada' => $this->aliancaSelecionada,
            //     'alianca_atual' => $this->aliancaId
            // ]);
        } else {
            $this->membrosDisponiveis = collect();
        }
    }

    public function abrirModalReuniao()
    {
        $this->resetModalReuniao();
        $this->carregarAliancasDisponiveis();
        $this->carregarLideresDisponiveis();
        $this->carregarMembrosDisponiveis();
        $this->isEditing = false;
    }

    public function updatedAliancaSelecionada()
    {
        // Quando a aliança for selecionada, recarregar líderes e membros
        $this->carregarLideresDisponiveis();
        $this->carregarMembrosDisponiveis();
    }

    public function editarReuniao($reuniaoId)
    {
        $reuniao = \App\Models\Eventos\Agendamento::find($reuniaoId);

        if (!$reuniao || $reuniao->organizador_id !== Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Você não tem permissão para editar esta reunião.'
            ]);
            return;
        }

        if ($reuniao->status === 'cancelado') {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível editar uma reunião cancelada.'
            ]);
            return;
        }

        // Verificar se o evento já começou
        $dataHoraAtual = now();
        $dataHoraReuniao = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reuniao->data_agendamento->format('Y-m-d') . ' ' . $reuniao->hora_inicio->format('H:i:s'));

        if ($dataHoraAtual->gte($dataHoraReuniao)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível editar uma reunião que já começou ou está em andamento.'
            ]);
            return;
        }

        // Verificação adicional: não permitir edição se faltar menos de 1 hora para o evento
        $diferencaEmHoras = $dataHoraAtual->diffInHours($dataHoraReuniao, false);
        if ($diferencaEmHoras < 1 && $diferencaEmHoras >= 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível editar uma reunião com menos de 1 hora de antecedência.'
            ]);
            return;
        }

        $this->isEditing = true;
        $this->titulo = $reuniao->titulo;
        $this->descricao = $reuniao->descricao;
        $this->tipo = $reuniao->tipo;
        $this->data_agendamento = $reuniao->data_agendamento->format('Y-m-d');
        $this->hora_inicio = $reuniao->hora_inicio ? $reuniao->hora_inicio->format('H:i') : '';
        $this->hora_fim = $reuniao->hora_fim ? $reuniao->hora_fim->format('H:i') : '';
        $this->local = $reuniao->local;
        $this->modalidade = $reuniao->modalidade;
        $this->link_reuniao = $reuniao->link_reuniao;
        $this->responsavel_id = $reuniao->responsavel_id;
        $this->convidado_id = $reuniao->convidado_id;
        $this->observacoes = $reuniao->observacoes;
        $this->aliancaSelecionada = $reuniao->alianca_id;
        $this->reuniaoSelecionada = $reuniao->id; // Definir o ID da reunião sendo editada

        $this->carregarLideresDisponiveis();
        $this->carregarMembrosDisponiveis();

        $this->dispatch('open-meeting-modal');
    }

    public function salvarReuniao()
    {
        $this->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:reuniao,consulta,acompanhamento,outro',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'nullable|date_format:H:i|after:hora_inicio',
            'modalidade' => 'required|in:presencial,online,hibrido',
            'local' => 'required_if:modalidade,presencial,hibrido',
            'link_reuniao' => 'nullable', // Removido required_if para permitir flexibilidade
            'aliancaSelecionada' => 'required|exists:aliancas_igrejas,id',
            'convidado_id' => 'nullable|exists:users,id',
        ]);

        $dados = [
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'tipo' => $this->tipo,
            'data_agendamento' => $this->data_agendamento,
            'hora_inicio' => $this->hora_inicio,
            'hora_fim' => $this->hora_fim ?: null,
            'local' => $this->local,
            'modalidade' => $this->modalidade,
            'link_reuniao' => $this->link_reuniao,
            'organizador_id' => Auth::id(),
            'responsavel_id' => $this->responsavel_id ?: null,
            'convidado_id' => $this->convidado_id,
            'observacoes' => $this->observacoes,
            'status' => 'agendado',
        ];

        // Adicionar relacionamento com aliança ou igreja
        if ($this->aliancaId) {
            $dados['alianca_id'] = $this->aliancaId;
        } elseif ($this->membroAtual && Auth::user()->getIgrejaId()) {
            $dados['igreja_id'] = Auth::user()->getIgrejaId();
        }

        if ($this->isEditing) {
            // Para edição, usamos o ID da reunião que foi passada quando abrimos o modal
            $reuniaoExistente = \App\Models\Eventos\Agendamento::find($this->reuniaoSelecionada);

            if ($reuniaoExistente && $reuniaoExistente->organizador_id === Auth::id()) {
                $reuniaoExistente->update($dados);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Reunião atualizada com sucesso!'
                ]);
            } else {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Reunião não encontrada ou você não tem permissão para editá-la.'
                ]);
                return;
            }
        } else {
            \App\Models\Eventos\Agendamento::create($dados);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Reunião agendada com sucesso!'
            ]);
        }

        $this->resetModalReuniao();
        $this->carregarReunioes();

        $this->dispatch('close-meeting-modal');
    }

    public function cancelarReuniao($reuniaoId)
    {
        $reuniao = \App\Models\Eventos\Agendamento::find($reuniaoId);

        if (!$reuniao || $reuniao->organizador_id !== Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Você não tem permissão para cancelar esta reunião.'
            ]);
            return;
        }

        if ($reuniao->status === 'cancelado') {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Esta reunião já foi cancelada.'
            ]);
            return;
        }

        // Verificar se o evento já começou
        $dataHoraAtual = now();
        $dataHoraReuniao = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reuniao->data_agendamento->format('Y-m-d') . ' ' . $reuniao->hora_inicio->format('H:i:s'));

        if ($dataHoraAtual->gte($dataHoraReuniao)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível cancelar uma reunião que já começou ou está em andamento.'
            ]);
            return;
        }

        // Verificação adicional: não permitir cancelamento se faltar menos de 1 hora para o evento
        $diferencaEmHoras = $dataHoraAtual->diffInHours($dataHoraReuniao, false);
        if ($diferencaEmHoras < 1 && $diferencaEmHoras >= 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível cancelar uma reunião com menos de 1 hora de antecedência.'
            ]);
            return;
        }

        // Preparar modal de cancelamento
        $this->reuniaoParaCancelar = $reuniao;
        $this->senhaCancelamento = '';
        $this->confirmacaoOmnigrejas = '';
        $this->confirmacaoTitulo = '';

        $this->dispatch('open-cancel-meeting-modal');
    }

    public function confirmarCancelamentoReuniao()
    {
        // Validações rigorosas
        $this->validate([
            'senhaCancelamento' => 'required|string',
            'confirmacaoOmnigrejas' => 'required|string',
            'confirmacaoTitulo' => 'required|string',
        ]);

        // Verificar se a reunião ainda existe e pertence ao usuário
        if (!$this->reuniaoParaCancelar || $this->reuniaoParaCancelar->organizador_id !== Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Reunião não encontrada ou você não tem permissão para cancelá-la.'
            ]);
            $this->fecharModalCancelamento();
            return;
        }

        // Verificar senha
        if (!\Illuminate\Support\Facades\Hash::check($this->senhaCancelamento, Auth::user()->password)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Senha incorreta.'
            ]);
            return;
        }

        // Verificar confirmação exata de "Omnigrejas"
        if (trim($this->confirmacaoOmnigrejas) !== 'Omnigrejas') {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Confirmação "Omnigrejas" incorreta. Digite exatamente "Omnigrejas".'
            ]);
            return;
        }

        // Verificar confirmação exata do título (sem espaços extras)
        if (trim($this->confirmacaoTitulo) !== trim($this->reuniaoParaCancelar->titulo)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Título da reunião incorreto. Digite exatamente o título sem espaços extras.'
            ]);
            return;
        }

        // Cancelar a reunião
        $this->reuniaoParaCancelar->update([
            'status' => 'cancelado',
            'motivo_cancelamento' => 'Cancelado pelo organizador com confirmação de segurança'
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Reunião cancelada com sucesso!'
        ]);

        // Limpar seleção e recarregar
        $this->reuniaoSelecionada = null;
        $this->carregarReunioes();

        // Fechar modal
        $this->fecharModalCancelamento();
    }

    public function fecharModalCancelamento()
    {
        $this->reuniaoParaCancelar = null;
        $this->senhaCancelamento = '';
        $this->confirmacaoOmnigrejas = '';
        $this->confirmacaoTitulo = '';
        $this->dispatch('close-cancel-meeting-modal');
    }

    public function excluirReuniao($reuniaoId)
    {
        $reuniao = \App\Models\Eventos\Agendamento::find($reuniaoId);

        if (!$reuniao || $reuniao->organizador_id !== Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Você não tem permissão para excluir esta reunião.'
            ]);
            return;
        }

        if ($reuniao->status !== 'cancelado') {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Só é possível excluir reuniões canceladas.'
            ]);
            return;
        }

        $reuniao->delete();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Reunião excluída com sucesso!'
        ]);

        $this->carregarReunioes();
        $this->reuniaoSelecionada = null; // Limpar seleção
    }

    public function selecionarReuniao($reuniaoId)
    {
        $this->reuniaoSelecionada = $reuniaoId;
    }


    protected function resetModalReuniao()
    {
        $this->isEditing = false;
        $this->titulo = '';
        $this->descricao = '';
        $this->tipo = 'reuniao';
        $this->data_agendamento = '';
        $this->hora_inicio = '';
        $this->hora_fim = '';
        $this->local = '';
        $this->modalidade = 'presencial';
        $this->link_reuniao = '';
        $this->responsavel_id = '';
        $this->convidado_id = '';
        $this->observacoes = '';
        $this->aliancaSelecionada = '';
        $this->reuniaoSelecionada = ''; // Limpar ID da reunião sendo editada
    }

    public function updatedFiltroReunioes()
    {
        $this->currentPage = 1; // Reset para primeira página quando filtro muda
        $this->carregarTodasReunioes();
    }

    public function nextPage()
    {
        $totalPages = ceil($this->totalReunioes / $this->perPage);
        if ($this->currentPage < $totalPages) {
            $this->currentPage++;
            $this->carregarTodasReunioes();
        }
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->carregarTodasReunioes();
        }
    }

    public function goToPage($page)
    {
        $totalPages = ceil($this->totalReunioes / $this->perPage);
        if ($page >= 1 && $page <= $totalPages) {
            $this->currentPage = $page;
            $this->carregarTodasReunioes();
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

            $this->carregarMensagensPrivadas2();
            $this->carregarConversasPrivadas2();
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Mensagem deletada com sucesso!']);

        } catch (\Exception $e) {
            $this->dispatch('toast', ['type' => 'danger', 'message' => 'Erro ao deletar mensagem: ' . $e->getMessage()]);
        }
    }

    public function deletarMensagemAlianca($mensagemId)
    {
        try {
            // Determinar qual modelo usar baseado no tipo de chat
            if ($this->tipoChat === 'lideres') {
                $mensagem = AliancaMensagem::find($mensagemId);
            } elseif ($this->tipoChat === 'comunidade') {
                $mensagem = AliancaComunidadeMensagem::find($mensagemId);
            } else {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Tipo de chat inválido.'
                ]);
                return;
            }

            if (!$mensagem) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Mensagem não encontrada.'
                ]);
                return;
            }

            // Verificar se o usuário é o autor da mensagem
            if ($mensagem->remetente_id !== $this->membroAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Você só pode deletar suas próprias mensagens.'
                ]);
                return;
            }

            // Verificar se a mensagem não tem mais de 5 minutos
            if ($mensagem->created_at->diffInMinutes(now()) > 5) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Mensagens só podem ser deletadas dentro de 5 minutos após o envio.'
                ]);
                return;
            }

            // Remover anexos do Supabase se existirem
            if (!empty($mensagem->anexos)) {
                foreach ($mensagem->anexos as $anexo) {
                    if (isset($anexo['url'])) {
                        SupabaseHelper::removerArquivoAlianca($anexo['url']);
                    }
                }
            }

            // Deletar a mensagem
            $mensagem->delete();

            // Recarregar mensagens
            $this->carregarMensagens();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Mensagem deletada com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao deletar mensagem da aliança: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao deletar mensagem.'
            ]);
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

    public function render()
    {
        return view('church.alliance.community', [
            'mensagensNaoLidas' => $this->aliancaId ? $this->getMensagensNaoLidasCount() : 0,
            'totalMensagens' => $this->aliancaId ? $this->getTotalMensagens() : 0,
            'totalLideres' => $this->aliancaId ? $this->getLideresCount() : 0,
            'mensagensPrivadasNaoLidasTotal' => $this->aliancaId ? $this->getMensagensPrivadasNaoLidasTotal() : 0,
            'mensagensPrivadas2NaoLidasTotal' => $this->aliancaId ? $this->getMensagensPrivadas2NaoLidasTotal() : 0,
            'membroAtual' => $this->membroAtual,
            'mensagens' => $this->mensagens,
            'lideresOnline' => $this->lideresOnline,
            'conversas' => $this->conversas,
            'mensagensPrivadas' => $this->mensagensPrivadas,
            'conversaAtiva' => $this->conversaAtiva,
            'conversas2' => $this->conversas2,
            'mensagensPrivadas2' => $this->mensagensPrivadas2,
            'conversaAtiva2' => $this->conversaAtiva2,
            'usuariosDisponiveis' => $this->usuariosDisponiveis,
            'aliancaId' => $this->aliancaId,
            'minhasAliancas' => $this->minhasAliancas,
            // Variáveis para reuniões
            'proximasReunioes' => $this->proximasReunioes,
            'todasReunioesData' => $this->todasReunioesData,
            'totalReunioes' => $this->totalReunioes,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'reuniaoSelecionada' => $this->reuniaoSelecionada,
            'isEditing' => $this->isEditing,
            'lideresDisponiveis' => $this->lideresDisponiveis,
            'membrosDisponiveis' => $this->membrosDisponiveis,
            'aliancasDisponiveis' => $this->aliancasDisponiveis,
        ]);
    }
}

