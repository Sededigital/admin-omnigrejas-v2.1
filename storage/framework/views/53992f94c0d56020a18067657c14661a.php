<!-- Delete Member Modal -->
<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-labelledby="deleteMemberModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title" id="deleteMemberModalLabel">
                    <i class="fas fa-exclamation-triangle me-1"></i>Confirmar Exclusão
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body py-3" style="min-height: 200px;">
                <!--[if BLOCK]><![endif]--><?php if($memberToDelete): ?>
                <div class="text-center mb-3">
                    <div class="user-avatar bg-primary text-white mx-auto mb-2" style="width: 50px; height: 50px; font-size: 20px;">
                        <?php echo e(strtoupper(substr($memberToDelete->user->name ?? 'N', 0, 2))); ?>

                    </div>
                    <h6 class="mb-1 fw-bold"><?php echo e($memberToDelete->user->name ?? 'N/A'); ?></h6>
                    <p class="text-muted small mb-2"><?php echo e($memberToDelete->user->email ?? 'N/A'); ?></p>
                </div>

                <div class="alert alert-warning py-2 mb-3">
                    <small><i class="fas fa-exclamation-triangle me-1"></i><strong>Atenção!</strong> Esta ação não pode ser desfeita.</small>
                </div>

                <form id="deleteMemberForm" wire:submit.prevent="confirmDeleteMember">
                    <div class="mb-3">
                        <label for="deletePassword" class="form-label small fw-semibold">
                            <i class="fas fa-lock me-1"></i>Senha para confirmar:
                        </label>
                        <input type="password" autocomplete="new-password"  
                               class="form-control form-control-sm <?php $__errorArgs = ['deletePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               id="deletePassword"
                               wire:model="deletePassword"
                               placeholder="Digite sua senha"
                               autocomplete="current-password">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['deletePassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback small"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if($deleteError): ?>
                            <div class="invalid-feedback small d-block"><?php echo e($deleteError); ?></div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </form>

                <div class="modal-footer border-top py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit"
                            form="deleteMemberForm"
                            class="btn btn-sm btn-danger rounded-pill px-3 btn-confirm-delete"
                            wire:loading.attr="disabled"
                            wire:loading.class="btn-loading">
                        <span wire:loading.remove wire:target="confirmDeleteMember">
                            <i class="fas fa-trash me-1"></i>Remover
                        </span>
                        <span wire:loading wire:target="confirmDeleteMember">
                            <i class="fas fa-spinner fa-spin me-1"></i>Processando...
                        </span>
                    </button>
                </div>
                <?php else: ?>
                <div class="text-center py-3 d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                    <i class="fas fa-spinner fa-spin text-primary mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted small">Carregando dados do membro...</p>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
    
</div>
               
<script>
document.addEventListener('livewire:navigated', function () {
    // Re-inicializar listeners após navegação SPA
    initDeleteModalListeners();
});

document.addEventListener('livewire:updated', function () {
    // Re-inicializar listeners após atualização do componente
    initDeleteModalListeners();
});

function initDeleteModalListeners() {
    // Sempre que o modal fechar, enviar evento para o componente Livewire limpar os campos
    const modal = document.getElementById('deleteMemberModal');
    if (modal) {
        // Remover listeners anteriores
        modal.removeEventListener('hidden.bs.modal', handleModalClose);

        // Adicionar listener para enviar evento ao componente
        modal.addEventListener('hidden.bs.modal', handleModalClose);
    }
}

function handleModalClose() {
    // Disparar evento para o componente Livewire limpar os campos
    Livewire.dispatch('clearDeleteModalFields');
}

// Inicializar listeners na primeira carga
initDeleteModalListeners();

// Listener compatível com Livewire 3 para fechar modal
document.addEventListener('closeDeleteModal', function () {
    const modal = document.getElementById('deleteMemberModal');
    if (modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    }
});
</script><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/church/members/modals/delete-member-modal.blade.php ENDPATH**/ ?>