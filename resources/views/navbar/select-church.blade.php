<div>
    <div class="d-flex align-items-center mt-1">
        <label class="form-label mb-0 me-2 fw-semibold text-white" style="font-size: 0.875rem;">Igreja:</label>
        <div class="vr text-white opacity-50 me-3" style="height: 20px;"></div>
        <div class="dropdown me-2">
            <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center"
                    type="button"
                    id="churchDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    style="min-width: 350px; font-size: 0.875rem; border-radius: 6px;">
                <i class="fas fa-church me-2 text-primary"></i>
                <span id="selectedChurchText">
                    @if($selectedIgrejaId)
                        @php
                            $selectedChurch = collect($igrejas)->firstWhere('id', $selectedIgrejaId);
                        @endphp
                        {{ $selectedChurch['nome'] ?? 'Igreja Selecionada' }}
                        @if($selectedChurch['sigla'] ?? null)
                            ({{ $selectedChurch['sigla'] }})
                        @endif
                    @else
                        Selecione uma igreja...
                    @endif
                </span>
                <div class="ms-auto" id="churchSpinner" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </button>
            <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="churchDropdown" style="min-width: 320px; max-height: 350px; overflow-y: auto;">
                <li>
                    <a class="dropdown-item text-muted small fw-semibold px-3 py-2" href="#" style="pointer-events: none;">
                        <i class="fas fa-list me-2"></i>Suas Igrejas
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                @foreach($igrejas as $igreja)
                @php
                    $isSelected = (session('igreja_atual.id') ?? null) == $igreja['id'];
                @endphp
                <li>
                    @if($isSelected)
                        <!-- Igreja selecionada - não clicável -->
                        <a class="dropdown-item d-flex align-items-center px-3 py-2 active bg-dark  text-white"
                           href="#"
                           style="cursor: default; pointer-events: none;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $igreja['nome'] }}</div>
                                <div class="small ">
                                    @if($igreja['sigla'])
                                        <span class="badge bg-light text-dark me-1">{{ $igreja['sigla'] }}</span>
                                    @endif
                                    <span class="fw-semibold">{{ $igreja['categoria'] }}</span>
                                    @if($igreja['localizacao'])
                                        <br><i class="fas fa-map-marker-alt me-1"></i>{{ $igreja['localizacao'] }}
                                    @endif
                                </div>
                            </div>
                            <i class="fas fa-check-circle text-light ms-2"></i>
                            <small class="text-light ms-2 opacity-75"></small>
                        </a>
                    @else
                        <!-- Outras igrejas - clicáveis -->
                        <a class="dropdown-item d-flex align-items-center px-3 py-2"
                           href="#"
                           wire:click.prevent="selectChurchFromDropdown({{ $igreja['id'] }})"
                           style="cursor: pointer;">
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ $igreja['nome'] }}</div>
                                <div class="small ">
                                    @if($igreja['sigla'])
                                        <span class="badge bg-secondary me-1">{{ $igreja['sigla'] }}</span>
                                    @endif
                                    <span class="fw-semibold">{{ $igreja['categoria'] }}</span>
                                    @if($igreja['localizacao'])
                                        <br><i class="fas fa-map-marker-alt me-1"></i>{{ $igreja['localizacao'] }}
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endif
                </li>
                @endforeach
                @if(count($igrejas) === 0)
                <li>
                    <a class="dropdown-item text-center text-muted py-3" href="#" style="pointer-events: none;">
                        <i class="fas fa-info-circle mb-2" style="font-size: 1.5rem;"></i>
                        <div>Nenhuma igreja encontrada</div>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
    @error('selectedIgrejaId')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror

    {{-- MODAL DE CÓDIGO DE ACESSO --}}
    <div class="modal fade" id="accessCodeModal" tabindex="-1" aria-labelledby="accessCodeModalLabel" aria-hidden="true"
         data-bs-backdrop="false" data-bs-keyboard="true" wire:ignore.self style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="accessCodeModalLabel">
                        <i class="fas fa-key me-2"></i>Código de Acesso
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <form wire:submit.prevent="validateAccessCode">
                    <div class="modal-body p-3">
                        <div class="text-center mb-3">
                            <i class="fas fa-shield-alt text-primary display-4 mb-2"></i>
                            <h5 class="text-primary mb-2">Acesso Seguro</h5>
                        </div>

                        <div class="alert alert-info py-2 mb-3" role="alert">
                            <strong>Esta igreja possui código de acesso!</strong>
                        </div>

                        <!-- Informações da Igreja -->
                        <div class="border rounded p-3 bg-light mb-3">
                            <div class="row g-2 text-sm">
                                <div class="col-12 fw-semibold">{{ $igrejas[array_search($selectedIgrejaId, array_column($igrejas, 'id'))]['nome'] ?? 'Igreja Selecionada' }}</div>
                                <div class="col-12"><strong>Categoria:</strong> {{ $igrejas[array_search($selectedIgrejaId, array_column($igrejas, 'id'))]['categoria'] ?? 'Geral' }}</div>
                                @if($igrejas[array_search($selectedIgrejaId, array_column($igrejas, 'id'))]['localizacao'] ?? null)
                                <div class="col-12"><strong>Localização:</strong> {{ $igrejas[array_search($selectedIgrejaId, array_column($igrejas, 'id'))]['localizacao'] }}</div>
                                @endif
                            </div>
                        </div>

                        <p class="text-muted text-center small mb-3">
                            Digite o código de acesso para continuar
                        </p>

                        <div class="mb-2">
                            <input
                                type="password"
                                class="form-control form-control-lg text-center @error('accessCode') is-invalid @enderror"
                                wire:model="accessCode"
                                placeholder="Digite o código de acesso"
                                maxlength="20"
                                autocomplete="off"
                                autofocus
                            >
                            @error('accessCode')
                                <div class="invalid-feedback text-center">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Footer do Modal -->
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Fechar">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center" style="min-width: 100px; min-height: 36px;" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-check me-1"></i>Validar
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-1"></i>Validando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            // Mostrar spinner quando uma igreja é selecionada
            Livewire.on('show-spinner', () => {
                const spinner = document.getElementById('churchSpinner');
                if (spinner) {
                    spinner.style.display = 'block';
                }
            });

            // Esconder spinner
            Livewire.on('hide-spinner', () => {
                const spinner = document.getElementById('churchSpinner');
                if (spinner) {
                    spinner.style.display = 'none';
                }
            });

            Livewire.on('open-access-code-modal', () => {
                console.log('[Modal] Opening access code modal');
                // Esconder spinner quando modal abre
                const spinner = document.getElementById('churchSpinner');
                if (spinner) {
                    spinner.style.display = 'none';
                }

                // Pequeno delay para garantir que o modal esteja no DOM
                setTimeout(() => {
                    const modalElement = document.getElementById('accessCodeModal');
                    console.log('[Modal] Modal element found:', modalElement);
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement, {
                            backdrop: false,
                            keyboard: true,
                            focus: true
                        });
                        modal.show();
                        console.log('[Modal] Modal shown successfully');

                        // Focar no input após o modal estar totalmente renderizado
                        setTimeout(() => {
                            const input = modalElement.querySelector('input[type="password"]');
                            console.log('[Modal] Input element found:', input);
                            if (input) {
                                input.focus();
                                input.select(); // Seleciona todo o texto se houver
                                console.log('[Modal] Input focused and selected');
                            } else {
                                console.error('[Modal] Password input not found in modal');
                            }
                        }, 300);
                    } else {
                        console.error('[Modal] Modal element not found');
                    }
                }, 100);
            });
        });
    </script>
</div>
