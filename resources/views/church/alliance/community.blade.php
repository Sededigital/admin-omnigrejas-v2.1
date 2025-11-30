@php
    use App\Helpers\RBAC\PermissionHelper;
    use App\Helpers\Billings\ResourceHelper;
    
    $permissionHelper = new PermissionHelper(Auth::user());
    $resourceHelper = new ResourceHelper();

@endphp

<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-comments me-2"></i>Comunidade das Alianças
                        </h1>
                        <p class="mb-0 text-muted">
                            Troque experiências, mensagens privadas e organize reuniões com outros líderes
                        </p>
                    </div>
                    @if($permissionHelper->hasPermission('gerenciar_aliancas') && $resourceHelper->userCanAccessResource('gerenciar_aliancas'))
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('churches.alliance.tools') }}" wire:navigate class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Voltar
                        </a>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>


        <!-- Navegação por Abas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm" wire:ignore>
                    <div class="card-body p-0">
                        <ul class="nav nav-pills nav-fill m-0" id="communityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-0 py-3" id="alliance-messages-tab" data-bs-toggle="pill" data-bs-target="#alliance-messages" type="button" role="tab">
                                    <i class="fas fa-comments me-2"></i>
                                    <span class="d-none d-sm-inline">Mensagens</span>
                                    <span class="d-sm-none">Mensagens</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-0 py-3" id="private-messages-tab" data-bs-toggle="pill" data-bs-target="#private-messages" type="button" role="tab" wire:click="mudarAba('private')">
                                    <i class="fas fa-envelope me-2"></i>
                                    <span class="d-none d-sm-inline">Privadas</span>
                                    <span class="d-sm-none">Privadas</span>
                                    @if(isset($mensagensPrivadas2NaoLidasTotal) && $mensagensPrivadas2NaoLidasTotal > 0)
                                        <span class="badge bg-danger ms-1">{{ $mensagensPrivadas2NaoLidasTotal }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-0 py-3" id="meetings-tab" data-bs-toggle="pill" data-bs-target="#meetings" type="button" role="tab">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <span class="d-none d-sm-inline">Reuniões</span>
                                    <span class="d-sm-none">Reuniões</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @push('styles')
            <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
            @endpush

            {{-- Script para Alliance/Meetings --}}
            <script src="{{ asset('system/js/alliance.js') }}"></script>

        </div>
        <!-- Conteúdo das Abas -->
        <div class="tab-content mb-5" id="communityTabsContent" >
            <!-- Aba Mensagens da Comunidade -->
            <div class="tab-pane fade show active" id="alliance-messages" role="tabpanel" wire:ignore.self>
                <div class="row" style="height: calc(100vh - 300px);">
                    <!-- Lista Lateral de Alianças -->
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-users text-primary me-2"></i>Minhas Alianças
                                </h6>
                            </div>
                            <div class="card-body p-0" style="overflow-y: auto; height: calc(100vh - 400px);">
                                @if(!empty($minhasAliancas ?? []))
                                     @foreach($minhasAliancas ?? [] as $alianca)
                                         <!-- Chat de Líderes -->
                                         <div class="alliance-item p-3 border-bottom cursor-pointer {{ $aliancaId == $alianca->alianca_id && $tipoChat == 'lideres' ? 'bg-primary text-white' : 'hover-bg-light' }}"
                                              wire:click="selecionarAlianca({{ $alianca->alianca_id }}, 'lideres')">
                                             <div class="d-flex align-items-center">
                                                 <div class="alliance-avatar me-3">
                                                     <i class="fas fa-crown text-{{ $aliancaId == $alianca->alianca_id && $tipoChat == 'lideres' ? 'white' : 'warning' }}" style="font-size: 1.2rem;"></i>
                                                 </div>
                                                 <div class="flex-grow-1">
                                                     <div class="fw-semibold">{{ Str::limit($alianca->alianca->nome, 15) }} (Líderes)</div>
                                                     <small class="{{ $aliancaId == $alianca->alianca_id && $tipoChat == 'lideres' ? 'text-white-50' : 'text-muted' }}">
                                                         @if($aliancaId == $alianca->alianca_id && $tipoChat == 'lideres')
                                                             {{ $totalMensagens ?? 0 }} mensagens
                                                         @else
                                                             Chat exclusivo para líderes
                                                         @endif
                                                     </small>
                                                 </div>
                                                 @if($aliancaId != $alianca->alianca_id && isset($mensagensNaoLidas) && $mensagensNaoLidas > 0)
                                                     <span class="badge bg-danger ms-2">{{ $mensagensNaoLidas }}</span>
                                                 @endif
                                             </div>
                                         </div>

                                         <!-- Chat da Comunidade Geral -->
                                         <div class="alliance-item p-3 border-bottom cursor-pointer {{ $aliancaId == $alianca->alianca_id && $tipoChat == 'comunidade' ? 'bg-primary text-white' : 'hover-bg-light' }}"
                                              wire:click="selecionarAlianca({{ $alianca->alianca_id }}, 'comunidade')">
                                             <div class="d-flex align-items-center">
                                                 <div class="alliance-avatar me-3">
                                                     <i class="fas fa-users text-{{ $aliancaId == $alianca->alianca_id && $tipoChat == 'comunidade' ? 'white' : 'success' }}" style="font-size: 1.2rem;"></i>
                                                 </div>
                                                 <div class="flex-grow-1">
                                                     <div class="fw-semibold">{{ Str::limit($alianca->alianca->nome, 15) }} (Geral)</div>
                                                     <small class="{{ $aliancaId == $alianca->alianca_id && $tipoChat == 'comunidade' ? 'text-white-50' : 'text-muted' }}">
                                                         @if($aliancaId == $alianca->alianca_id && $tipoChat == 'comunidade')
                                                             {{ $totalMensagens ?? 0 }} mensagens
                                                         @else
                                                             Chat para toda comunidade
                                                         @endif
                                                     </small>
                                                 </div>
                                                 @if($aliancaId != $alianca->alianca_id && isset($mensagensNaoLidas) && $mensagensNaoLidas > 0)
                                                     <span class="badge bg-danger ms-2">{{ $mensagensNaoLidas }}</span>
                                                 @endif
                                             </div>
                                         </div>
                                     @endforeach
                                 @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-handshake text-muted mb-3" style="font-size: 2rem;"></i>
                                        <h6 class="text-muted">Nenhuma aliança</h6>
                                        <p class="text-muted small">Participe de alianças para conversar</p>
                                        <a href="{{ route('churches.alliance.my') }}" wire:navigate class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Gerenciar
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                    <!-- Área de Chat da Aliança -->
                    <div class="col-md-8 col-lg-9">
                        @if($aliancaId)
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="">
                                        <h6 class="mb-0">
                                                <i class="fas fa-comments text-primary me-2"></i>
                                                {{ collect($minhasAliancas ?? [])->firstWhere('alianca_id', $aliancaId)->alianca->nome ?? 'Aliança' }}
                                                @if($tipoChat == 'lideres')
                                                    <span class="badge bg-warning text-dark ms-2">Líderes</span>
                                                @else
                                                    <span class="badge bg-success ms-2">Geral</span>
                                                @endif
                                            </h6>
                                            <small class="text-muted m-2 mb-2 mt-2">
                                                <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                                @if($tipoChat == 'lideres')
                                                    {{ $totalLideres ?? 0 }} líderes online
                                                @else
                                                    {{ $totalLideres ?? 0 }} membros online
                                                @endif
                                            </small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" wire:click="marcarMensagensComoLidas">
                                                <i class="fas fa-check-double me-2"></i>Marcar como lidas
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="{{ route('churches.alliance.tools') }}" wire:navigate>
                                                <i class="fas fa-cog me-2"></i>Gerenciar Alianças
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body p-0 d-flex flex-column" style="height: calc(100vh - 350px);">
                                    <!-- Área de Mensagens -->
                                    <div class="chat-messages" id="chatMessages" style="overflow-y: auto !important; padding: 10px !important; height: calc(100vh - 350px) !important; min-height: 200px !important; max-height: calc(100vh - 180px) !important; display: block !important; visibility: visible !important; background: #f8f9fa !important; border: 1px solid #dee2e6 !important; margin-bottom: 0 !important;">


                                        <!-- Mensagens reais com estilos inline -->
                                        @if(isset($mensagens) && !empty($mensagens) && isset($membroAtual))
                                            @foreach($mensagens as $mensagem)
                                                @php
                                                    $isOwn = $mensagem['remetente_id'] === $membroAtual->id;
                                                    $senderName = $mensagem['remetente']['user']['name'] ?? 'Usuário';
                                                    $messageText = $mensagem['mensagem'];
                                                    $messageTime = \Carbon\Carbon::parse($mensagem['created_at'])->format('H:i');
                                                @endphp

                                                <div class="whatsapp-message {{ $isOwn ? 'own' : 'other' }}" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: {{ $isOwn ? 'right' : 'left' }} !important;">
                                                    @if(!$isOwn)
                                                        <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">{{ $senderName }}</div>
                                                    @endif
                                                    <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: {{ $isOwn ? '18px 18px 4px 18px' : '18px 18px 18px 4px' }} !important; background-color: {{ $isOwn ? '#dcf8c6' : 'white' }} !important; color: #303030 !important; {{ !$isOwn ? 'border: 1px solid #e0e0e0 !important;' : '' }} position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                                            @if($mensagem['tipo_mensagem'] === 'audio')
                                                                @php
                                                                    $audioAnexo = !empty($mensagem['anexos']) && is_array($mensagem['anexos']) ? $mensagem['anexos'][0] : null;
                                                                    $audioUrl = $audioAnexo ? Storage::disk('supabase')->url($audioAnexo['url']) : '';
                                                                @endphp
                                                                @if($audioUrl)
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
                                                                                    onclick="setMensagemParaDeletarAlianca('{{ $mensagem['id'] }}')"
                                                                                    style="font-size: 14px !important; opacity: 0.7 !important;"
                                                                                    title="Deletar áudio">
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </button>
                                                                        @endif

                                                                        <!-- Audio Element (oculto) -->
                                                                        <audio class="hidden-audio" preload="metadata">
                                                                            <source src="{{ $audioUrl }}" type="audio/webm">
                                                                            <source src="{{ $audioUrl }}" type="audio/mp3">
                                                                        </audio>
                                                                    </div>
                                                                @endif

                                                            @else
                                                                <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">{{ $messageText }}</div>

                                                                <!-- Exibir anexos se existirem -->
                                                                @if(!empty($mensagem['anexos']))
                                                                    @foreach($mensagem['anexos'] as $anexo)
                                                                        @php
                                                                            $fileUrl = Storage::disk('supabase')->url($anexo['url']);
                                                                            $fileName = $anexo['nome'];
                                                                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                            $isAudio = in_array($fileExtension, ['mp3', 'wav', 'ogg', 'aac', 'm4a', 'webm']);
                                                                        @endphp

                                                                        @if($isAudio)
                                                                            <!-- Player de Áudio WhatsApp-style -->
                                                                            <div class="whatsapp-audio-player" style="display: flex !important; align-items: center !important; gap: 10px !important; padding: 8px !important; background: rgba(0,0,0,0.05) !important; border-radius: 12px !important; margin-top: 5px !important; min-width: 200px !important;">
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
                                                                                    <source src="{{ $fileUrl }}" type="audio/webm">
                                                                                    <source src="{{ $fileUrl }}" type="audio/mp3">
                                                                                </audio>
                                                                            </div>

                                                                        @elseif($isImage)
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
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                            <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                                <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">{{ $messageTime }}</span>
                                                                @if($isOwn)
                                                                    <span class="status" style="visibility: visible !important; font-size: 11px !important; color: #4fc3f7 !important;"><i class="fas fa-check-double" style="visibility: visible !important; font-size: 10px !important;"></i></span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Mensagens de exemplo quando não há mensagens reais -->
                                            <div class="whatsapp-message other" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: left !important;">
                                                <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">Sistema Omnigrejas</div>
                                                <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: 18px 18px 18px 4px !important; background-color: white !important; color: #303030 !important; border: 1px solid #e0e0e0 !important; position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                                    <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">Essa é a comunidade de uma Aliança onde todos os membros podem fazer e trocar ideias.</div>
                                                    <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                        <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">{{ now()->format('H:i') }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                        @endif
                                    </div>

                                    <!-- Área de Input -->
                                    <div class="card-footer border-0" style="padding: 8px 15px !important; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important; position: relative !important; margin-top: 0 !important; border-top: 1px solid rgba(0,0,0,0.05) !important;">
                                        <!-- Anexos em exibição -->
                                        @if($arquivoAudioAlianca || $arquivoAnexoAlianca)
                                        <div class="mb-2 p-2 bg-light rounded d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-paperclip text-primary me-2"></i>
                                                <small class="text-muted">
                                                    @if($arquivoAudioAlianca)
                                                        🎵 {{ $arquivoAudioAlianca->getClientOriginalName() }}
                                                    @elseif($arquivoAnexoAlianca)
                                                        📎 {{ $arquivoAnexoAlianca->getClientOriginalName() }}
                                                    @endif
                                                </small>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    wire:click="limparAnexosAlianca">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        @endif

                                        <!-- Controles de Mídia -->
                                        <div class="d-flex align-items-center mb-2 gap-2">
                                            <!-- Botão Gravar Áudio -->
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                    id="btn-record-audio-alliance"
                                                    onclick="toggleAudioRecordingAlliance()">
                                                <i class="fas fa-microphone" id="record-icon-alliance"></i>
                                                <span id="record-text-alliance">Gravar</span>
                                            </button>

                                            <!-- Botão Parar Gravação (inicialmente oculto) -->
                                            <button type="button" class="btn btn-outline-danger btn-sm d-none"
                                                    id="btn-stop-recording-alliance"
                                                    onclick="stopAudioRecordingAlliance()">
                                                <i class="fas fa-stop"></i>
                                                <span>Parar</span>
                                            </button>

                                            <!-- Upload de Arquivo -->
                                            <label class="btn btn-outline-secondary btn-sm mb-0">
                                                <i class="fas fa-paperclip"></i> Arquivo
                                                <input type="file" class="d-none" wire:model="arquivoAnexoAlianca"
                                                       accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                                            </label>

                                            <!-- Upload de Áudio -->
                                            <label class="btn btn-outline-info btn-sm mb-0">
                                                <i class="fas fa-music"></i> Áudio
                                                <input type="file" class="d-none" wire:model="arquivoAudioAlianca"
                                                       accept="audio/*">
                                            </label>
                                        </div>

                                        <!-- Indicador de Gravação -->
                                        <div class="d-none" id="recording-indicator-alliance">
                                            <div class="d-flex align-items-center gap-2 text-danger">
                                                <i class="fas fa-circle fa-beat"></i>
                                                <small class="fw-semibold">Gravando áudio...</small>
                                                <small id="recording-timer-alliance">00:00</small>
                                            </div>
                                        </div>

                                        <!-- Campo de Mensagem -->
                                        <form wire:submit.prevent="enviarMensagemChat" style="margin: 0 !important;">
                                            <div class="input-group" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04) !important; border-radius: 28px !important; overflow: hidden !important; border: 1px solid rgba(0,0,0,0.06) !important;">
                                                <input type="text"  autocomplete="new-password" autocomplete="new-password"
                                                       class="form-control border-0"
                                                       wire:model="novaMensagemChat"
                                                       placeholder="Digite sua mensagem..."
                                                       maxlength="1000"
                                                       wire:keydown.enter.prevent="enviarMensagemChat"
                                                       style="border-radius: 28px 0 0 28px !important; padding: 8px 15px !important; font-size: 14px !important; background: rgba(255,255,255,0.8) !important; border: none !important; box-shadow: none !important; font-weight: 400 !important;">
                                                <button class="btn border-0"
                                                        type="button"
                                                        wire:loading.attr="disabled"
                                                        wire:target="enviarMensagemChat"
                                                        onclick="handleSendButtonAlliance()"
                                                        style="border-radius: 0 28px 28px 0 !important; width: 40px !important; height: 40px !important; padding: 0 !important; display: flex !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important; transition: all 0.2s ease !important; box-shadow: 0 2px 8px rgba(0,123,255,0.3) !important;"
                                                        onmouseover="this.style.background='linear-gradient(135deg, #0056b3 0%, #004085 100%) !important; this.style.boxShadow='0 4px 12px rgba(0,123,255,0.4) !important'; this.style.transform='scale(1.05) !important';"
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
                                    <h4 class="text-muted">Selecione uma aliança</h4>
                                    <p class="text-muted">Escolha uma aliança da lista ao lado para começar a conversar</p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>

                @push('scripts')
                <script>
                // Função para ativar aba privada
                function ativarAbaPrivada() {
                    console.log('Ativando aba privada...');
                    const tabElement = document.querySelector('#private-messages-tab');
                    if (tabElement) {
                        console.log('Elemento da aba encontrado:', tabElement);
                        // Usar setTimeout para garantir que execute após o Livewire
                        setTimeout(() => {
                            try {
                                const tab = new bootstrap.Tab(tabElement);
                                tab.show();
                                console.log('Aba privada ativada com sucesso');
                            } catch (error) {
                                console.error('Erro ao ativar aba privada:', error);
                            }
                        }, 200);
                    } else {
                        console.error('Elemento da aba privada não encontrado');
                    }
                }

                // Função chamada quando clica em "Nova" ou "Iniciar Conversa"
                function prepararAbaPrivada() {
                    console.log('Preparando aba privada...');
                    // Marcar que queremos a aba privada
                    sessionStorage.setItem('forcePrivateTab', 'true');
                }

                document.addEventListener('DOMContentLoaded', function() {
                    console.log('DOM carregado, verificando aba privada...');

                    // Verificar se devemos forçar a aba privada
                    const forcePrivate = sessionStorage.getItem('forcePrivateTab');
                    if (forcePrivate === 'true') {
                        console.log('Forçando ativação da aba privada');
                        sessionStorage.removeItem('forcePrivateTab');
                        ativarAbaPrivada();
                    }

                    // Listener para eventos do Livewire
                    document.addEventListener('livewire:loaded', () => {
                        console.log('Livewire carregado, configurando listener...');
                        Livewire.on('ativar-aba-privada', () => {
                            console.log('Evento ativar-aba-privada recebido');
                            ativarAbaPrivada();
                        });
                    });

                    // Quando uma aba é clicada, limpar o force
                    document.querySelectorAll('#communityTabs .nav-link').forEach(tab => {
                        tab.addEventListener('click', function() {
                            console.log('Aba clicada, limpando force');
                            sessionStorage.removeItem('forcePrivateTab');
                        });
                    });
                });
                </script>

                {{-- Script para Scroll Automático --}}
                <script>
                // Função para fazer scroll até o final do container
                function scrollToBottom(containerId) {
                    const container = document.getElementById(containerId);
                    if (container) {
                        // Usar setTimeout para garantir que as mensagens sejam renderizadas primeiro
                        setTimeout(() => {
                            container.scrollTop = container.scrollHeight;
                            console.log('Scroll automático executado para:', containerId);
                        }, 100);
                    } else {
                        console.warn('Container não encontrado:', containerId);
                    }
                }

                // Listener para o evento de scroll automático
                document.addEventListener('livewire:loaded', () => {
                    console.log('Livewire carregado - configurando listener de scroll');

                    Livewire.on('scroll-to-bottom', (data) => {
                        console.log('Evento scroll-to-bottom recebido:', data);
                        if (data.containerId) {
                            scrollToBottom(data.containerId);
                        }
                    });
                });

                // Também escutar quando o DOM for atualizado (para casos onde o Livewire re-renderiza)
                document.addEventListener('DOMContentLoaded', function() {
                    console.log('DOM carregado - configurando scroll automático');

                    // Fazer scroll automático quando uma conversa for selecionada
                    Livewire.on('scroll-to-bottom', (data) => {
                        console.log('Evento scroll-to-bottom (DOM) recebido:', data);
                        if (data.containerId) {
                            // Pequeno delay para garantir que as mensagens sejam renderizadas
                            setTimeout(() => {
                                scrollToBottom(data.containerId);
                            }, 150);
                        }
                    });
                });
                </script>
                @endpush


            </div>


            <!-- Aba Mensagens Privadas -->
            <div class="tab-pane fade" id="private-messages" role="tabpanel" wire:ignore.self>
                <div class="row" style="height: calc(100vh - 300px);">
                    <!-- Lista Lateral de Conversas -->
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-user-friends text-primary me-2"></i>Conversas
                                </h6>
                                <button class="btn btn-outline-primary btn-sm" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                    <i class="fas fa-plus me-1"></i>Nova
                                </button>
                            </div>
                            <div class="card-body p-0" style="overflow-y: auto; height: calc(100vh - 400px);">
                                @if(!isset($conversas2) || empty($conversas2))
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox text-muted mb-3" style="font-size: 2rem;"></i>
                                        <h6 class="text-muted">Nenhuma conversa</h6>
                                        <p class="text-muted small">Inicie uma conversa com membros da aliança</p>
                                        <button class="btn btn-primary btn-sm" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                            <i class="fas fa-plus me-1"></i>Iniciar Conversa
                                        </button>
                                    </div>
                                @else
                                    @foreach($conversas2 as $conversa)
                                        <div class="alliance-item p-3 border-bottom cursor-pointer {{ (isset($conversaAtiva2) && $conversaAtiva2 == $conversa['usuario_id']) ? 'bg-primary text-white' : 'hover-bg-light' }}"
                                             wire:click="selecionarConversa2('{{ $conversa['usuario_id'] }}')">
                                            <div class="d-flex align-items-center">
                                                <div class="alliance-avatar me-3">
                                                    <i class="fas fa-user-circle text-{{ (isset($conversaAtiva2) && $conversaAtiva2 == $conversa['usuario_id']) ? 'white' : 'primary' }}" style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">{{ Str::limit($conversa['usuario']['name'], 20) }}</div>
                                                    <small class="{{ (isset($conversaAtiva2) && $conversaAtiva2 == $conversa['usuario_id']) ? 'text-white-50' : 'text-muted' }}">
                                                        @if(isset($conversaAtiva2) && $conversaAtiva2 == $conversa['usuario_id'])
                                                            {{ count($mensagensPrivadas2 ?? []) }} mensagens
                                                        @else
                                                            {{ Str::limit($conversa['ultima_mensagem']['conteudo'], 25) }}
                                                        @endif
                                                    </small>
                                                </div>
                                                @if($conversa['nao_lidas'] > 0)
                                                    <span class="badge bg-danger ms-2">{{ $conversa['nao_lidas'] }}</span>
                                                @endif
                                            </div>
                                            <small class="{{ (isset($conversaAtiva2) && $conversaAtiva2 == $conversa['usuario_id']) ? 'text-white-50' : 'text-muted' }}">
                                                {{ $conversa['ultima_atividade']->diffForHumans() }}
                                            </small>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Área de Chat da Conversa Privada -->
                    <div class="col-md-8 col-lg-9">
                        @if(isset($conversaAtiva2) && $conversaAtiva2)
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="">
                                        <h6 class="mb-0">
                                            <i class="fas fa-comments text-primary me-2"></i>
                                            Conversa com {{ (isset($conversas2) && isset($conversaAtiva2)) ? collect($conversas2)->firstWhere('usuario_id', $conversaAtiva2)['usuario']['name'] ?? 'Usuário' : 'Usuário' }}
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
                                            <li><a class="dropdown-item" href="#" >
                                                <i class="fas fa-check-double me-2"></i>Marcar como lidas
                                            </a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item" href="#" wire:click="$set('conversaAtiva2', null)">
                                                <i class="fas fa-times me-2"></i>Fechar conversa
                                            </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body p-0 d-flex flex-column" style="height: calc(100vh - 350px);">
                                    <!-- Área de Mensagens -->
                                    <div class="chat-messages" id="privateChatMessages" style="overflow-y: auto !important; padding: 10px !important; height: calc(100vh - 350px) !important; min-height: 200px !important; max-height: calc(100vh - 180px) !important; display: block !important; visibility: visible !important; background: #f8f9fa !important; border: 1px solid #dee2e6 !important; margin-bottom: 0 !important;">

                                        <!-- Mensagens reais com estilos inline -->
                                        @if(isset($mensagensPrivadas2) && !empty($mensagensPrivadas2))
                                            @foreach($mensagensPrivadas2 as $mensagem)
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
                                                                            onclick="setMensagemParaDeletarAlianca('{{ $mensagem['id'] }}')"
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
                                                            @if($isOwn)
                                                                <span class="status" style="visibility: visible !important; font-size: 11px !important; color: #4fc3f7 !important;"><i class="fas fa-check-double" style="visibility: visible !important; font-size: 10px !important;"></i></span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <!-- Mensagens de exemplo quando não há mensagens reais -->
                                            <div class="whatsapp-message other" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: left !important;">
                                                <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">João Silva</div>
                                                <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: 18px 18px 18px 4px !important; background-color: white !important; color: #303030 !important; border: 1px solid #e0e0e0 !important; position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                                    <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">Olá! Vamos conversar sobre a aliança?</div>
                                                    <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                        <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">15:40</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="whatsapp-message own" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: right !important;">
                                                <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: 18px 18px 4px 18px !important; background-color: #dcf8c6 !important; color: #303030 !important; position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                                    <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">Claro! O que você gostaria de discutir?</div>
                                                    <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                        <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">15:41</span>
                                                        <span class="status" style="visibility: visible !important; font-size: 11px !important; color: #4fc3f7 !important;"><i class="fas fa-check-double" style="visibility: visible !important; font-size: 10px !important;"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="whatsapp-message other" style="display: block !important; visibility: visible !important; margin-bottom: 8px !important; padding: 0 10px !important; clear: both !important; text-align: left !important;">
                                                <div class="sender-name" style="display: block !important; visibility: visible !important; font-size: 12px !important; font-weight: 600 !important; color: #007aff !important; margin-bottom: 2px !important;">Maria Santos</div>
                                                <div class="message-bubble" style="display: inline-block !important; visibility: visible !important; max-width: 75% !important; padding: 6px 10px !important; border-radius: 18px 18px 18px 4px !important; background-color: white !important; color: #303030 !important; border: 1px solid #e0e0e0 !important; position: relative !important; font-size: 14px !important; line-height: 1.3 !important; word-wrap: break-word !important; box-shadow: 0 1px 2px rgba(0,0,0,0.1) !important;">
                                                    <div class="message-text" style="display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important;">Que tal organizarmos um evento conjunto?</div>
                                                    <div class="message-info" style="display: flex !important; visibility: visible !important; justify-content: flex-end !important; align-items: center !important; margin-top: 3px !important; gap: 3px !important;">
                                                        <span class="time" style="visibility: visible !important; font-size: 11px !important; color: #8e8e93 !important; font-weight: 500 !important;">15:42</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
                                                    id="btn-record-audio-private"
                                                    onclick="toggleAudioRecordingPrivate()">
                                                <i class="fas fa-microphone" id="record-icon-private"></i>
                                                <span id="record-text-private">Gravar</span>
                                            </button>

                                            <!-- Botão Parar Gravação (inicialmente oculto) -->
                                            <button type="button" class="btn btn-outline-danger btn-sm d-none"
                                                    id="btn-stop-recording-private"
                                                    onclick="stopAudioRecordingPrivate()">
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
                                        <div class="d-none" id="recording-indicator-private">
                                            <div class="d-flex align-items-center gap-2 text-danger">
                                                <i class="fas fa-circle fa-beat"></i>
                                                <small class="fw-semibold">Gravando áudio...</small>
                                                <small id="recording-timer-private">00:00</small>
                                            </div>
                                        </div>

                                        <!-- Campo de Mensagem -->
                                        <form wire:submit.prevent="enviarMensagemPrivada2" style="margin: 0 !important;">
                                            <div class="input-group" style="box-shadow: 0 4px 20px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04) !important; border-radius: 28px !important; overflow: hidden !important; border: 1px solid rgba(0,0,0,0.06) !important;">
                                                <input type="text"  autocomplete="new-password" autocomplete="new-password"
                                                        class="form-control border-0"
                                                        wire:model="novaMensagemPrivada2"
                                                        placeholder="Digite sua mensagem..."
                                                        maxlength="1000"
                                                        wire:keydown.enter.prevent="enviarMensagemPrivada2"
                                                        style="border-radius: 28px 0 0 28px !important; padding: 8px 15px !important; font-size: 14px !important; background: rgba(255,255,255,0.8) !important; border: none !important; box-shadow: none !important; font-weight: 400 !important;">
                                                <button class="btn border-0"
                                                        type="button"
                                                        wire:loading.attr="disabled"
                                                        wire:target="enviarMensagemPrivada2"
                                                        onclick="handleSendButtonPrivate()"
                                                        style="border-radius: 0 28px 28px 0 !important; width: 40px !important; height: 40px !important; padding: 0 !important; display: flex !important; align-items: center !important; justify-content: center !important; background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important; transition: all 0.2s ease !important; box-shadow: 0 2px 8px rgba(0,123,255,0.3) !important;"
                                                        onmouseover="this.style.background='linear-gradient(135deg, #0056b3 0%, #004085 100%) !important; this.style.boxShadow='0 4px 12px rgba(0,123,255,0.4) !important'; this.style.transform='scale(1.05) !important';"
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
                                    <h4 class="text-muted">Selecione uma conversa</h4>
                                    <p class="text-muted">Escolha uma conversa da lista ao lado para começar a conversar</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Aba Reuniões -->
            <div class="tab-pane fade" id="meetings" role="tabpanel" wire:ignore.self>
                <div class="row g-4">
                    <!-- Painel Lateral - Próximas Reuniões -->
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-clock text-primary me-2"></i>Próximas Reuniões
                                </h6>
                                <button class="btn btn-outline-primary btn-sm" wire:click="abrirModalReuniao" data-bs-toggle="modal" data-bs-target="#meetingModal">
                                    <i class="fas fa-plus me-1"></i>Nova
                                </button>
                            </div>
                            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                                @if(isset($proximasReunioes) && $proximasReunioes->count() > 0)
                                    @foreach($proximasReunioes as $reuniao)
                                        @php
                                            // Calcular status temporal da reunião
                                            $agora = now();
                                            $dataHoraInicio = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reuniao->data_agendamento->format('Y-m-d') . ' ' . $reuniao->hora_inicio->format('H:i:s'));

                                            // Calcular hora de fim (se não tiver, assumir 2 horas)
                                            if ($reuniao->hora_fim) {
                                                $dataHoraFim = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reuniao->data_agendamento->format('Y-m-d') . ' ' . $reuniao->hora_fim->format('H:i:s'));
                                            } else {
                                                $dataHoraFim = $dataHoraInicio->copy()->addHours(2);
                                            }

                                            // Determinar status temporal
                                            $jaTerminou = $agora->gt($dataHoraFim);
                                            $emAndamento = $agora->gte($dataHoraInicio) && $agora->lte($dataHoraFim);
                                            $proximoDeComeco = $dataHoraInicio->diffInMinutes($agora, false) <= 30 && $dataHoraInicio->diffInMinutes($agora, false) >= 0;

                                            // Definir ícone e cor baseado no status
                                            if ($reuniao->status === 'cancelado') {
                                                $statusIcon = 'times-circle';
                                                $statusColor = 'danger';
                                                $statusText = 'Cancelado';
                                            } elseif ($reuniao->status === 'realizado' || $jaTerminou) {
                                                $statusIcon = 'check-circle';
                                                $statusColor = 'success';
                                                $statusText = 'Realizado';
                                            } elseif ($emAndamento) {
                                                $statusIcon = 'play-circle';
                                                $statusColor = 'warning';
                                                $statusText = 'Em andamento';
                                            } elseif ($proximoDeComeco) {
                                                $statusIcon = 'clock';
                                                $statusColor = 'info';
                                                $statusText = 'Iniciando em breve';
                                            } else {
                                                $statusIcon = 'calendar-check';
                                                $statusColor = 'primary';
                                                $statusText = 'Agendado';
                                            }
                                        @endphp

                                        <div class="meeting-item p-3 border-bottom cursor-pointer {{ $reuniaoSelecionada == $reuniao->id ? 'bg-light' : 'hover-bg-light' }}"
                                             wire:click="selecionarReuniao('{{ $reuniao->id }}')">
                                            <div class="d-flex align-items-start">
                                                <div class="meeting-icon me-3 position-relative">
                                                    <!-- Ícone principal (modalidade) -->
                                                    <i class="fas fa-{{ $reuniao->modalidade === 'online' ? 'video' : 'map-marker-alt' }} text-{{ $reuniao->modalidade === 'online' ? 'success' : 'primary' }}" style="font-size: 1.2rem;"></i>

                                                    <!-- Ícone de status sobreposto -->
                                                    <div class="position-absolute" style="top: -8px; right: -8px;">
                                                        <i class="fas fa-{{ $statusIcon }} text-{{ $statusColor }}"
                                                           style="font-size: 0.8rem; background: white; border-radius: 50%; padding: 2px;"
                                                           title="{{ $statusText }}"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold small">{{ Str::limit($reuniao->titulo, 25) }}</div>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-calendar me-1"></i>{{ $reuniao->data_agendamento->format('d/m') }}
                                                        <i class="fas fa-clock ms-2 me-1"></i>{{ $reuniao->hora_inicio->format('H:i') }}

                                                        <!-- Badge de status -->
                                                        <span class="badge bg-{{ $statusColor }} ms-2" style="font-size: 0.65rem;">
                                                            {{ $statusText }}
                                                        </span>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <i class="fas fa-{{ $reuniao->temAlianca() ? 'handshake' : 'church' }} me-1"></i>
                                                        {{ $reuniao->contexto }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 2rem;"></i>
                                        <h6 class="text-muted">Nenhuma reunião próxima</h6>
                                        <p class="text-muted small">Agende sua primeira reunião</p>
                                        <button class="btn btn-primary btn-sm" wire:click="abrirModalReuniao" data-bs-toggle="modal" data-bs-target="#meetingModal">
                                            <i class="fas fa-plus me-1"></i>Agendar Reunião
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Área Principal - Detalhes da Reunião -->
                    <div class="col-md-8 col-lg-9">
                        @if(isset($reuniaoSelecionada) && $reuniaoSelecionada)
                            @php
                                $reuniao = \App\Models\Eventos\Agendamento::with(['organizador', 'responsavel', 'convidado', 'igreja', 'alianca'])->find($reuniaoSelecionada);
                            @endphp
                            @if($reuniao)
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-outline-secondary btn-sm me-3" wire:click="$set('reuniaoSelecionada', null)">
                                                <i class="fas fa-arrow-left me-1"></i>Voltar
                                            </button>
                                            <div>
                                                <h5 class="mb-0">
                                                    <i class="fas fa-calendar-check text-primary me-2"></i>{{ $reuniao->titulo }}
                                                </h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-{{ $reuniao->modalidade === 'online' ? 'video' : 'map-marker-alt' }} me-1"></i>
                                                    {{ ucfirst($reuniao->modalidade) }}
                                                    @if($reuniao->local)
                                                        • {{ $reuniao->local }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        @if($reuniao->organizador_id === Auth::id())
                                            @php
                                                $dataHoraAtual = now();
                                                $dataHoraReuniao = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reuniao->data_agendamento->format('Y-m-d') . ' ' . $reuniao->hora_inicio->format('H:i:s'));
                                                $eventoEmAndamento = $dataHoraAtual->gte($dataHoraReuniao);
                                                $diferencaEmHoras = $dataHoraAtual->diffInHours($dataHoraReuniao, false);
                                                $poucoTempoRestante = $diferencaEmHoras < 1 && $diferencaEmHoras >= 0;
                                                $podeEditar = !$eventoEmAndamento && !$poucoTempoRestante;
                                                $podeCancelar = !$eventoEmAndamento && !$poucoTempoRestante;
                                            @endphp
                                            <div class="btn-group">
                                                @if($reuniao->status !== 'cancelado')
                                                    @if($podeEditar)
                                                        <button class="btn btn-outline-warning btn-sm" wire:click="editarReuniao('{{ $reuniao->id }}')" data-bs-toggle="modal" data-bs-target="#meetingModal">
                                                            <i class="fas fa-edit me-1"></i>Editar
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary btn-sm" disabled title="{{ $eventoEmAndamento ? 'Não é possível editar uma reunião em andamento' : 'Não é possível editar com menos de 1 hora de antecedência' }}">
                                                            <i class="fas fa-edit me-1"></i>Editar
                                                        </button>
                                                    @endif
                                                    @if($podeCancelar)
                                                        <button class="btn btn-outline-danger btn-sm" wire:click="cancelarReuniao('{{ $reuniao->id }}')">
                                                            <i class="fas fa-times me-1"></i>Cancelar
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-secondary btn-sm" disabled title="{{ $eventoEmAndamento ? 'Não é possível cancelar uma reunião em andamento' : 'Não é possível cancelar com menos de 1 hora de antecedência' }}">
                                                            <i class="fas fa-times me-1"></i>Cancelar
                                                        </button>
                                                    @endif
                                                @else
                                                    <button class="btn btn-outline-danger btn-sm" wire:click="excluirReuniao('{{ $reuniao->id }}')">
                                                        <i class="fas fa-trash me-1"></i>Excluir
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <!-- Informações da Reunião -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <div class="meeting-info-item">
                                                    <i class="fas fa-calendar text-primary me-2"></i>
                                                    <strong>Data:</strong> {{ $reuniao->data_agendamento->format('d/m/Y') }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="meeting-info-item">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    <strong>Horário:</strong> {{ $reuniao->hora_inicio->format('H:i') }}
                                                    @if($reuniao->hora_fim)
                                                        às {{ $reuniao->hora_fim->format('H:i') }}
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="meeting-info-item">
                                                    <i class="fas fa-user text-primary me-2"></i>
                                                    <strong>Organizador:</strong> {{ $reuniao->organizador->name ?? 'N/A' }}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="meeting-info-item">
                                                    <i class="fas fa-tag text-primary me-2"></i>
                                                    <strong>Tipo:</strong> {{ ucfirst($reuniao->tipo) }}
                                                </div>
                                            </div>
                                            @if($reuniao->responsavel)
                                                <div class="col-md-6">
                                                    <div class="meeting-info-item">
                                                        <i class="fas fa-user-tie text-primary me-2"></i>
                                                        <strong>Responsável:</strong> {{ $reuniao->responsavel->name }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($reuniao->convidado)
                                                <div class="col-md-6">
                                                    <div class="meeting-info-item">
                                                        <i class="fas fa-user-check text-primary me-2"></i>
                                                        <strong>Convidado:</strong> {{ $reuniao->convidado->name }}
                                                    </div>
                                                </div>
                                            @endif
                                            @if($reuniao->link_reuniao)
                                                <div class="col-12">
                                                    <div class="meeting-info-item">
                                                        <i class="fas fa-link text-primary me-2"></i>
                                                        <strong>Link:</strong>
                                                        <a href="{{ $reuniao->link_reuniao }}" target="_blank" class="text-decoration-none ms-1">
                                                            {{ Str::limit($reuniao->link_reuniao, 50) }}
                                                            <i class="fas fa-external-link-alt ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Descrição -->
                                        @if($reuniao->descricao)
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary mb-2">
                                                    <i class="fas fa-align-left me-2"></i>Descrição
                                                </h6>
                                                <p class="text-muted mb-0">{{ $reuniao->descricao }}</p>
                                            </div>
                                        @endif

                                        <!-- Observações -->
                                        @if($reuniao->observacoes)
                                            <div class="mb-4">
                                                <h6 class="fw-bold text-primary mb-2">
                                                    <i class="fas fa-sticky-note me-2"></i>Observações
                                                </h6>
                                                <p class="text-muted mb-0">{{ $reuniao->observacoes }}</p>
                                            </div>
                                        @endif

                                        <!-- Status -->
                                        <div class="alert alert-{{ match($reuniao->status) {
                                            'agendado' => 'info',
                                            'confirmado' => 'success',
                                            'realizado' => 'secondary',
                                            'cancelado' => 'danger',
                                            default => 'secondary'
                                        } }} d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Status:</strong>
                                            <span class="ms-2">{{ ucfirst($reuniao->status) }}</span>
                                            @if($reuniao->data_confirmacao)
                                                <small class="ms-auto text-muted">
                                                    Confirmado em {{ $reuniao->data_confirmacao->format('d/m/Y H:i') }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card h-100">
                                    <div class="card-body text-center py-5">
                                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3">Reunião não encontrada</h5>
                                        <p class="text-muted">A reunião solicitada não foi encontrada no sistema.</p>
                                        <button class="btn btn-outline-secondary" wire:click="$set('reuniaoSelecionada', null)">
                                            <i class="fas fa-arrow-left me-1"></i>Voltar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @else
                            <!-- Lista Geral de Reuniões -->
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-alt text-primary me-2"></i>Todas as Reuniões
                                    </h5>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm" wire:model.live="filtroReunioes" style="width: auto;">
                                            <option value="todas">Todas</option>
                                            <option value="minhas">Minhas</option>
                                            <option value="hoje">Hoje</option>
                                            <option value="semana">Esta Semana</option>
                                            <option value="mes">Este Mês</option>
                                        </select>
                                        <button class="btn btn-primary btn-sm" wire:click="abrirModalReuniao" data-bs-toggle="modal" data-bs-target="#meetingModal">
                                            <i class="fas fa-plus me-1"></i>Nova Reunião
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                                    @if(isset($todasReunioesData) && count($todasReunioesData) > 0)
                                        @php
                                            $reunioesPorDia = collect($todasReunioesData)->groupBy(function($reuniao) {
                                                return \Carbon\Carbon::parse($reuniao['data_agendamento'])->format('Y-m-d');
                                            });
                                        @endphp
                                        @foreach($reunioesPorDia as $data => $reunioesDoDia)
                                            <div class="meeting-day-group mb-4">
                                                <h6 class="text-primary fw-bold mb-3">
                                                    <i class="fas fa-calendar-day me-2"></i>
                                                    {{ \Carbon\Carbon::parse($data)->locale('pt_BR')->isoFormat('dddd, DD [de] MMMM [de] YYYY') }}
                                                </h6>
                                                @foreach($reunioesDoDia as $reuniao)
                                                    <div class="meeting-card card mb-2 cursor-pointer {{ $reuniaoSelecionada == $reuniao['id'] ? 'border-primary' : '' }}"
                                                         wire:click="selecionarReuniao('{{ $reuniao['id'] }}')">
                                                        <div class="card-body p-3">
                                                            <div class="row align-items-center">
                                                                <div class="col-auto">
                                                                    <div class="meeting-time text-center">
                                                                        <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($reuniao['hora_inicio'])->format('H:i') }}</div>
                                                                        @if($reuniao['hora_fim'])
                                                                            <small class="text-muted">{{ \Carbon\Carbon::parse($reuniao['hora_fim'])->format('H:i') }}</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $reuniao['titulo'] }}</h6>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-{{ $reuniao['modalidade'] === 'online' ? 'video' : 'map-marker-alt' }} me-1"></i>
                                                                                {{ ucfirst($reuniao['modalidade']) }}
                                                                                @if($reuniao['local'])
                                                                                    • {{ Str::limit($reuniao['local'], 30) }}
                                                                                @endif
                                                                            </small>
                                                                            <br>
                                                                            <small class="text-muted">
                                                                                <i class="fas fa-{{ isset($reuniao['alianca']) ? 'handshake' : 'church' }} me-1"></i>
                                                                                @if(isset($reuniao['alianca']))
                                                                                    Aliança: {{ $reuniao['alianca']['nome'] }}
                                                                                @elseif(isset($reuniao['igreja']))
                                                                                    Igreja: {{ $reuniao['igreja']['nome'] }}
                                                                                @else
                                                                                    Geral
                                                                                @endif
                                                                            </small>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <span class="badge bg-{{ match($reuniao['status']) {
                                                                                'agendado' => 'info',
                                                                                'confirmado' => 'success',
                                                                                'realizado' => 'secondary',
                                                                                'cancelado' => 'danger',
                                                                                default => 'secondary'
                                                                            } }}">
                                                                                {{ ucfirst($reuniao['status']) }}
                                                                            </span>
                                                                            @if($reuniao['organizador_id'] === Auth::id())
                                                                                <div class="mt-1">
                                                                                    <small class="text-muted">
                                                                                        <i class="fas fa-crown text-warning me-1"></i>Organizador
                                                                                    </small>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted">Nenhuma reunião encontrada</h5>
                                            <p class="text-muted">Não há reuniões agendadas com os filtros atuais.</p>
                                            <button class="btn btn-primary" wire:click="abrirModalReuniao" data-bs-toggle="modal" data-bs-target="#meetingModal">
                                                <i class="fas fa-plus me-1"></i>Agendar Primeira Reunião
                                            </button>
                                        </div>
                                    @endif

                                    {{-- Paginação Simples --}}
                                    @if($totalReunioes > $perPage)
                                        <div class="mt-3 d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Mostrando {{ (($currentPage - 1) * $perPage) + 1 }}-{{ min($currentPage * $perPage, $totalReunioes) }} de {{ $totalReunioes }} reuniões
                                            </small>
                                            <nav aria-label="Navegação de reuniões">
                                                <ul class="pagination pagination-sm mb-0">
                                                    <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                                                        <a class="page-link" href="#" wire:click.prevent="previousPage" aria-label="Anterior">
                                                            <span aria-hidden="true">&laquo;</span>
                                                        </a>
                                                    </li>

                                                    @php
                                                        $totalPages = ceil($totalReunioes / $perPage);
                                                        $startPage = max(1, $currentPage - 2);
                                                        $endPage = min($totalPages, $currentPage + 2);
                                                    @endphp

                                                    @for($i = $startPage; $i <= $endPage; $i++)
                                                        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                                            <a class="page-link" href="#" wire:click.prevent="goToPage({{ $i }})">{{ $i }}</a>
                                                        </li>
                                                    @endfor

                                                    <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                                                        <a class="page-link" href="#" wire:click.prevent="nextPage" aria-label="Próxima">
                                                            <span aria-hidden="true">&raquo;</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>



        {{-- Modal para Iniciar Nova Conversa --}}
        <div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true"
            data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title fw-bold" id="newConversationModalLabel">
                            <i class="fas fa-plus-circle text-white me-2"></i>Iniciar Nova Conversa Privada
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-envelope text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="fw-bold text-center mb-3">Selecione um membro para iniciar a conversa</h6>
                        <p class="text-muted mb-4">
                            Escolha qualquer membro da aliança ou da sua igreja para enviar uma mensagem privada.
                            Todos os membros ativos aparecem nesta lista.
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user-friends text-primary me-1"></i>Selecione um membro
                            </label>
                            <select class="form-select @error('usuarioSelecionado') is-invalid @enderror"
                                    wire:model.live="usuarioSelecionado">
                                <option value="">Escolher membro...</option>
                                @if(isset($usuariosDisponiveis) && !empty($usuariosDisponiveis))
                                    @foreach($usuariosDisponiveis as $usuario)
                                        <option value="{{ $usuario['user_id'] }}">
                                            {{ $usuario['user']['name'] }} - {{ $usuario['igreja']['nome'] }}
                                            @if($usuario['cargo'] === 'admin')
                                                (Administrador)
                                            @elseif($usuario['cargo'] === 'pastor')
                                                (Pastor)
                                            @elseif($usuario['cargo'] === 'ministro')
                                                (Ministro)
                                            @elseif($usuario['cargo'] === 'obreiro')
                                                (Obreiro)
                                            @elseif($usuario['cargo'] === 'diacono')
                                                (Diácono)
                                            @else
                                                (Membro)
                                            @endif
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>Nenhum membro disponível</option>
                                @endif
                            </select>
                            @error('usuarioSelecionado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Você pode conversar com qualquer membro da aliança atual ou da sua própria igreja.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-primary"
                                wire:click="iniciarConversa2"
                                wire:loading.attr="disabled"
                                data-bs-dismiss="modal">
                            <span wire:loading.remove wire:target="iniciarConversa2">
                                <i class="fas fa-paper-plane me-1"></i>Iniciar Conversa
                            </span>
                            <span wire:loading wire:target="iniciarConversa2">
                                <i class="fas fa-spinner fa-spin me-1"></i>Iniciando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de Reunião --}}
        @include('church.alliance.modals.meeting-modal')

        {{-- Modal de Cancelamento de Reunião --}}
        @include('church.alliance.modals.cancel-meeting-modal')

        {{-- Modal para Visualizar Imagens --}}
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalImage" src="" alt="" class="img-fluid rounded" style="max-height: 70vh;">
                        <p id="imageCaption" class="mt-2 text-muted small"></p>
                    </div>
                    <div class="modal-footer">
                        <a id="downloadImageBtn" href="" download class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de Confirmação de Delete (Bootstrap) --}}
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
                                    onclick="if(window.mensagemParaDeletarAlianca) { executarDeletarMensagemAlianca(); } else { executarDeletarMensagem(); }"
                                    data-bs-dismiss="modal"
                                    style="min-width: 80px;">
                                Deletar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Script para Gravação de Áudio --}}
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

        // Função para enviar áudio gravado (privado)
        function enviarAudioGravado(audioBlob, fileName) {
            if (window.livewireComponent) {
                const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (componentId) {
                    window.livewireComponent.find(componentId).call('receberAudioGravado', audioBlob, fileName);
                }
            } else {
                console.error('Componente Livewire não encontrado');
            }
        }

        // Função para enviar áudio gravado (aliança)
        function enviarAudioGravadoAlianca(audioBlob, fileName) {
            // Usar sintaxe correta do Livewire 3
            if (window.livewireComponent) {
                const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                if (componentId) {
                    window.livewireComponent.find(componentId).call('receberAudioGravadoAlianca', audioBlob, fileName);
                }
            }
        }

        // Função para lidar com o clique no botão enviar (privado)
        function handleSendButtonPrivate() {
            // Verificar se há gravação em andamento
            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                // Parar gravação - o áudio será enviado automaticamente no onstop
                stopAudioRecordingPrivate();
                return;
            }

            // Se não há gravação em andamento, verificar se há mensagem ou anexos
            const messageInput = document.querySelector('input[wire\\:model="novaMensagemPrivada2"]');
            const hasMessage = messageInput && messageInput.value.trim() !== '';
            const hasAudioFile = document.querySelector('input[wire\\:model="arquivoAudio"]')?.files.length > 0;
            const hasAttachment = document.querySelector('input[wire\\:model="arquivoAnexo"]')?.files.length > 0;

            // Se há conteúdo para enviar, chamar o método Livewire
            if (hasMessage || hasAudioFile || hasAttachment) {
                if (window.livewireComponent) {
                    const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        window.livewireComponent.find(componentId).call('enviarMensagemPrivada2');
                    }
                }
            }
        }

        // Função para lidar com o clique no botão enviar (aliança)
        function handleSendButtonAlliance() {
            // Verificar se há gravação em andamento
            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                // Parar gravação - o áudio será enviado automaticamente no onstop
                stopAudioRecordingAlliance();
                return;
            }

            // Se não há gravação em andamento, verificar se há mensagem ou anexos
            const messageInput = document.querySelector('input[wire\\:model="novaMensagemChat"]');
            const hasMessage = messageInput && messageInput.value.trim() !== '';
            const hasAudioFile = document.querySelector('input[wire\\:model="arquivoAudioAlianca"]')?.files.length > 0;
            const hasAttachment = document.querySelector('input[wire\\:model="arquivoAnexoAlianca"]')?.files.length > 0;

            // Se há conteúdo para enviar, chamar o método Livewire
            if (hasMessage || hasAudioFile || hasAttachment) {
                if (window.livewireComponent) {
                    const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        window.livewireComponent.find(componentId).call('enviarMensagemChat');
                    }
                }
            }
        }

        // Função para alternar gravação de áudio
        async function toggleAudioRecordingPrivate() {
            const btnRecord = document.getElementById('btn-record-audio-private');
            const btnStop = document.getElementById('btn-stop-recording-private');
            const recordIcon = document.getElementById('record-icon-private');
            const recordText = document.getElementById('record-text-private');
            const indicator = document.getElementById('recording-indicator-private');

            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                // Já está gravando, parar
                stopAudioRecordingPrivate();
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
                btnRecord.classList.add('d-none');
                btnRecord.classList.remove('btn-outline-primary');
                recordIcon.classList.remove('fa-microphone');
                recordIcon.classList.add('fa-stop');
                recordText.textContent = 'Gravando...';
                indicator.classList.remove('d-none');

                // Mostrar botão parar
                btnStop.classList.remove('d-none');

                // Iniciar timer
                startRecordingTimerPrivate();

                // Notificação
                showToast('Gravação iniciada', 'success');

            } catch (error) {
                console.error('Erro ao iniciar gravação:', error);
                showToast('Erro ao acessar microfone. Verifique as permissões.', 'error');

                // Resetar UI em caso de erro
                resetRecordingUIPrivate();
            }
        }

        // Função para parar gravação
        function stopAudioRecordingPrivate() {
            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                // Não resetar UI aqui, será feito no onstop
            } else {
                // Se não está gravando, apenas resetar UI
                resetRecordingUIPrivate();
            }
        }

        // Função para resetar UI de gravação
        function resetRecordingUIPrivate() {
            const btnRecord = document.getElementById('btn-record-audio-private');
            const btnStop = document.getElementById('btn-stop-recording-private');
            const recordIcon = document.getElementById('record-icon-private');
            const recordText = document.getElementById('record-text-private');
            const indicator = document.getElementById('recording-indicator-private');

            btnRecord.classList.remove('d-none');
            btnRecord.classList.add('btn-outline-primary');
            recordIcon.classList.add('fa-microphone');
            recordIcon.classList.remove('fa-stop');
            recordText.textContent = 'Gravar';
            indicator.classList.add('d-none');
            btnStop.classList.add('d-none');

            // Resetar timer
            const timerElement = document.getElementById('recording-timer-private');
            if (timerElement) {
                timerElement.textContent = '00:00';
            }
        }

        // Função para iniciar timer de gravação (privado)
        function startRecordingTimerPrivate() {
            recordingTimer = setInterval(() => {
                if (recordingStartTime) {
                    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                    const minutes = Math.floor(elapsed / 60);
                    const seconds = elapsed % 60;

                    const timerElement = document.getElementById('recording-timer-private');
                    if (timerElement) {
                        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    }
                }
            }, 1000);
        }

        // ==========================================
        // SISTEMA DE GRAVAÇÃO DE ÁUDIO - ALIANÇA
        // ==========================================

        // Função para alternar gravação de áudio (aliança)
        async function toggleAudioRecordingAlliance() {
            const btnRecord = document.getElementById('btn-record-audio-alliance');
            const btnStop = document.getElementById('btn-stop-recording-alliance');
            const recordIcon = document.getElementById('record-icon-alliance');
            const recordText = document.getElementById('record-text-alliance');
            const indicator = document.getElementById('recording-indicator-alliance');

            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                // Já está gravando, parar
                stopAudioRecordingAlliance();
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
                            // Enviar para o Livewire usando sintaxe correta do Livewire 3
                            @this.call('receberAudioGravadoAlianca', base64Audio, fileName);
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
                btnRecord.classList.add('d-none');
                btnRecord.classList.remove('btn-outline-primary');
                recordIcon.classList.remove('fa-microphone');
                recordIcon.classList.add('fa-stop');
                recordText.textContent = 'Gravando...';
                indicator.classList.remove('d-none');

                // Mostrar botão parar
                btnStop.classList.remove('d-none');

                // Iniciar timer
                startRecordingTimerAlliance();

                // Notificação
                showToast('Gravação iniciada', 'success');

            } catch (error) {
                console.error('Erro ao iniciar gravação:', error);
                showToast('Erro ao acessar microfone. Verifique as permissões.', 'error');

                // Resetar UI em caso de erro
                resetRecordingUIAlliance();
            }
        }

        // Função para parar gravação (aliança)
        function stopAudioRecordingAlliance() {
            if (typeof mediaRecorder !== 'undefined' && mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                // Não resetar UI aqui, será feito no onstop
            } else {
                // Se não está gravando, apenas resetar UI
                resetRecordingUIAlliance();
            }
        }

        // Função para resetar UI de gravação (aliança)
        function resetRecordingUIAlliance() {
            const btnRecord = document.getElementById('btn-record-audio-alliance');
            const btnStop = document.getElementById('btn-stop-recording-alliance');
            const recordIcon = document.getElementById('record-icon-alliance');
            const recordText = document.getElementById('record-text-alliance');
            const indicator = document.getElementById('recording-indicator-alliance');

            btnRecord.classList.remove('d-none');
            btnRecord.classList.add('btn-outline-primary');
            recordIcon.classList.add('fa-microphone');
            recordIcon.classList.remove('fa-stop');
            recordText.textContent = 'Gravar';
            indicator.classList.add('d-none');
            btnStop.classList.add('d-none');

            // Resetar timer
            const timerElement = document.getElementById('recording-timer-alliance');
            if (timerElement) {
                timerElement.textContent = '00:00';
            }
        }

        // Função para iniciar timer de gravação (aliança)
        function startRecordingTimerAlliance() {
            recordingTimer = setInterval(() => {
                if (recordingStartTime) {
                    const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                    const minutes = Math.floor(elapsed / 60);
                    const seconds = elapsed % 60;

                    const timerElement = document.getElementById('recording-timer-alliance');
                    if (timerElement) {
                        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
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

        // ==========================================
        // MODAL DE CONFIRMAÇÃO DE DELETE (BOOTSTRAP)
        // ==========================================

        // Variável global para armazenar ID da mensagem a ser deletada (SPA-safe)
        window.mensagemParaDeletar = window.mensagemParaDeletar || null;

        // Função para definir mensagem a ser deletada (privada)
        function setMensagemParaDeletar(mensagemId) {
            window.mensagemParaDeletar = mensagemId;
        }

        // Função para definir mensagem da aliança a ser deletada
        function setMensagemParaDeletarAlianca(mensagemId) {
            window.mensagemParaDeletarAlianca = mensagemId;
        }

        // Função para confirmar e executar deleção (privada)
        function executarDeletarMensagem() {
            if (window.mensagemParaDeletar) {
                // Executar deleção via Livewire
                if (window.livewireComponent) {
                    const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        window.livewireComponent.find(componentId).call('deletarMensagem', window.mensagemParaDeletar);
                        console.log('🗑️ Solicitação de deleção (privada) enviada para:', window.mensagemParaDeletar);
                    }
                }

                // Limpar variável
                window.mensagemParaDeletar = null;
            }
        }

        // Função para confirmar e executar deleção (aliança)
        function executarDeletarMensagemAlianca() {
            if (window.mensagemParaDeletarAlianca) {
                // Executar deleção via Livewire
                if (window.livewireComponent) {
                    const componentId = document.querySelector('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        window.livewireComponent.find(componentId).call('deletarMensagemAlianca', window.mensagemParaDeletarAlianca);
                        console.log('🗑️ Solicitação de deleção (aliança) enviada para:', window.mensagemParaDeletarAlianca);
                    }
                }

                // Limpar variável
                window.mensagemParaDeletarAlianca = null;
            }
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
        // MODAL DE IMAGENS
        // ==========================================

        // Função para abrir modal de imagem
        function openImageModal(imageSrc, imageName) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalLabel = document.getElementById('imageModalLabel');
            const downloadBtn = document.getElementById('downloadImageBtn');
            const imageCaption = document.getElementById('imageCaption');

            if (modal && modalImage) {
                modalImage.src = imageSrc;
                modalImage.alt = imageName || 'Imagem';
                modalLabel.textContent = imageName || 'Imagem';
                downloadBtn.href = imageSrc;
                downloadBtn.download = imageName || 'imagem.jpg';
                imageCaption.textContent = imageName || '';

                // Mostrar modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        }

        // Inicializar componente Livewire quando disponível
        document.addEventListener('livewire:init', () => {
            window.livewireComponent = window.Livewire;
        });
        </script>

    </div>

</div>
