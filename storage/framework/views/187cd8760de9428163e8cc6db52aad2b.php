<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-users me-2"></i>Gestão de Membros
                        </h1>
                        <p class="mb-0 text-muted">Gerencie todos os membros da igreja</p>
                    </div>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-members')): ?>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex gap-2 justify-content-end flex-nowrap">
                            <button class="btn btn-outline-<?php echo e($envioCredenciaisAtivado ? 'success' : 'secondary'); ?> btn-sm btn-toggle-credentials"
                                    wire:click="toggleEnvioCredenciais"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="btn-loading"
                                    title="<?php echo e($envioCredenciaisAtivado ? 'Desativar envio de credenciais' : 'Ativar envio de credenciais'); ?>">
                                <span wire:loading.remove wire:target="toggleEnvioCredenciais">
                                    <i class="fas fa-<?php echo e($envioCredenciaisAtivado ? 'lock-open' : 'lock'); ?>"></i>
                                </span>
                                <span wire:loading wire:target="toggleEnvioCredenciais">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            <button class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#memberModal">
                                <i class="fas fa-user-plus me-1"></i>Adicionar Membro
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>


        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary"><?php echo e($stats['total']); ?></div>
                        <div class="text-muted small">Total de Membros</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-check text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success"><?php echo e($stats['active']); ?></div>
                        <div class="text-muted small">Membros Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-times text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning"><?php echo e($stats['inactive']); ?></div>
                        <div class="text-muted small">Membros Inativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-plus text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info"><?php echo e($stats['new_this_month']); ?></div>
                        <div class="text-muted small">Novos (Este Mês)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Ministério e Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Membro</label>
                        <div class="input-group">

                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome ou email">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Ministério</label>
                        <select class="form-select" wire:model.live="selectedMinistry">
                            <option value="">Todos os ministérios</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ministerios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ministerio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ministerio->id); ?>"><?php echo e($ministerio->nome); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos os status</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="falecido">Falecido</option>
                            <option value="transferido">Transferido</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botões de Filtro Rápido -->
                <div class="row g-2 mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm" wire:click="setMinistryFilter('')">
                                <i class="fas fa-users me-1"></i>Todos
                            </button>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ministerios->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ministerio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button class="btn btn-outline-info btn-sm" wire:click="setMinistryFilter(<?php echo e($ministerio->id); ?>)">
                                    <i class="fas fa-church me-1"></i><?php echo e($ministerio->nome); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Membros
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Membro</th>
                                <th>Cargo</th>
                                <th>Gênero</th>
                                <th>Data Entrada</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-members')): ?>
                                <th class="text-center">Ações</th>
                                <?php endif; ?>

                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!--[if BLOCK]><![endif]--><?php if($member->user->photo_url): ?>
                                            <img src="<?php echo e(Storage::disk('supabase')->url($member->user->photo_url)); ?>"
                                            class="me-3 rounded-circle border"
                                            alt="Logo <?php echo e($member->user->name); ?>"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="user-avatar bg-primary text-white me-3">
                                                <?php echo e(strtoupper(substr($member->user->name ?? 'N', 0, 2))); ?>

                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <div>
                                            <div class="fw-semibold">
                                                <?php echo e($member->user->name ?? 'N/A'); ?>

                                                <button class="btn btn-link btn-sm p-0 ms-1 text-decoration-none btn-generate-pdf-<?php echo e($member->id); ?>"
                                                        wire:click="generateMemberCard('<?php echo e($member->id); ?>')"
                                                        wire:loading.attr="disabled"
                                                        wire:loading.class="btn-loading"
                                                        title="Gerar Ficha de Membro">
                                                    <span wire:loading.remove wire:target="generateMemberCard('<?php echo e($member->id); ?>')">
                                                        <i class="fas fa-file-pdf text-danger"></i>
                                                    </span>
                                                    <span wire:loading wire:target="generateMemberCard('<?php echo e($member->id); ?>')">
                                                        <i class="fas fa-spinner fa-spin text-danger" style="font-size: 0.875rem;"></i>
                                                    </span>
                                                </button>
                                            </div>
                                            <small class="text-muted"><?php echo e($member->user->email ?? 'N/A'); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo e(Str::ucfirst($member->cargo) ?? 'N/A'); ?></td>
                                <td><?php echo e(Str::ucfirst($member->membroPerfil?->genero) ?? 'N/A'); ?></td>
                                <td><?php echo e($member->data_entrada ? $member->data_entrada->format('d/m/Y') : 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($this->getStatusBadgeClass($member->status)); ?>">
                                        <?php echo e($this->getStatusLabel($member->status)); ?>

                                    </span>
                                </td>
                                <td>
                                    <div><?php echo e($member->created_at->format('d/m/Y')); ?></div>
                                    <small class="text-muted"><?php echo e($member->created_at->diffForHumans()); ?></small>
                                </td>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-members')): ?>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('<?php echo e($member->id); ?>')" data-bs-toggle="modal" data-bs-target="#memberModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-success" wire:click="openMinistryModal('<?php echo e($member->id); ?>')" data-bs-toggle="modal" data-bs-target="#ministryModal" title="Adicionar Ministério">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-send-credentials-<?php echo e($member->id); ?>"
                                                wire:click="enviarCredenciais('<?php echo e($member->id); ?>')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="btn-loading"
                                                <?php if(!$envioCredenciaisAtivado): ?> disabled <?php endif; ?>
                                                title="<?php echo e($envioCredenciaisAtivado ? 'Enviar Credenciais' : 'Envio desativado'); ?>">
                                            <span wire:loading.remove wire:target=".btn-send-credentials-<?php echo e($member->id); ?>">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <span wire:loading wire:target=".btn-send-credentials-<?php echo e($member->id); ?>">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                        <button class="btn btn-outline-<?php echo e($member->status === 'ativo' ? 'warning' : 'success'); ?> btn-toggle-status-<?php echo e($member->id); ?>"
                                                wire:click="toggleMemberStatus('<?php echo e($member->id); ?>')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="btn-loading"
                                                title="<?php echo e($member->status === 'ativo' ? 'Desativar' : 'Ativar'); ?>">
                                            <span wire:loading.remove wire:target="toggleMemberStatus('<?php echo e($member->id); ?>')">
                                                <i class="fas fa-<?php echo e($member->status === 'ativo' ? 'user-times' : 'user-check'); ?>"></i>
                                            </span>
                                            <span wire:loading wire:target="toggleMemberStatus('<?php echo e($member->id); ?>')">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                        <button class="btn btn-outline-danger btn-delete-member-<?php echo e($member->id); ?>"
                                                wire:click="openDeleteModal('<?php echo e($member->id); ?>')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="btn-loading"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteMemberModal"
                                                title="Excluir Membro">
                                            <span wire:loading.remove wire:target="openDeleteModal('<?php echo e($member->id); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </span>
                                            <span wire:loading wire:target="openDeleteModal('<?php echo e($member->id); ?>')">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                    </div>
                                </td>
                                <?php endif; ?>

                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-users text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum membro encontrado</div>
                                </td>
                            </tr>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando <?php echo e($members->firstItem()); ?>-<?php echo e($members->lastItem()); ?> de <?php echo e($members->total()); ?> registros</span>
                        <nav aria-label="Paginação">
                            <?php echo e($members->links()); ?>

                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <!--[if BLOCK]><![endif]--><?php if($member->user->photo_url): ?>
                                        <img src="<?php echo e(Storage::disk('supabase')->url($member->user->photo_url)); ?>"
                                        class="me-3 rounded-circle border"
                                        alt="Logo <?php echo e($member->user->name); ?>"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="user-avatar bg-primary text-white me-3">
                                            <?php echo e(strtoupper(substr($member->user->name ?? 'N', 0, 2))); ?>

                                        </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                    <div>
                                        <h6 class="card-title mb-1">
                                            <?php echo e($member->user->name ?? 'N/A'); ?>

                                            <button class="btn btn-link btn-sm p-0 ms-1 text-decoration-none btn-generate-pdf-mobile-<?php echo e($member->id); ?>"
                                                    wire:click="generateMemberCard('<?php echo e($member->id); ?>')"
                                                    wire:loading.attr="disabled"
                                                    wire:loading.class="btn-loading"
                                                    title="Gerar Ficha de Membro">
                                                <span wire:loading.remove wire:target=".btn-generate-pdf-mobile-<?php echo e($member->id); ?>">
                                                    <i class="fas fa-file-pdf text-danger"></i>
                                                </span>
                                                <span wire:loading wire:target=".btn-generate-pdf-mobile-<?php echo e($member->id); ?>">
                                                    <i class="fas fa-spinner fa-spin text-danger" style="font-size: 0.875rem;"></i>
                                                </span>
                                            </button>
                                        </h6>
                                        <span class="badge bg-<?php echo e($this->getStatusBadgeClass($member->status)); ?>">
                                            <?php echo e($this->getStatusLabel($member->status)); ?>

                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?php echo e($member->status === 'ativo' ? 'success' : 'secondary'); ?> mb-2">
                                        <?php echo e($member->status === 'ativo' ? 'Ativo' : 'Inativo'); ?>

                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-envelope text-muted me-1"></i>
                                <small class="text-muted"><?php echo e($member->user->email ?? 'N/A'); ?></small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user-tag text-muted me-1"></i>
                                <small class="text-muted"><?php echo e($member->cargo ?? 'N/A'); ?></small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-venus-mars text-muted me-1"></i>
                                <small class="text-muted"><?php echo e($member->membroPerfil->genero ?? 'N/A'); ?></small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted"><?php echo e($member->data_entrada ? $member->data_entrada->format('d/m/Y') : 'N/A'); ?></small>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-members')): ?>
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-primary btn-sm flex-fill" wire:click="openModal('<?php echo e($member->id); ?>')" data-bs-toggle="modal" data-bs-target="#memberModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-info btn-sm btn-send-credentials-mobile-<?php echo e($member->id); ?>"
                                        wire:click="enviarCredenciais(<?php echo e($member->id); ?>)"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="btn-loading"
                                        <?php if(!$envioCredenciaisAtivado): ?> disabled <?php endif; ?>>
                                    <span wire:loading.remove wire:target=".btn-send-credentials-mobile-<?php echo e($member->id); ?>">
                                        <i class="fas fa-envelope me-1"></i>Credenciais
                                    </span>
                                    <span wire:loading wire:target=".btn-send-credentials-mobile-<?php echo e($member->id); ?>">
                                        <i class="fas fa-spinner fa-spin me-1"></i>Enviando...
                                    </span>
                                </button>
                                <button class="btn btn-outline-danger btn-sm btn-delete-member-mobile-<?php echo e($member->id); ?>"
                                        wire:click="openDeleteModal(<?php echo e($member->id); ?>)"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="btn-loading"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteMemberModal">
                                    <span wire:loading.remove wire:target=".btn-delete-member-mobile-<?php echo e($member->id); ?>">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                    <span wire:loading wire:target=".btn-delete-member-mobile-<?php echo e($member->id); ?>">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </button>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-users text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum membro encontrado</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <!-- Paginação Mobile -->
            <!--[if BLOCK]><![endif]--><?php if($members->hasPages()): ?>
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    <?php echo e($members->links()); ?>

                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando <?php echo e($members->firstItem()); ?>-<?php echo e($members->lastItem()); ?> de <?php echo e($members->total()); ?> registros</small>
                </div>
            </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>



        <!-- Scripts para Members -->
        <script src="<?php echo e(asset('system/js/members.js')); ?>" data-navigate-once></script>

        
        <?php echo $__env->make('church.members.modals.member-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('church.members.modals.ministry-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('church.members.modals.delete-member-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Estilos para o spinner -->
        <style>
            .btn-loading {
                opacity: 0.6;
                pointer-events: none;
            }
            .fa-spin {
                animation: fa-spin 1s infinite linear;
            }
            @keyframes fa-spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/church/members/members.blade.php ENDPATH**/ ?>