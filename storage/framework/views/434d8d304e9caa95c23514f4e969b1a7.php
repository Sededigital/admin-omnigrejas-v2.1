<div class="container-fluid py-1">
    <!-- Hero Section -->
    <div class="card bg-gradient-hero text-white border-0 shadow-lg mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-receipt me-3"></i>
                        Status dos Pagamentos
                    </h1>
                    <p class="lead mb-4">
                        Acompanhe o status dos seus pagamentos de assinatura e veja quando sua assinatura será ativada.
                    </p>

                    <!-- Estatísticas Rápidas -->
                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h4 mb-1"><?php echo e($estatisticas['total']); ?></div>
                            <div class="small opacity-75">Total de Pagamentos</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1 text-warning"><?php echo e($estatisticas['pendentes']); ?></div>
                            <div class="small opacity-75">Pendentes</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1 text-success"><?php echo e($estatisticas['confirmados']); ?></div>
                            <div class="small opacity-75">Confirmados</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1"><?php echo e(number_format($estatisticas['valor_total'], 2, ',', '.')); ?> AOA</div>
                            <div class="small opacity-75">Valor Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <i class="fas fa-chart-line fa-5x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <!-- Seleção de Igreja (se múltiplas) -->
                <!--[if BLOCK]><![endif]--><?php if($igrejasDisponiveis->count() > 1): ?>
                    <div class="col-lg-3">
                        <label class="form-label fw-semibold">Igreja</label>
                        <select class="form-select" wire:model.live="igrejaSelecionada">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $igrejasDisponiveis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $igreja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($igreja['id']); ?>">
                                    <?php echo e($igreja['nome']); ?>

                                    <!--[if BLOCK]><![endif]--><?php if($igreja['sigla']): ?> (<?php echo e($igreja['sigla']); ?>) <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Busca -->
                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                           placeholder="Referência, pacote...">
                </div>

                <!-- Filtro Status -->
                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="rejeitado">Rejeitado</option>
                        <option value="expirado">Expirado</option>
                    </select>
                </div>

                <!-- Filtro Método -->
                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Método</label>
                    <select class="form-select" wire:model.live="metodoFilter">
                        <option value="">Todos</option>
                        <option value="deposito">Depósito</option>
                        <option value="multicaixa_express">Multicaixa</option>
                        <option value="transferencia">Transferência</option>
                    </select>
                </div>

                <!-- Itens por página -->
                <div class="col-lg-2">
                    <label class="form-label fw-semibold">Mostrar</label>
                    <select class="form-select" wire:model.live="perPage">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Pagamentos -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <!--[if BLOCK]><![endif]--><?php if($pagamentos->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">Data</th>
                                <th class="border-0 fw-semibold">Pacote</th>
                                <th class="border-0 fw-semibold">Valor</th>
                                <th class="border-0 fw-semibold">Método</th>
                                <th class="border-0 fw-semibold">Status</th>
                                <th class="border-0 fw-semibold text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pagamentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($pagamento->data_pagamento->format('d/m/Y')); ?></div>
                                        <small class="text-muted"><?php echo e($pagamento->data_pagamento->format('H:i')); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?php echo e($pagamento->pacote_nome ?: $pagamento->pacote->nome); ?></div>
                                        <small class="text-muted">
                                            <!--[if BLOCK]><![endif]--><?php if($pagamento->is_vitalicio): ?>
                                                Vitalício
                                            <?php elseif($pagamento->duracao_meses): ?>
                                                <?php echo e($pagamento->duracao_meses); ?> <?php echo e($pagamento->duracao_meses == 1 ? 'mês' : 'meses'); ?>

                                            <?php else: ?>
                                                <?php echo e($pagamento->pacote->duracao_meses); ?> <?php echo e($pagamento->pacote->duracao_meses == 1 ? 'mês' : 'meses'); ?>

                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary"><?php echo e($pagamento->getValorFormatado()); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas <?php echo e($pagamento->metodo_pagamento === 'deposito' ? 'fa-university' : ($pagamento->metodo_pagamento === 'multicaixa_express' ? 'fa-mobile-alt' : 'fa-exchange-alt')); ?> me-1"></i>
                                            <?php echo e($pagamento->getMetodoFormatado()); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($pagamento->getStatusBadgeClass()); ?> fs-6">
                                            <?php echo e($pagamento->getStatusFormatado()); ?>

                                        </span>
                                        <!--[if BLOCK]><![endif]--><?php if($pagamento->isConfirmado() && $pagamento->data_confirmacao): ?>
                                            <br><small class="text-muted"><?php echo e($pagamento->getDataConfirmacaoFormatada()); ?></small>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                 
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary"
                                                onclick="verDetalhesPagamento(<?php echo e($pagamento->id); ?>, '<?php echo e($pagamento->referencia); ?>', '<?php echo e($pagamento->pacote_nome ?: $pagamento->pacote->nome); ?>', '<?php echo e($pagamento->getValorFormatado()); ?>', '<?php echo e($pagamento->getMetodoFormatado()); ?>', '<?php echo e($pagamento->getStatusFormatado()); ?>')"
                                                title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <!--[if BLOCK]><![endif]--><?php if($pagamento->temComprovativo()): ?>
                                            <button class="btn btn-sm btn-outline-secondary ms-1"
                                                    onclick="verComprovativo('<?php echo e($pagamento->comprovativo_url); ?>', '<?php echo e($pagamento->comprovativo_tipo); ?>')"
                                                    title="Ver comprovativo">
                                                <i class="fas fa-file-<?php echo e($pagamento->getComprovativoIcone() === 'fas fa-file-pdf' ? 'pdf' : 'image'); ?>"></i>
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-body border-top">
                    <?php echo e($pagamentos->links()); ?>

                </div>
            <?php else: ?>
                <!-- Estado Vazio -->
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Nenhum pagamento encontrado</h4>
                    <p class="text-muted">Você ainda não fez nenhum pagamento de assinatura para esta igreja.</p>
                    <a href="<?php echo e(route('ecommerce.subscription.upgrade')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Fazer Assinatura
                    </a>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    
    </div>
    
      
    
</div>

<script>
    // Função para ver detalhes do pagamento via SweetAlert2
    function verDetalhesPagamento(id, referencia, pacote, valor, metodo, status) {
        Swal.fire({
            title: '<i class="fas fa-receipt text-primary me-2"></i>Detalhes do Pagamento',
            html: `
                <div class="text-center">
                    <div class="card border-0 bg-light mb-0">
                        <div class="card-body p-4">
                            <!-- Pacote e Valor -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-box text-primary fa-2x mb-2"></i>
                                        <h6 class="text-primary fw-bold mb-1">Pacote</h6>
                                        <p class="mb-0 fw-semibold">${pacote}</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-money-bill-wave text-success fa-2x mb-2"></i>
                                        <h6 class="text-success fw-bold mb-1">Valor</h6>
                                        <p class="mb-0 fw-bold text-success">${valor}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Método e Status -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-credit-card text-info fa-2x mb-2"></i>
                                        <h6 class="text-info fw-bold mb-1">Método</h6>
                                        <p class="mb-0">${metodo}</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-info-circle text-${status === 'Confirmado' ? 'success' : status === 'Pendente' ? 'warning' : 'secondary'} fa-2x mb-2"></i>
                                        <h6 class="text-${status === 'Confirmado' ? 'success' : status === 'Pendente' ? 'warning' : 'secondary'} fw-bold mb-1">Status</h6>
                                        <span class="badge bg-${status === 'Confirmado' ? 'success' : status === 'Pendente' ? 'warning' : 'secondary'} fs-6 px-3 py-2">${status}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Referência -->
                            <div class="text-center">
                                <i class="fas fa-hashtag text-muted fa-lg mb-2"></i>
                                <h6 class="text-muted fw-bold mb-2">Referência</h6>
                                <div class="bg-white rounded p-2 border">
                                    <code class="text-dark small">${referencia}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: '<i class="fas fa-times me-2"></i>Fechar',
            customClass: {
                popup: 'swal-wide-modal',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        });
    }

    // Função para visualizar comprovativo
    function verComprovativo(url, tipo) {
        // Sempre mostrar no SweetAlert2 para maior segurança
        Swal.fire({
            title: '<i class="fas fa-file text-primary me-2"></i>Comprovativo',
            html: `
                <div class="text-center">
                    ${tipo.includes('pdf')
                        ? `<iframe src="${url}" width="100%" height="400px" style="border: none; border-radius: 8px;"></iframe>`
                        : `<img src="${url}" class="img-fluid rounded shadow" style="max-height: 400px;" alt="Comprovativo">`
                    }
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'swal-wide-modal'
            },
            showClass: {
                popup: 'animate__animated animate__zoomIn animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            }
        });
    }
</script><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/ecommerce/payment-assignature-churche.blade.php ENDPATH**/ ?>