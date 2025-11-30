<?php

namespace App\Livewire\Sms;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\SmsService\SmsConversation;
use App\Models\SmsService\SmsMessage;
use App\Models\SmsService\SmsMessageRead;
use App\Models\SmsService\SmsAttachment;
use App\Models\SmsService\SmsNotification;
use App\Models\SmsService\SmsSettings;
use App\Models\Igrejas\IgrejaMembro;
use App\Helpers\SupabaseHelper;

#[Layout('layouts.app')]
#[Title('Sistema SMS - OMNIGREJAS')]
class SmsManager extends Component
{
    use WithFileUploads;

    // ========================================
    // PROPRIEDADES PÚBLICAS
    // ========================================

    public $showChat = false;
    public $activeConversation = null;
    public $newMessage = '';
    public $searchTerm = '';
    public $selectedFiles;
    public $showFileUpload = false;
    public $newConversationTitle = '';
    public $newConversationDescription = '';
    public $selectedPriority = 'normal';

    // Filtros e paginação
    public $statusFilter = 'todas';
    public $priorityFilter = '';
    public $unreadOnly = false;
    public $perPage = 20;

    // Configurações do usuário
    public $userSettings = [];

    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================

    public function getConversationsProperty(): Collection
    {
        try {
            $query = SmsConversation::query()
                ->with(['iniciadaPor', 'resolvidaPor']);

            // Filtros
            if ($this->statusFilter && $this->statusFilter !== 'todas') {
                $query->where('status', $this->statusFilter);
            }

            if ($this->priorityFilter) {
                $query->where('prioridade', $this->priorityFilter);
            }

            if ($this->unreadOnly) {
                $query->whereHas('mensagens', function($q) {
                    $q->leftJoin('sms_message_reads', function($join) {
                        $join->on('sms_messages.id', '=', 'sms_message_reads.message_id')
                             ->where('sms_message_reads.user_id', '=', Auth::id());
                    })
                    ->whereNull('sms_message_reads.id')
                    ->whereIn('sms_messages.status', ['enviada', 'entregue']);
                });
            }

            // Busca
            if ($this->searchTerm) {
                $query->where(function($q) {
                    $q->where('titulo', 'ILIKE', '%' . $this->searchTerm . '%')
                      ->orWhere('descricao', 'ILIKE', '%' . $this->searchTerm . '%');
                });
            }

            // Aplicar filtros de permissão
            $user = Auth::user();
            if ($user->isSuperAdmin()) {
                // Super admin vê todas as conversas
            } elseif ($user->isIgrejaAdmin()) {
                // Admin da igreja vê conversas da própria igreja
                $igreja = $user->getIgreja();
                if ($igreja) {
                    $query->where('igreja_id', $igreja->id);
                } else {
                    // Se admin não tem igreja associada, mostrar conversas que iniciou
                    $query->where('iniciada_por', $user->id);
                }
            } else {
                // Usuário normal vê apenas conversas que iniciou
                $query->where('iniciada_por', $user->id);
            }

            // Carregar conversas com validação rigorosa de UUID
            $conversations = $query->with(['iniciadaPor', 'resolvidaPor'])
                ->whereNotNull('id')
                ->whereRaw("id::text ~ '^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$'") // Apenas UUIDs válidos
                ->whereRaw("id::text != '00000000-0000-0000-0000-000000000000'") // Excluir UUID nulo
                ->orderBy('ultima_mensagem_em', 'desc')
                ->get();

            // Log::info('SMS Conversations loaded', [
            //     'total' => $conversations->count(),
            //     'user_id' => Auth::id(),
            //     'user_role' => Auth::user()->role ?? 'unknown',
            //     'status_filter' => $this->statusFilter,
            //     'unread_only' => $this->unreadOnly,
            //     'sample_ids' => $conversations->take(3)->pluck('id')->toArray()
            // ]);

            return $conversations;
        } catch (\Exception $e) {
            // Em caso de erro (tabelas não existem, etc), retornar coleção vazia
            Log::error('Erro ao carregar conversas SMS', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return collect();
        }
    }

    public function getActiveConversationMessagesProperty(): Collection
    {
        if (!$this->activeConversation || !$this->activeConversation->id) {
            return collect();
        }

        try {
            return SmsMessage::where('conversation_id', $this->activeConversation->id)
                ->with(['remetente', 'anexos'])
                ->orderBy('enviada_em', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Erro ao carregar mensagens da conversa', [
                'conversation_id' => $this->activeConversation->id,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }

    public function getUnreadCountProperty(): int
    {
        try {
            $user = Auth::user();
            $userId = $user->id;

            // Query direta para evitar problemas com relacionamentos
            $query = SmsMessage::leftJoin('sms_message_reads', function($join) use ($userId) {
                $join->on('sms_messages.id', '=', 'sms_message_reads.message_id')
                     ->where('sms_message_reads.user_id', '=', $userId);
            })
            ->whereNull('sms_message_reads.id') // Não tem leitura para este usuário
            ->whereIn('sms_messages.status', ['enviada', 'entregue']); // Apenas mensagens não lidas

            // Filtrar conversas por permissões do usuário
            if ($user->isSuperAdmin()) {
                // Super admin vê tudo
            } elseif ($user->isIgrejaAdmin()) {
                $igreja = $user->getIgreja();
                if ($igreja) {
                    $query->whereHas('conversation', function($q) use ($igreja) {
                        $q->where('igreja_id', $igreja->id);
                    });
                } else {
                    return 0;
                }
            } else {
                // Usuário normal vê conversas que iniciou
                $query->whereHas('conversation', function($q) use ($user) {
                    $q->where('iniciada_por', $user->id);
                });
            }

            return $query->count();
        } catch (\Exception $e) {
            Log::error('Erro ao contar mensagens não lidas', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return 0;
        }
    }

    public function getTotalConversationsProperty(): int
    {
        return $this->conversations->count();
    }

    // ========================================
    // MÉTODOS DE CICLO DE VIDA
    // ========================================

    public function mount()
    {
        // Verificar se as tabelas SMS existem
        if (!$this->checkSmsTablesExist()) {
            // Tabelas não existem, desabilitar funcionalidade
            $this->showChat = false;
            return;
        }

        $this->loadUserSettings();
        $this->checkPermissions();
        // Chat começa sempre fechado - só abre mediante interação explícita
        $this->showChat = false;
    }

    public function hydrate()
    {
        $this->loadUserSettings();
    }

    // ========================================
    // MÉTODOS PÚBLICOS
    // ========================================

    public function openChat($conversationId = null)
    {
        // Só abre o chat mediante interação explícita do usuário
        $this->showChat = true;

        if ($conversationId) {
            $this->activeConversation = SmsConversation::find($conversationId);
            if ($this->activeConversation && $this->activeConversation->id) {
                $this->markMessagesAsRead();
            } else {
                $this->activeConversation = null;
            }
        }

        // Dispatch para garantir que o offcanvas seja mostrado
        $this->dispatch('chat-opened');
    }

    public function closeChat()
    {
        $this->showChat = false;
        $this->activeConversation = null;
        $this->newMessage = '';
        $this->selectedFiles = null;
        $this->showFileUpload = false;

        // Dispatch para sincronizar com o JavaScript
        $this->dispatch('close-chat');
    }

    public function selectConversation($conversationId)
    {
        // Validar se o ID é um UUID válido
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $conversationId)) {
            $this->activeConversation = null;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'ID de conversa inválido'
            ]);
            return;
        }

        $this->activeConversation = SmsConversation::find($conversationId);
        if ($this->activeConversation && $this->activeConversation->id) {
            $this->markMessagesAsRead();
            $this->dispatch('conversation-selected', conversationId: $conversationId);
        } else {
            $this->activeConversation = null;
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Conversa não encontrada'
            ]);
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required_without:selectedFiles|string|max:5000',
            'selectedFiles' => 'nullable|file|max:25600', // 25MB max
        ]);

        if (!$this->activeConversation || !$this->activeConversation->id) {
            $this->addError('conversation', 'Nenhuma conversa válida selecionada');
            return;
        }

        // Validar se o ID da conversa é um UUID válido
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $this->activeConversation->id)) {
            $this->addError('conversation', 'Conversa com ID inválido');
            return;
        }

        try {
            $user = Auth::user();

            // Determinar destinatário baseado no tipo de usuário
            $destinatarioTipo = $user->isSuperAdmin() ? 'igreja_admin' : 'super_admin';
            $igrejaDestinoId = $user->isSuperAdmin() ? $this->activeConversation->igreja_id : null;

            // Criar mensagem
            $message = SmsMessage::create([
                'conversation_id' => $this->activeConversation->id,
                'remetente_id' => $user->id,
                'tipo' => $this->selectedFiles ? $this->determineMessageType() : 'texto',
                'conteudo' => $this->newMessage ?: null,
                'destinatario_tipo' => $destinatarioTipo,
                'igreja_destino_id' => $igrejaDestinoId,
                'prioridade' => $this->selectedPriority,
            ]);

            // Processar anexos se houver
            if ($this->selectedFiles) {
                $this->processFileUploads($message);
            }

            // Criar notificações
            $this->createNotifications($message);

            // Limpar campos
            $this->newMessage = '';
            $this->selectedFiles = null;
            $this->showFileUpload = false;

            // Atualizar conversa
            $this->activeConversation->refresh();
            $this->dispatch('message-sent', messageId: $message->id);

            // Fechar modal de nova conversa se estiver aberto
            $this->dispatch('sms-close-new-conversation-modal');

            // Feedback
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Mensagem enviada com sucesso!'
            ]);

        } catch (\Exception $e) {
            $this->addError('send', 'Erro ao enviar mensagem: ' . $e->getMessage());
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao enviar mensagem'
            ]);
        }
    }

    public function createNewConversation()
    {
        $this->validate([
            'newConversationTitle' => 'required|string|max:255',
            'newConversationDescription' => 'nullable|string|max:1000',
        ]);

        try {
            $user = Auth::user();
            $igreja = $user->getIgreja();

            if (!$igreja && !$user->isSuperAdmin()) {
                throw new \Exception('Usuário deve estar associado a uma igreja');
            }

            $conversation = SmsConversation::create([
                'titulo' => $this->newConversationTitle,
                'descricao' => $this->newConversationDescription,
                'igreja_id' => $user->isSuperAdmin() ? null : $igreja->id, // Se super_admin, igreja_id pode ser null
                'iniciada_por' => $user->id,
                'prioridade' => $this->selectedPriority,
                'categoria' => 'geral',
            ]);

            $this->newConversationTitle = '';
            $this->newConversationDescription = '';
            $this->selectedPriority = 'normal';

            // Definir conversa ativa sem fechar o chat
            $this->activeConversation = $conversation;
            $this->markMessagesAsRead();

            // Fechar apenas o modal de nova conversa
            $this->dispatch('sms-close-new-conversation-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Conversa criada com sucesso!'
            ]);

        } catch (\Exception $e) {
            $this->addError('conversation', 'Erro ao criar conversa: ' . $e->getMessage());
        }
    }

    public function archiveConversation($conversationId)
    {
        // Validar se o ID é um UUID válido
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $conversationId)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'ID de conversa inválido'
            ]);
            return;
        }

        $conversation = SmsConversation::find($conversationId);
        if ($conversation && $conversation->id && $this->canModifyConversation($conversation)) {
            $conversation->update(['status' => 'arquivada']);
            $this->dispatch('conversation-archived');
            $this->dispatch('toast', [
                'type' => 'info',
                'message' => 'Conversa arquivada'
            ]);
        }
    }

    public function resolveConversation($conversationId)
    {
        $conversation = SmsConversation::find($conversationId);
        if ($conversation && $conversation->id && $this->canModifyConversation($conversation)) {
            $conversation->resolver(Auth::user());
            $this->dispatch('conversation-resolved');
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Conversa resolvida'
            ]);
        }
    }

    public function updateSettings()
    {
        try {
            $userId = Auth::id();
            $settings = $this->userSettings;

            SmsSettings::updateOrCreate(
                [
                    'tipo' => 'user',
                    'user_id' => $userId,
                ],
                $settings
            );

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Configurações salvas!'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao salvar configurações'
            ]);
        }
    }

    public function markAllAsRead()
    {
        if ($this->activeConversation && $this->activeConversation->id) {
            SmsMessageRead::whereHas('message', function($q) {
                $q->where('conversation_id', $this->activeConversation->id);
            })
            ->where('user_id', Auth::id())
            ->delete(); // Remove leituras antigas

            // Marca todas como lidas
            $userId = Auth::id();
            foreach ($this->activeConversationMessages as $message) {
                if (!$message->foiLida()) {
                    \Illuminate\Support\Facades\DB::table('sms_message_reads')
                        ->updateOrInsert(
                            [
                                'message_id' => $message->id,
                                'user_id' => $userId,
                            ],
                            [
                                'lida_em' => now(),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                }
            }

            $this->dispatch('all-marked-read');
        }
    }

    // ========================================
    // MÉTODOS PRIVADOS
    // ========================================

    private function loadUserSettings()
    {
        $this->userSettings = SmsSettings::obterConfiguracaoUsuario(Auth::id());
    }

    private function checkPermissions()
    {
        // Verificar se usuário tem permissão para acessar SMS
        // Temporariamente desabilitado para teste - TODO: reabilitar após configurar permissões
        // if (!PermissionHelper::hasPermissionStatic(Auth::user(), 'gerenciar_sms', false)) {
        //     abort(403, 'Acesso negado ao sistema SMS');
        // }
    }

    private function checkSmsTablesExist(): bool
    {
        try {
            // Verificar se as tabelas principais existem
            $tablesExist = \Illuminate\Support\Facades\Schema::hasTable('sms_conversations') &&
                          \Illuminate\Support\Facades\Schema::hasTable('sms_messages');

            if (!$tablesExist) {
                Log::warning('Tabelas SMS não encontradas no banco de dados', [
                    'user_id' => Auth::id()
                ]);
            }

            return $tablesExist;
        } catch (\Exception $e) {
            Log::error('Erro ao verificar existência das tabelas SMS', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return false;
        }
    }


    private function markMessagesAsRead()
    {
        if (!$this->activeConversation) return;

        $userId = Auth::id();
        foreach ($this->activeConversationMessages as $message) {
            if (!$message->foiLida()) {
                \Illuminate\Support\Facades\DB::table('sms_message_reads')
                    ->updateOrInsert(
                        [
                            'message_id' => $message->id,
                            'user_id' => $userId,
                        ],
                        [
                            'lida_em' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
            }
        }
    }

    private function determineMessageType(): string
    {
        if (!$this->selectedFiles) {
            return 'texto';
        }

        $file = $this->selectedFiles;
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            return 'imagem';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (str_starts_with($mimeType, 'application/pdf') ||
                  str_starts_with($mimeType, 'application/msword') ||
                  str_starts_with($mimeType, 'application/vnd.openxmlformats')) {
            return 'documento';
        } else {
            return 'arquivo';
        }
    }

    private function processFileUploads(SmsMessage $message)
    {
        if (!$this->selectedFiles) {
            return;
        }

        try {
            $tipo = $this->getFileType($this->selectedFiles);
            $uploadResult = SupabaseHelper::fazerUploadSmsAnexo($this->selectedFiles, $tipo);

            SmsAttachment::create([
                'message_id' => $message->id,
                'nome_original' => $uploadResult['nome_original'],
                'nome_arquivo' => $uploadResult['nome_arquivo'],
                'caminho_completo' => $uploadResult['caminho_completo'],
                'tamanho_bytes' => $uploadResult['tamanho_bytes'],
                'tipo_mime' => $uploadResult['tipo_mime'],
                'extensao' => $uploadResult['extensao'],
            ]);

        } catch (\Exception $e) {
            // Log error
            Log::error('Erro ao fazer upload de anexo SMS', [
                'message_id' => $message->id,
                'file' => $this->selectedFiles->getClientOriginalName(),
                'error' => $e->getMessage()
            ]);
            throw $e; // Re-throw to handle in sendMessage
        }
    }

    private function getFileType($file): string
    {
        $mimeType = $file->getMimeType();

        if (str_starts_with($mimeType, 'image/')) {
            return 'imagem';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (str_starts_with($mimeType, 'application/pdf') ||
                  str_starts_with($mimeType, 'application/msword') ||
                  str_starts_with($mimeType, 'application/vnd.openxmlformats')) {
            return 'documento';
        } else {
            return 'arquivo';
        }
    }

    private function createNotifications(SmsMessage $message)
    {
        $user = Auth::user();
        $destinatarios = [];

        if ($user->isSuperAdmin()) {
            // Super admin enviando para admin da igreja
            if ($message->igreja_destino_id) {
                $admins = IgrejaMembro::where('igreja_id', $message->igreja_destino_id)
                    ->where('cargo', 'admin')
                    ->orWhere('cargo' )
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();

                $destinatarios = $admins->pluck('id')->toArray();
            }
        } else {
            // Admin da igreja enviando para super admin
            $superAdmins = \App\Models\User::where('role', 'super_admin')->pluck('id')->toArray();
            $destinatarios = $superAdmins;
        }

        foreach ($destinatarios as $destinatarioId) {
            if ($destinatarioId !== $user->id) {
                // Determinar tipo de notificação baseado nas configurações do destinatário
                $destinatarioSettings = SmsSettings::obterConfiguracaoUsuario($destinatarioId);
                $tipoNotificacao = 'email'; // padrão

                if ($destinatarioSettings['notificacoes_push']) {
                    $tipoNotificacao = 'push';
                } elseif ($destinatarioSettings['notificacoes_email']) {
                    $tipoNotificacao = 'email';
                } elseif ($destinatarioSettings['notificacoes_sms']) {
                    $tipoNotificacao = 'sms';
                }

                SmsNotification::create([
                    'message_id' => $message->id,
                    'user_id' => $destinatarioId,
                    'tipo' => $tipoNotificacao,
                    'titulo' => 'Nova mensagem SMS',
                    'mensagem' => $message->conteudo ? substr($message->conteudo, 0, 100) : 'Mensagem com anexo',
                ]);
            }
        }
    }

    private function canModifyConversation(SmsConversation $conversation): bool
    {
        $user = Auth::user();

        // Super admin pode modificar tudo
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Admin da igreja pode modificar conversas da própria igreja
        if ($user->isIgrejaAdmin()) {
            $igreja = $user->getIgreja();
            return $igreja && $conversation->igreja_id === $igreja->id;
        }

        // Usuário normal só pode modificar conversas que iniciou
        return $conversation->iniciada_por === $user->id;
    }

    private function isValidConversationId($conversationId): bool
    {
        return is_string($conversationId) &&
               preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $conversationId);
    }

    // ========================================
    // LISTENERS
    // ========================================

    #[On('refresh-conversations')]
    public function refreshConversations()
    {
        // Força atualização da lista de conversas
        $this->dispatch('$refresh');
    }

    #[On('new-message-received')]
    public function handleNewMessage($messageId)
    {
        // Atualizar conversa ativa se necessário
        if ($this->activeConversation) {
            $this->activeConversation->refresh();
        }

        // Notificar usuário se não estiver na conversa
        $this->dispatch('new-message-notification', messageId: $messageId);
    }

    // ========================================
    // RENDER
    // ========================================

    public function render()
    {
        return view('sms.sms-manager', [
            'conversations' => $this->conversations,
            'activeConversationMessages' => $this->activeConversationMessages,
            'unreadCount' => $this->unreadCount,
            'totalConversations' => $this->totalConversations,
        ]);
    }
}
