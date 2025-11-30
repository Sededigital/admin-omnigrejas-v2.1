<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Recursos dos Pacotes</h1>
                            <p>Configure os limites de recursos para cada pacote SaaS</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#recursoModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Limite
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header.png')); ?>" alt="header" class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header1.png')); ?>" alt="header" class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header2.png')); ?>" alt="header" class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header3.png')); ?>" alt="header" class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header4.png')); ?>" alt="header" class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
            <img src="<?php echo e(asset('assets/images/dashboard/top-header5.png')); ?>" alt="header" class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>

    
    <div class="row">
        
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar limites..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" wire:model.live="pacoteFilter">
                                <option value="">Todos os pacotes</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pacote->id); ?>"><?php echo e($pacote->nome); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="recursoFilter">
                                <option value="">Todos os recursos</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recursoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                <i class="fas fa-eraser me-1"></i>
                                Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Limites de Recursos (<?php echo e($pacoteRecursos->total()); ?>)</h4>
                    <div class="dropdown">
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Pacote</th>
                                    <th>Recurso</th>
                                    <th>Limite</th>
                                    <th>Unidade</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $pacoteRecursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recurso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($recurso->pacote->nome ?? 'P', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($recurso->pacote->nome ?? 'Pacote'); ?></h6>
                                                <small class="text-muted">AOA <?php echo e(number_format($recurso->pacote->preco ?? 0, 2, ',', '.')); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($recursoOptions[$recurso->recurso_tipo] ?? $recurso->recurso_tipo); ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-primary"><?php echo e(number_format($recurso->limite_valor, 0, ',', '.')); ?></strong>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($unidadeOptions[$recurso->unidade] ?? $recurso->unidade); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal(<?php echo e($recurso->id); ?>)" data-bs-toggle="modal" data-bs-target="#recursoModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteRecurso(<?php echo e($recurso->id); ?>)" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-cogs fa-2x mb-2"></i>
                                            <p>Nenhum limite de recurso encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($pacoteRecursos->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($pacoteRecursos->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="recursoModal" tabindex="-1" aria-labelledby="recursoModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="recursoModalLabel">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        <span id="modal-title"><?php echo e($editingRecurso ? 'Editar Limite' : 'Novo Limite de Recurso'); ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveRecurso">

                        <!-- Seleção do Pacote -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['pacote_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="pacote_id">
                                        <option value="">Selecione um pacote</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($pacote->id); ?>"><?php echo e($pacote->nome); ?> - AOA <?php echo e(number_format($pacote->preco, 2, ',', '.')); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-box text-primary me-1"></i>Pacote *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pacote_id'];
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

                            <!-- Tipo de Recurso e Limite -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['recurso_tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="recurso_tipo">
                                        <option value="">Selecione o recurso</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $recursoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-tags text-primary me-1"></i>Tipo de Recurso *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['recurso_tipo'];
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

                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control <?php $__errorArgs = ['limite_valor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           wire:model="limite_valor" placeholder="0" min="0" step="0.01">
                                    <label><i class="fas fa-tachometer-alt text-primary me-1"></i>Limite </label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['limite_valor'];
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

                            <!-- Unidade -->
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['unidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="unidade">
                                        <option value="">Selecione</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $unidadeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-balance-scale text-primary me-1"></i>Unidade *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['unidade'];
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
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveRecurso" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveRecurso">
                            <i class="fas fa-save me-1"></i><?php echo e($editingRecurso ? 'Atualizar Limite' : 'Salvar Limite'); ?>

                        </span>
                        <span wire:loading wire:target="saveRecurso">
                            <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingRecurso ? 'Atualizando...' : 'Salvando...'); ?>

                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/pacote-recursos.blade.php ENDPATH**/ ?>