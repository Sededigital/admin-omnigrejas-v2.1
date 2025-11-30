{{-- Modal de Confirmação Universal --}}
@if($showConfirmModal ?? false)
<div class="modal fade show" id="confirmModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @if($confirmButtonClass === 'btn-danger')
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                    @elseif($confirmButtonClass === 'btn-success')
                        <i class="fas fa-check-circle text-success me-2"></i>
                    @elseif($confirmButtonClass === 'btn-info')
                        <i class="fas fa-info-circle text-info me-2"></i>
                    @else
                        <i class="fas fa-question-circle text-primary me-2"></i>
                    @endif
                    {{ $confirmTitle }}
                </h5>
                <button type="button" class="btn-close" wire:click="cancelarAcao" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">{{ $confirmMessage }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="cancelarAcao">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn {{ $confirmButtonClass }}" wire:click="executarAcaoConfirmada">
                    <i class="fas fa-check me-1"></i>
                    {{ $confirmButtonText }}
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Toast Notifications Melhorado --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-info-circle text-primary me-2"></i>
            <strong class="me-auto">Omnigrejas</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toast-message">
            <!-- Mensagem será inserida aqui -->
        </div>
    </div>
</div>

<script>
// Listener para toast notifications
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (event) => {
        const data = event.detail ? event.detail[0] : event;
        showToast(data.message, data.type);
    });
});

function showToast(message, type = 'info') {
    const toast = document.getElementById('liveToast');
    if (!toast) return;
    
    const toastBody = document.getElementById('toast-message');
    const toastHeader = toast.querySelector('.toast-header i');
    
    // Define ícone e cor baseado no tipo
    const typeConfig = {
        'success': { icon: 'fas fa-check-circle text-success' },
        'error': { icon: 'fas fa-exclamation-circle text-danger' },
        'warning': { icon: 'fas fa-exclamation-triangle text-warning' },
        'info': { icon: 'fas fa-info-circle text-primary' }
    };
    
    const config = typeConfig[type] || typeConfig['info'];
    toastHeader.className = config.icon + ' me-2';
    toastBody.textContent = message;
    
    // Mostra o toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>