<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Permissões de Pacotes</h1>
                            <p>Gerencie as permissões de acesso dos pacotes aos módulos</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#permissaoModal">
                                <i class="fas fa-plus me-2"></i>
                                Nova Permissão
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
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar permissões..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="pacoteFilter">
                                <option value="">Todos os pacotes</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pacote->id); ?>"><?php echo e($pacote->nome); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="moduloFilter">
                                <option value="">Todos os módulos</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($modulo->id); ?>"><?php echo e($modulo->nome); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                <i class="fas fa-eraser me-1"></i>
                                Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Permissões de Pacotes (<?php echo e($permissoes->total()); ?>)</h4>
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
                                    <th>Módulo</th>
                                    <th>Permissão</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $permissoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-success rounded">
                                                <span class="avatar-title"><?php echo e(substr($permissao->pacote->nome ?? 'P', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($permissao->pacote->nome ?? 'Pacote'); ?></h6>
                                                <small class="text-muted"><?php echo e(number_format($permissao->pacote->preco ?? 0, 2, ',', '.')); ?> Kz</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-info rounded">
                                                <span class="avatar-title"><?php echo e(substr($permissao->modulo->nome ?? 'M', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($permissao->modulo->nome ?? 'Módulo'); ?></h6>
                                                <small class="text-muted"><?php echo e(Str::limit($permissao->modulo->descricao ?? '', 30)); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $permissaoClass = match($permissao->permissao) {
                                                'leitura' => 'info',
                                                'escrita' => 'success',
                                                'nenhuma' => 'danger',
                                                default => 'secondary'
                                            };
                                            $permissaoLabel = $permissaoOptions[$permissao->permissao] ?? $permissao->permissao;
                                        ?>
                                        <span class="badge bg-<?php echo e($permissaoClass); ?>"><?php echo e($permissaoLabel); ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($permissao->created_at->format('d/m/Y H:i')); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal(<?php echo e($permissao->id); ?>)" data-bs-toggle="modal" data-bs-target="#permissaoModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePermissao(<?php echo e($permissao->id); ?>)" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir esta permissão?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                                            <p>Nenhuma permissão encontrada.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($permissoes->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($permissoes->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <script src="<?php echo e(asset('system/js/assignatures.js')); ?>" data-navigate-once></script>

    </div>

    
    <div class="modal fade" id="permissaoModal" tabindex="-1" aria-labelledby="permissaoModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="permissaoModalLabel">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        <span id="modal-title"><?php echo e($editingPermissao ? 'Editar Permissão' : 'Nova Permissão'); ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="savePermissao">

                        <!-- Seleção do Pacote e Módulo -->
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
                                            wire:model.live="pacote_id">
                                        <option value="">Selecione um pacote</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($pacote->id); ?>"><?php echo e($pacote->nome); ?> - <?php echo e(number_format($pacote->preco, 2, ',', '.')); ?> Kz</option>
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

                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['modulo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model.live="modulo_id" <?php echo e($pacote_id ? '' : 'disabled'); ?>>
                                        <option value="">
                                            <?php echo e($pacote_id ? 'Selecione um módulo' : 'Selecione primeiro um pacote'); ?>

                                        </option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($modulo->id); ?>"><?php echo e($modulo->nome); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-cube text-primary me-1"></i>Módulo *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['modulo_id'];
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

                            <!-- Permissão -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['permissao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model.live="permissao" <?php echo e($pacote_id && $modulo_id ? '' : 'disabled'); ?>>
                                        <option value="">
                                            <?php echo e($pacote_id && $modulo_id ? 'Selecione uma permissão' : 'Selecione pacote e módulo primeiro'); ?>

                                        </option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permissaoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-key text-primary me-1"></i>Permissão *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['permissao'];
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

                            <!-- Status Visual -->
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong>
                                    <span class="text-muted">
                                        <?php echo e($editingPermissao ? 'Editando Permissão' : 'Nova Permissão'); ?>

                                    </span>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($editingPermissao): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Atenção:</strong> Alterar esta permissão afetará todas as igrejas que possuem este pacote.
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="savePermissao" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePermissao">
                            <i class="fas fa-save me-1"></i><?php echo e($editingPermissao ? 'Atualizar Permissão' : 'Salvar Permissão'); ?>

                        </span>
                        <span wire:loading wire:target="savePermissao">
                            <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingPermissao ? 'Atualizando...' : 'Salvando...'); ?>

                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/pacote-permissoes.blade.php ENDPATH**/ ?>