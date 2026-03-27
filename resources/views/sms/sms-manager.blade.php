<div>

   {{-- 
    
    <a class="btn bg-info text-light btn-icon btn-setting position-fixed"
       style="top: 300px; right: 0; z-index: 1050; margin: 0; padding: 0.5rem;"
       data-bs-toggle="offcanvas"
       data-bs-target="#smsOffcanvas"
       role="button"
       aria-controls="smsOffcanvas"
       title="Sistema de Mensagens">
       <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
        </svg>
        @if($unreadCount > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle badge-sm"
                  style="font-size: 0.7rem;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>
    
   --}}

    {{-- Offcanvas SMS - Design Leve e Profissional --}}
    <div class="offcanvas offcanvas-end sms-offcanvas {{ $showChat ? 'show' : '' }}"
         tabindex="-1"
         id="smsOffcanvas"
         aria-labelledby="smsOffcanvasLabel"
         style="{{ $showChat ? 'visibility: visible;' : '' }}" wire:ignore.self>

        {{-- Header Minimalista do Offcanvas --}}
        <div class="offcanvas-header bg-white border-bottom px-3 py-3">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded-circle p-2 me-3">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                </div>
                <div>
                    <h5 class="offcanvas-title mb-0 fw-semibold text-dark" id="smsOffcanvasLabel">
                        Sistema de Mensagens
                    </h5>
                    <small class="text-muted">
                        @if($unreadCount > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1">
                                {{ $unreadCount }} não lida{{ $unreadCount > 1 ? 's' : '' }}
                            </span>
                        @else
                            <span class="text-success">✓ Todas lidas</span>
                        @endif
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                {{-- Status Online --}}
                <div class="d-flex align-items-center me-2">
                    <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                    <small class="text-muted d-none d-md-inline">Online</small>
                </div>

                {{-- Botões de Ação --}}
                <button class="btn btn-sm btn-outline-secondary border-0"
                        data-bs-toggle="modal"
                        data-bs-target="#smsSettingsModal"
                        data-bs-toggle="tooltip"
                        title="Configurações">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                </button>

                @if(!Auth::user()->isSuperAdmin())
                <button class="btn btn-sm btn-outline-primary border-0"
                        data-bs-toggle="modal"
                        data-bs-target="#smsNewConversationModal"
                        data-bs-toggle="tooltip"
                        title="Nova Conversa">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </button>
                @endif

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="offcanvas"
                        aria-label="Fechar"></button>
            </div>
        </div>

        {{-- Corpo do Offcanvas --}}
        <div class="offcanvas-body p-0 d-flex" style="height: calc(100vh - 80px);">

            {{-- Sidebar - Lista de Conversas --}}
            <div class="conversations-sidebar border-end bg-light" style="width: 350px; flex-shrink: 0; height: 100%;">
                        {{-- Header da Sidebar --}}
                        <div class="p-3 border-bottom bg-white">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0 fw-semibold text-dark">
                                    Conversas
                                    <span class="badge bg-secondary-subtle text-secondary ms-2 rounded-pill">{{ $totalConversations }}</span>
                                </h6>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><h6 class="dropdown-header">Filtrar por Status</h6></li>
                                        <li><a class="dropdown-item {{ $statusFilter === 'todas' ? 'active' : '' }}" wire:click="$set('statusFilter', 'todas')">Todas</a></li>
                                        <li><a class="dropdown-item {{ $statusFilter === 'ativa' ? 'active' : '' }}" wire:click="$set('statusFilter', 'ativa')">Ativas</a></li>
                                        <li><a class="dropdown-item {{ $statusFilter === 'arquivada' ? 'active' : '' }}" wire:click="$set('statusFilter', 'arquivada')">Arquivadas</a></li>
                                        <li><a class="dropdown-item {{ $statusFilter === 'fechada' ? 'active' : '' }}" wire:click="$set('statusFilter', 'fechada')">Fechadas</a></li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Barra de Pesquisa --}}
                            <div class="position-relative">
                                <input type="text"  autocomplete="new-password"
                                       class="form-control form-control-sm ps-4 border-0 bg-light m-2"
                                       placeholder="Buscar conversas..."
                                       wire:model.live.debounce.300ms="searchTerm"
                                       style="border-radius: 20px;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                     class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                            </div>

                            {{-- Filtros Rápidos --}}
                            <div class="d-flex align-items-center justify-content-between mt-3">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" wire:model.live="unreadOnly" id="unreadOnly">
                                    <label class="form-check-label small text-muted" for="unreadOnly">
                                        Apenas não lidas
                                    </label>
                                </div>
                                <small class="text-muted">{{ $unreadCount }} não lida{{ $unreadCount !== 1 ? 's' : '' }}</small>
                            </div>
                        </div>

                {{-- Lista de Conversas --}}
                <div class="conversations-container" style="height: calc(100% - 120px); overflow-y: auto;">
                    @forelse($conversations as $conversation)
                        @php
                            $isValidId = $conversation->id && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $conversation->id);
                        @endphp
                        <div class="conversation-item p-3 border-bottom {{ $activeConversation && $activeConversation->id === $conversation->id ? 'active bg-info text-light bg-opacity-10' : '' }}"
                             @if($isValidId)
                                 wire:click="selectConversation('{{ $conversation->id }}')"
                             @endif
                             style="cursor: {{ $isValidId ? 'pointer' : 'not-allowed' }}; opacity: {{ $isValidId ? '1' : '0.6' }};">
                            <div class="d-flex align-items-start gap-3">
                                <div class="avatar-wrapper">
                                    <div class="avatar avatar-sm rounded-circle bg-info text-light text-white d-flex align-items-center justify-content-center">
                                        <span class="fw-bold small">{{ substr($conversation->titulo, 0, 1) }}</span>
                                    </div>
                                    @if($conversation->getMensagensNaoLidas() > 0)
                                        <span class="badge bg-danger position-absolute top-0 end-0 badge-sm rounded-circle p-1">
                                            <span class="visually-hidden">Não lidas</span>
                                        </span>
                                    @endif
                                </div>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <h6 class="mb-0 text-truncate">
                                            {{ $conversation->titulo }}
                                            @if(!$isValidId)
                                                <small class="text-danger ms-1">(Inválida)</small>
                                            @endif
                                        </h6>
                                        <small class="text-muted">
                                            {{ $conversation->ultima_mensagem_em?->diffForHumans() ?? 'Nunca' }}
                                        </small>
                                    </div>

                                    <p class="mb-1 text-muted small text-truncate">
                                        {{ $conversation->getPreviewUltimaMensagem() }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <small class="text-muted">
                                            @if($conversation->igreja)
                                                {{ $conversation->igreja->nome }}
                                            @else
                                                Sistema
                                            @endif
                                        </small>

                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge {{ $conversation->getStatusBadgeClass() }} badge-sm">
                                                {{ $conversation->getStatusLabel() }}
                                            </span>
                                            @if($conversation->prioridade !== 'normal')
                                                <span class="badge {{ $conversation->getPrioridadeBadgeClass() }} badge-sm">
                                                    {{ $conversation->prioridade }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <svg width="48" height="48" fill="currentColor" class="mb-3 opacity-50" viewBox="0 0 24 24">
                                <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z"/>
                                <circle cx="7.5" cy="9.5" r="1.5" fill="currentColor"/>
                                <circle cx="12" cy="9.5" r="1.5" fill="currentColor"/>
                                <circle cx="16.5" cy="9.5" r="1.5" fill="currentColor"/>
                            </svg>
                            <p>Nenhuma conversa encontrada</p>
                            @if(!Auth::user()->isSuperAdmin())
                            <button class="btn bg-info text-light btn-sm" data-bs-toggle="modal" data-bs-target="#smsNewConversationModal">
                                Iniciar Conversa
                            </button>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Área do Chat --}}
            <div class="chat-area flex-grow-1 d-flex flex-column" style="height: 100%;">

                @if($activeConversation)
                    @php
                        $activeConversationValid = $activeConversation->id && preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $activeConversation->id);
                    @endphp
                    {{-- Header do Chat --}}
                    <div class="chat-header p-3 border-bottom bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-sm rounded-circle bg-info text-light text-white d-flex align-items-center justify-content-center">
                                    <span class="fw-bold">{{ substr($activeConversation->titulo, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0">
                                        {{ $activeConversation->titulo }}
                                        @if(!$activeConversationValid)
                                            <small class="text-danger ms-1">(Inválida)</small>
                                        @endif
                                    </h6>
                                    <small class="text-muted">
                                        @if($activeConversation->igreja)
                                            {{ $activeConversation->igreja->nome }}
                                        @else
                                            Sistema Administrativo
                                        @endif
                                        @if(!$activeConversationValid)
                                            <br><small class="text-danger">Esta conversa tem dados corrompidos e não pode ser usada.</small>
                                        @endif
                                    </small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-sm btn-outline-secondary"
                                        wire:click="markAllAsRead"
                                        title="Marcar todas como lidas">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                    </svg>
                                </button>

                                @if($activeConversationValid && $activeConversation->estaAtiva())
                                    <button class="btn btn-sm btn-outline-success"
                                            wire:click="resolveConversation('{{ $activeConversation->id }}')"
                                            title="Resolver conversa">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </button>
                                @endif

                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown">
                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($activeConversationValid)
                                            <li><a class="dropdown-item" wire:click="archiveConversation('{{ $activeConversation->id }}')">Arquivar</a></li>
                                        @else
                                            <li><a class="dropdown-item disabled text-muted">Arquivar (Conversa Inválida)</a></li>
                                        @endif
                                        <li><a class="dropdown-item text-danger" wire:click="closeChat">Fechar Chat</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mensagens --}}
                    <div class="chat-messages" id="messagesContainer" style="overflow-y: auto !important; padding: 10px !important; height: calc(100vh - 200px) !important; min-height: 200px !important; max-height: calc(100vh - 130px) !important; display: block !important; visibility: visible !important; background: #f8f9fa !important; border: 1px solid #dee2e6 !important; margin-bottom: 0 !important;">
                        @forelse($activeConversationMessages as $message)
                            @php
                                $isOwn = $message->ehDoUsuarioAtual();
                            @endphp

                            <div class="whatsapp-message {{ $isOwn ? 'own' : 'other' }}" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: {{ $isOwn ? 'right' : 'left' }} !important;">
                                @if(!$isOwn)
                                    <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">{{ $message->remetente->name }}</div>
                                @endif
                                <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: {{ $isOwn ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }} !important; background-color: {{ $isOwn ? '#dcf8c6' : 'white' }} !important; color: #303030 !important; {{ !$isOwn ? 'border: 1px solid #e0e0e0 !important;' : '' }} position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                    @if($message->anexos->count() > 0)
                                        <div class="message-attachments mb-1">
                                            @foreach($message->anexos as $anexo)
                                                <div class="attachment-item d-flex align-items-center gap-2 p-2 bg-white rounded mb-1 border" style="border-color: rgba(0,0,0,0.1) !important;">
                                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                                    </svg>
                                                    <div class="flex-grow-1">
                                                        <small class="fw-bold">{{ $anexo->nome_original }}</small>
                                                        <br>
                                                        <small class="text-muted">{{ $anexo->getTamanhoFormatado() }}</small>
                                                    </div>
                                                    <a href="{{ $anexo->getUrlCompleta() }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-primary btn-xs">
                                                        <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($message->conteudo)
                                        <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">{{ $message->conteudo }}</div>
                                    @endif

                                    <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                        <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">{{ $message->enviada_em->format('H:i') }}</span>
                                        @if($isOwn)
                                            <span class="status" style="visibility: visible !important; font-size: 11px !important; color: #4fc3f7 !important;"><i class="fas fa-check-double" style="visibility: visible !important; font-size: 10px !important;"></i></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <svg width="64" height="64" fill="currentColor" class="mb-3 opacity-25" viewBox="0 0 24 24">
                                    <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z"/>
                                    <circle cx="7.5" cy="9.5" r="1.5" fill="currentColor"/>
                                    <circle cx="12" cy="9.5" r="1.5" fill="currentColor"/>
                                    <circle cx="16.5" cy="9.5" r="1.5" fill="currentColor"/>
                                </svg>
                                <p class="mb-0">Nenhuma mensagem ainda</p>
                                <small class="text-muted">Seja o primeiro a enviar uma mensagem!</small>
                            </div>
                        @endforelse
                    </div>

                    {{-- Área de Input --}}
                    <div class="message-input-area p-3 border-top bg-white" style="flex-shrink: 0;">
                        @if(!$activeConversationValid)
                            <div class="alert alert-warning mb-3">
                                <small>Esta conversa tem dados corrompidos. Não é possível enviar mensagens.</small>
                            </div>
                        @endif
                        <form wire:submit="sendMessage" @if(!$activeConversationValid) style="pointer-events: none; opacity: 0.6;" @endif>
                            {{-- Upload de Arquivos --}}
                            @if($showFileUpload)
                                <div class="file-upload-area mb-3 p-3 border rounded bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <small class="fw-bold">Anexar arquivos</small>
                                        <button type="button" class="btn-close btn-sm" wire:click="$set('showFileUpload', false)"></button>
                                    </div>
                                    <input type="file"
                                           class="form-control form-control-sm"
                                           wire:model="selectedFiles"
                                           accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.xls,.xlsx,.ppt,.pptx,.zip,.rar">
                                    <small class="text-muted mt-1 d-block">
                                        Máximo 25MB por arquivo. Tipos aceitos: imagens, vídeos, áudios, documentos, arquivos compactados.
                                    </small>
                                </div>
                            @endif

                            <div class="d-flex align-items-end gap-2">
                                <div class="flex-grow-1">
                                    <textarea class="form-control"
                                              rows="1"
                                              placeholder="Digite sua mensagem..."
                                              wire:model="newMessage"
                                              wire:keydown.ctrl.enter="sendMessage"
                                              style="resize: none; min-height: 38px; max-height: 120px;"></textarea>
                                </div>

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        wire:click="$toggle('showFileUpload')"
                                        title="Anexar arquivo">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                                    </svg>
                                </button>

                                <select class="form-select" wire:model="selectedPriority" style="width: auto;">
                                    <option value="baixa">Baixa</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>

                                <button type="submit"
                                        class="btn bg-info text-light"
                                        wire:loading.attr="disabled"
                                        wire:target="sendMessage">
                                    <span wire:loading.remove wire:target="sendMessage">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                                        </svg>
                                    </span>
                                    <span wire:loading wire:target="sendMessage">
                                        <div class="spinner-border spinner-border-sm" role="status"></div>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Chat Vazio --}}
                    <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                        <div class="text-center">
                            <svg width="80" height="80" fill="currentColor" class="mb-4 opacity-25" viewBox="0 0 24 24">
                                <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z"/>
                                <circle cx="7.5" cy="9.5" r="1.5" fill="currentColor"/>
                                <circle cx="12" cy="9.5" r="1.5" fill="currentColor"/>
                                <circle cx="16.5" cy="9.5" r="1.5" fill="currentColor"/>
                            </svg>
                            <h5>Selecione uma conversa</h5>
                            <p class="mb-4">Escolha uma conversa da lista para começar a conversar</p>
                            @if(!Auth::user()->isSuperAdmin())
                            <button class="btn bg-info text-light" data-bs-toggle="modal" data-bs-target="#smsNewConversationModal">
                                <svg width="16" height="16" class="me-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                </svg>
                                Nova Conversa
                            </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Nova Conversa - Compacto e Estiloso --}}
    <div class="modal fade" id="smsNewConversationModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4" style="max-width: 480px;">
                <div class="modal-header bg-gradient-primary text-white border-0 rounded-top-4 py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                        </div>
                        <div>
                            <h6 class="modal-title mb-0 fw-bold">Nova Conversa</h6>
                            <small class="opacity-75">Inicie uma nova conversa</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form wire:submit="createNewConversation">
                    <div class="modal-body p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-2">Título da Conversa *</label>
                            <input type="text"  autocomplete="new-password"
                                   class="form-control form-control-lg border-0 bg-light rounded-3"
                                   wire:model="newConversationTitle"
                                   placeholder="Ex: Problema técnico, Dúvida financeira..."
                                   style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);"
                                   required>
                            @error('newConversationTitle') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small mb-2">Descrição</label>
                            <textarea class="form-control border-0 bg-light rounded-3"
                                      rows="1"
                                      wire:model="newConversationDescription"
                                      placeholder="Descreva brevemente o assunto..."
                                      style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); resize: none;"></textarea>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small mb-2">Prioridade</label>
                            <select class="form-select form-select-lg border-0 bg-light rounded-3 fw-semibold"
                                    wire:model="selectedPriority"
                                    style="box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                                <option value="baixa" class="text-success">📊 Baixa</option>
                                <option value="normal" selected class="text-info">⚖️ Normal</option>
                                <option value="alta" class="text-warning">⚡ Alta</option>
                                <option value="urgente" class="text-danger">🚨 Urgente</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light rounded-bottom-4 p-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">
                            <svg width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 24 24">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                            Cancelar
                        </button>
                        <button type="submit" class="btn bg-info text-light btn-sm px-4 rounded-pill" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <svg width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 24 24">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                </svg>
                                Criar
                            </span>
                            <span wire:loading>
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                Criando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Configurações - Compacto --}}
    <div class="modal fade" id="smsSettingsModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title">Configurações SMS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                    <form wire:submit="updateSettings">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Notificações</h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.notificacoes_push" id="notificacoes_push">
                                            <label class="form-check-label" for="notificacoes_push">
                                                Notificações Push
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.notificacoes_email" id="notificacoes_email">
                                            <label class="form-check-label" for="notificacoes_email">
                                                Notificações por Email
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.notificacoes_sms" id="notificacoes_sms">
                                            <label class="form-check-label" for="notificacoes_sms">
                                                Notificações por SMS
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6>Interface</h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.mostrar_imagens" id="mostrar_imagens">
                                            <label class="form-check-label" for="mostrar_imagens">
                                                Mostrar imagens automaticamente
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.mostrar_online" id="mostrar_online">
                                            <label class="form-check-label" for="mostrar_online">
                                                Mostrar status online
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.mostrar_digitando" id="mostrar_digitando">
                                            <label class="form-check-label" for="mostrar_digitando">
                                                Mostrar "digitando..."
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Download Automático</h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.auto_download_arquivos" id="auto_download">
                                            <label class="form-check-label" for="auto_download">
                                                Download automático de arquivos
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tamanho máximo para download automático</label>
                                        <select class="form-select" wire:model="userSettings.tamanho_max_download">
                                            <option value="1048576">1 MB</option>
                                            <option value="5242880">5 MB</option>
                                            <option value="10485760" selected>10 MB</option>
                                            <option value="26214400">25 MB</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6>Sons</h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.som_notificacao" id="som_notificacao">
                                            <label class="form-check-label" for="som_notificacao">
                                                Som de notificação
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   wire:model="userSettings.vibracao" id="vibracao">
                                            <label class="form-check-label" for="vibracao">
                                                Vibração
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn bg-info text-light" wire:loading.attr="disabled">
                                <span wire:loading.remove>Salvar Configurações</span>
                                <span wire:loading>
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Salvando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    {{-- Scripts para funcionalidades avançadas --}}
    <script>

        document.addEventListener('livewire:loaded', () => {
            // Inicializar tooltips do Bootstrap
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Auto-resize textarea
            document.addEventListener('input', function(e) {
                if (e.target.tagName.toLowerCase() === 'textarea') {
                    e.target.style.height = 'auto';
                    e.target.style.height = (e.target.scrollHeight) + 'px';
                }
            });

            // Controlar abertura/fechamento do offcanvas (mantido para compatibilidade)
            Livewire.on('chat-opened', () => {
                const offcanvas = document.getElementById('smsOffcanvas');
                if (offcanvas) {
                    const bsOffcanvas = new bootstrap.Offcanvas(offcanvas);
                    bsOffcanvas.show();
                }
            });

            // Scroll automático para mensagens
            Livewire.on('message-sent', () => {
                setTimeout(() => {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 100);
            });

            // Scroll quando conversa é selecionada
            Livewire.on('conversation-selected', () => {
                setTimeout(() => {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 100);
            });

            // Fechar offcanvas quando o componente solicita
            Livewire.on('close-chat', () => {
                const offcanvas = document.getElementById('smsOffcanvas');
                if (offcanvas) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }
            });

            // Fechar modal de nova conversa
            Livewire.on('sms-close-new-conversation-modal', () => {
                const modal = document.getElementById('smsNewConversationModal');
                if (modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
            });
        });

        // Sincronizar estado do offcanvas com Livewire
        document.addEventListener('hidden.bs.offcanvas', function (event) {
            if (event.target.id === 'smsOffcanvas') {
                // Quando o offcanvas for fechado, atualizar o estado no Livewire
                if (typeof $wire !== 'undefined') {
                    $wire.call('closeChat');
                }
            }
        });
    </script>

    {{-- Estilos customizados - Design Minimalista e Leve --}}
    <style>


        /* Offcanvas SMS */
        .sms-offcanvas {
            width: 95vw !important;
            max-width: 1200px;
            box-shadow: -4px 0 20px rgba(0,0,0,0.1);
        }

        .sms-offcanvas.offcanvas-end {
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sms-offcanvas.show {
            transform: translateX(0);
        }

        /* Sidebar de Conversas */
        .conversations-sidebar {
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        }

        .conversations-container {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 transparent;
        }

        .conversations-container::-webkit-scrollbar {
            width: 6px;
        }

        .conversations-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .conversations-container::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 3px;
        }

        /* Itens de Conversa */
        .conversation-item {
            transition: all 0.2s ease;
            cursor: pointer;
            border-radius: 8px;
            margin: 2px 8px;
        }

        .conversation-item:hover {
            background-color: rgba(13, 110, 253, 0.08) !important;
            transform: translateX(2px);
        }

        .conversation-item.active {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%) !important;
            border-left: 3px solid #0d6efd;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
        }

        /* Área do Chat */
        .chat-area {
            background: #ffffff;
            min-width: 0; /* Permite que o flex-shrink funcione */
        }

        /* Estilos específicos para o SMS Manager */
        .whatsapp-message.own {
            text-align: right !important;
        }

        .whatsapp-message.other {
            text-align: left !important;
        }

        /* Anexos */
        .attachment-item {
            background: rgba(255,255,255,0.95) !important;
            border: 1px solid rgba(0,0,0,0.06) !important;
            border-radius: 6px;
            transition: all 0.15s ease;
            padding: 6px 8px !important;
            margin-bottom: 4px !important;
        }

        .attachment-item:hover {
            background: rgba(255,255,255,1) !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .btn-xs {
            padding: 2px 6px !important;
            font-size: 0.7rem !important;
            line-height: 1.2 !important;
            border-radius: 3px !important;
        }

        /* Avatares */
        .avatar {
            width: 36px;
            height: 36px;
            border: 2px solid rgba(255,255,255,0.8);
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .avatar-xs {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        /* Badges */
        .badge-sm {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            border-radius: 12px;
        }

        /* Campo de Mensagem */
        .message-input-area textarea {
            border: none !important;
            border-radius: 20px !important;
            background: #f8f9fa !important;
            transition: all 0.2s ease;
        }

        .message-input-area textarea:focus {
            background: #ffffff !important;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25) !important;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .sms-offcanvas {
                width: 100vw !important;
            }

            .offcanvas-body {
                flex-direction: column !important;
            }

            .conversations-sidebar {
                width: 100% !important;
                height: 40vh !important;
                max-height: 40vh;
            }

            .chat-area {
                width: 100% !important;
                height: 60vh !important;
                max-height: 60vh;
            }


            .message-bubble {
                max-width: 85% !important;
            }
        }

        @media (max-width: 576px) {
            .conversations-sidebar {
                max-height: 35vh;
            }

        }



    </style>
</div>
