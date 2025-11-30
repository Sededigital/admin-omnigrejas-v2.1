<div>
    
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Gerenciar Solicitações de Trial</h1>
                            <p>Aprove ou rejeite solicitações de período de teste</p>
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
                                <input type="text" autocomplete="new-password" class="form-control" placeholder="Buscar solicitações..." wire:model.live.debounce.300ms="search">
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
                    <h4 class="card-title">Solicitações de Trial (<?php echo e($requests->total()); ?>)</h4>
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
                                    <th>Solicitante</th>
                                    <th>Igreja</th>
                                    <th>Status</th>
                                    <th>Data Solicitação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title"><?php echo e(substr($request->nome, 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($request->nome); ?></h6>
                                                <small class="text-muted"><?php echo e($request->email); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo e($request->igreja_nome); ?></strong><br>
                                            <small class="text-muted"><?php echo e($request->denominacao); ?></small>
                                            <!--[if BLOCK]><![endif]--><?php if($request->cidade): ?>
                                                <br><small class="text-muted"><?php echo e($request->cidade); ?>, <?php echo e($request->provincia); ?></small>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                            $statusClass = match($request->status) {
                                                'pendente' => 'warning',
                                                'aprovado' => 'success',
                                                'rejeitado' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>
                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($statusOptions[$request->status] ?? $request->status); ?></span>
                                        <!--[if BLOCK]><![endif]--><?php if($request->status === 'aprovado' && $request->aprovado_em): ?>
                                            <br><small class="text-muted">Em: <?php echo e($request->aprovado_em->format('d/m/Y')); ?></small>
                                        <?php elseif($request->status === 'rejeitado' && $request->rejeitado_em): ?>
                                            <br><small class="text-muted">Em: <?php echo e($request->rejeitado_em->format('d/m/Y')); ?></small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo e($request->created_at->format('d/m/Y H:i')); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!--[if BLOCK]><![endif]--><?php if($request->isPendente()): ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" wire:click="confirmarAprovacao(<?php echo e($request->id); ?>)" title="Aprovar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="confirmarRejeicao(<?php echo e($request->id); ?>)" title="Rejeitar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info" wire:click="showRequestDetails(<?php echo e($request->id); ?>)" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-info" wire:click="showRequestDetails(<?php echo e($request->id); ?>)" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>Nenhuma solicitação encontrada.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--[if BLOCK]><![endif]--><?php if($requests->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($requests->links()); ?>

                </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
          
    <script>
        /**
         * JavaScript para a página de Trial Requests (Solicitações de Trial)
         * Arquivo: trial-requests.js (inline)
         * Compatível com Livewire 3 e navegação SPA
         */

        // Instâncias globais dos modais SweetAlert - evitar redeclaração
        if (typeof window.trialRequestsModalInstances === 'undefined') {
            window.trialRequestsModalInstances = {
                confirmation: null,
                rejection: null,
                processing: null,
                details: null
            };
        }

        // Inicialização global - executada imediatamente quando o script carrega
        if (typeof window.initTrialRequestsPage === 'undefined') {
            window.initTrialRequestsPage = () => {
                // console.log('Trial Requests page JavaScript loaded');

                // Setup SweetAlert modal listeners
                const setupSweetAlertListeners = () => {
                    // Listeners já são configurados no livewire:init
                    // Esta função pode ser usada para configurações adicionais se necessário
                };

                // Executar todas as inicializações
                setupSweetAlertListeners();

                // console.log('All Trial Requests page JavaScript initialized successfully');
            };
        }

        // Event listeners para Livewire e navegação SPA
        document.addEventListener('livewire:loaded', () => {
            if (window.initTrialRequestsPage) window.initTrialRequestsPage();
        });
        document.addEventListener('livewire:navigated', () => {
            if (window.initTrialRequestsPage) window.initTrialRequestsPage();
        });
        document.addEventListener('DOMContentLoaded', () => {
            if (window.initTrialRequestsPage) window.initTrialRequestsPage();
        });

        // Cleanup para SPA navigation
        document.addEventListener('livewire:navigating', () => {
            // Limpar instâncias dos modais SweetAlert antes da navegação
            window.trialRequestsModalInstances.confirmation = null;
            window.trialRequestsModalInstances.rejection = null;
            window.trialRequestsModalInstances.processing = null;
            window.trialRequestsModalInstances.details = null;

            // Fechar qualquer modal SweetAlert aberto
            if (Swal.isVisible()) {
                Swal.close();
            }
        });

        document.addEventListener('livewire:init', () => {
            // Evento para confirmação de aprovação
            Livewire.on('confirmarAprovacao', (requestId) => {
                console.log('Evento confirmarAprovacao recebido:', requestId);
                Swal.fire({
                    title: 'Confirmar Aprovação',
                    text: 'Tem certeza que deseja aprovar esta solicitação de trial? O usuário receberá acesso imediato ao sistema.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, Aprovar',
                    cancelButtonText: 'Cancelar',
                    input: 'textarea',
                    inputPlaceholder: 'Observações (opcional)...',
                    inputAttributes: {
                        'aria-label': 'Observações sobre a aprovação'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const observacoes = result.value || '';
                        console.log('Enviando aprovarRequest:', { requestId: requestId, observacoes: observacoes });

                        // Fechar modal atual e abrir modal de processamento
                        Swal.close();

                        // Modal de processamento
                        Swal.fire({
                            title: 'Processando Aprovação...',
                            html: `
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-spinner fa-spin fa-4x text-success"></i>
                                    </div>
                                    <p class="h4 fw-semibold text-body-emphasis mb-3">Aprovando solicitação de trial</p>
                                    <p class="text-muted">Aguarde enquanto criamos o trial e enviamos o email de acesso...</p>
                                </div>
                            `,
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'swal-wide-modal'
                            }
                        });

                        Livewire.dispatch('aprovarRequest', requestId, observacoes);
                    }
                });
            });

            // Evento para confirmação de rejeição
            Livewire.on('confirmarRejeicao', (requestId) => {
                console.log('Evento confirmarRejeicao recebido:', requestId);
                Swal.fire({
                    title: 'Confirmar Rejeição',
                    text: 'Tem certeza que deseja rejeitar esta solicitação de trial?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, Rejeitar',
                    cancelButtonText: 'Cancelar',
                    input: 'textarea',
                    inputPlaceholder: 'Motivo da rejeição (obrigatório)...',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'O motivo da rejeição é obrigatório!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const motivo = result.value;
                        console.log('Enviando rejeitarRequest:', { requestId: requestId, motivo: motivo });

                        // Fechar modal atual e abrir modal de processamento
                        Swal.close();

                        // Modal de processamento
                        Swal.fire({
                            title: 'Processando Rejeição...',
                            html: `
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-spinner fa-spin fa-4x text-danger"></i>
                                    </div>
                                    <p class="h4 fw-semibold text-body-emphasis mb-3">Rejeitando solicitação de trial</p>
                                    <p class="text-muted">Aguarde enquanto notificamos o solicitante...</p>
                                </div>
                            `,
                            showConfirmButton: false,
                            showCancelButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            customClass: {
                                popup: 'swal-wide-modal'
                            }
                        });

                        Livewire.dispatch('rejeitarRequest', requestId, motivo);
                    }
                });
            });

            // Evento de sucesso da aprovação
            Livewire.on('trial-aprovacao-sucesso', (data) => {
                const trial = data[0];
                console.log(trial.dias_restantes);
                Swal.fire({
                    title: '<span class="fw-bold text-dark">Trial Aprovado com Sucesso</span>',
                    icon: 'success',
                    html: `
                        <div class="text-center">
                            <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-success-subtle mb-3 border border-success border-2">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>

                            <p class="h5 fw-semibold text-body-emphasis mb-2">Solicitação aprovada!</p>
                            <p class="text-muted small mb-4">O trial foi criado e o email de acesso foi enviado ao usuário.</p>

                            <div class="row g-2 justify-content-center text-center">
                                <div class="col-6">
                                    <div class="border rounded-3 p-3 bg-light shadow-sm">
                                        <h6 class="text-primary mb-1 small fw-bold text-uppercase">Usuário</h6>
                                        <strong class="fs-6 text-dark">${trial.user.name}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-3 p-3 bg-light shadow-sm">
                                        <h6 class="text-success mb-1 small fw-bold text-uppercase">Expira em</h6>
                                        <span class="badge bg-success fs-6 py-1 px-3 fw-bold">${trial.dias_restantes} dias</span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert mt-4 p-3 rounded-3 text-start border-success-subtle bg-success-subtle">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope me-3 text-success fs-4"></i>
                                    <div>
                                        <strong class="text-success">Email enviado:</strong>
                                        <div class="small text-dark">
                                            O usuário recebeu as credenciais de acesso por email.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    customClass: {
                        popup: 'swal2-responsive-modal shadow-lg',
                        confirmButton: 'btn btn-success btn-lg px-4 fw-bold'
                    },
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check me-2"></i> Entendido',
                    buttonsStyling: false,
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            });

            // Evento de sucesso da rejeição
            Livewire.on('trial-rejeicao-sucesso', (data) => {
                const request = data[0];

                Swal.fire({
                    title: '<span class="fw-bold text-dark">Solicitação Rejeitada</span>',
                    icon: 'info',
                    html: `
                        <div class="text-center">
                            <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-danger-subtle mb-3 border border-danger border-2">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>

                            <p class="h5 fw-semibold text-body-emphasis mb-2">Solicitação rejeitada!</p>
                            <p class="text-muted small mb-4">O solicitante foi notificado sobre a decisão.</p>

                            <div class="row g-2 justify-content-center text-center">
                                <div class="col-6">
                                    <div class="border rounded-3 p-3 bg-light shadow-sm">
                                        <h6 class="text-primary mb-1 small fw-bold text-uppercase">Solicitante</h6>
                                        <strong class="fs-6 text-dark">${request.nome}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-3 p-3 bg-light shadow-sm">
                                        <h6 class="text-danger mb-1 small fw-bold text-uppercase">Status</h6>
                                        <span class="badge bg-danger fs-6 py-1 px-3 fw-bold">Rejeitado</span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert mt-4 p-3 rounded-3 text-start border-danger-subtle bg-danger-subtle">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope me-3 text-danger fs-4"></i>
                                    <div>
                                        <strong class="text-danger">Email enviado:</strong>
                                        <div class="small text-dark">
                                            O solicitante recebeu a notificação de rejeição.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    customClass: {
                        popup: 'swal2-responsive-modal shadow-lg',
                        confirmButton: 'btn btn-primary btn-lg px-4 fw-bold'
                    },
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check me-2"></i> Entendido',
                    buttonsStyling: false,
                    backdrop: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            });

            // Evento para fechar modal de processamento em caso de erro
            Livewire.on('close-processing-modal', () => {
                Swal.close();
            });

            // Evento para mostrar detalhes da solicitação
            Livewire.on('showRequestDetails', (data) => {
                const request = data[0];

                Swal.fire({
                    title: '<span class="fw-bold text-dark">Detalhes da Solicitação</span>',
                    icon: 'info',
                    html: `
                        <div class="text-start">
                            <div class="row g-3">
                                <!-- Informações Pessoais -->
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="text-primary mb-3 fw-bold">
                                            <i class="fas fa-user me-2"></i>Informações Pessoais
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nome:</strong> ${request.nome}<br>
                                                <strong>Email:</strong> ${request.email}<br>
                                                <strong>Telefone:</strong> ${request.telefone || 'Não informado'}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Cidade:</strong> ${request.cidade || 'Não informado'}<br>
                                                <strong>Província:</strong> ${request.provincia || 'Não informado'}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações da Igreja -->
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="text-success mb-3 fw-bold">
                                            <i class="fas fa-church me-2"></i>Informações da Igreja
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nome da Igreja:</strong> ${request.igreja_nome}<br>
                                                <strong>Denominação:</strong> ${request.denominacao}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Período Solicitado:</strong> ${request.periodo_dias} dias
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status e Datas -->
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-light">
                                        <h6 class="text-warning mb-3 fw-bold">
                                            <i class="fas fa-clock me-2"></i>Status e Datas
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Status:</strong>
                                                <span class="badge bg-${request.status === 'pendente' ? 'warning' : request.status === 'aprovado' ? 'success' : 'danger'} ms-2">
                                                    ${request.status.charAt(0).toUpperCase() + request.status.slice(1)}
                                                </span><br>
                                                <strong>Data da Solicitação:</strong> ${new Date(request.created_at).toLocaleString('pt-BR')}
                                            </div>
                                            <div class="col-md-6">
                                                ${request.aprovado_em ? `<strong>Aprovado em:</strong> ${new Date(request.aprovado_em).toLocaleString('pt-BR')}<br>` : ''}
                                                ${request.rejeitado_em ? `<strong>Rejeitado em:</strong> ${new Date(request.rejeitado_em).toLocaleString('pt-BR')}<br>` : ''}
                                                ${request.motivo_rejeicao ? `<strong>Motivo da Rejeição:</strong> ${request.motivo_rejeicao}` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observações -->
                                ${request.observacoes ? `
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-info-subtle">
                                        <h6 class="text-info mb-2 fw-bold">
                                            <i class="fas fa-sticky-note me-2"></i>Observações
                                        </h6>
                                        <p class="mb-0 text-dark">${request.observacoes}</p>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `,
                    customClass: {
                        popup: 'swal2-wide-modal shadow-lg',
                        confirmButton: 'btn btn-primary btn-lg px-4 fw-bold'
                    },
                    showConfirmButton: true,
                    confirmButtonText: '<i class="fas fa-check me-2"></i> Fechar',
                    buttonsStyling: false,
                    backdrop: true,
                    allowOutsideClick: true,
                    allowEscapeKey: true,
                    width: '800px'
                });
            });
        });
    </script>

    <?php $__env->stopPush(); ?>
  
 
</div><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/billings/trial-requests.blade.php ENDPATH**/ ?>