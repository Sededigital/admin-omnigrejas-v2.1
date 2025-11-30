<!-- Modal para Cadastro/Edição de Produto -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="productModalLabel">
                    <i class="fas fa-box text-primary me-2"></i>
                    <span>{{ $isEditing ? 'Editar Produto' : 'Cadastrar Novo Produto' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarProduct">

                    <!-- Aba: Informações do Produto -->
                    <div class="row g-3">
                        <!-- Nome -->
                        <div class="col-md-8">
                            <div class="form-floating mb-3">
                                <input type="text"  autocomplete="new-password" class="form-control @error('nome') is-invalid @enderror"
                                       wire:model="nome" placeholder="Nome do produto">
                                <label><i class="fas fa-tag text-primary me-1"></i>Nome *</label>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preço -->
                        <div class="col-md-4">
                            <div class="form-floating mb-3">
                                <input type="number" step="0.01" min="0" class="form-control @error('preco') is-invalid @enderror"
                                       wire:model="preco" placeholder="0.00">
                                <label><i class="fas fa-dollar-sign text-primary me-1"></i>Preço (AOA) *</label>
                                @error('preco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <textarea class="form-control @error('descricao') is-invalid @enderror"
                                          wire:model="descricao" rows="3" placeholder="Descrição detalhada do produto"></textarea>
                                <label><i class="fas fa-align-left text-primary me-1"></i>Descrição</label>
                                @error('descricao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Estoque -->
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" min="0" class="form-control @error('estoque') is-invalid @enderror"
                                       wire:model="estoque" placeholder="0">
                                <label><i class="fas fa-warehouse text-primary me-1"></i>Estoque *</label>
                                @error('estoque')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="ativo" id="ativo-sim" value="1" wire:model="ativo">
                                            <label class="form-check-label" for="ativo-sim">
                                                <i class="fas fa-check-circle text-success me-1"></i>Ativo
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="ativo" id="ativo-nao" value="0" wire:model="ativo">
                                            <label class="form-check-label" for="ativo-nao">
                                                <i class="fas fa-times-circle text-danger me-1"></i>Inativo
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('ativo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Visual -->
                        <div class="col-12">
                            <div class="alert alert-light border">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <strong>Status:</strong>
                                <span class="text-muted">
                                    {{ $isEditing ? 'Editando Produto' : 'Novo Produto' }}
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
                <button type="button" class="btn btn-primary" wire:click="salvarProduct" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarProduct">
                        <i class="fas fa-save me-1"></i>{{ $isEditing ? 'Atualizar Produto' : 'Salvar Produto' }}
                    </span>
                    <span wire:loading wire:target="salvarProduct">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $isEditing ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
