<!-- Pastoral Care Modal -->
<div class="modal fade" id="pastoralCareModal" tabindex="-1" aria-labelledby="pastoralCareModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title fw-bold" id="pastoralCareModalLabel">
                    <i class="fas fa-<?php echo e($isEditing ? 'edit' : 'plus'); ?> text-white me-2"></i>
                    <?php echo e($isEditing ? 'Editar Atendimento Pastoral' : 'Registrar Atendimento Pastoral'); ?>

                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="salvarAtendimento">
                    <div class="text-center mb-4">
                        <i class="fas fa-praying-hands text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-bold text-center mb-3">
                        <?php echo e($isEditing ? 'Atualize as informações do atendimento' : 'Registre um novo atendimento pastoral'); ?>

                    </h6>
                    <p class="text-muted mb-4">
                        Documente os cuidados pastorais prestados aos membros da igreja.
                    </p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i>Membro *
                            </label>
                            <select class="form-select <?php $__errorArgs = ['membro_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    wire:model="membro_id">
                                <option value="">Selecione o membro...</option>
                                <!--[if BLOCK]><![endif]--><?php if(isset($membrosDisponiveis)): ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $membrosDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($membro['id']); ?>"><?php echo e($membro['nome']); ?> (<?php echo e(ucfirst($membro['cargo'])); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['membro_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user-tie text-primary me-1"></i>Pastor *
                            </label>
                            <select class="form-select <?php $__errorArgs = ['pastor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    wire:model="pastor_id">
                                <option value="">Selecione o pastor...</option>
                                <!--[if BLOCK]><![endif]--><?php if(isset($pastoresDisponiveis)): ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pastoresDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pastor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($pastor['id']); ?>"><?php echo e($pastor['nome']); ?> (<?php echo e(ucfirst($pastor['cargo'])); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pastor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-list text-primary me-1"></i>Tipo de Atendimento *
                            </label>
                            <select class="form-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    wire:model="tipo">
                                <option value="">Selecione o tipo...</option>
                                <!--[if BLOCK]><![endif]--><?php if(isset($tiposAtendimento)): ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tiposAtendimento; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar text-primary me-1"></i>Data do Atendimento
                            </label>
                            <input type="date"
                                   class="form-control <?php $__errorArgs = ['data_atendimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   wire:model="data_atendimento">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['data_atendimento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left text-primary me-1"></i>Descrição do Atendimento (opcional)
                        </label>
                        <textarea class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  wire:model="descricao"
                                  rows="4"
                                  placeholder="Descreva o atendimento, orientações dadas, orações realizadas, etc..."
                                  maxlength="1000"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Registre todos os detalhes importantes do atendimento pastoral para acompanhamento futuro.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="salvarAtendimento">
                        <span wire:loading.remove wire:target="salvarAtendimento">
                            <i class="fas fa-<?php echo e($isEditing ? 'save' : 'plus'); ?> me-1"></i>
                            <?php echo e($isEditing ? 'Atualizar' : 'Registrar'); ?>

                        </span>
                        <span wire:loading wire:target="salvarAtendimento">
                            <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
                        </span>
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    const modalElement = document.getElementById('pastoralCareModal');

    Livewire.on('open-pastoral-care-modal', () => {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    Livewire.on('close-pastoral-care-modal', () => {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    });

    // Preencher campos quando o modal for mostrado
    modalElement.addEventListener('show.bs.modal', function () {
        // Pequeno delay para garantir que o componente Livewire atualizou
        setTimeout(() => {
            // Usar wire:model para sincronizar automaticamente
            <!--[if BLOCK]><![endif]--><?php if($isEditing): ?>
                // Os campos serão preenchidos automaticamente pelo wire:model
                // Não precisamos fazer nada aqui
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        }, 100);
    });

    // Garantir que o modal seja completamente fechado
    modalElement.addEventListener('hidden.bs.modal', function () {
        // Forçar remoção de classes do Bootstrap que podem permanecer
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    });
});
</script><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/church/only/modals/pastoral-care-modal.blade.php ENDPATH**/ ?>