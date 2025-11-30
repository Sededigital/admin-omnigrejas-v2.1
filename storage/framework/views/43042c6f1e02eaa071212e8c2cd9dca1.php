<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Cupons de Desconto</h1>
                            <p>Gerencie cupons de desconto para assinaturas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#cupomModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Cupom
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
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar cupons..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
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
                    <h4 class="card-title">Cupons (<?php echo e($cupons->total()); ?>)</h4>
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
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Desconto</th>
                                    <th>Validade</th>
                                    <th>Uso</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $cupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cupom): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($cupom->codigo, 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($cupom->codigo); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e(Str::limit($cupom->descricao ?? 'Sem descrição', 40)); ?></small>
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($cupom->desconto_percentual): ?>
                                            <span class="badge bg-success"><?php echo e($cupom->desconto_percentual); ?>%</span>
                                        <?php elseif($cupom->desconto_valor): ?>
                                            <span class="badge bg-info"><?php echo e(number_format($cupom->desconto_valor, 2, ',', '.')); ?> Kz</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($cupom->valido_de && $cupom->valido_ate): ?>
                                            <small class="text-muted">
                                                <?php echo e($cupom->valido_de->format('d/m/Y')); ?><br>
                                                até <?php echo e($cupom->valido_ate->format('d/m/Y')); ?>

                                            </small>
                                        <?php elseif($cupom->valido_de): ?>
                                            <small class="text-muted">
                                                A partir de<br><?php echo e($cupom->valido_de->format('d/m/Y')); ?>

                                            </small>
                                        <?php elseif($cupom->valido_ate): ?>
                                            <small class="text-muted">
                                                Até<br><?php echo e($cupom->valido_ate->format('d/m/Y')); ?>

                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">Sem limite</small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small class="text-muted"><?php echo e($cupom->usado ?? 0); ?>/<?php echo e($cupom->uso_max); ?></small>
                                            <!--[if BLOCK]><![endif]--><?php if($cupom->usado > 0): ?>
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                         style="width: <?php echo e(($cupom->usado / $cupom->uso_max) * 100); ?>%"></div>
                                                </div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td>
                                        <!--[if BLOCK]><![endif]--><?php if($cupom->ativo): ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Ativo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Inativo
                                            </span>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($cupom->created_at->format('d/m/Y H:i')); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal(<?php echo e($cupom->id); ?>)" data-bs-toggle="modal" data-bs-target="#cupomModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" wire:click="toggleStatus(<?php echo e($cupom->id); ?>)" title="<?php echo e($cupom->ativo ? 'Desativar' : 'Ativar'); ?>">
                                                <i class="fas fa-<?php echo e($cupom->ativo ? 'ban' : 'check'); ?>"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteCupom(<?php echo e($cupom->id); ?>)" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                            <p>Nenhum cupom encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($cupons->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($cupons->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <div class="modal fade" id="cupomModal" tabindex="-1" aria-labelledby="cupomModalLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="cupomModalLabel">
                            <i class="fas fa-ticket-alt text-primary me-2"></i>
                            <span id="modal-title"><?php echo e($editingCupom ? 'Editar Cupom' : 'Novo Cupom'); ?></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveCupom">

                            <!-- Código e Descrição -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control text-uppercase <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="codigo" placeholder="Código do cupom" required>
                                        <label><i class="fas fa-hashtag text-primary me-1"></i>Código *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['codigo'];
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
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="generateCodigo">
                                        <i class="fas fa-magic me-1"></i>Gerar Código
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control <?php $__errorArgs = ['uso_max'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="uso_max" placeholder="1" min="1" required>
                                        <label><i class="fas fa-users text-primary me-1"></i>Uso Máximo *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['uso_max'];
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

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control <?php $__errorArgs = ['descricao'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                  wire:model="descricao" rows="2"
                                                  placeholder="Descrição do cupom"></textarea>
                                        <label><i class="fas fa-comment text-primary me-1"></i>Descrição</label>
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
                                </div>

                                <!-- Tipo de Desconto -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control <?php $__errorArgs = ['desconto_percentual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="desconto_percentual" placeholder="0" min="0" max="100">
                                        <label><i class="fas fa-percent text-primary me-1"></i>Desconto Percentual (%)</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['desconto_percentual'];
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

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['desconto_valor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="desconto_valor" placeholder="0.00" min="0">
                                        <label><i class="fas fa-dollar-sign text-primary me-1"></i>Desconto em Valor (Kz)</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['desconto_valor'];
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

                                <!-- Validade -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control date_flatpicker <?php $__errorArgs = ['valido_de'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="valido_de">
                                        <label><i class="fas fa-calendar-plus text-primary me-1"></i>Válido De</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['valido_de'];
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

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control date_flatpicker <?php $__errorArgs = ['valido_ate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="valido_ate">
                                        <label><i class="fas fa-calendar-minus text-primary me-1"></i>Válido Até</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['valido_ate'];
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

                                <!-- Status -->
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input <?php $__errorArgs = ['ativo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               type="checkbox" wire:model="ativo" id="ativoSwitch">
                                        <label class="form-check-label" for="ativoSwitch">
                                            <i class="fas fa-toggle-on text-primary me-1"></i>Cupom Ativo
                                        </label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ativo'];
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
                                            <?php echo e($editingCupom ? 'Editando Cupom' : 'Novo Cupom'); ?>

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
                        <button type="button" class="btn btn-primary" wire:click="saveCupom" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveCupom">
                                <i class="fas fa-save me-1"></i><?php echo e($editingCupom ? 'Atualizar Cupom' : 'Salvar Cupom'); ?>

                            </span>
                            <span wire:loading wire:target="saveCupom">
                                <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingCupom ? 'Atualizando...' : 'Salvando...'); ?>

                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <script src="<?php echo e(asset('system/js/assignatures.js')); ?>" data-navigate-once></script>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/cupons.blade.php ENDPATH**/ ?>