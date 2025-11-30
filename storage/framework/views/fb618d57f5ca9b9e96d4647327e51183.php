<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Gestão de Pacotes</h1>
                            <p>Gerencie os pacotes de assinatura disponíveis no sistema</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#pacoteModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Pacote
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar pacotes..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
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
                    <h4 class="card-title">Pacotes (<?php echo e($pacotes->total()); ?>)</h4>
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
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Preço</th>
                                    <th>Duração</th>
                                    <th>Trial Dias</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($pacote->nome, 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($pacote->nome); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e(Str::limit($pacote->descricao ?? 'Sem descrição', 50)); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo e(number_format($pacote->preco, 2, ',', '.')); ?> Kz</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($pacote->duracao_meses); ?> meses</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo e($pacote->trial_dias ?? 0); ?> dias</span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($pacote->created_at->format('d/m/Y H:i')); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal(<?php echo e($pacote->id); ?>)" data-bs-toggle="modal" data-bs-target="#pacoteModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePacote(<?php echo e($pacote->id); ?>)" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este pacote?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2"></i>
                                            <p>Nenhum pacote encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($pacotes->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($pacotes->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <div class="modal fade" id="pacoteModal" tabindex="-1" aria-labelledby="pacoteModalLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="pacoteModalLabel">
                            <i class="fas fa-box text-primary me-2"></i>
                            <span id="modal-title"><?php echo e($editingPacote ? 'Editar Pacote' : 'Novo Pacote'); ?></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="savePacote">

                            <!-- Seleção do Nome -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="nome" placeholder="Nome do pacote" required>
                                        <label><i class="fas fa-tag text-primary me-1"></i>Nome *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['nome'];
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

                                <!-- Preço e Duração -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['preco'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="preco" placeholder="0.00" required>
                                        <label><i class="fas fa-dollar-sign text-primary me-1"></i>Preço (Kz) *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['preco'];
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
                                        <input type="number" class="form-control <?php $__errorArgs = ['duracao_meses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="duracao_meses" placeholder="1" min="1" required>
                                        <label><i class="fas fa-calendar text-primary me-1"></i>Duração (meses) *</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['duracao_meses'];
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
                                        <input type="number" class="form-control <?php $__errorArgs = ['trial_dias'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               wire:model="trial_dias" placeholder="0" min="0">
                                        <label><i class="fas fa-clock text-primary me-1"></i>Trial (dias)</label>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trial_dias'];
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
                                                  wire:model="descricao" rows="3"
                                                  placeholder="Descrição do pacote"></textarea>
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

                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <strong>Status:</strong>
                                        <span class="text-muted">
                                            <?php echo e($editingPacote ? 'Editando Pacote' : 'Novo Pacote'); ?>

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
                        <button type="button" class="btn btn-primary" wire:click="savePacote" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="savePacote">
                                <i class="fas fa-save me-1"></i><?php echo e($editingPacote ? 'Atualizar Pacote' : 'Salvar Pacote'); ?>

                            </span>
                            <span wire:loading wire:target="savePacote">
                                <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingPacote ? 'Atualizando...' : 'Salvando...'); ?>

                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        
        <script src="<?php echo e(asset('system/js/assignatures.js')); ?>" data-navigate-once></script>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/pacotes.blade.php ENDPATH**/ ?>