 {{-- Modal de Assinatura --}}
    <div class="modal fade" id="assinaturaModal" tabindex="-1" aria-labelledby="assinaturaModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="assinaturaModalLabel">
                        <i class="fas fa-file-signature text-info me-2"></i>
                        <span id="modal-title">{{ $editingAssinatura ? 'Editar Assinatura' : 'Nova Assinatura' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveAssinatura">

                        <!-- Seleção da Igreja -->
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="input-group mb-2">
                                    <div class="form-floating flex-grow-1">
                                        <input type="text"  autocomplete="new-password"
                                               class="form-control @error('igreja_id') is-invalid @enderror"
                                               id="igreja_search"
                                               placeholder="Digite para pesquisar igreja..."
                                               autocomplete="off"
                                               list="igrejas_list"
                                               wire:model.live="igreja_nome"
                                               wire:loading.attr="readonly">
                                        <label><i class="fas fa-church text-info me-1"></i>Igreja *</label>
                                        <datalist id="igrejas_list">
                                            @foreach($igrejas as $igreja)
                                                <option value="{{ $igreja->nome }} ({{ $igreja->nif }})" data-id="{{ $igreja->id }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <span class="input-group-text border-0"
                                          style="min-width: 50px; justify-content: center;">
                                        <span class="spinner-border spinner-border-sm"
                                              wire:loading.class="text-info"
                                              wire:loading wire:target="igreja_nome"
                                              role="status"
                                              aria-hidden="true"></span>
                                    </span>
                                </div>
                                {{-- Campo hidden para armazenar o ID --}}
                                <input type="hidden" wire:model="igreja_id">
                                @error('igreja_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                             <!-- Pacote e Status -->
                             <div class="col-md-8">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <select class="form-select @error('pacote_id') is-invalid @enderror"
                                                 wire:model.live="pacote_id"
                                                 wire:key="pacote-select-{{ $igreja_id }}-{{ $vitalicio ? 'vitalicio' : 'normal' }}-{{ time() }}"
                                                 wire:loading.attr="disabled"
                                                 {{ $igreja_id ? '' : 'disabled' }}>
                                             <option value="">
                                                 @if($igreja_id)
                                                     @if($pacotes && $pacotes->count() > 0)
                                                         Selecione um pacote disponível
                                                     @else
                                                         Nenhum pacote disponível para esta igreja
                                                     @endif
                                                 @else
                                                     Selecione uma igreja primeiro
                                                 @endif
                                             </option>
                                             @if($pacotes && $pacotes->count() > 0)
                                                 @foreach($pacotes as $pacote)
                                                     <option value="{{ $pacote->id }}" {{ $pacote->id == $pacote_id ? 'selected' : '' }}>
                                                         {{ $pacote->nome }} -
                                                         @if($vitalicio && $pacote->preco_vitalicio)
                                                             {{ number_format($pacote->preco_vitalicio, 2, ',', '.') }} Kz (Vitalício)
                                                         @else
                                                             {{ number_format($pacote->preco, 2, ',', '.') }} Kz
                                                         @endif
                                                     </option>
                                                 @endforeach
                                             @endif
                                         </select>
                                         <label><i class="fas fa-box text-info me-1"></i>Pacote *</label>
                                     </div>
                                     <span class="input-group-text border-0"
                                           style="min-width: 50px; justify-content: center;">
                                         <span class="spinner-border spinner-border-sm"
                                               wire:loading.class="text-info"
                                               wire:loading wire:target="pacote_id"
                                               role="status"
                                               aria-hidden="true"></span>
                                     </span>
                                 </div>
                                 @error('pacote_id')
                                     <div class="invalid-feedback d-block">{{ $message }}</div>
                                 @enderror
                             </div>

                             <div class="col-md-4">
                                 <div class="form-floating mb-2">
                                     <select class="form-select @error('status') is-invalid @enderror"
                                             wire:model="status">
                                         <option value="Ativo">Ativo</option>
                                         <option value="Cancelado">Cancelado</option>
                                         <option value="Expirado">Expirado</option>
                                     </select>
                                     <label><i class="fas fa-toggle-on text-info me-1"></i>Status *</label>
                                     @error('status')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>

                             <!-- Datas em uma linha -->
                             <div class="col-md-4">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <input type="text"  autocomplete="new-password"
                                                class="form-control date_flatpicker @error('data_inicio') is-invalid @enderror"
                                                wire:model.defer="data_inicio"
                                                placeholder="dd/mm/aaaa"
                                                autocomplete="off"
                                                readonly
                                                style="cursor: pointer;">
                                         <label><i class="fas fa-calendar-plus text-info me-1"></i>Data Início *</label>
                                     </div>
                                     <button type="button"
                                             class="btn btn-sm border-0"
                                             wire:click="clearDataInicio"
                                             title="Limpar Data Início">
                                         <i class="fas fa-times text-info"></i>
                                     </button>
                                 </div>
                                 @error('data_inicio')
                                     <div class="invalid-feedback d-block">{{ $message }}</div>
                                 @enderror
                             </div>

                             @if(!$vitalicio)
                             <div class="col-md-4">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <input type="text"  autocomplete="new-password"
                                                class="form-control date_flatpicker @error('data_fim') is-invalid @enderror"
                                                wire:model.defer="data_fim"
                                                placeholder="dd/mm/aaaa"
                                                autocomplete="off"
                                                readonly
                                                style="cursor: pointer;">
                                         <label><i class="fas fa-calendar-minus text-info me-1"></i>Data Fim *</label>
                                     </div>
                                     <button type="button"
                                             class="btn  btn-sm border-0"
                                             wire:click="clearDataFim"
                                             title="Limpar Data Fim">
                                         <i class="fas fa-times text-info"></i>
                                     </button>
                                 </div>
                                 @error('data_fim')
                                     <div class="invalid-feedback d-block">{{ $message }}</div>
                                 @enderror
                             </div>
                             @else
                             <div class="col-md-4">
                                 <div class="alert alert-info mb-2">
                                     <i class="fas fa-infinity me-2"></i>
                                     <small>Assinatura vitalícia - sem data de fim</small>
                                 </div>
                             </div>
                             @endif

                             <div class="col-md-4">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <input type="text"  autocomplete="new-password"
                                                class="form-control date_flatpicker @error('trial_fim') is-invalid @enderror"
                                                wire:model.live="trial_fim"
                                                placeholder="dd/mm/aaaa"
                                                autocomplete="off"
                                                readonly
                                                style="cursor: pointer;">
                                         <label><i class="fas fa-clock text-info me-1"></i>Trial Fim</label>
                                     </div>
                                     <button type="button"
                                             class="btn  btn-sm border-0"
                                             wire:click="clearTrialFim"
                                             title="Limpar Trial Fim">
                                         <i class="fas fa-times text-info"></i>
                                     </button>
                                 </div>
                                 @error('trial_fim')
                                     <div class="invalid-feedback d-block">{{ $message }}</div>
                                 @enderror
                             </div>

                             <!-- Duração e Vitalício -->
                             <div class="col-md-6">
                                 <div class="form-floating mb-2">
                                     <input type="number" class="form-control @error('duracao_meses_custom') is-invalid @enderror"
                                            wire:model="duracao_meses_custom" placeholder="Calculado automaticamente" min="0" readonly>
                                     <label><i class="fas fa-calendar-alt text-info me-1"></i>Duração (meses)</label>
                                     @error('duracao_meses_custom')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>

                             <div class="col-md-6 d-flex align-items-center">
                                 <div class="form-check mb-2">
                                     <input class="form-check-input @error('vitalicio') is-invalid @enderror"
                                            type="checkbox" wire:model.live="vitalicio" id="vitalicio"
                                            style="width: 1.2rem; height: 1.2rem; margin-right: 0.5rem;">
                                     <label class="form-check-label fw-bold" for="vitalicio">
                                         <i class="fas fa-infinity text-info me-2"></i>Assinatura Vitalícia
                                     </label>
                                     @error('vitalicio')
                                         <div class="invalid-feedback">{{ $message }}</div>
                                     @enderror
                                 </div>
                             </div>

                             <!-- Mensagem explicativa compacta -->
                             @if(!$igreja_id)
                                 <div class="col-12">
                                     <small class="text-muted">
                                         <i class="fas fa-info-circle me-1"></i>
                                         Selecione uma igreja primeiro
                                     </small>
                                 </div>
                             @endif
                         </div>
                     </form>
                 </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn bg-info text-light" wire:click="saveAssinatura" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAssinatura">
                            <i class="fas fa-save me-1"></i>{{ $editingAssinatura ? 'Atualizar Assinatura' : 'Salvar Assinatura' }}
                        </span>
                        <span wire:loading wire:target="saveAssinatura">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingAssinatura ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>
