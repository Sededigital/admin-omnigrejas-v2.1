<!-- Modal de Permissão -->
<div class="modal fade" id="permissaoModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key text-primary me-2"></i>
                    {{ $isEditingPermissao ? 'Editar Permissão' : 'Nova Permissão' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit="salvarPermissao">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nome da Permissão <span class="text-danger">*</span></label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('permissaoNome') is-invalid @enderror"
                                   wire:model="permissaoNome" placeholder="Ex: Gerenciar Membros">
                            @error('permissaoNome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nível <span class="text-danger">*</span></label>
                            <select class="form-select @error('permissaoNivel') is-invalid @enderror" wire:model="permissaoNivel">
                                <option value="baixo">Baixo</option>
                                <option value="medio" selected>Médio</option>
                                <option value="alto">Alto</option>
                                <option value="critico">Crítico</option>
                            </select>
                            @error('permissaoNivel')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Código Único <span class="text-danger">*</span></label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('permissaoCodigo') is-invalid @enderror"
                                   wire:model="permissaoCodigo" placeholder="Ex: gerenciar_membros"
                                   {{ $isEditingPermissao ? 'readonly' : '' }}>
                            <small class="text-muted">Código único para identificação (não pode ser alterado após criação)</small>
                            @error('permissaoCodigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Categoria <span class="text-danger">*</span></label>
                            <select class="form-select @error('permissaoCategoria') is-invalid @enderror" wire:model="permissaoCategoria">
                                <option value="">Selecione uma categoria</option>
                                <option value="admin">Administração</option>
                                <option value="visualizacao">Visualização</option>
                                <option value="edicao">Edição</option>
                            </select>
                            @error('permissaoCategoria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('permissaoDescricao') is-invalid @enderror"
                                      wire:model="permissaoDescricao" rows="3"
                                      placeholder="Descreva o que esta permissão permite fazer..."></textarea>
                            @error('permissaoDescricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="permissaoAtiva" id="permissaoAtiva">
                                <label class="form-check-label fw-semibold" for="permissaoAtiva">
                                    Permissão Ativa
                                </label>
                                <br><small class="text-muted">Permissões inativas não podem ser atribuídas a funções</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-1"></i>{{ $isEditingPermissao ? 'Atualizar' : 'Criar' }} Permissão
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

