<div>
        <div class="container-fluid p-4">
        <!-- Conteúdo do Chat Privado -->
        <div class="row" style="height: calc(100vh - 100px);">
            <!-- Lista Lateral de Conversas -->
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-user-friends text-info me-2"></i>Conversas Privadas
                        </h6>
                        <button class="btn btn-sm bg-info text-light" wire:click="abrirModalNovaConversa" data-bs-toggle="modal"
                        data-bs-target="#abrirModalNovaConversa" title="Nova conversa">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="card-body p-0" style="overflow-y: auto; height: calc(100vh - 200px);">
                        @if(!empty($conversas))
                            @foreach($conversas as $conversa)
                                <div class="alliance-item p-3 border-bottom cursor-pointer {{ $conversaAtiva == $conversa['usuario']['id'] ? 'bg-info text-light text-white' : 'hover-bg-light' }}"
                                     wire:click="selecionarConversa('{{ $conversa['usuario']['id'] }}')">
                                    <div class="d-flex align-items-center">
                                        <div class="alliance-avatar me-3">
                                            @if($conversa['usuario']['photo_url'])
                                                <img src="{{ Storage::disk('supabase')->url($conversa['usuario']['photo_url']) }}"
                                                     alt="{{ $conversa['usuario']['name'] }}"
                                                     class="rounded-circle"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                    {{ substr($conversa['usuario']['name'], 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold">{{ Str::limit($conversa['usuario']['name'], 20) }}</div>
                                            <small class="{{ $conversaAtiva == $conversa['usuario']['id'] ? 'text-white-50' : 'text-muted' }}">
                                                @if($conversaAtiva == $conversa['usuario']['id'])
                                                    {{ count($mensagens ?? []) }} mensagens
                                                @else
                                                    @if(!empty($conversa['ultima_mensagem']))
                                                        {{ Str::limit($conversa['ultima_mensagem']['conteudo'] ?? '', 25) }}
                                                    @else
                                                        Sem mensagens
                                                    @endif
                                                @endif
                                            </small>
                                        </div>
                                        @if($conversaAtiva != $conversa['usuario']['id'] && !empty($conversa['nao_lidas']) && $conversa['nao_lidas'] > 0)
                                            <span class="badge bg-danger ms-2">{{ $conversa['nao_lidas'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-user-friends text-muted mb-3" style="font-size: 2rem;"></i>
                                <h6 class="text-muted">Nenhuma conversa</h6>
                                <p class="text-muted small">Suas conversas privadas aparecerão aqui</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Área de Chat Privado -->
            <div class="col-md-8 col-lg-9">
                @if($conversaAtiva && $usuarioDestino)
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="">
                                <h6 class="mb-0" style="cursor: pointer;">
                                    <i class="fas fa-user text-info me-2"></i>
                                    {{ $usuarioDestino->name }}
                                </h6>
                                <small class="text-muted m-2 mb-2 mt-2">
                                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                    Conversa privada
                                </small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" wire:click="abrirModalNovaConversa" data-bs-toggle="modal"
                                        data-bs-target="#abrirModalNovaConversa" title="Nova conversa">
                                        <i class="fas fa-plus me-2"></i>Nova conversa
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="#" data-bs-toggle="modal" data-bs-target="#confirmarLimparConversa">
                                        <i class="fas fa-trash-alt me-2"></i>Limpar conversa
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" wire:click="fecharConversa">
                                        <i class="fas fa-times me-2"></i>Fechar conversa
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-0 d-flex flex-column" style="height: calc(100vh - 200px);">
                            <!-- Área de Mensagens -->
                            <div class="chat-messages" id="privateChatMessages" style="overflow-y: auto !important; padding: 10px !important; height: calc(100vh - 200px) !important; min-height: 200px !important; max-height: calc(100vh - 130px) !important; display: block !important; visibility: visible !important; background: #f8f9fa !important; border: 1px solid #dee2e6 !important; margin-bottom: 0 !important;">

                                <!-- Mensagens reais -->
                                @if(!empty($mensagens))
                                    @foreach($mensagens as $mensagem)
                                        @php
                                            $isOwn = $mensagem['remetente_id'] === Auth::id();
                                            $senderName = $mensagem['remetente']['name'] ?? 'Usuário';
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
                                                        @if($isOwn)
                                                            <button class="btn btn-sm btn-link p-0 text-danger audio-delete-btn"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#confirmarDeleteModal"
                                                                    onclick="setMensagemParaDeletar('{{ $mensagem['id'] }}')"
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
                                                                    <i class="fas fa-file-{{ $fileExtension === 'pdf' ? 'pdf' : 'alt' }} text-info me-2"
                                                                       style="font-size: 24px;"></i>
                                                                    <div class="flex-grow-1">
                                                                        <div class="fw-semibold small">{{ Str::limit($fileName, 25) }}</div>
                                                                        <small class="text-muted">{{ strtoupper($fileExtension) }} • Download</small>
                                                                    </div>
                                                                    <i class="fas fa-download text-info ms-2"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endif
                                                <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                    <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">{{ $messageTime }}</span>
                                                    @if($mensagem['tipo_mensagem'] !== 'audio' && $isOwn)
                                                        <button class="btn btn-sm btn-link p-0 ms-1 text-muted"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#confirmarDeleteModal"
                                                                onclick="setMensagemParaDeletar('{{ $mensagem['id'] }}')"
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
                                            <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">Esta é uma conversa privada. Suas mensagens são visíveis apenas para você e {{ $usuarioDestino->name }}.</div>
                                            <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">Agora</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Modal de Confirmação de Delete (Bootstrap) -->
                                <div class="modal fade" id="confirmarDeleteModal" tabindex="-1" aria-labelledby="confirmarDeleteModalLabel" aria-hidden="true">
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
                                                            data-bs-dismiss="modal"
                                                            style="min-width: 80px;">
                                                        Cancelar
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm px-3"
                                                            onclick="executarDeletarMensagem()"
                                                            data-bs-dismiss="modal"
                                                            style="min-width: 80px;">
                                                        Deletar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal de Confirmação para Limpar Conversa -->
                            <div class="modal fade" id="confirmarLimparConversa" tabindex="-1" aria-labelledby="confirmarLimparConversaLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" style="max-width: 350px;">
                                    <div class="modal-content" style="border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                                        <div class="modal-body text-center p-4">
                                            <div class="mb-3">
                                                <i class="fas fa-trash-alt text-warning" style="font-size: 2.5rem;"></i>
                                            </div>
                                            <h6 class="modal-title fw-bold mb-2" id="confirmarLimparConversaLabel">Limpar conversa?</h6>
                                            <p class="text-muted small mb-4">Todas as mensagens desta conversa serão deletadas permanentemente. Esta ação não pode ser desfeita.</p>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-3"
                                                        data-bs-dismiss="modal"
                                                        style="min-width: 80px;">
                                                    Cancelar
                                                </button>
                                                <button type="button" class="btn btn-warning btn-sm px-3"
                                                        onclick="executarLimparConversa()"
                                                        data-bs-dismiss="modal"
                                                        style="min-width: 80px;">
                                                    Limpar
                                                </button>
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
                                        <i class="fas fa-paperclip text-info me-2"></i>
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
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password" 
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
                @endif
            </div>

            </div>
        </div>


        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush

        <!-- Modal Nova Conversa -->

        <div class="modal fade" id="abrirModalNovaConversa" tabindex="-1" style="background-color: rgba(0,0,0,0.5);"  aria-labelledby="abrirModalNovaConversa" aria-hidden="true"
        data-bs-backdrop="true" data-bs-keyboard="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus me-2"></i>Nova Conversa Privada
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="fecharModalNovaConversa"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Campo de busca -->
                        <div class="mb-3">
                            <input type="text"  autocomplete="new-password"  autocomplete="new-password" 
                                   class="form-control"
                                   wire:model.live.debounce.300ms="termoBuscaMembro"
                                   placeholder="Buscar membro..."
                                   autocomplete="off">
                        </div>

                        <!-- Lista de membros -->
                        <div class="list-group" style="max-height: 300px; overflow-y: auto;" id="membersList">
                            @if(!empty($membrosDisponiveis))
                                @foreach($membrosDisponiveis as $membro)
                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center"
                                   wire:click="iniciarConversaCom('{{ $membro['id'] }}')"
                                   data-bs-dismiss="modal">
                                    <div class="me-3">
                                        @if($membro['photo_url'])
                                            <img src="{{ Storage::disk('supabase')->url($membro['photo_url']) }}"
                                                 alt="{{ $membro['name'] }}"
                                                 class="rounded-circle"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                {{ substr($membro['name'], 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $membro['name'] }}</div>
                                        <small class="text-muted">{{ $membro['email'] ?? '' }}</small>
                                    </div>
                                </a>
                                @endforeach

                                <!-- Botão Carregar Mais -->
                                @if(!$todosMembrosCarregados)
                                <div class="text-center py-2">
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm"
                                            wire:click="carregarMaisMembros"
                                            wire:loading.attr="disabled"
                                            wire:target="carregarMaisMembros">
                                        <span wire:loading.remove>
                                            <i class="fas fa-plus me-1"></i>Carregar mais
                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin me-1"></i>Carregando...
                                        </span>
                                    </button>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-search text-muted mb-2" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0">Nenhum membro encontrado</p>
                                </div>
                            @endif

                            <!-- Loader de busca -->
                            <div wire:loading wire:target="termoBuscaMembro" class="text-center py-3">
                                <i class="fas fa-spinner fa-spin text-info me-2"></i>
                                <small class="text-muted">Buscando membros...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @push('scripts')
        <script>
                // ==========================================
                // VARIÁVEIS GLOBAIS PARA MEDIA RECORDER
                // ==========================================

                // Declarar variáveis globais para media recorder (SPA-safe)
                window.mediaRecorder = window.mediaRecorder || null;
                window.audioChunks = window.audioChunks || [];
                window.recordingStartTime = window.recordingStartTime || null;
                window.recordingTimer = window.recordingTimer || null;
                window.stream = window.stream || null;
                window.recordedAudioBlob = window.recordedAudioBlob || null; // Armazenar o áudio gravado
                window.recordedAudioFileName = window.recordedAudioFileName || null; // Armazenar o nome do arquivo

                // ==========================================
                // GERENCIAMENTO DE ESTADO PARA SPA
                // ==========================================

                // Estado global do componente (SPA-safe)
                window.spaState = window.spaState || {
                    isInitialized: false,
                    listenersAttached: false,
                    currentChatId: null,
                    mediaRecorderActive: false
                };
                // Componente Livewire (SPA-safe)
                window.livewireComponent = window.livewireComponent || null;

                // Componente Livewire (SPA-safe)
                window.livewireComponent = window.livewireComponent || null;

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
                    }, 50);
                }

                function handleComponentUpdate() {
                    // Verificar se o chat ativo mudou
                    const newChatId = getCurrentChatIdFromDOM();
                    if (newChatId !== spaState.currentChatId) {
                        spaState.currentChatId = newChatId;
                    }
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
                // MODAL DE CONFIRMAÇÃO DE DELETE (BOOTSTRAP)
                // ==========================================

                // Variável global para armazenar ID da mensagem a ser deletada (SPA-safe)
                window.mensagemParaDeletar = window.mensagemParaDeletar || null;

                // Função para definir mensagem a ser deletada
                function setMensagemParaDeletar(mensagemId) {
                    window.mensagemParaDeletar = mensagemId;
                }

                // Função para confirmar e executar deleção
                function executarDeletarMensagem() {
                    if (window.mensagemParaDeletar) {
                        // Executar deleção via Livewire usando @this
                        @this.call('deletarMensagem', window.mensagemParaDeletar);
                        console.log('🗑️ Solicitação de deleção enviada para:', window.mensagemParaDeletar);

                        // Limpar variável
                        window.mensagemParaDeletar = null;
                    }
                }

                // ==========================================
                // MODAL DE CONFIRMAÇÃO PARA LIMPAR CONVERSA
                // ==========================================

                // Função para confirmar e executar limpeza da conversa
                function executarLimparConversa() {
                    @this.call('limparConversa');
                    console.log('🧹 Solicitação de limpeza de conversa enviada');
                }

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

                // ==========================================
                // SISTEMA DE GRAVAÇÃO DE ÁUDIO
                // ==========================================

                // Função para enviar áudio gravado
                function enviarAudioGravado(audioBlob, fileName) {
                    @this.call('receberAudioGravado', audioBlob, fileName);
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
                        @this.call('enviarMensagem');
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

                // Inicializar componente Livewire quando disponível
                document.addEventListener('livewire:init', () => {
                    window.livewireComponent = window.Livewire;
                    initializeSPAState();
                });
        </script>
        @endpush

</div>
