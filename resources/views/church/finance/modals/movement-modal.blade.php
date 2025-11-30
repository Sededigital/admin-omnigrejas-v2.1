<!-- Movement Modal -->
<div class="modal fade" id="movementModal" tabindex="-1" aria-labelledby="movementModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movementModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>{{ $editingMovement ? 'Editar Movimento' : 'Novo Movimento' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="saveMovement">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Movimento *</label>
                            <select class="form-select" wire:model="tipo" required>
                                <option value="">Selecione</option>
                                <option value="entrada">Entrada</option>
                                <option value="saida">Saída</option>
                            </select>
                            @error('tipo') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data do Movimento *</label>
                            <input type="text"  autocomplete="new-password"
                                   class="form-control date_flatpicker @error('movement_data_transacao') is-invalid @enderror"
                                   wire:model.defer="data_transacao"
                                   placeholder="dd/mm/aaaa"
                                   data-min-date="2010-01-01"
                                   data-max-date="today"
                                   autocomplete="off"
                                   readonly
                                   style="border: 2px solid #007bff; border-radius: 0.375rem; cursor: pointer;">
                            @error('data_transacao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Valor *</label>
                            <div class="input-group">
                                <span class="input-group-text">AOA</span>
                                <input type="number" step="0.01" class="form-control" wire:model="valor" required placeholder="0,00">
                            </div>
                            @error('valor') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Conta *</label>
                            <select class="form-select" wire:model="conta_id" required>
                                <option value="">Selecione uma conta</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->banco }} - {{ $account->numero_conta }}</option>
                                @endforeach
                            </select>
                            @error('conta_id') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoria</label>
                            <select class="form-select" wire:model="categoria_id">
                                <option value="">Selecione uma categoria</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Forma de Pagamento</label>
                            <select class="form-select" wire:model="metodo_pagamento">
                                <option value="">Selecione</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="cartao_credito">Cartão de Crédito</option>
                                <option value="cartao_debito">Cartão de Débito</option>
                                <option value="transferencia">Transferência</option>
                                <option value="cheque">Cheque</option>
                                <option value="pix">PIX</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição *</label>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model="descricao" required placeholder="Descrição do movimento">
                            @error('descricao') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações</label>
                            <textarea class="form-control" rows="3" wire:model="observacoes" placeholder="Observações adicionais..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Estilos para Flatpickr - Forçar sempre desktop -->
                    <style>
                    /* Forçar Flatpickr sempre visível */
                    .flatpickr-calendar {
                        z-index: 10000 !important;
                        display: none;
                    }
                    
                    .flatpickr-calendar.open {
                        display: block !important;
                    }
                    
                    /* Overlay para mobile */
                    .flatpickr-overlay {
                        position: fixed !important;
                        top: 0 !important;
                        left: 0 !important;
                        width: 100% !important;
                        height: 100% !important;
                        background: rgba(0,0,0,0.5) !important;
                        z-index: 9999 !important;
                    }
                    
                    /* Responsivo para telas pequenas */
                    @media (max-width: 768px) {
                        .flatpickr-calendar {
                            position: fixed !important;
                            top: 50% !important;
                            left: 50% !important;
                            transform: translate(-50%, -50%) !important;
                            width: 320px !important;
                            max-width: 90vw !important;
                        }
                    
                        .flatpickr-calendar .flatpickr-month {
                            height: 40px !important;
                        }
                    
                        .flatpickr-calendar .flatpickr-day {
                            height: 35px !important;
                            line-height: 35px !important;
                        }
                    }
                    
                    /* Melhorar inputs de data */
                    .date_flatpicker {
                        cursor: pointer !important;
                        background-color: white !important;
                    }
                    
                    .date_flatpicker:focus {
                        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
                    }
                    </style>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" wire:click="saveMovement" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-save me-2"></i>Salvar</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Salvando...</span>
                </button>
            </div>
        </div>
    </div>
</div>
