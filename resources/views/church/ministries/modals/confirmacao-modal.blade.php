<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmacaoModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem certeza que deseja excluir este ministério? Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary btn-sm w-100" wire:click="cancelarExclusao">
                            <i class="fas fa-times me-1"></i>Não
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger btn-sm w-100" wire:click="executarExclusao">
                            <i class="fas fa-trash me-1"></i>Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('confirmarExclusao', (ministerioId) => {
        const modal = new bootstrap.Modal(document.getElementById('confirmacaoModal'));
        modal.show();
    });
});
</script>
