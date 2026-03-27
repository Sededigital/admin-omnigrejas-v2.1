        {{-- Modal de Cupom --}}
        <div class="modal fade" id="cupomModal" tabindex="-1" aria-labelledby="cupomModalLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="cupomModalLabel">
                            <i class="fas fa-ticket-alt text-info me-2"></i>
                            <span id="modal-title">{{ $editingCupom ? 'Editar Cupom' : 'Novo Cupom' }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveCupom">

                            <!-- Código e Descrição -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control text-uppercase @error('codigo') is-invalid @enderror"
                                               wire:model="codigo" placeholder="Código do cupom" required>
                                        <label><i class="fas fa-hashtag text-info me-1"></i>Código *</label>
                                        @error('codigo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="generateCodigo">
                                        <i class="fas fa-magic me-1"></i>Gerar Código
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('uso_max') is-invalid @enderror"
                                               wire:model="uso_max" placeholder="1" min="1" required>
                                        <label><i class="fas fa-users text-info me-1"></i>Uso Máximo *</label>
                                        @error('uso_max')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="2"
                                                  placeholder="Descrição do cupom"></textarea>
                                        <label><i class="fas fa-comment text-info me-1"></i>Descrição</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Desconto -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('desconto_percentual') is-invalid @enderror"
                                               wire:model="desconto_percentual" placeholder="0" min="0" max="100">
                                        <label><i class="fas fa-percent text-info me-1"></i>Desconto Percentual (%)</label>
                                        @error('desconto_percentual')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" step="0.01" class="form-control @error('desconto_valor') is-invalid @enderror"
                                               wire:model="desconto_valor" placeholder="0.00" min="0">
                                        <label><i class="fas fa-dollar-sign text-info me-1"></i>Desconto em Valor (Kz)</label>
                                        @error('desconto_valor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Validade -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control date_flatpicker @error('valido_de') is-invalid @enderror"
                                               wire:model="valido_de">
                                        <label><i class="fas fa-calendar-plus text-info me-1"></i>Válido De</label>
                                        @error('valido_de')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control date_flatpicker @error('valido_ate') is-invalid @enderror"
                                               wire:model="valido_ate">
                                        <label><i class="fas fa-calendar-minus text-info me-1"></i>Válido Até</label>
                                        @error('valido_ate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('ativo') is-invalid @enderror"
                                               type="checkbox" wire:model="ativo" id="ativoSwitch">
                                        <label class="form-check-label" for="ativoSwitch">
                                            <i class="fas fa-toggle-on text-info me-1"></i>Cupom Ativo
                                        </label>
                                        @error('ativo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <strong>Status:</strong>
                                        <span class="text-muted">
                                            {{ $editingCupom ? 'Editando Cupom' : 'Novo Cupom' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer do Modal -->
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn bg-info text-light" wire:click="saveCupom" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveCupom">
                                <i class="fas fa-save me-1"></i>{{ $editingCupom ? 'Atualizar Cupom' : 'Salvar Cupom' }}
                            </span>
                            <span wire:loading wire:target="saveCupom">
                                <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingCupom ? 'Atualizando...' : 'Salvando...' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
