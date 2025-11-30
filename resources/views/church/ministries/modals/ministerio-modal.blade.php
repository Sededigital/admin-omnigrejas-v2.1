<!-- Modal de Ministério -->
<div class="modal fade" id="ministerioModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-church text-success me-2"></i>
                    {{ $isEditingMinisterio ? 'Editar Ministério' : 'Novo Ministério' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form wire:submit="salvarMinisterio">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nome do Ministério <span class="text-danger">*</span></label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('ministerioNome') is-invalid @enderror"
                                   wire:model="ministerioNome" placeholder="Ex: Ministério de Louvor">
                            @error('ministerioNome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control @error('ministerioDescricao') is-invalid @enderror"
                                      wire:model="ministerioDescricao" rows="3"
                                      placeholder="Descreva as atividades e objetivos deste ministério..."></textarea>
                            @error('ministerioDescricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model="ministerioAtivo" id="ministerioAtivo">
                                <label class="form-check-label fw-semibold" for="ministerioAtivo">
                                    Ministério Ativo
                                </label>
                                <br><small class="text-muted">Ministérios inativos não aparecem nas listagens</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-save me-1"></i>{{ $isEditingMinisterio ? 'Atualizar' : 'Criar' }} Ministério
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

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-ministerio-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('ministerioModal'));
        modal.show();
    });

    Livewire.on('close-ministerio-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('ministerioModal'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>
