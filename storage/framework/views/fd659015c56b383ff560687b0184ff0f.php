<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Histórico de Assinaturas</h1>
                            <p>Visualize o histórico completo de assinaturas das igrejas</p>
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar histórico..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Todos os status</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="pacoteFilter">
                                <option value="">Todos os pacotes</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($pacote->id); ?>"><?php echo e($pacote->nome); ?></option>
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
                    <h4 class="card-title">Histórico de Assinaturas (<?php echo e($historico->total()); ?>)</h4>
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
                                    <th>Igreja</th>
                                    <th>Pacote</th>
                                    <th>Data Início</th>
                                    <th>Data Fim</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Forma Pagamento</th>
                                    <th>Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $historico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($item->igreja->nome ?? 'I', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($item->igreja->nome ?? 'Igreja'); ?></h6>
                                                <small class="text-muted"><?php echo e($item->igreja->nif ?? '-'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                <span class="avatar-title"><?php echo e(substr($item->pacote->nome ?? 'P', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <span class="fw-bold"><?php echo e($item->pacote->nome ?? 'Pacote'); ?></span><br>
                                                <small class="text-muted"><?php echo e($item->duracao_meses_custom ?? 0); ?> meses</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($item->data_inicio->format('d/m/Y')); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($item->data_fim ? $item->data_fim->format('d/m/Y') : '-'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo e(number_format($item->valor, 2, ',', '.')); ?> Kz</span>
                                    </td>
                                    <td>
                                        <?php
                                            $statusClass = match($item->status) {
                                                'Ativo' => 'success',
                                                'Cancelado' => 'danger',
                                                'Expirado' => 'warning',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($item->status); ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($item->forma_pagamento ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($item->created_at->format('d/m/Y H:i')); ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-2x mb-2"></i>
                                            <p>Nenhum registro no histórico encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($historico->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($historico->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/assinaturas-historico.blade.php ENDPATH**/ ?>