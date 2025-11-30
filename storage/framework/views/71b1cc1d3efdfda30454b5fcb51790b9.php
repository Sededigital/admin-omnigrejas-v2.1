<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Assinaturas Atuais</h1>
                            <p>Gerencie as assinaturas ativas das igrejas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#assinaturaModal">
                                <i class="fas fa-plus me-2"></i>
                                Nova Assinatura
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
                                <input type="text"  autocomplete="new-password" autocomplete="new-password"  class="form-control" placeholder="Buscar assinaturas..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="Ativo">Ativo</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Expirado">Expirado</option>
                            </select>
                        </div>
                        <div class="col-md-5 text-end">
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
                    <h4 class="card-title">Assinaturas Atuais (<?php echo e($assinaturas->total()); ?>)</h4>
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
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $assinaturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assinatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($assinatura->igreja->nome ?? 'I', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($assinatura->igreja->nome ?? 'Igreja'); ?></h6>
                                                <small class="text-muted"><?php echo e($assinatura->igreja->nif ?? '-'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                <span class="avatar-title"><?php echo e(substr($assinatura->pacote->nome ?? 'P', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <span class="fw-bold"><?php echo e($assinatura->pacote->nome ?? 'Pacote'); ?></span><br>
                                                <small class="text-muted"><?php echo e(number_format($assinatura->pacote->preco ?? 0, 2, ',', '.')); ?> Kz</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($assinatura->data_inicio->format('d/m/Y')); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($assinatura->data_fim ? $assinatura->data_fim->format('d/m/Y') : '-'); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                            $statusClass = match($assinatura->status) {
                                                'Ativo' => 'success',
                                                'Cancelado' => 'danger',
                                                'Expirado' => 'warning',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($assinatura->status); ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal(<?php echo e($assinatura->igreja->id); ?>)" data-bs-toggle="modal" data-bs-target="#assinaturaModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-file-signature fa-2x mb-2"></i>
                                            <p>Nenhuma assinatura encontrada.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($assinaturas->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($assinaturas->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <script src="<?php echo e(asset('system/js/assignatures.js')); ?>" data-navigate-once></script>


        <script>
            document.addEventListener('livewire:loaded', function() {
                console.log('Teste de sobrevivencia');


                Livewire.on('refresh-pacotes-select', () => {
                    setTimeout(() => {

                        $wire.call('$refresh');
                    }, 50);
                });

            });

            document.addEventListener('livewire:navigated', function() {

            });
        </script>

    </div>

    
    <div class="modal fade" id="assinaturaModal" tabindex="-1" aria-labelledby="assinaturaModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="assinaturaModalLabel">
                        <i class="fas fa-file-signature text-primary me-2"></i>
                        <span id="modal-title"><?php echo e($editingAssinatura ? 'Editar Assinatura' : 'Nova Assinatura'); ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveAssinatura">

                        <!-- Seleção da Igreja -->
                        <div class="row g-2">
                            <div class="col-12">
                                <div class="input-group mb-2">
                                    <div class="form-floating flex-grow-1">
                                        <input type="text"  autocomplete="new-password" autocomplete="new-password"
                                               class="form-control <?php $__errorArgs = ['igreja_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               id="igreja_search"
                                               placeholder="Digite para pesquisar igreja..."
                                               autocomplete="off"
                                               list="igrejas_list"
                                               wire:model.live="igreja_nome"
                                               wire:loading.attr="readonly">
                                        <label><i class="fas fa-church text-primary me-1"></i>Igreja *</label>
                                        <datalist id="igrejas_list">
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $igrejas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($igreja->nome); ?> (<?php echo e($igreja->nif); ?>)" data-id="<?php echo e($igreja->id); ?>">
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </datalist>
                                    </div>
                                    <span class="input-group-text border-0"
                                          style="min-width: 50px; justify-content: center;">
                                        <span class="spinner-border spinner-border-sm"
                                              wire:loading.class="text-primary"
                                              wire:loading wire:target="igreja_nome"
                                              role="status"
                                              aria-hidden="true"></span>
                                    </span>
                                </div>
                                
                                <input type="hidden" wire:model="igreja_id">
                                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['igreja_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                             <!-- Pacote e Status -->
                             <div class="col-md-8">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <select class="form-select <?php $__errorArgs = ['pacote_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                 wire:model.live="pacote_id"
                                                 wire:key="pacote-select-<?php echo e($igreja_id); ?>-<?php echo e($vitalicio ? 'vitalicio' : 'normal'); ?>-<?php echo e(time()); ?>"
                                                 wire:loading.attr="disabled"
                                                 <?php echo e($igreja_id ? '' : 'disabled'); ?>>
                                             <option value="">
                                                 <?php if($igreja_id): ?>
                                                     <!--[if BLOCK]><![endif]--><?php if($pacotes && $pacotes->count() > 0): ?>
                                                         Selecione um pacote disponível
                                                     <?php else: ?>
                                                         Nenhum pacote disponível para esta igreja
                                                     <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                 <?php else: ?>
                                                     Selecione uma igreja primeiro
                                                 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                             </option>
                                             <!--[if BLOCK]><![endif]--><?php if($pacotes && $pacotes->count() > 0): ?>
                                                 <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                     <option value="<?php echo e($pacote->id); ?>" <?php echo e($pacote->id == $pacote_id ? 'selected' : ''); ?>>
                                                         <?php echo e($pacote->nome); ?> -
                                                         <!--[if BLOCK]><![endif]--><?php if($vitalicio && $pacote->preco_vitalicio): ?>
                                                             <?php echo e(number_format($pacote->preco_vitalicio, 2, ',', '.')); ?> Kz (Vitalício)
                                                         <?php else: ?>
                                                             <?php echo e(number_format($pacote->preco, 2, ',', '.')); ?> Kz
                                                         <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                     </option>
                                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                             <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                         </select>
                                         <label><i class="fas fa-box text-primary me-1"></i>Pacote *</label>
                                     </div>
                                     <span class="input-group-text border-0"
                                           style="min-width: 50px; justify-content: center;">
                                         <span class="spinner-border spinner-border-sm"
                                               wire:loading.class="text-primary"
                                               wire:loading wire:target="pacote_id"
                                               role="status"
                                               aria-hidden="true"></span>
                                     </span>
                                 </div>
                                 <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['pacote_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                     <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                             </div>

                             <div class="col-md-4">
                                 <div class="form-floating mb-2">
                                     <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                             wire:model="status">
                                         <option value="Ativo">Ativo</option>
                                         <option value="Cancelado">Cancelado</option>
                                         <option value="Expirado">Expirado</option>
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

                             <!-- Datas em uma linha -->
                             <div class="col-md-4">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <input type="text"  autocomplete="new-password" autocomplete="new-password"
                                                class="form-control date_flatpicker <?php $__errorArgs = ['data_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model.defer="data_inicio"
                                                placeholder="dd/mm/aaaa"
                                                autocomplete="off"
                                                readonly
                                                style="cursor: pointer;">
                                         <label><i class="fas fa-calendar-plus text-primary me-1"></i>Data Início *</label>
                                     </div>
                                     <button type="button"
                                             class="btn btn-sm border-0"
                                             wire:click="clearDataInicio"
                                             title="Limpar Data Início">
                                         <i class="fas fa-times text-primary"></i>
                                     </button>
                                 </div>
                                 <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['data_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                     <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                             </div>

                             <!--[if BLOCK]><![endif]--><?php if(!$vitalicio): ?>
                             <div class="col-md-4">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <input type="text"  autocomplete="new-password" autocomplete="new-password"
                                                class="form-control date_flatpicker <?php $__errorArgs = ['data_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model.defer="data_fim"
                                                placeholder="dd/mm/aaaa"
                                                autocomplete="off"
                                                readonly
                                                style="cursor: pointer;">
                                         <label><i class="fas fa-calendar-minus text-primary me-1"></i>Data Fim *</label>
                                     </div>
                                     <button type="button"
                                             class="btn  btn-sm border-0"
                                             wire:click="clearDataFim"
                                             title="Limpar Data Fim">
                                         <i class="fas fa-times text-primary"></i>
                                     </button>
                                 </div>
                                 <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['data_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                     <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                             </div>
                             <?php else: ?>
                             <div class="col-md-4">
                                 <div class="alert alert-info mb-2">
                                     <i class="fas fa-infinity me-2"></i>
                                     <small>Assinatura vitalícia - sem data de fim</small>
                                 </div>
                             </div>
                             <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                             <div class="col-md-4">
                                 <div class="input-group mb-2">
                                     <div class="form-floating flex-grow-1">
                                         <input type="text"  autocomplete="new-password" autocomplete="new-password" 
                                                class="form-control date_flatpicker <?php $__errorArgs = ['trial_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                wire:model.live="trial_fim"
                                                placeholder="dd/mm/aaaa"
                                                autocomplete="off"
                                                readonly
                                                style="cursor: pointer;">
                                         <label><i class="fas fa-clock text-primary me-1"></i>Trial Fim</label>
                                     </div>
                                     <button type="button"
                                             class="btn  btn-sm border-0"
                                             wire:click="clearTrialFim"
                                             title="Limpar Trial Fim">
                                         <i class="fas fa-times text-primary"></i>
                                     </button>
                                 </div>
                                 <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['trial_fim'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                     <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                             </div>

                             <!-- Duração e Vitalício -->
                             <div class="col-md-6">
                                 <div class="form-floating mb-2">
                                     <input type="number" class="form-control <?php $__errorArgs = ['duracao_meses_custom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="duracao_meses_custom" placeholder="Calculado automaticamente" min="0" readonly>
                                     <label><i class="fas fa-calendar-alt text-primary me-1"></i>Duração (meses)</label>
                                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['duracao_meses_custom'];
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

                             <div class="col-md-6 d-flex align-items-center">
                                 <div class="form-check mb-2">
                                     <input class="form-check-input <?php $__errorArgs = ['vitalicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            type="checkbox" wire:model.live="vitalicio" id="vitalicio"
                                            style="width: 1.2rem; height: 1.2rem; margin-right: 0.5rem;">
                                     <label class="form-check-label fw-bold" for="vitalicio">
                                         <i class="fas fa-infinity text-primary me-2"></i>Assinatura Vitalícia
                                     </label>
                                     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['vitalicio'];
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

                             <!-- Mensagem explicativa compacta -->
                             <!--[if BLOCK]><![endif]--><?php if(!$igreja_id): ?>
                                 <div class="col-12">
                                     <small class="text-muted">
                                         <i class="fas fa-info-circle me-1"></i>
                                         Selecione uma igreja primeiro
                                     </small>
                                 </div>
                             <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                         </div>
                     </form>
                 </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveAssinatura" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAssinatura">
                            <i class="fas fa-save me-1"></i><?php echo e($editingAssinatura ? 'Atualizar Assinatura' : 'Salvar Assinatura'); ?>

                        </span>
                        <span wire:loading wire:target="saveAssinatura">
                            <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingAssinatura ? 'Atualizando...' : 'Salvando...'); ?>

                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>


</div>

<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/assinaturas-atuais.blade.php ENDPATH**/ ?>