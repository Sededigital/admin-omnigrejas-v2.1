<!-- Modal de Confirmação de Exclusão de Post -->
<div class="modal fade" id="deletePostModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                @if($postSelecionado)
                    <div class="mb-3">
                        <i class="fas fa-trash-alt text-danger mb-3" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold">Excluir Post</h5>
                        <p class="text-muted mb-2">Tem certeza que deseja excluir este post?</p>
                    </div>

                    <!-- Preview do post -->
                    <div class="card border-warning mb-3">
                        <div class="card-body p-2">
                            <h6 class="card-title mb-1 text-truncate">
                                {{ Str::limit($postSelecionado->titulo, 30) }}
                            </h6>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>{{ $postSelecionado->autor->name }}
                            </small>
                        </div>
                    </div>

                    <!-- Alerta sobre mídia -->
                    @if($postSelecionado->media_url)
                        <div class="alert alert-warning py-2 mb-0">
                            <small>
                                <i class="fas fa-exclamation-circle me-1"></i>
                                A mídia associada também será removida permanentemente.
                            </small>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-question-circle text-warning mb-3" style="font-size: 3rem;"></i>
                        <h5>Post não encontrado</h5>
                        <p class="text-muted">Não foi possível carregar as informações do post.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                @if($postSelecionado)
                    <button type="button" class="btn btn-danger" wire:click="confirmarExclusaoPost" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-trash me-1"></i>Excluir
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Excluindo...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-delete-post-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('deletePostModal'));
        modal.show();
    });

    Livewire.on('close-delete-post-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deletePostModal'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>