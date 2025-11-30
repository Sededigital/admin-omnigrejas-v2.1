<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Pagamentos de Assinaturas</h1>
                            <p>Gerencie os pagamentos das assinaturas das igrejas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#pagamentoModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Pagamento
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
                <div class="card-header"  wire:ignore>
                    <ul class="nav nav-tabs" id="pagamentosTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo e($activeTab === 'pagamentos' ? 'active' : ''); ?>" id="pagamentos-tab" data-bs-toggle="tab"
                               href="#pagamentos" role="tab" aria-controls="pagamentos" aria-selected="<?php echo e($activeTab === 'pagamentos' ? 'true' : 'false'); ?>">
                                <i class="fas fa-credit-card me-2"></i>Pagamentos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="ciclos-tab" data-bs-toggle="tab"
                               href="#ciclos" role="tab" aria-controls="ciclos" aria-selected="false">
                                <i class="fas fa-calendar-alt me-2"></i>Ciclos de Cobrança
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="falhas-tab" data-bs-toggle="tab"
                               href="#falhas" role="tab" aria-controls="falhas" aria-selected="false">
                                <i class="fas fa-exclamation-triangle me-2"></i>Falhas de Pagamento
                            </a>
                        </li>
                    </ul>
                </div>

                
                <div class="card-body"  wire:ignore>
                    <div class="tab-content" id="pagamentosTabContent">
                        
                        <div class="tab-pane fade <?php echo e($activeTab === 'pagamentos' ? 'show active' : ''); ?>" id="pagamentos" role="tabpanel" aria-labelledby="pagamentos-tab" tabindex="0" >
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar..." wire:model.live.debounce.300ms="search">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="statusFilter">
                                        <option value="">Todos os status</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="metodoFilter">
                                        <option value="">Todos os métodos</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $metodoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="igrejaFilter">
                                        <option value="">Todas as igrejas</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $igrejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($igreja->id); ?>"><?php echo e($igreja->nome); ?></option>
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

                            
                            <div class="card"  wire:ignore.self>
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Pagamentos (<?php echo e($pagamentos->total()); ?>)</h4>
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
                                                    <th>Valor</th>
                                                    <th>Método</th>
                                                    <th>Status</th>
                                                    <th>Data</th>
                                                    <th>Referência</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $pagamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title"><?php echo e(substr($pagamento->igreja->nome ?? 'I', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0"><?php echo e($pagamento->igreja->nome ?? 'Igreja'); ?></h6>
                                                                <small class="text-muted"><?php echo e($pagamento->igreja->nif ?? '-'); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                                <span class="avatar-title"><?php echo e(substr($pagamento->assinatura->pacote->nome ?? 'P', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold"><?php echo e($pagamento->assinatura->pacote->nome ?? 'Pacote'); ?></span><br>
                                                                <small class="text-muted"><?php echo e($pagamento->assinatura->pacote->duracao_meses ?? 0); ?> meses</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success"><?php echo e(number_format($pagamento->valor, 2, ',', '.')); ?> Kz</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo e($metodoOptions[$pagamento->metodo_pagamento] ?? $pagamento->metodo_pagamento); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $statusClass = match($pagamento->status) {
                                                                'confirmado' => 'success',
                                                                'pendente' => 'warning',
                                                                'falhou' => 'danger',
                                                                'estornado' => 'secondary',
                                                                default => 'secondary'
                                                            };
                                                        ?>
                                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($statusOptions[$pagamento->status] ?? $pagamento->status); ?></span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($pagamento->data_pagamento ? $pagamento->data_pagamento->format('d/m/Y H:i') : '-'); ?></small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($pagamento->referencia ?? '-'); ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click.prevent="openModal('<?php echo e($pagamento->id); ?>')" data-bs-toggle="modal" data-bs-target="#pagamentoModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePagamento(<?php echo e($pagamento->id); ?>)" title="Excluir"
                                                                    onclick="return confirm('Tem certeza que deseja excluir este pagamento?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                            <p>Nenhum pagamento encontrado.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($pagamentos->hasPages()): ?>
                                <div class="card-footer">
                                    <?php echo e($pagamentos->links()); ?>

                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="ciclos" role="tabpanel" aria-labelledby="ciclos-tab" tabindex="0">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar ciclos..." wire:model.live.debounce.300ms="searchCiclos">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="statusCicloFilter">
                                        <option value="">Todos os status</option>
                                        <option value="pendente">Pendente</option>
                                        <option value="pago">Pago</option>
                                        <option value="atrasado">Atrasado</option>
                                        <option value="falhou">Falhou</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-end">
                                    <button type="button" class="btn btn-primary" wire:click="openModalCiclo" data-bs-toggle="modal" data-bs-target="#cicloModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Novo Ciclo
                                    </button>
                                </div>
                            </div>

                            
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Ciclos de Cobrança (<?php echo e($ciclos->total()); ?>)</h4>
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
                                                    <th>Período</th>
                                                    <th>Valor</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $ciclos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ciclo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title"><?php echo e(substr($ciclo->assinaturaHistorico->igreja->nome ?? 'I', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0"><?php echo e($ciclo->assinaturaHistorico->igreja->nome ?? 'Igreja'); ?></h6>
                                                                <small class="text-muted"><?php echo e($ciclo->assinaturaHistorico->igreja->nif ?? '-'); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                                <span class="avatar-title"><?php echo e(substr($ciclo->assinaturaHistorico->pacote->nome ?? 'P', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold"><?php echo e($ciclo->assinaturaHistorico->pacote->nome ?? 'Pacote'); ?></span><br>
                                                                <small class="text-muted"><?php echo e($ciclo->assinaturaHistorico->pacote->duracao_meses ?? 0); ?> meses</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo e($ciclo->inicio ? $ciclo->inicio->format('d/m/Y') : '-'); ?><br>
                                                            até <?php echo e($ciclo->fim ? $ciclo->fim->format('d/m/Y') : '-'); ?>

                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success"><?php echo e(number_format($ciclo->valor, 2, ',', '.')); ?> Kz</span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                            $statusClass = match($ciclo->status) {
                                                                'pago' => 'success',
                                                                'pendente' => 'warning',
                                                                'atrasado' => 'danger',
                                                                'falhou' => 'secondary',
                                                                default => 'secondary'
                                                            };
                                                        ?>
                                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e(ucfirst($ciclo->status)); ?></span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModalCiclo(<?php echo e($ciclo->id); ?>)" data-bs-toggle="modal" data-bs-target="#cicloModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteCiclo(<?php echo e($ciclo->id); ?>)" title="Excluir"
                                                                    onclick="return confirm('Tem certeza que deseja excluir este ciclo?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                                            <p>Nenhum ciclo encontrado.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($ciclos->hasPages()): ?>
                                <div class="card-footer">
                                    <?php echo e($ciclos->links()); ?>

                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>

                        
                        <div class="tab-pane fade" id="falhas" role="tabpanel" aria-labelledby="falhas-tab" tabindex="0">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar falhas..." wire:model.live.debounce.300ms="searchFalhas">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="statusFalhaFilter">
                                        <option value="">Todos os status</option>
                                        <option value="resolvido">Resolvido</option>
                                        <option value="pendente">Pendente</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-end">
                                    <button type="button" class="btn btn-primary" wire:click="openModalFalha" data-bs-toggle="modal" data-bs-target="#falhaModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Nova Falha
                                    </button>
                                </div>
                            </div>

                            
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Falhas de Pagamento (<?php echo e($falhas->total()); ?>)</h4>
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
                                                    <th>Motivo</th>
                                                    <th>Data</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $falhas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $falha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title"><?php echo e(substr($falha->pagamento->igreja->nome ?? 'I', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0"><?php echo e($falha->pagamento->igreja->nome ?? 'Igreja'); ?></h6>
                                                                <small class="text-muted"><?php echo e($falha->pagamento->igreja->nif ?? '-'); ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                                <span class="avatar-title"><?php echo e(substr($falha->pagamento->assinatura->pacote->nome ?? 'P', 0, 1)); ?></span>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold"><?php echo e($falha->pagamento->assinatura->pacote->nome ?? 'Pacote'); ?></span><br>
                                                                <small class="text-muted"><?php echo e(number_format($falha->pagamento->valor, 2, ',', '.')); ?> Kz</small>
                                                            </div>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e(Str::limit($falha->motivo, 40)); ?></small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?php echo e($falha->data ? $falha->data->format('d/m/Y H:i') : '-'); ?></small>
                                                    </td>
                                                    <td>
                                                        <!--[if BLOCK]><![endif]--><?php if($falha->resolvido): ?>
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Resolvido
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Pendente
                                                            </span>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModalFalha(<?php echo e($falha->id); ?>)" data-bs-toggle="modal" data-bs-target="#falhaModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteFalha(<?php echo e($falha->id); ?>)" title="Excluir"
                                                                    onclick="return confirm('Tem certeza que deseja excluir esta falha?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                            <p>Nenhuma falha encontrada.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($falhas->hasPages()): ?>
                                <div class="card-footer">
                                    <?php echo e($falhas->links()); ?>

                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="modal fade" id="cicloModal" tabindex="-1" aria-labelledby="cicloModalLabel" aria-hidden="true"
                 data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light border-bottom">
                            <h5 class="modal-title fw-bold" id="cicloModalLabel">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span id="modal-title"><?php echo e($editingCiclo ? 'Editar Ciclo' : 'Novo Ciclo'); ?></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form wire:submit.prevent="saveCiclo">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select <?php $__errorArgs = ['ciclo_assinatura_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    wire:model.live="ciclo_assinatura_id">
                                                <option value="">Selecione uma assinatura</option>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $assinaturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assinatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($assinatura->id); ?>">
                                                        <?php echo e($assinatura->igreja->nome ?? 'Igreja'); ?> - <?php echo e($assinatura->pacote->nome ?? 'Pacote'); ?> (<?php echo e(number_format($assinatura->valor, 2, ',', '.')); ?> Kz)
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </select>
                                            <label><i class="fas fa-file-signature text-primary me-1"></i>Assinatura *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ciclo_assinatura_id'];
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
                                            <input type="date" class="form-control <?php $__errorArgs = ['ciclo_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   wire:model="ciclo_inicio" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <label><i class="fas fa-calendar-plus text-primary me-1"></i>Data Início *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ciclo_inicio'];
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
                                            <input type="date" class="form-control <?php $__errorArgs = ['ciclo_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   wire:model="ciclo_fim" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <label><i class="fas fa-calendar-minus text-primary me-1"></i>Data Fim *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ciclo_fim'];
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
                                            <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['ciclo_valor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   wire:model="ciclo_valor" placeholder="0.00" min="0" readonly>
                                            <label><i class="fas fa-dollar-sign text-primary me-1"></i>Valor *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ciclo_valor'];
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
                                            <select class="form-select <?php $__errorArgs = ['ciclo_status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    wire:model="ciclo_status">
                                                <option value="pendente">Pendente</option>
                                                <option value="pago">Pago</option>
                                                <option value="atrasado">Atrasado</option>
                                                <option value="falhou">Falhou</option>
                                            </select>
                                            <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['ciclo_status'];
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
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Status:</strong>
                                            <span class="text-muted">
                                                <?php echo e($editingCiclo ? 'Editando Ciclo' : 'Novo Ciclo'); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-top bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveCiclo" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveCiclo">
                                    <i class="fas fa-save me-1"></i><?php echo e($editingCiclo ? 'Atualizar Ciclo' : 'Salvar Ciclo'); ?>

                                </span>
                                <span wire:loading wire:target="saveCiclo">
                                    <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingCiclo ? 'Atualizando...' : 'Salvando...'); ?>

                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="modal fade" id="falhaModal" tabindex="-1" aria-labelledby="falhaModalLabel" aria-hidden="true"
                 data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light border-bottom">
                            <h5 class="modal-title fw-bold" id="falhaModalLabel">
                                <i class="fas fa-exclamation-triangle text-primary me-2"></i>
                                <span id="modal-title"><?php echo e($editingFalha ? 'Editar Falha' : 'Nova Falha'); ?></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form wire:submit.prevent="saveFalha">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select <?php $__errorArgs = ['falha_pagamento_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    wire:model="falha_pagamento_id">
                                                <option value="">Selecione um pagamento</option>
                                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pagamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($pagamento->id); ?>">
                                                        <?php echo e($pagamento->igreja->nome ?? 'Igreja'); ?> - <?php echo e(number_format($pagamento->valor, 2, ',', '.')); ?> Kz
                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                            </select>
                                            <label><i class="fas fa-credit-card text-primary me-1"></i>Pagamento *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['falha_pagamento_id'];
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
                                            <textarea class="form-control <?php $__errorArgs = ['falha_motivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                      wire:model="falha_motivo" rows="3"
                                                      placeholder="Descreva o motivo da falha"></textarea>
                                            <label><i class="fas fa-comment text-primary me-1"></i>Motivo *</label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['falha_motivo'];
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
                                        <div class="form-check form-switch">
                                            <input class="form-check-input <?php $__errorArgs = ['falha_resolvido'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   type="checkbox" wire:model="falha_resolvido" id="falhaResolvidoSwitch">
                                            <label class="form-check-label" for="falhaResolvidoSwitch">
                                                <i class="fas fa-check-circle text-primary me-1"></i>Falha Resolvida
                                            </label>
                                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['falha_resolvido'];
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
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Status:</strong>
                                            <span class="text-muted">
                                                <?php echo e($editingFalha ? 'Editando Falha' : 'Nova Falha'); ?>

                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-top bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveFalha" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveFalha">
                                    <i class="fas fa-save me-1"></i><?php echo e($editingFalha ? 'Atualizar Falha' : 'Salvar Falha'); ?>

                                </span>
                                <span wire:loading wire:target="saveFalha">
                                    <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingFalha ? 'Atualizando...' : 'Salvando...'); ?>

                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>


    
    <div class="modal fade" id="pagamentoModal" tabindex="-1" aria-labelledby="pagamentoModalLabel" aria-hidden="true"
          data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="pagamentoModalLabel">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        <span id="modal-title"><?php echo e($editingPagamento ? 'Editar Pagamento' : 'Novo Pagamento'); ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="savePagamento" onsubmit="console.log('Form submitted with metodo_pagamento:', document.querySelector('[name=metodo_pagamento]').value)">

                        <!-- Seleção da Assinatura e Igreja -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['assinatura_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="assinatura_id" disabled>
                                        <option value="">Selecione uma assinatura</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $assinaturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assinatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($assinatura->id); ?>">
                                                <?php echo e($assinatura->pacote->nome ?? 'Pacote'); ?> - <?php echo e($assinatura->igreja->nome ?? 'Igreja'); ?> (<?php echo e(number_format($assinatura->valor, 2, ',', '.')); ?> Kz)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-file-signature text-primary me-1"></i>Assinatura *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['assinatura_id'];
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
                                    <select class="form-select <?php $__errorArgs = ['igreja_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="igreja_id" disabled>
                                        <option value="">Selecione uma igreja</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $igrejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($igreja->id); ?>"><?php echo e($igreja->nome); ?> (<?php echo e($igreja->nif); ?>)</option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-church text-primary me-1"></i>Igreja *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['igreja_id'];
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

                            <!-- Valor e Método -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['valor'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           wire:model="valor" placeholder="0.00" required disabled>
                                    <label><i class="fas fa-dollar-sign text-primary me-1"></i>Valor (Kz) *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['valor'];
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
                                    <select class="form-select <?php $__errorArgs = ['metodo_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="metodo_pagamento"
                                            onchange="console.log('Método changed to:', this.value)">
                                        <option value="">Selecione um método</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $metodoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php echo e($metodo_pagamento === $key ? 'selected' : ''); ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-money-bill-wave text-primary me-1"></i>Método *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['metodo_pagamento'];
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

                            <!-- Referência e Status -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control <?php $__errorArgs = ['referencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           wire:model="referencia" placeholder="Referência do pagamento" readonly>
                                    <label><i class="fas fa-hashtag text-primary me-1"></i>Referência</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['referencia'];
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
                                    <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="status">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['status'];
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

                            <!-- Data do Pagamento -->
                            <div class="col-12">
                                <div class="form-floating mb-3" wire:ignore>
                                    <input type="date" class="form-control date_flatpicker <?php $__errorArgs = ['data_pagamento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           wire:model="data_pagamento">
                                    <label><i class="fas fa-calendar-alt text-primary me-1"></i>Data do Pagamento</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['data_pagamento'];
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
                                        <?php echo e($editingPagamento ? 'Editando Pagamento' : 'Novo Pagamento'); ?>

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
                    <button type="button" class="btn btn-outline-info" wire:click="gerarNovaReferencia" wire:loading.attr="disabled" <?php echo e($editingPagamento ? 'disabled' : ''); ?> >
                        <span wire:loading.remove wire:target="gerarNovaReferencia">
                            <i class="fas fa-refresh me-1"></i>Nova Referência
                        </span>
                        <span wire:loading wire:target="gerarNovaReferencia">
                            <i class="fas fa-spinner fa-spin me-1"></i>Gerando...
                        </span>
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="savePagamento" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePagamento">
                            <i class="fas fa-save me-1"></i><?php echo e($editingPagamento ? 'Atualizar Pagamento' : 'Salvar Pagamento'); ?>

                        </span>
                        <span wire:loading wire:target="savePagamento">
                            <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingPagamento ? 'Atualizando...' : 'Salvando...'); ?>

                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

     
     <script src="<?php echo e(asset('system/js/assignatures.js')); ?>" data-navigate-once></script>

     
     <button type="button" id="openPagamentoModalBtn" data-bs-toggle="modal" data-bs-target="#pagamentoModal" style="display: none;"></button>

</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/pagamentos.blade.php ENDPATH**/ ?>