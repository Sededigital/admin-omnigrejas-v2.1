<div>
    <div class="container-fluid p-4">
        <!-- Conteúdo do Chat da Igreja -->
        <div class="row" style="height: calc(100vh - 100px);">
            <!-- Lista Lateral de Chats -->
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-comments text-primary me-2"></i>Chats da Igreja
                        </h6>
                        @if($isAdmin)
                            <button class="btn btn-outline-primary btn-sm" onclick="openCriarChatModal()">
                                <i class="fas fa-plus me-1"></i>Novo
                            </button>
                        @else
                            <div class="text-muted small">Apenas administradores podem criar chats</div>
                        @endif
                    </div>
                    <div class="card-body p-0" style="overflow-y: auto; height: calc(100vh - 200px);">
                        @if(!empty($chats))
                            @foreach($chats as $chat)
                                <div class="alliance-item p-3 border-bottom cursor-pointer {{ $chatAtivo == $chat['id'] ? 'bg-primary text-white' : 'hover-bg-light' }}"
                                     wire:click="selecionarChat('{{ $chat['id'] }}')">
                                    <div class="d-flex align-items-center">
                                        <div class="alliance-avatar me-3">
                                            <i class="fas fa-comment-dots text-{{ $chatAtivo == $chat['id'] ? 'white' : 'primary' }}" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ Str::limit($chat['nome'], 20) }}</div>
                                            <small class="{{ $chatAtivo == $chat['id'] ? 'text-white-50' : 'text-muted' }}">
                                                @if($chatAtivo == $chat['id'])
                                                    {{ count($mensagens ?? []) }} mensagens
                                                @else
                                                    @if(!empty($chat['mensagens']))
                                                        {{ Str::limit($chat['mensagens'][0]['conteudo'] ?? '', 25) }}
                                                    @else
                                                        Sem mensagens
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                        @if($chatAtivo != $chat['id'] && !empty($chat['mensagens']))
                                            <span class="badge bg-danger ms-2">{{ count($chat['mensagens']) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-comments text-muted mb-3" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Nenhum chat</h6>
                                <p class="text-muted small">Seja o primeiro a criar um chat</p>
                                @if($isAdmin)
                                    <button class="btn btn-primary btn-sm" onclick="openCriarChatModal()">
                                        <i class="fas fa-plus me-1"></i>Criar Primeiro Chat
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Área de Chat da Igreja -->
            <div class="col-md-8 col-lg-9">
                @if($chatAtivo)
                    @php
                        $chatSelecionado = collect($chats)->firstWhere('id', $chatAtivo);
                    @endphp
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="">
                                <h6 class="mb-0" style="cursor: pointer;" onclick="abrirSobreChat()">
                                    <i class="fas fa-comments text-primary me-2"></i>
                                    {{ $chatSelecionado['nome'] ?? 'Chat da Igreja' }}
                                </h6>
                                <small class="text-muted m-2 mb-2 mt-2">
                                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                    Chat da igreja • {{ count($chats) }} chats disponíveis
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" wire:click="$set('chatAtivo', null)">
                                        <i class="fas fa-times me-2"></i>Fechar chat
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#" onclick="abrirSobreChat()">
                                        <i class="fas fa-info-circle me-2"></i>Sobre
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="confirmarSairDoChat()">
                                        <i class="fas fa-sign-out-alt me-2"></i>Sair do Chat
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-0 d-flex flex-column" style="height: calc(100vh - 200px);">
                            <!-- Área de Mensagens -->
                            <div class="chat-messages" id="churchChatMessages" style="overflow-y: auto !important; padding: 10px !important; height: calc(100vh - 200px) !important; min-height: 200px !important; max-height: calc(100vh - 130px) !important; display: block !important; visibility: visible !important; background: #f8f9fa !important; border: 1px solid #dee2e6 !important; margin-bottom: 0 !important;">

                                <!-- Mensagens reais -->
                                @if(!empty($mensagens))
                                    @foreach($mensagens as $mensagem)
                                        @php
                                            $isOwn = $mensagem['autor_id'] === Auth::id();
                                            $senderName = $mensagem['autor']['name'] ?? 'Usuário';
                                            $messageText = $mensagem['conteudo'];
                                            $messageTime = \Carbon\Carbon::parse($mensagem['created_at'])->format('H:i');
                                        @endphp

                                        <div class="whatsapp-message {{ $isOwn ? 'own' : 'other' }}" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: {{ $isOwn ? 'right' : 'left' }} !important;">
                                            @if(!$isOwn)
                                                <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">{{ $senderName }}</div>
                                            @endif
                                            <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: {{ $isOwn ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }} !important; background-color: {{ $isOwn ? '#dcf8c6' : 'white' }} !important; color: #303030 !important; {{ !$isOwn ? 'border: 1px solid #e0e0e0 !important;' : '' }} position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                                    @if($mensagem['tipo_mensagem'] === 'audio')
                                                        <!-- Player de Áudio WhatsApp-style -->
                                                        <div class="whatsapp-audio-player" style="display: flex !important; align-items: center !important; gap: 10px !important; padding: 8px !important; background: rgba(0,0,0,0.05) !important; border-radius: 12px !important; margin-bottom: 5px !important; min-width: 200px !important;">
                                                            <!-- Botão Play/Pause -->
                                                            <button class="audio-play-btn" onclick="toggleAudioPlay(this)" style="background: {{ $isOwn ? '#dcf8c6' : '#ffffff' }} !important; border: none !important; border-radius: 50% !important; width: 36px !important; height: 36px !important; display: flex !important; align-items: center !important; justify-content: center !important; cursor: pointer !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;">
                                                                <i class="fas fa-play" style="color: #007bff !important; font-size: 12px !important; margin-left: 2px !important;"></i>
                                                            </button>

                                                            <!-- Barra de Progresso e Controles -->
                                                            <div class="audio-controls" style="flex: 1 !important; display: flex !important; flex-direction: column !important; gap: 2px !important;">
                                                                <!-- Barra de Progresso -->
                                                                <div class="progress-bar" style="height: 3px !important; background: rgba(0,0,0,0.2) !important; border-radius: 2px !important; position: relative !important; cursor: pointer !important;" onclick="seekAudio(event, this)">
                                                                    <div class="progress-fill" style="height: 100% !important; background: #007bff !important; border-radius: 2px !important; width: 0% !important; transition: width 0.1s ease !important;"></div>
                                                                </div>

                                                                <!-- Tempo e Duração -->
                                                                <div class="audio-time" style="display: flex !important; justify-content: space-between !important; align-items: center !important;">
                                                                    <span class="current-time" style="font-size: 11px !important; color: #666 !important; font-weight: 500 !important;">0:00</span>
                                                                    <span class="duration" style="font-size: 11px !important; color: #666 !important; font-weight: 500 !important;">0:00</span>
                                                                </div>
                                                            </div>

                                                            <!-- Botão Excluir (apenas para autor) -->
                                                            @if($isOwn || $isAdminGrupo)
                                                                <button class="btn btn-sm btn-link p-0 text-danger audio-delete-btn"
                                                                        onclick="confirmarDeletarMensagem('{{ $mensagem['id'] }}')"
                                                                        style="font-size: 14px !important; opacity: 0.7 !important;"
                                                                        title="Deletar áudio">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </button>
                                                            @endif

                                                            <!-- Audio Element (oculto) -->
                                                            <audio class="hidden-audio" preload="metadata">
                                                                <source src="{{ Storage::disk('supabase')->url($mensagem['anexo_url']) }}" type="audio/webm">
                                                                <source src="{{ Storage::disk('supabase')->url($mensagem['anexo_url']) }}" type="audio/mp3">
                                                            </audio>
                                                        </div>

                                                    @else
                                                        <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">{{ $messageText }}</div>

                                                        <!-- Exibir anexos se existirem -->
                                                        @if(!empty($mensagem['anexo_url']))
                                                            @php
                                                                $fileUrl = Storage::disk('supabase')->url($mensagem['anexo_url']);
                                                                $fileName = basename($mensagem['anexo_url']);
                                                                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            @endphp

                                                            @if($isImage)
                                                                <!-- Thumbnail para imagens -->
                                                                <div class="message-attachment image-attachment mt-2">
                                                                    <img src="{{ $fileUrl }}"
                                                                         alt="{{ $fileName }}"
                                                                         class="img-thumbnail"
                                                                         style="max-width: 200px; max-height: 150px; cursor: pointer; border-radius: 8px !important;"
                                                                         onclick="openImageModal('{{ $fileUrl }}', '{{ $fileName }}')"
                                                                         loading="lazy">
                                                                    <div class="attachment-info mt-1">
                                                                        <small class="text-muted">{{ Str::limit($fileName, 20) }}</small>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <!-- Ícone para documentos -->
                                                                <div class="message-attachment document-attachment mt-2">
                                                                    <a href="{{ $fileUrl }}"
                                                                       target="_blank"
                                                                       download="{{ $fileName }}"
                                                                       class="d-flex align-items-center p-2 border rounded"
                                                                       style="text-decoration: none; color: inherit; background: rgba(0,123,255,0.1); border-color: rgba(0,123,255,0.3) !important;">
                                                                        <i class="fas fa-file-{{ $fileExtension === 'pdf' ? 'pdf' : 'alt' }} text-primary me-2"
                                                                           style="font-size: 24px;"></i>
                                                                        <div class="flex-grow-1">
                                                                            <div class="fw-semibold small">{{ Str::limit($fileName, 25) }}</div>
                                                                            <small class="text-muted">{{ strtoupper($fileExtension) }} • Download</small>
                                                                        </div>
                                                                        <i class="fas fa-download text-primary ms-2"></i>
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                        <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">{{ $messageTime }}</span>
                                                        @if($mensagem['tipo_mensagem'] !== 'audio' && ($isOwn || $isAdminGrupo))
                                                            <button class="btn btn-sm btn-link p-0 ms-1 text-muted"
                                                                    onclick="confirmarDeletarMensagem('{{ $mensagem['id'] }}')"
                                                                    style="font-size: 10px !important;"
                                                                    title="Deletar mensagem">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        @endif
                                                        @if($isOwn)
                                                            <span class="status" style="visibility: visible !important; font-size: 11px !important; color: #4fc3f7 !important;"><i class="fas fa-check-double" style="visibility: visible !important; font-size: 10px !important;"></i></span>
                                                        @endif
                                                    </div>
                                                </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Mensagens de exemplo -->
                                    <div class="whatsapp-message other" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: left !important;">
                                        <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">Sistema</div>
                                        <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: 18px 18px 18px 4px !important; background-color: white !important; color: #303030 !important; border: 1px solid #e0e0e0 !important; position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                            <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">Bem-vindo ao chat da igreja! Aqui você pode conversar com todos os membros.</div>
                                            <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">Agora</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Modal de Confirmação de Delete (WhatsApp-style) -->
                                    <div class="modal fade" id="confirmarDeleteModal" tabindex="-1" aria-labelledby="confirmarDeleteModalLabel" aria-hidden="true"
                                         style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1055; display: none;">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 300px;">
                                            <div class="modal-content" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                                                <div class="modal-body text-center p-4">
                                                    <div class="mb-3">
                                                        <i class="fas fa-trash-alt text-danger" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <h6 class="modal-title fw-bold mb-2" id="confirmarDeleteModalLabel">Deletar mensagem?</h6>
                                                    <p class="text-muted small mb-4">Esta ação não pode ser desfeita.</p>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                                                onclick="fecharConfirmarDeleteModal()"
                                                                style="min-width: 80px;">
                                                            Cancelar
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm px-3"
                                                                onclick="executarDeletarMensagem()"
                                                                style="min-width: 80px;">
                                                            Deletar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de Confirmação de Sair do Chat -->
                                    <div class="modal fade" id="confirmarSairChatModal" tabindex="-1" aria-labelledby="confirmarSairChatModalLabel" aria-hidden="true"
                                         style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1055; display: none;">
                                        <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
                                            <div class="modal-content" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                                                <div class="modal-body text-center p-4">
                                                    <div class="mb-3">
                                                        <i class="fas fa-sign-out-alt text-warning" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <h6 class="modal-title fw-bold mb-2" id="confirmarSairChatModalLabel">Sair do Chat?</h6>
                                                    <p class="text-muted small mb-4">Você não poderá mais ver as mensagens deste chat. Esta ação pode ser revertida apenas por um administrador.</p>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                                                onclick="fecharConfirmarSairChatModal()"
                                                                style="min-width: 80px;">
                                                            Cancelar
                                                        </button>
                                                        <button type="button" class="btn btn-warning btn-sm px-3"
                                                                onclick="executarSairDoChat()"
                                                                style="min-width: 80px;">
                                                            Sair do Chat
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <!-- Área de Input com Mídia -->
                            <div class="card-footer border-0" style="padding: 8px 15px !important; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important; position: relative !important; margin-top: 0 !important; border-top: 1px solid rgba(0,0,0,0.05) !important;">
                                <!-- Anexos em exibição -->
                                @if($arquivoAudio || $arquivoAnexo)
                                <div class="mb-2 p-2 bg-light rounded d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-paperclip text-primary me-2"></i>
                                        <small class="text-muted">
                                            @if($arquivoAudio)
                                                🎵 {{ $arquivoAudio->getClientOriginalName() }}
                                            @elseif($arquivoAnexo)
                                                📎 {{ $arquivoAnexo->getClientOriginalName() }}
                                            @endif
                                        </small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="$set('arquivoAudio', null); $set('arquivoAnexo', null);">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif

                                <!-- Controles de Mídia -->
                                <div class="d-flex align-items-center mb-2 gap-2">
                                    <!-- Botão Gravar Áudio -->
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="btn-record-audio"
                                            onclick="toggleAudioRecording()">
                                        <i class="fas fa-microphone" id="record-icon"></i>
                                        <span id="record-text">Gravar</span>
                                    </button>

                                    <!-- Botão Parar Gravação (inicialmente oculto) -->
                                    <button type="button" class="btn btn-outline-danger btn-sm d-none"
                                            id="btn-stop-recording"
                                            onclick="stopAudioRecording()">
                                        <i class="fas fa-stop"></i>
                                        <span>Parar</span>
                                    </button>

                                    <!-- Upload de Arquivo -->
                                    <label class="btn btn-outline-secondary btn-sm mb-0">
                                        <i class="fas fa-paperclip"></i> Arquivo
                                        <input type="file" class="d-none" wire:model="arquivoAnexo"
                                               accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                                    </label>

                                    <!-- Upload de Áudio -->
                                    <label class="btn btn-outline-info btn-sm mb-0">
                                        <i class="fas fa-music"></i> Áudio
                                        <input type="file" class="d-none" wire:model="arquivoAudio"
                                               accept="audio/*">
                                    </label>
                                </div>

                                <!-- Indicador de Gravação -->
                                <div class="d-none" id="recording-indicator">
                                    <div class="d-flex align-items-center gap-2 text-danger">
                                        <i class="fas fa-circle fa-beat"></i>
                                        <small class="fw-semibold">Gravando áudio...</small>
                                        <small id="recording-timer">00:00</small>
                                    </div>
                                </div>

                                <!-- Campo de Mensagem -->
                                <form wire:submit.prevent="enviarMensagem" style="margin: 0 !important;">
                                    <div class="input-group" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04) !important; border-radius: 28px !important; overflow: hidden !important; border: 1px solid rgba(0,0,0,0.06) !important;">
                                        <input type="text"  autocomplete="new-password" autocomplete="new-password"
                                               class="form-control border-0"
                                               wire:model="novaMensagem"
                                               placeholder="Digite sua mensagem..."
                                               maxlength="1000"
                                               wire:keydown.enter.prevent="enviarMensagem"
                                               style="border-radius: 28px 0 0 28px !important; padding: 8px 15px !important; font-size: 14px !important; background: rgba(255,255,255,0.8) !important; border: none !important; box-shadow: none !important; font-weight: 400 !important;">
                                        <button class="btn border-0"
                                                type="button"
                                                wire:loading.attr="disabled"
                                                wire:target="enviarMensagem"
                                                onclick="handleSendButton()"
                                                style="border-radius: 0 28px 28px 0 !important; width: 40px !important; height: 40px !important; padding: 0 !important; display: flex !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important; transition: all 0.2s ease !important; box-shadow: 0 2px 8px rgba(0,123,255,0.3) !important;"
                                                onmouseover="this.style.background='linear-gradient(135deg, #0056b3 0%, #004085 100%) !important'; this.style.boxShadow='0 4px 12px rgba(0,123,255,0.4) !important'; this.style.transform='scale(1.05) !important';"
                                                onmouseout="this.style.background='linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important'; this.style.boxShadow='0 2px 8px rgba(0,123,255,0.3) !important'; this.style.transform='scale(1) !important';">
                                            <span wire:loading.remove>
                                                <i class="fas fa-paper-plane" style="font-size: 13px !important; color: white !important;"></i>
                                            </span>
                                            <span wire:loading>
                                                <i class="fas fa-spinner fa-spin" style="font-size: 13px !important; color: white !important;"></i>
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card h-100 d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <i class="fas fa-comments text-muted mb-4" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">Selecione um chat</h4>
                            <p class="text-muted">Escolha um chat da lista ao lado para começar a conversar</p>
                        </div>
                    </div>
                </div>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush

        @push('scripts')
        <script>
                // ==========================================
                // VARIÁVEIS GLOBAIS PARA MEDIA RECORDER
                // ==========================================

                // ==========================================
                // VARIÁVEIS GLOBAIS PARA MEDIA RECORDER (SPA-SAFE)
                // ==========================================

                // Declarar variáveis globais para media recorder (SPA-safe)
                window.mediaRecorder = window.mediaRecorder || null;
                window.audioChunks = window.audioChunks || [];
                window.recordingStartTime = window.recordingStartTime || null;
                window.recordingTimer = window.recordingTimer || null;
                window.stream = window.stream || null;
                window.recordedAudioBlob = window.recordedAudioBlob || null; // Armazenar o áudio gravado
                window.recordedAudioFileName = window.recordedAudioFileName || null; // Armazenar o nome do arquivo

                // Estado global do componente (SPA-safe)
                window.spaState = window.spaState || {
                    isInitialized: false,
                    listenersAttached: false,
                    currentChatId: null,
                    mediaRecorderActive: false
                };

                // Componente Livewire (SPA-safe)
                window.livewireComponent = window.livewireComponent || null;

                // Variável global para armazenar ID da mensagem a ser deletada (SPA-safe)
                window.mensagemParaDeletar = window.mensagemParaDeletar || null;

                // Estado do modal Sobre do Chat (SPA-safe)
                window.sobreChatAberto = window.sobreChatAberto || false;


                // Função para inicializar estado SPA
                function initializeSPAState() {
                    if (spaState.isInitialized) return;

                    spaState.isInitialized = true;

                    // Detectar se estamos em modo SPA
                    const isSPA = window.Livewire && window.Livewire.navigate;

                    if (isSPA) {
                        console.log('🎯 Modo SPA detectado - Inicializando listeners otimizados');
                    }

                    // Inicializar listeners principais
                    attachLivewireListeners();
                    attachSPASpecificListeners();
                }

                // Função para anexar listeners do Livewire
                function attachLivewireListeners() {
                    if (spaState.listenersAttached) return;

                    // Listener para scroll automático
                    Livewire.on('scroll-to-bottom', (data) => {
                        handleScrollToBottom(data);
                    });

                    // Listener para fechar modal de criar chat
                    Livewire.on('close-criar-chat-modal', () => {
                        handleCloseCriarChatModal();
                    });

                    // Listener para atualizar chat ativo
                    Livewire.on('chat-selecionado', (data) => {
                        spaState.currentChatId = data.chatId;
                        handleChatSelection(data);
                    });

                    spaState.listenersAttached = true;
                }

                // Função para anexar listeners específicos de SPA
                function attachSPASpecificListeners() {
                    // Listener para navegação SPA (Livewire 3)
                    document.addEventListener('livewire:navigated', () => {
                        console.log('🚀 Navegação SPA detectada - Reinicializando estado');
                        handleSPANavigation();
                    });

                    // Listener para quando o componente é atualizado
                    document.addEventListener('livewire:updated', () => {
                        handleComponentUpdate();
                    });

                    // Listener para quando o componente é carregado
                    document.addEventListener('livewire:loaded', () => {
                        handleComponentLoaded();
                    });
                }

                // Handlers para eventos SPA
                function handleSPANavigation() {
                    // Reinicializar estado após navegação
                    resetSPAState();

                    // Re-inicializar listeners
                    setTimeout(() => {
                        initializeSPAState();
                        initializeModalListeners();
                        initializeLivewireListeners();
                    }, 50);
                }

                function handleComponentUpdate() {
                    // Verificar se o chat ativo mudou
                    const newChatId = getCurrentChatIdFromDOM();
                    if (newChatId !== spaState.currentChatId) {
                        spaState.currentChatId = newChatId;

                        // Reinicializar modais quando o chat muda
                        setTimeout(() => {
                            initializeBootstrapModals();
                        }, 150);
                    }

                    // Sempre reinicializar modais em atualização de componente
                    setTimeout(() => {
                        initializeBootstrapModals();
                    }, 100);
                }

                function handleComponentLoaded() {
                    // Componente totalmente carregado
                    initializeSPAState();
                }

                function handleScrollToBottom(data) {
                    if (data.containerId) {
                        const container = document.getElementById(data.containerId);
                        if (container) {
                            // Usar requestAnimationFrame para melhor performance em SPA
                            requestAnimationFrame(() => {
                                container.scrollTop = container.scrollHeight;
                            });
                        }
                    }
                }

                function handleCloseCriarChatModal() {
                    // Usar sistema customizado em vez do Bootstrap
                    closeCriarChatModal();
                }

                function handleChatSelection(data) {
                    // Lógica adicional para seleção de chat em SPA
                    console.log('📱 Chat selecionado em modo SPA:', data.chatId);

                    // Reinicializar modais após mudança de chat para evitar conflitos
                    setTimeout(() => {
                        initializeBootstrapModals();
                    }, 200);
                }

                // Função para obter chat ID atual do DOM
                function getCurrentChatIdFromDOM() {
                    // Implementar lógica para extrair chat ID do DOM se necessário
                    return null;
                }

                // Função para resetar estado SPA
                function resetSPAState() {
                    spaState.currentChatId = null;
                    spaState.mediaRecorderActive = false;

                    // Limpar timers se existirem
                    if (window.recordingTimer) {
                        clearInterval(window.recordingTimer);
                        window.recordingTimer = null;
                    }

                    // Parar media recorder se ativo
                    if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }

                    // Limpar streams
                    if (typeof stream !== 'undefined' && stream) {
                        stream.getTracks().forEach(track => track.stop());
                        stream = null;
                    }
                }

                // ==========================================
                // MODAL CUSTOMIZADO (SEM BOOTSTRAP)
                // ==========================================

                // Função para abrir modal de criar chat
                function openCriarChatModal() {
                    // Primeiro, fechar chat ativo se existir
                    if (spaState.currentChatId) {
                        console.log('🔄 Fechando chat ativo antes de abrir modal...');

                        // Usar Livewire para fechar o chat
                        if (livewireComponent) {
                            const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                            if (componentId) {
                                livewireComponent.find(componentId).call('$set', 'chatAtivo', null);

                                // Aguardar um pouco para o chat fechar antes de abrir o modal
                                setTimeout(() => {
                                    openModalAfterChatClose();
                                }, 200);
                            } else {
                                // Fallback: abrir modal diretamente se não conseguir fechar chat
                                openModalAfterChatClose();
                            }
                        } else {
                            // Fallback: abrir modal diretamente se Livewire não estiver disponível
                            openModalAfterChatClose();
                        }
                    } else {
                        // Nenhum chat ativo, abrir modal diretamente
                        openModalAfterChatClose();
                    }
                }

                // Função auxiliar para abrir o modal após fechar chat
                function openModalAfterChatClose() {
                    const modal = document.getElementById('criarChatModal');
                    if (modal) {
                        modal.style.display = 'block';
                        modal.classList.add('show');
                        document.body.classList.add('modal-open');

                        // Criar backdrop customizado
                        let backdrop = document.querySelector('.modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop show';
                            document.body.appendChild(backdrop);
                        }

                        // Focar no modal
                        modal.focus();

                        console.log('📂 Modal de criar chat aberto (após fechar chat)');
                    }
                }

                // Função para fechar modal de criar chat
                function closeCriarChatModal() {
                    const modal = document.getElementById('criarChatModal');
                    if (modal) {
                        modal.style.display = 'none';
                        modal.classList.remove('show');
                        document.body.classList.remove('modal-open');

                        // Remover backdrop
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }

                        console.log('📂 Modal de criar chat fechado (custom)');
                    }
                }

                // ==========================================
                // FUNÇÕES DE INICIALIZAÇÃO PARA COMPATIBILIDADE
                // ==========================================

                // Função para inicializar listeners de modal (compatibilidade)
                function initializeModalListeners() {
                    // Esta função é chamada para compatibilidade SPA
                    // Os listeners reais são gerenciados pelo sistema SPA
                    console.log('📱 Modal listeners inicializados (SPA mode)');
                }

                // ==========================================
                // MODAL DE CONFIRMAÇÃO DE DELETE (WHATSAPP-STYLE)
                // ==========================================

                // Função para confirmar deleção de mensagem
                function confirmarDeletarMensagem(mensagemId) {
                    mensagemParaDeletar = mensagemId;
                    const modal = document.getElementById('confirmarDeleteModal');
                    if (modal) {
                        modal.style.display = 'block';
                        modal.classList.add('show');
                        document.body.classList.add('modal-open');

                        // Criar backdrop customizado
                        let backdrop = document.querySelector('.modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop show';
                            document.body.appendChild(backdrop);
                        }

                        // Focar no modal
                        modal.focus();

                        console.log('📂 Modal de confirmação de delete aberto');
                    }
                }

                // Função para confirmar e executar deleção
                function executarDeletarMensagem() {
                    if (mensagemParaDeletar) {
                        // Fechar modal primeiro
                        fecharConfirmarDeleteModal();

                        // Executar deleção via Livewire
                        if (livewireComponent) {
                            const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                            if (componentId) {
                                livewireComponent.find(componentId).call('deletarMensagem', mensagemParaDeletar);
                                console.log('🗑️ Solicitação de deleção enviada para:', mensagemParaDeletar);
                            }
                        }

                        // Limpar variável
                        mensagemParaDeletar = null;
                    }
                }

                // Função para fechar modal de confirmação
                function fecharConfirmarDeleteModal() {
                    const modal = document.getElementById('confirmarDeleteModal');
                    if (modal) {
                        modal.style.display = 'none';
                        modal.classList.remove('show');
                        document.body.classList.remove('modal-open');

                        // Remover backdrop
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }

                        console.log('📂 Modal de confirmação de delete fechado');
                    }
                }

                // ==========================================
                // SISTEMA SOBRE DO CHAT (JAVASCRIPT PURO - ULTRA RÁPIDO)
                // ==========================================

                // Função para abrir sobre do chat (JavaScript puro - ultra rápido)
                function abrirSobreChat() {
                    console.log('🚀 Abrindo Sobre do Chat via JavaScript puro');

                    // Esconder área de chat
                    const chatArea = document.querySelector('.col-md-8.col-lg-9');
                    if (chatArea) {
                        chatArea.style.display = 'none';
                    }

                    // Criar e mostrar container do Sobre
                    let sobreContainer = document.getElementById('sobre-chat-container');
                    if (!sobreContainer) {
                        sobreContainer = document.createElement('div');
                        sobreContainer.id = 'sobre-chat-container';
                        sobreContainer.className = 'col-md-8 col-lg-9';
                        sobreContainer.innerHTML = getSobreChatHTML();
                        chatArea.parentNode.insertBefore(sobreContainer, chatArea.nextSibling);
                    } else {
                        sobreContainer.style.display = 'block';
                    }

                    sobreChatAberto = true;
                    console.log('✅ Sobre do Chat aberto com sucesso');
                }

                // Função para fechar sobre do chat
                function fecharSobreChat() {
                    console.log('🔄 Fechando Sobre do Chat');

                    const sobreContainer = document.getElementById('sobre-chat-container');
                    const chatArea = document.querySelector('.col-md-8.col-lg-9');

                    if (sobreContainer) {
                        sobreContainer.style.display = 'none';
                    }

                    if (chatArea) {
                        chatArea.style.display = 'block';
                    }

                    sobreChatAberto = false;
                    console.log('✅ Sobre do Chat fechado');
                }

                // Função para gerar HTML do Sobre
                function getSobreChatHTML() {
                    const chatName = document.querySelector('.col-md-8.col-lg-9 h6 .fa-comments')?.parentElement?.textContent?.trim() || 'Chat da Igreja';

                    return `
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center bg-light">
                                <button class="btn btn-link p-0 me-3 text-decoration-none" onclick="fecharSobreChat()">
                                    <i class="fas fa-arrow-left text-primary" style="font-size: 1.2rem;"></i>
                                </button>
                                <h6 class="mb-0 flex-grow-1">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Sobre
                                </h6>
                            </div>
                            <div class="card-body p-0" style="overflow-y: auto; height: calc(100vh - 200px);">
                                <div class="p-4">
                                    <div class="text-center mb-4 position-relative">
                                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px; font-size: 2rem;">
                                            <i class="fas fa-comment-dots"></i>
                                        </div>
                                        <h5 class="mb-1">${chatName}</h5>
                                        <p class="text-muted small">Informações sobre este chat</p>

                                        <!-- Botão de editar (apenas para admins) -->
                                        <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0 mt-2 me-2"
                                                onclick="abrirModalEditarChat()"
                                                title="Editar chat">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <button class="btn btn-outline-primary w-100" wire:click="carregarAdminsChat">
                                                <i class="fas fa-shield-alt me-2"></i>
                                                <div class="d-block small">Administradores</div>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-outline-success w-100" onclick="alterarAbaSobre('membros')">
                                                <i class="fas fa-users me-2"></i>
                                                <div class="d-block small">Membros</div>
                                                <small class="text-muted">Carregando...</small>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // ==========================================
                // SISTEMA DE EDIÇÃO DE CHAT
                // ==========================================

                // Função para abrir modal de edição do chat
                function abrirModalEditarChat() {
                    console.log('📝 Abrindo modal de edição do chat');

                    // Fechar modal anterior se existir
                    fecharModalEditarChat();

                    const chatName = document.querySelector('.col-md-8.col-lg-9 h6 .fa-comments')?.parentElement?.textContent?.trim() || 'Chat da Igreja';

                    // Criar modal de edição com estilos inline mais robustos
                    const modalHTML = `
                        <div class="modal fade show" id="editarChatModal" tabindex="-1" aria-labelledby="editarChatModalLabel"
                            style="display: block !important; position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; z-index: 1060 !important; background: rgba(0,0,0,0.5) !important; opacity: 1 !important; visibility: visible !important;"
                            wire:ignore.self>
                            <div class="modal-dialog modal-dialog-centered" style="position: relative !important; top: 50% !important; transform: translateY(-50%) !important; z-index: 1061 !important;">
                                <div class="modal-content" style="border-radius: 12px !important; box-shadow: 0 10px 30px rgba(0,0,0,0.3) !important; position: relative !important; z-index: 1062 !important;">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="editarChatModalLabel">
                                            <i class="fas fa-edit me-2"></i>Editar Chat
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" onclick="fecharModalEditarChat()"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="formEditarChat">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-tag text-primary me-1"></i>Nome do Chat *
                                                </label>
                                                <input type="text" autocomplete="off" class="form-control" id="editChatNome" value="${chatName}" maxlength="255" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    <i class="fas fa-align-left text-primary me-1"></i>Descrição (opcional)
                                                </label>
                                                <textarea class="form-control" id="editChatDescricao" rows="3" maxlength="500" placeholder="Descreva o propósito deste chat..."></textarea>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" onclick="fecharModalEditarChat()">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="salvarEdicaoChat()">
                                            <i class="fas fa-save me-1"></i>Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Adicionar modal ao DOM
                    document.body.insertAdjacentHTML('beforeend', modalHTML);

                    // Criar backdrop com estilos inline mais robustos
                    let backdrop = document.querySelector('.modal-backdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop show';
                        backdrop.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; background: rgba(0,0,0,0.5) !important; z-index: 1050 !important; opacity: 1 !important; visibility: visible !important;';
                        document.body.appendChild(backdrop);
                    }

                    // Garantir que o body tenha a classe modal-open
                    document.body.classList.add('modal-open');

                    // Forçar foco no modal após um pequeno delay para garantir que foi renderizado
                    setTimeout(() => {
                        const modal = document.getElementById('editarChatModal');
                        if (modal) {
                            modal.focus();
                            console.log('✅ Modal de edição do chat focado');
                        } else {
                            console.error('❌ Modal não encontrado após criação');
                        }
                    }, 100);

                    console.log('✅ Modal de edição do chat criado e exibido');
                }

                // Função para fechar modal de edição
                function fecharModalEditarChat() {
                    const modal = document.getElementById('editarChatModal');
                    if (modal) {
                        modal.remove();
                    }

                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }

                    console.log('✅ Modal de edição do chat fechado');
                }

                // Função para salvar edição do chat
                async function salvarEdicaoChat() {
                    const nome = document.getElementById('editChatNome').value.trim();
                    const descricao = document.getElementById('editChatDescricao').value.trim();

                    if (!nome) {
                        alert('O nome do chat é obrigatório!');
                        return;
                    }

                    try {
                        console.log('💾 Salvando edição do chat...');

                        // Fazer chamada AJAX para o backend
                        const response = await fetch('/admin/church/chat/editar', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                nome: nome,
                                descricao: descricao
                            })
                        });

                        if (response.ok) {
                            const result = await response.json();
                            console.log('✅ Chat editado com sucesso:', result);

                            // Fechar modal
                            fecharModalEditarChat();

                            // Atualizar a página Sobre com os novos dados
                            abrirSobreChat();

                            // Mostrar toast de sucesso
                            showToast('Chat editado com sucesso!', 'success');
                        } else {
                            throw new Error('Erro ao editar chat');
                        }

                    } catch (error) {
                        console.error('❌ Erro ao editar chat:', error);
                        showToast('Erro ao editar chat. Tente novamente.', 'error');
                    }
                }

                // ==========================================
                // SISTEMA DE GERENCIAMENTO DE ADMINS
                // ==========================================

                // Função para alterar aba (agora com funcionalidade completa)
                function alterarAbaSobre(aba) {
                    console.log('Alterando para aba:', aba);

                    if (aba === 'permissoes') {
                        mostrarAbaPermissoes();
                    } else if (aba === 'membros') {
                        mostrarAbaMembros();
                    }
                }

                // Função para mostrar aba de permissões
                function mostrarAbaPermissoes() {
                    const sobreContainer = document.getElementById('sobre-chat-container');
                    if (!sobreContainer) return;

                    const permissoesHTML = `
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center bg-light">
                                <button class="btn btn-link p-0 me-3 text-decoration-none" onclick="abrirSobreChat()">
                                    <i class="fas fa-arrow-left text-primary" style="font-size: 1.2rem;"></i>
                                </button>
                                <h6 class="mb-0 flex-grow-1">
                                    <i class="fas fa-shield-alt text-primary me-2"></i>Administradores
                                </h6>
                                <button class="btn btn-sm btn-outline-success" onclick="abrirModalAdicionarAdmin()">
                                    <i class="fas fa-plus me-1"></i>Adicionar
                                </button>
                            </div>
                            <div class="card-body p-0" style="overflow-y: auto; height: calc(100vh - 200px);">
                                <div class="p-4">
                                    <div id="lista-admins">
                                        <div class="text-center py-4">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="text-muted mt-2">Carregando administradores...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    sobreContainer.innerHTML = permissoesHTML;

                    // Carregar lista de admins
                    carregarListaAdmins();
                }

                // Função para carregar lista de admins
                async function carregarListaAdmins() {
                    try {
                        console.log('📋 Carregando lista de administradores...');

                        const response = await fetch('/admin/church/chat/admins', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });

                        if (response.ok) {
                            const admins = await response.json();
                            console.log('✅ Administradores carregados:', admins);

                            renderizarListaAdmins(admins);
                        } else {
                            throw new Error('Erro ao carregar admins');
                        }

                    } catch (error) {
                        console.error('❌ Erro ao carregar admins:', error);
                        document.getElementById('lista-admins').innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                                <p class="text-muted">Erro ao carregar administradores</p>
                                <button class="btn btn-sm btn-outline-primary" onclick="carregarListaAdmins()">
                                    <i class="fas fa-redo me-1"></i>Tentar Novamente
                                </button>
                            </div>
                        `;
                    }
                }

                // Função para renderizar lista de admins
                function renderizarListaAdmins(admins) {
                    const listaContainer = document.getElementById('lista-admins');

                    if (!admins || admins.length === 0) {
                        listaContainer.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-shield-alt text-muted mb-3" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Nenhum administrador</h6>
                                <p class="text-muted small">Adicione administradores para gerenciar este chat</p>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    admins.forEach(admin => {
                        const podeRemover = admins.length > 1; // Só pode remover se houver mais de um admin

                        html += `
                            <div class="d-flex align-items-center p-3 border-bottom">
                                <div class="flex-shrink-0 me-3">
                                    ${admin.photo_url ?
                                        `<img src="${admin.photo_url}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">` :
                                        `<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            ${admin.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)}
                                        </div>`
                                    }
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">${admin.name}</div>
                                    <small class="text-muted">Administrador</small>
                                </div>
                                <div class="flex-shrink-0">
                                    ${podeRemover ?
                                        `<button class="btn btn-outline-danger btn-sm" onclick="removerAdmin('${admin.id}')">
                                            <i class="fas fa-user-minus me-1"></i>Remover
                                        </button>` :
                                        `<span class="badge bg-warning text-dark">
                                            <i class="fas fa-crown me-1"></i>Único Admin
                                        </span>`
                                    }
                                </div>
                            </div>
                        `;
                    });

                    listaContainer.innerHTML = html;
                }

                // Função para abrir modal de adicionar admin
                function abrirModalAdicionarAdmin() {
                    console.log('➕ Abrindo modal para adicionar admin');

                    // Criar modal
                    const modalHTML = `
                        <div class="modal fade" id="adicionarAdminModal" tabindex="-1" aria-labelledby="adicionarAdminModalLabel" aria-hidden="true"
                            style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1055; display: block;" wire:igonore.self>
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="adicionarAdminModalLabel">
                                            <i class="fas fa-user-plus me-2"></i>Adicionar Administrador
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" onclick="fecharModalAdicionarAdmin()"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                <i class="fas fa-users text-success me-1"></i>Selecionar Membro
                                            </label>
                                            <select class="form-select" id="selectNovoAdmin">
                                                <option value="">Carregando membros...</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Nota:</strong> O membro selecionado terá permissões para editar o chat, adicionar/remover membros e gerenciar administradores.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" onclick="fecharModalAdicionarAdmin()">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="adicionarAdmin()">
                                            <i class="fas fa-plus me-1"></i>Adicionar Admin
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    document.body.insertAdjacentHTML('beforeend', modalHTML);

                    // Criar backdrop
                    let backdrop = document.querySelector('.modal-backdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop show';
                        document.body.appendChild(backdrop);
                    }

                    // Carregar lista de membros disponíveis
                    carregarMembrosDisponiveis();
                }

                // Função para fechar modal de adicionar admin
                function fecharModalAdicionarAdmin() {
                    const modal = document.getElementById('adicionarAdminModal');
                    if (modal) {
                        modal.remove();
                    }

                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }

                // Função para carregar membros disponíveis para admin
                async function carregarMembrosDisponiveis() {
                    try {
                        const response = await fetch('/admin/church/chat/membros-disponiveis', {
                            method: 'GET',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });

                        if (response.ok) {
                            const membros = await response.json();

                            const select = document.getElementById('selectNovoAdmin');
                            if (membros.length > 0) {
                                let options = '<option value="">Selecione um membro...</option>';
                                membros.forEach(membro => {
                                    options += `<option value="${membro.id}">${membro.name}</option>`;
                                });
                                select.innerHTML = options;
                            } else {
                                select.innerHTML = '<option value="">Nenhum membro disponível</option>';
                            }
                        } else {
                            throw new Error('Erro ao carregar membros');
                        }

                    } catch (error) {
                        console.error('❌ Erro ao carregar membros:', error);
                        document.getElementById('selectNovoAdmin').innerHTML = '<option value="">Erro ao carregar</option>';
                    }
                }

                // Função para adicionar admin
                async function adicionarAdmin() {
                    const userId = document.getElementById('selectNovoAdmin').value;

                    if (!userId) {
                        alert('Selecione um membro para adicionar como administrador!');
                        return;
                    }

                    try {
                        console.log('👑 Adicionando administrador...');

                        const response = await fetch('/admin/church/chat/adicionar-admin', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                user_id: userId
                            })
                        });

                        if (response.ok) {
                            const result = await response.json();
                            console.log('✅ Administrador adicionado:', result);

                            // Fechar modal
                            fecharModalAdicionarAdmin();

                            // Recarregar lista de admins
                            carregarListaAdmins();

                            // Mostrar toast
                            showToast('Administrador adicionado com sucesso!', 'success');
                        } else {
                            throw new Error('Erro ao adicionar admin');
                        }

                    } catch (error) {
                        console.error('❌ Erro ao adicionar admin:', error);
                        showToast('Erro ao adicionar administrador. Tente novamente.', 'error');
                    }
                }

                // Função para remover admin
                async function removerAdmin(userId) {
                    if (!confirm('Tem certeza que deseja remover este administrador?')) {
                        return;
                    }

                    try {
                        console.log('👑 Removendo administrador...');

                        const response = await fetch('/admin/church/chat/remover-admin', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({
                                user_id: userId
                            })
                        });

                        if (response.ok) {
                            const result = await response.json();
                            console.log('✅ Administrador removido:', result);

                            // Recarregar lista de admins
                            carregarListaAdmins();

                            // Mostrar toast
                            showToast('Administrador removido com sucesso!', 'success');
                        } else {
                            throw new Error('Erro ao remover admin');
                        }

                    } catch (error) {
                        console.error('❌ Erro ao remover admin:', error);
                        showToast('Erro ao remover administrador. Tente novamente.', 'error');
                    }
                }

                // Função para mostrar aba de membros (placeholder)
                function mostrarAbaMembros() {
                    console.log('👥 Mostrando aba de membros');
                    // Implementar depois se necessário
                }

                // ==========================================
                // MODAL DE CONFIRMAÇÃO DE SAIR DO CHAT
                // ==========================================

                // Função para confirmar saída do chat
                function confirmarSairDoChat() {
                    const modal = document.getElementById('confirmarSairChatModal');
                    if (modal) {
                        modal.style.display = 'block';
                        modal.classList.add('show');
                        document.body.classList.add('modal-open');

                        // Criar backdrop customizado
                        let backdrop = document.querySelector('.modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop show';
                            document.body.appendChild(backdrop);
                        }

                        // Focar no modal
                        modal.focus();

                        console.log('📂 Modal de confirmação de sair do chat aberto');
                    }
                }

                // Função para confirmar e executar saída do chat
                function executarSairDoChat() {
                    // Fechar modal primeiro
                    fecharConfirmarSairChatModal();

                    // Executar saída do chat via Livewire
                    if (livewireComponent) {
                        const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            livewireComponent.find(componentId).call('sairDoChat');
                            console.log('🚪 Solicitação de saída do chat enviada');
                        }
                    }
                }

                // Função para fechar modal de confirmação de sair do chat
                function fecharConfirmarSairChatModal() {
                    const modal = document.getElementById('confirmarSairChatModal');
                    if (modal) {
                        modal.style.display = 'none';
                        modal.classList.remove('show');
                        document.body.classList.remove('modal-open');

                        // Remover backdrop
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }

                        console.log('📂 Modal de confirmação de sair do chat fechado');
                    }
                }

                // ==========================================
                // MODAL CUSTOMIZADO (SEM BOOTSTRAP)
                // ==========================================

                // Função para abrir modal de criar chat
                function openCriarChatModal() {
                    const modal = document.getElementById('criarChatModal');
                    if (modal) {
                        modal.style.display = 'block';
                        modal.classList.add('show');
                        document.body.classList.add('modal-open');

                        // Criar backdrop customizado
                        let backdrop = document.querySelector('.modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop show';
                            document.body.appendChild(backdrop);
                        }

                        // Focar no modal
                        modal.focus();

                        // Adicionar listener para fechar com ESC
                        const closeModal = (e) => {
                            if (e.key === 'Escape') {
                                closeCriarChatModal();
                                document.removeEventListener('keydown', closeModal);
                            }
                        };
                        document.addEventListener('keydown', closeModal);

                        // Adicionar listener para fechar clicando no backdrop
                        backdrop.addEventListener('click', closeCriarChatModal);

                        console.log('📂 Modal de criar chat aberto (custom)');
                    }
                }

                // Função para fechar modal de criar chat
                function closeCriarChatModal() {
                    const modal = document.getElementById('criarChatModal');
                    if (modal) {
                        modal.style.display = 'none';
                        modal.classList.remove('show');
                        document.body.classList.remove('modal-open');

                        // Remover backdrop
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }

                        console.log('📂 Modal de criar chat fechado (custom)');
                    }
                }

                // Função para inicializar modais Bootstrap corretamente (fallback)
                function initializeBootstrapModals() {
                    // Esta função agora é apenas um fallback
                    // Os modais são controlados pelo sistema customizado
                    console.log('🔧 Sistema de modais customizado ativo');
                }

                // Função para inicializar listeners Livewire (compatibilidade)
                function initializeLivewireListeners() {
                    // Esta função é chamada para compatibilidade SPA
                    // Os listeners reais são gerenciados pelo sistema SPA
                    console.log('🔄 Livewire listeners inicializados (SPA mode)');
                }

                // Inicializar componente Livewire quando disponível
                document.addEventListener('livewire:init', () => {
                    livewireComponent = window.Livewire;
                    initializeSPAState();

                    // Inicializar modais Bootstrap logo no início
                    initializeBootstrapModals();
                });

                // ==========================================
                // VARIÁVEIS GLOBAIS PARA MEDIA RECORDER
                // ==========================================

                // Declarar variáveis globais para media recorder

                // Função para enviar áudio gravado
                        function enviarAudioGravado(audioBlob, fileName) {
                            if (livewireComponent) {
                                const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                                if (componentId) {
                                    livewireComponent.find(componentId).call('receberAudioGravado', audioBlob, fileName);
                                }
                            } else {
                                console.error('Componente Livewire não encontrado');
                            }
                        }


                        // Função para lidar com o clique no botão enviar
                        function handleSendButton() {
                            // Verificar se há gravação em andamento
                            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                                // Parar gravação - o áudio será enviado automaticamente no onstop
                                stopAudioRecording();
                                return;
                            }

                            // Se não há gravação em andamento, verificar se há mensagem ou anexos
                            const messageInput = document.querySelector('input[wire\\:model="novaMensagem"]');
                            const hasMessage = messageInput && messageInput.value.trim() !== '';
                            const hasAudioFile = document.querySelector('input[wire\\:model="arquivoAudio"]')?.files.length > 0;
                            const hasAttachment = document.querySelector('input[wire\\:model="arquivoAnexo"]')?.files.length > 0;

                            // Se há conteúdo para enviar, chamar o método Livewire
                            if (hasMessage || hasAudioFile || hasAttachment) {
                                if (livewireComponent) {
                                    const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                                    if (componentId) {
                                        livewireComponent.find(componentId).call('enviarMensagem');
                                    }
                                }
                            }
                        }

                        // Função para alternar gravação de áudio
                        async function toggleAudioRecording() {
                            const btnRecord = document.getElementById('btn-record-audio');
                            const btnStop = document.getElementById('btn-stop-recording');
                            const recordIcon = document.getElementById('record-icon');
                            const recordText = document.getElementById('record-text');
                            const indicator = document.getElementById('recording-indicator');

                            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                                // Já está gravando, parar
                                stopAudioRecording();
                                return;
                            }

                            try {
                                // Solicitar permissão para microfone
                                stream = await navigator.mediaDevices.getUserMedia({
                                    audio: {
                                        echoCancellation: true,
                                        noiseSuppression: true,
                                        sampleRate: 44100
                                    }
                                });

                                // Criar MediaRecorder
                                mediaRecorder = new MediaRecorder(stream, {
                                    mimeType: 'audio/webm;codecs=opus'
                                });

                                audioChunks = [];
                                recordingStartTime = Date.now();

                                // Event listeners
                                mediaRecorder.ondataavailable = (event) => {
                                    if (event.data.size > 0) {
                                        audioChunks.push(event.data);
                                    }
                                };

                                mediaRecorder.onstop = async () => {
                                    try {
                                        // Criar blob do áudio
                                        const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                                        const fileName = `gravacao_audio_${Date.now()}.webm`;

                                        // Usar FileReader de forma mais eficiente
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            const base64Audio = e.target.result.split(',')[1]; // Remover o prefixo data:
                                            // Enviar para o Livewire imediatamente
                                            enviarAudioGravado(base64Audio, fileName);
                                        };
                                        reader.readAsDataURL(audioBlob);

                                    } catch (error) {
                                        console.error('Erro ao processar áudio gravado:', error);
                                        showToast('Erro ao processar áudio gravado', 'error');
                                    } finally {
                                        // Limpar stream
                                        if (stream) {
                                            stream.getTracks().forEach(track => track.stop());
                                            stream = null;
                                        }
                                    }
                                };

                                // Iniciar gravação
                                mediaRecorder.start(1000); // Coletar dados a cada segundo

                                // Atualizar UI
                                btnRecord.classList.add('btn-danger');
                                btnRecord.classList.remove('btn-outline-primary');
                                recordIcon.classList.remove('fa-microphone');
                                recordIcon.classList.add('fa-stop');
                                recordText.textContent = 'Gravando...';
                                indicator.classList.remove('d-none');

                                // Mostrar botão parar
                                btnStop.classList.remove('d-none');

                                // Iniciar timer
                                startRecordingTimer();

                                // Notificação
                                showToast('Gravação iniciada', 'success');

                            } catch (error) {
                                console.error('Erro ao iniciar gravação:', error);
                                showToast('Erro ao acessar microfone. Verifique as permissões.', 'error');

                                // Resetar UI em caso de erro
                                resetRecordingUI();
                            }
                        }

                        // Função para parar gravação
                        function stopAudioRecording() {
                            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                                mediaRecorder.stop();
                                // Não resetar UI aqui, será feito no onstop
                            } else {
                                // Se não está gravando, apenas resetar UI
                                resetRecordingUI();
                            }
                        }

                        // Função para resetar UI de gravação
                        function resetRecordingUI() {
                            const btnRecord = document.getElementById('btn-record-audio');
                            const btnStop = document.getElementById('btn-stop-recording');
                            const recordIcon = document.getElementById('record-icon');
                            const recordText = document.getElementById('record-text');
                            const indicator = document.getElementById('recording-indicator');

                            btnRecord.classList.remove('btn-danger');
                            btnRecord.classList.add('btn-outline-primary');
                            recordIcon.classList.add('fa-microphone');
                            recordIcon.classList.remove('fa-stop');
                            recordText.textContent = 'Gravar';
                            indicator.classList.add('d-none');
                            btnStop.classList.add('d-none');

                            // Resetar timer
                            const timerElement = document.getElementById('recording-timer');
                            if (timerElement) {
                                timerElement.textContent = '00:00';
                            }
                        }

                        // Função para iniciar timer de gravação
                        function startRecordingTimer() {
                            recordingTimer = setInterval(() => {
                                if (recordingStartTime) {
                                    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                                    const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
                                    const seconds = (elapsed % 60).toString().padStart(2, '0');

                                    const timerElement = document.getElementById('recording-timer');
                                    if (timerElement) {
                                        timerElement.textContent = `${minutes}:${seconds}`;
                                    }
                                }
                            }, 1000);
                        }

                        // Função para mostrar toast
                        function showToast(message, type = 'info') {
                            // Criar toast element
                            const toast = document.createElement('div');
                            toast.className = `toast align-items-center text-white bg-${type} border-0`;
                            toast.setAttribute('role', 'alert');
                            toast.innerHTML = `
                                <div class="d-flex">
                                    <div class="toast-body">${message}</div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                </div>
                            `;

                            // Adicionar ao container de toasts
                            const toastContainer = document.querySelector('.toast-container') || createToastContainer();
                            toastContainer.appendChild(toast);

                            // Inicializar e mostrar
                            const bsToast = new bootstrap.Toast(toast);
                            bsToast.show();

                            // Remover após ocultar
                            toast.addEventListener('hidden.bs.toast', () => {
                                toast.remove();
                            });
                        }

                        // Função para criar container de toasts se não existir
                        function createToastContainer() {
                            const container = document.createElement('div');
                            container.className = 'toast-container position-fixed top-0 end-0 p-3';
                            container.style.zIndex = '9999';
                            document.body.appendChild(container);
                            return container;
                        }

                        // Cleanup quando a página é descarregada
                        window.addEventListener('beforeunload', () => {
                            if (stream) {
                                stream.getTracks().forEach(track => track.stop());
                            }
                            if (recordingTimer) {
                                clearInterval(recordingTimer);
                            }
                        });

                        // ==========================================
                        // CONTROLE DO PLAYER DE ÁUDIO WHATSAPP-STYLE
                        // ==========================================

                        // Função para alternar play/pause do áudio
                        function toggleAudioPlay(button) {
                            const playerContainer = button.closest('.whatsapp-audio-player');
                            const audio = playerContainer.querySelector('.hidden-audio');
                            const playIcon = button.querySelector('i');
                            const progressFill = playerContainer.querySelector('.progress-fill');
                            const currentTimeSpan = playerContainer.querySelector('.current-time');
                            const durationSpan = playerContainer.querySelector('.duration');

                            if (audio.paused) {
                                // Pausar outros áudios que possam estar tocando
                                document.querySelectorAll('.hidden-audio').forEach(otherAudio => {
                                    if (otherAudio !== audio && !otherAudio.paused) {
                                        otherAudio.pause();
                                        const otherContainer = otherAudio.closest('.whatsapp-audio-player');
                                        const otherButton = otherContainer.querySelector('.audio-play-btn i');
                                        otherButton.className = 'fas fa-play';
                                        otherButton.style.marginLeft = '2px';
                                    }
                                });

                                // Iniciar este áudio
                                audio.play();
                                playIcon.className = 'fas fa-pause';
                                playIcon.style.marginLeft = '0px';

                                // Atualizar barra de progresso
                                const updateProgress = () => {
                                    if (!audio.paused) {
                                        const progress = (audio.currentTime / audio.duration) * 100;
                                        progressFill.style.width = progress + '%';
                                        currentTimeSpan.textContent = formatTime(audio.currentTime);
                                        requestAnimationFrame(updateProgress);
                                    }
                                };
                                updateProgress();

                            } else {
                                // Pausar este áudio
                                audio.pause();
                                playIcon.className = 'fas fa-play';
                                playIcon.style.marginLeft = '2px';
                            }
                        }

                        // Função para formatar tempo (segundos para mm:ss)
                        function formatTime(seconds) {
                            const mins = Math.floor(seconds / 60);
                            const secs = Math.floor(seconds % 60);
                            return mins + ':' + (secs < 10 ? '0' : '') + secs;
                        }

                        // Função para buscar posição no áudio clicando na barra
                        function seekAudio(event, progressBar) {
                            const playerContainer = progressBar.closest('.whatsapp-audio-player');
                            const audio = playerContainer.querySelector('.hidden-audio');
                            const rect = progressBar.getBoundingClientRect();
                            const clickX = event.clientX - rect.left;
                            const percentage = clickX / rect.width;
                            const newTime = percentage * audio.duration;

                            if (!isNaN(newTime)) {
                                audio.currentTime = newTime;
                                const progressFill = progressBar.querySelector('.progress-fill');
                                progressFill.style.width = (percentage * 100) + '%';
                                const currentTimeSpan = playerContainer.querySelector('.current-time');
                                currentTimeSpan.textContent = formatTime(newTime);
                            }
                        }

                        // Atualizar duração quando metadata carrega
                        document.addEventListener('DOMContentLoaded', () => {
                            document.querySelectorAll('.hidden-audio').forEach(audio => {
                                audio.addEventListener('loadedmetadata', function() {
                                    const playerContainer = this.closest('.whatsapp-audio-player');
                                    const durationSpan = playerContainer.querySelector('.duration');
                                    durationSpan.textContent = formatTime(this.duration);
                                });

                                // Resetar player quando áudio termina
                                audio.addEventListener('ended', function() {
                                    const playerContainer = this.closest('.whatsapp-audio-player');
                                    const playIcon = playerContainer.querySelector('.audio-play-btn i');
                                    const progressFill = playerContainer.querySelector('.progress-fill');
                                    const currentTimeSpan = playerContainer.querySelector('.current-time');

                                    playIcon.className = 'fas fa-play';
                                    playIcon.style.marginLeft = '2px';
                                    progressFill.style.width = '0%';
                                    currentTimeSpan.textContent = '0:00';
                                    this.currentTime = 0;
                                });
                            });
                        });
        </script>
        @endpush
    </div>

    <!-- Modal para Criar Novo Chat -->
    <div class="modal fade" id="criarChatModal" tabindex="-1" aria-labelledby="criarChatModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title fw-bold" id="criarChatModalLabel">
                        <i class="fas fa-plus-circle text-white me-2"></i>Criar Novo Chat
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="closeCriarChatModal()" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="criarChat">
                        <div class="text-center mb-4">
                            <i class="fas fa-comment-dots text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="fw-bold text-center mb-3">Crie um novo chat para a igreja</h6>
                        <p class="text-muted mb-4">
                            Todos os membros da igreja poderão participar e enviar mensagens neste chat.
                        </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-tag text-primary me-1"></i>Nome do Chat *
                        </label>
                        <input type="text"  autocomplete="new-password" autocomplete="new-password" 
                               class="form-control @error('nomeChat') is-invalid @enderror"
                               wire:model="nomeChat"
                               placeholder="Ex: Oração, Estudos Bíblicos, Avisos..."
                               maxlength="255">
                        @error('nomeChat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left text-primary me-1"></i>Descrição (opcional)
                        </label>
                        <textarea class="form-control @error('descricaoChat') is-invalid @enderror"
                                  wire:model="descricaoChat"
                                  placeholder="Descreva o propósito deste chat..."
                                  rows="3"
                                  maxlength="500"></textarea>
                        @error('descricaoChat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-eye text-primary me-1"></i>Visibilidade do Chat
                        </label>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="visibilidadeChat" value="publico" id="publico">
                                    <label class="form-check-label" for="publico">
                                        <i class="fas fa-globe text-success me-1"></i>
                                        <strong>Público</strong><br>
                                        <small class="text-muted">Todos os membros da igreja são adicionados automaticamente</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model="visibilidadeChat" value="privado" id="privado">
                                    <label class="form-check-label" for="privado">
                                        <i class="fas fa-lock text-warning me-1"></i>
                                        <strong>Privado</strong><br>
                                        <small class="text-muted">Apenas participantes adicionados manualmente</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @error('visibilidadeChat')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Como administrador, você pode criar chats para organizar as comunicações da igreja.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeCriarChatModal()">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="criarChat">
                        <span wire:loading.remove wire:target="criarChat">
                            <i class="fas fa-plus me-1"></i>Criar Chat
                        </span>
                        <span wire:loading wire:target="criarChat">
                            <i class="fas fa-spinner fa-spin me-1"></i>Criando...
                        </span>
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
