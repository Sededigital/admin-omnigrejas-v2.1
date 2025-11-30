<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Notificações de Assinaturas</h1>
                            <p>Gerencie as notificações relacionadas às assinaturas das igrejas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#notificacaoModal">
                                <i class="fas fa-plus me-2"></i>
                                Nova Notificação
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar notificações..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="tipoFilter">
                                <option value="">Todos os tipos</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tipoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Todos os status</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
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
                    <h4 class="card-title">Notificações (<?php echo e($notificacoes->total()); ?>)</h4>
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
                                    <th>Tipo</th>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Enviada em</th>
                                    <th>Lida em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $notificacoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notificacao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($notificacao->assinatura->igreja->nome ?? 'I', 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($notificacao->assinatura->igreja->nome ?? 'Igreja'); ?></h6>
                                                <small class="text-muted"><?php echo e($notificacao->assinatura->igreja->nif ?? '-'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $tipoClass = match($notificacao->tipo) {
                                                'lembrete' => 'info',
                                                'atraso' => 'warning',
                                                'cancelamento' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($tipoClass); ?>"><?php echo e($tipoOptions[$notificacao->tipo] ?? $notificacao->tipo); ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo e($notificacao->titulo); ?></strong>
                                            <!--[if BLOCK]><![endif]--><?php if($notificacao->mensagem): ?>
                                                <br><small class="text-muted"><?php echo e(Str::limit($notificacao->mensagem, 50)); ?></small>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $statusClass = match($notificacao->status) {
                                                'enviada' => 'primary',
                                                'lida' => 'success',
                                                'ignorada' => 'secondary',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($statusOptions[$notificacao->status] ?? $notificacao->status); ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($notificacao->enviada_em ? $notificacao->enviada_em->format('d/m/Y H:i') : '-'); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($notificacao->lida_em ? $notificacao->lida_em->format('d/m/Y H:i') : '-'); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal(<?php echo e($notificacao->id); ?>)" data-bs-toggle="modal" data-bs-target="#notificacaoModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <!--[if BLOCK]><![endif]--><?php if($notificacao->status === 'enviada'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" wire:click="marcarComoLida(<?php echo e($notificacao->id); ?>)" title="Marcar como Lida">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" wire:click="marcarComoIgnorada(<?php echo e($notificacao->id); ?>)" title="Marcar como Ignorada">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteNotificacao(<?php echo e($notificacao->id); ?>)" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir esta notificação?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-bell fa-2x mb-2"></i>
                                            <p>Nenhuma notificação encontrada.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($notificacoes->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($notificacoes->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>

        
        <script src="<?php echo e(asset('system/js/assignatures.js')); ?>"></script>

    </div>

    
    <div class="modal fade" id="notificacaoModal" tabindex="-1" aria-labelledby="notificacaoModalLabel" aria-hidden="true"
          data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="notificacaoModalLabel">
                        <i class="fas fa-bell text-primary me-2"></i>
                        <span id="modal-title"><?php echo e($editingNotificacao ? 'Editar Notificação' : 'Nova Notificação'); ?></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveNotificacao">

                        <!-- Seleção da Assinatura -->
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
                                            wire:model="assinatura_id">
                                        <option value="">Selecione uma assinatura</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $assinaturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assinatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($assinatura->id); ?>">
                                                <?php echo e($assinatura->igreja->nome ?? 'Igreja'); ?> - <?php echo e($assinatura->pacote->nome ?? 'Pacote'); ?> (<?php echo e(number_format($assinatura->valor, 2, ',', '.')); ?> Kz)
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

                            <!-- Tipo -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            wire:model="tipo">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tipoOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                    <label><i class="fas fa-tag text-primary me-1"></i>Tipo *</label>
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
                            </div>

                            <!-- Status -->
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

                            <!-- Título -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           wire:model="titulo" placeholder="Título da notificação" required>
                                    <label><i class="fas fa-heading text-primary me-1"></i>Título *</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['titulo'];
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

                            <!-- Mensagem -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control <?php $__errorArgs = ['mensagem'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                              wire:model="mensagem" rows="4"
                                              placeholder="Mensagem da notificação"></textarea>
                                    <label><i class="fas fa-comment text-primary me-1"></i>Mensagem</label>
                                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['mensagem'];
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
                                        <?php echo e($editingNotificacao ? 'Editando Notificação' : 'Nova Notificação'); ?>

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
                    <button type="button" class="btn btn-primary" wire:click="saveNotificacao" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveNotificacao">
                            <i class="fas fa-save me-1"></i><?php echo e($editingNotificacao ? 'Atualizar Notificação' : 'Salvar Notificação'); ?>

                        </span>
                        <span wire:loading wire:target="saveNotificacao">
                            <i class="fas fa-spinner fa-spin me-1"></i><?php echo e($editingNotificacao ? 'Atualizando...' : 'Salvando...'); ?>

                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/notificacoes.blade.php ENDPATH**/ ?>