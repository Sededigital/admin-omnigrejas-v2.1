<div>
    <div class="container-fluid p-4">
        <!-- Header (Mantido) -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-credit-card me-2"></i>Assinatura e Cobrança
                        </h1>
                        <p class="mb-0 text-muted">Gerencie sua assinatura e visualize informações de cobrança</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <!--[if BLOCK]><![endif]--><?php if($assinaturaAtual && $assinaturaAtual->status === 'Ativo'): ?>
                            <!-- Renovar (Ação Primária) -->
                            <button class="btn btn-success btn-md me-2" wire:click="renovarAssinatura">
                                <i class="fas fa-sync-alt me-2"></i>Renovar
                            </button>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>
        </div>

        <!--[if BLOCK]><![endif]--><?php if($assinaturaAtual): ?>
        <!-- =================================================================== -->
        <!-- ==================   NOVO LAYOUT: DASHBOARD   ===================== -->
        <!-- =================================================================== -->
        <div class="row g-4">
            
            <!-- ======================= COLUNA PRINCIPAL (Conteúdo) ======================= -->
            <div class="col-lg-8">
                
                <!-- Card do Plano (Design Premium) - INTOCADO -->
                <div class="card card-plano shadow-lg border-0 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="row align-items-center">
                            <div class="col-md-8 text-white">
                                <h5 class="text-white-50">Seu Plano Atual</h5>
                                <h2 class="h1 fw-bold text-white mb-3"><?php echo e($pacoteAtual->nome); ?></h2>
                                <p class="text-white-75 mb-4"><?php echo e($pacoteAtual->descricao); ?></p>

                                <div class="d-flex flex-wrap gap-4">
                                    <div>
                                        <small class="text-white-50 d-block">Valor Mensal</small>
                                        <span class="fw-bold fs-5 text-white">Kz <?php echo e(number_format($pacoteAtual->preco, 2, ',', '.')); ?></span>
                                    </div>
                                    <div>
                                        <small class="text-white-50 d-block">Duração</small>
                                        <span class="fw-bold fs-5 text-white"><?php echo e($pacoteAtual->assinaturasAtuais()->first()->duracao_meses_custom); ?> meses</span>
                                    </div>
                                    <!--[if BLOCK]><![endif]--><?php if($pacoteAtual->preco_vitalicio): ?>
                                    <div>
                                        <small class="text-white-50 d-block">Preço Vitalício</small>
                                        <span class="fw-bold fs-5 text-white">Kz <?php echo e(number_format($pacoteAtual->preco_vitalicio, 2, ',', '.')); ?></span>
                                    </div>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div class="col-md-4 text-center d-none d-md-block">
                                <i class="fas fa-gem fa-7x text-white opacity-25" style="transform: rotate(-15deg);"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico (Linha do Tempo) - INTOCADO -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs-bold nav-tabs-primary card-header-tabs" id="historyTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="true">
                                    <i class="fas fa-history m-2"></i>Histórico de Pagamentos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="subscriptions-tab" data-bs-toggle="tab" data-bs-target="#subscriptions" type="button" role="tab" aria-controls="subscriptions" aria-selected="false">
                                     <i class="fas fa-list m-2"></i>Histórico de Assinaturas
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body mt-2">
                        <!-- Tab panes -->
                        <div class="tab-content" id="historyTabsContent">
                            
                            <!-- Painel de Pagamentos (Mantido) -->
                            <div class="tab-pane fade show active" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                                <!-- Estatísticas Integradas -->
                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center bg-light p-3 rounded">
                                            <i class="fas fa-receipt text-primary fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold h5 mb-0 text-primary"><?php echo e($estatisticas['total_pagamentos']); ?></div>
                                                <div class="text-muted small">Total de Pagamentos</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center bg-light p-3 rounded">
                                            <i class="fas fa-money-bill-wave text-success fs-3 me-3"></i>
                                            <div>
                                                <div class="fw-bold h5 mb-0 text-success">Kz <?php echo e(number_format($estatisticas['valor_total_pago'], 2, ',', '.')); ?></div>
                                                <div class="text-muted small">Valor Total Pago</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Lista de Atividades (Mantido) -->
                                <!--[if BLOCK]><![endif]--><?php if($pagamentosRecentes->count() > 0): ?>
                                <ul class="list-group list-group-flush history-list">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pagamentosRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagamento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item px-1 py-3">
                                        <div class="d-flex w-100">
                                            <div class="flex-shrink-0 me-3">
                                                <span class="history-icon bg-<?php echo e($pagamento->status === 'confirmado' ? 'success' : ($pagamento->status === 'pendente' ? 'warning' : 'danger')); ?>-subtle text-<?php echo e($pagamento->status === 'confirmado' ? 'success' : ($pagamento->status === 'pendente' ? 'warning' : 'danger')); ?>">
                                                    <i class="fas fa-<?php echo e($pagamento->status === 'confirmado' ? 'check' : ($pagamento->status === 'pendente' ? 'hourglass-half' : 'times')); ?>"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-semibold">Pagamento <?php echo e($pagamento->status); ?></span>
                                                    <span class="fw-bold text-success">Kz <?php echo e(number_format($pagamento->valor, 2, ',', '.')); ?></span>
                                                </div>
                                                <small class="text-muted">Método: <?php echo e($pagamento->metodo_pagamento); ?> | Ref: <?php echo e($pagamento->referencia ?: 'N/A'); ?></small>
                                            </div>
                                            <div class="ms-3 text-end text-nowrap">
                                                <small class="text-muted"><?php echo e($pagamento->data_pagamento->format('d/m/Y')); ?><br><?php echo e($pagamento->data_pagamento->format('H:i')); ?></small>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </ul>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-receipt text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum pagamento encontrado</div>
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                            <!-- Painel de Assinaturas (Mantido) -->
                            <div class="tab-pane fade" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
                                <!--[if BLOCK]><![endif]--><?php if($assinaturasHistorico && $assinaturasHistorico->count() > 0): ?>
                                <ul class="list-group list-group-flush history-list">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $assinaturasHistorico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assinatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item px-1 py-3">
                                        <div class="d-flex w-100">
                                            <div class="flex-shrink-0 me-3">
                                                <span class="history-icon bg-<?php echo e($assinatura->status === 'Ativo' ? 'success' : ($assinatura->status === 'Cancelado' ? 'danger' : 'warning')); ?>-subtle text-<?php echo e($assinatura->status === 'Ativo' ? 'success' : ($assinatura->status === 'Cancelado' ? 'danger' : 'warning')); ?>">
                                                    <i class="fas fa-<?php echo e($assinatura->status === 'Ativo' ? 'star' : ($assinatura->status === 'Cancelado' ? 'ban' : 'pause-circle')); ?>"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <span class="fw-semibold"><?php echo e($assinatura->pacote->nome ?? 'N/A'); ?> (<?php echo e($assinatura->status); ?>)</span>
                                                    <span class="fw-bold text-success">Kz <?php echo e(number_format($assinatura->valor, 2, ',', '.')); ?></span>
                                                </div>
                                                <small class="text-muted">
                                                    <?php echo e($assinatura->data_inicio->format('d/m/Y')); ?>

                                                    <!--[if BLOCK]><![endif]--><?php if(!$assinatura->vitalicio): ?>
                                                    - <?php echo e($assinatura->data_fim->format('d/m/Y')); ?>

                                                    <?php else: ?>
                                                    (Vitalícia)
                                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                </small>
                                            </div>
                                            <div class="ms-3 text-end text-nowrap">
                                                <small class="text-muted">Inscrito em<br><?php echo e($assinatura->created_at->format('d/m/Y')); ?></small>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </ul>
                                <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-list text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum histórico de assinatura</div>
                                </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissões do Pacote (LISTA INTERATIVA E ROLÁVEL) -->
                <!--[if BLOCK]><![endif]--><?php if($permissoesPacote && $permissoesPacote->count() > 0): ?>
                <div class="card mb-4 shadow-sm border-0">
                    <!-- Novo cabeçalho: Fundo branco e acento de borda sutil -->
                    <div class="card-header bg-white border-bottom border-2 border-primary-subtle py-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-shield-alt me-2"></i>Permissões do Pacote
                        </h5>
                    </div>
                    
                    <div class="card-body p-0">
                        <!-- Barra de Pesquisa Rápida -->
                        <div class="p-3 border-bottom">
                            <!-- JS filterPermissions é adicionado no final do arquivo -->
                            <input type="text" class="form-control form-control-sm" placeholder="Buscar módulo ou permissão..." onkeyup="filterPermissions(this.value)">
                        </div>

                        <!-- Lista de Permissões Rolável (max-height para lidar com muitos registros) -->
                        <div class="list-group list-group-flush permissions-scroll-list" id="permissionsList">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $permissoesPacote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <!-- Note a classe list-group-item-action para o efeito de hover/clique e data-atributos para pesquisa -->
                            <a href="#" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center permission-item" 
                               data-module="<?php echo e($permissao->modulo->nome ?? ''); ?>"
                               data-permission="<?php echo e($permissao->getPermissaoFormatada() ?? ''); ?>"
                               title="<?php echo e($permissao->getDescricaoPermissao()); ?>">
                                
                                <div class="flex-shrink-0 me-3">
                                    <i class="<?php echo e($permissao->getPermissaoIcone()); ?> fs-5 text-<?php echo e($permissao->getPermissaoClass()); ?>"></i>
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="fw-bold mb-0 text-dark"><?php echo e($permissao->modulo->nome); ?></div>
                                    <small class="text-muted d-block"><?php echo e($permissao->getDescricaoPermissao()); ?></small>
                                </div>
                                
                                <div class="ms-auto">
                                    <span class="badge bg-<?php echo e($permissao->getPermissaoClass()); ?>-subtle text-<?php echo e($permissao->getPermissaoClass()); ?> fw-semibold px-3 py-2 text-uppercase">
                                        <?php echo e($permissao->getPermissaoFormatada()); ?>

                                    </span>
                                </div>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!-- Rodapé para indicar que há mais registros (Exemplo de paginação/visualização completa) -->
                        <!--[if BLOCK]><![endif]--><?php if($permissoesPacote->count() > 5): ?>
                        <div class="card-footer text-center bg-light">
                            <a href="#" class="text-primary fw-semibold" onclick="alert('Funcionalidade de Ver Todas as Permissões. Implemente a lógica de carregamento completo ou página separada.')">
                                <i class="fas fa-arrow-alt-circle-down me-1"></i> Ver todas as <?php echo e($permissoesPacote->count()); ?> permissões
                            </a>
                        </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- JavaScript para o filtro de pesquisa (apenas demonstrativo) -->
                <script>
                    // Função de filtro client-side para simular a interatividade da busca
                    function filterPermissions(searchTerm) {
                        const list = document.getElementById('permissionsList');
                        if (!list) return;

                        const items = list.querySelectorAll('.permission-item');
                        const lowerCaseSearch = searchTerm.toLowerCase();

                        items.forEach(item => {
                            // Busca nos atributos de dados
                            const moduleName = item.getAttribute('data-module').toLowerCase();
                            const permissionType = item.getAttribute('data-permission').toLowerCase();

                            if (moduleName.includes(lowerCaseSearch) || permissionType.includes(lowerCaseSearch)) {
                                item.style.display = 'flex'; // Exibe o item
                            } else {
                                item.style.display = 'none'; // Oculta o item
                            }
                        });
                    }
                </script>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            </div>

            <!-- ======================= BARRA LATERAL (Status e Ações) ======================= -->
            <div class="col-lg-4">
                
                <!-- Status da Assinatura (AGORA CARD 'RESUMO') - INTOCADO -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body text-center p-4">
                         <small class="text-muted text-uppercase fw-bold">Dias Restantes</small>
                         <div class="display-3 fw-bolder text-<?php echo e($estatisticas['dias_restantes'] <= 30 ? 'danger' : 'primary'); ?> mb-2">
                            <?php echo e($estatisticas['dias_restantes']); ?>

                         </div>
                         <div class="mb-4">
                            <span class="badge status-badge bg-<?php echo e($assinaturaAtual->status === 'Ativo' ? 'success' : ($assinaturaAtual->status === 'Expirado' ? 'danger' : 'warning')); ?>">
                                <?php echo e($assinaturaAtual->status); ?>

                            </span>
                         </div>
                         
                         <div class="d-flex justify-content-between text-start mb-2">
                             <small class="text-muted">Início:</small>
                             <span class="fw-semibold text-primary"><?php echo e($assinaturaAtual->data_inicio->format('d/m/Y')); ?></span>
                         </div>
                         <!--[if BLOCK]><![endif]--><?php if(!$assinaturaAtual->vitalicio): ?>
                         <div class="d-flex justify-content-between text-start mb-2">
                             <small class="text-muted">Expira:</small>
                             <span class="fw-semibold text-danger"><?php echo e($assinaturaAtual->data_fim->format('d/m/Y')); ?></span>
                         </div>
                         <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                         <div class="d-flex justify-content-between text-start">
                             <small class="text-muted">Tipo:</small>
                             <span class="fw-semibold <?php if($assinaturaAtual->vitalicio): ?> text-success <?php else: ?> text-dark <?php endif; ?>"><?php echo e($assinaturaAtual->vitalicio ? 'Vitalícia' : 'Periódica'); ?></span>
                         </div>
                    </div>
                </div>

                <!-- Ações Rápidas (DESIGN REFINADO - CABEÇALHO INTEGRADO) - INTOCADO -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body p-4">
                        <!-- NOVO CABEÇALHO INTEGRADO: Título com linha divisória sutil -->
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-primary-subtle">
                            <i class="fas fa-bolt me-2 fs-5 text-primary"></i>
                            <h5 class="mb-0 fw-bold text-primary">Ações Rápidas</h5>
                        </div>
                         <div class="row g-3">
                            <div class="col-6">
                                <a href="#" wire:click.prevent="renovarAssinatura" class="action-tile bg-primary-subtle border-primary-dark text-primary-dark">
                                    <i class="fas fa-sync-alt fa-2x mb-2"></i>
                                    <span class="d-block fw-bold">Renovar</span>
                                    <small>Estender prazo</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" wire:click.prevent="atualizarMetodoPagamento" class="action-tile bg-info-subtle border-info-dark text-info-dark">
                                    <i class="fas fa-credit-card fa-2x mb-2"></i>
                                    <span class="d-block fw-bold">Pagamento</span>
                                    <small>Alterar método</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" wire:click.prevent="cancelarAssinatura" class="action-tile bg-danger-subtle border-danger-dark text-danger-dark">
                                    <i class="fas fa-times-circle fa-2x mb-2"></i>
                                    <span class="d-block fw-bold">Cancelar</span>
                                    <small>Encerrar plano</small>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="#" wire:click.prevent="imprimirAssinatura" class="action-tile bg-success-subtle border-success-dark text-success-dark">
                                    <i class="fas fa-print fa-2x mb-2"></i>
                                    <span class="d-block fw-bold">Imprimir</span>
                                    <small>Gerar relatório</small>
                                </a>
                            </div>
                         </div>
                    </div>
                </div>

                <!-- Alertas Ativos (NOVO CABEÇALHO LIMPO) - INTOCADO -->
                <div class="card shadow-sm border-0">
                    <!-- Novo cabeçalho: Fundo branco e acento de borda sutil -->
                    <div class="card-header bg-white border-bottom border-2 border-warning-subtle py-3">
                         <h5 class="mb-0 fw-bold text-warning">
                            <i class="fas fa-bell me-2"></i>Alertas Ativos
                        </h5>
                    </div>
                    <?php
                        $alerts = \App\Helpers\Billings\SubscriptionHelper::getActiveAlerts($igreja->id ?? null, 3);
                    ?>
                    <div class="card-body p-0">
                        <!--[if BLOCK]><![endif]--><?php if($alerts && $alerts->count() > 0): ?>
                            <div class="list-group list-group-flush">
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $alerts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $alertType = $alert->tipo_alerta === 'expiracao_proxima' ? 'warning' : ($alert->tipo_alerta === 'limite_proximo' ? 'info' : 'danger');
                                ?>
                                <div class="alert-banner alert-banner-<?php echo e($alertType); ?> p-3 d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3 text-<?php echo e($alertType); ?> fs-3">
                                        <i class="fas fa-<?php echo e($alert->tipo_alerta === 'expiracao_proxima' ? 'clock' : ($alert->tipo_alerta === 'limite_proximo' ? 'chart-line' : 'exclamation-triangle')); ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?php echo e($alert->titulo); ?></div>
                                        <small class="text-muted d-block"><?php echo e($alert->mensagem); ?></small>
                                        <small class="text-primary fw-semibold"><?php echo e($alert->created_at->diffForHumans()); ?></small>
                                    </div>
                                    <button type="button" class="btn-close btn-sm ms-3" wire:click="marcarAlertaComoLido(<?php echo e($alert->id); ?>)" aria-label="Fechar"></button>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>

                             <!--[if BLOCK]><![endif]--><?php if($alerts->count() >= 3): ?>
                            <div class="card-footer text-center">
                                <a href="#" class="btn btn-sm btn-outline-primary" wire:click="verTodosAlertas">
                                    <i class="fas fa-eye me-1"></i>Ver Todos os Alertas
                                </a>
                            </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle text-success display-4 mb-3"></i>
                                <div class="text-muted">Nenhum alerta ativo</div>
                                <small class="text-muted">Sua assinatura está em dia!</small>
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

            </div>
        </div>

        <?php else: ?> <!-- SE NÃO TIVER ASSINATURA ATUAL -->
        <div class="card mb-4 border-warning bg-warning-subtle shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-exclamation-triangle text-warning display-3 mb-4"></i>
                <h3 class="text-warning mb-3">Nenhuma Assinatura Ativa</h3>
                <p class="text-muted fs-5 mb-4">Sua igreja não possui uma assinatura ativa no momento.</p>
                <a class="btn btn-primary btn-lg" href="<?php echo e(route('ecommerce.subscription.upgrade', Auth::user()->getIgrejaId())); ?>">
                    <i class="fas fa-plus me-2"></i>Contratar Plano
                </a>
            </div>
        </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


    </div>

    <!-- Scripts -->
    <script src="<?php echo e(asset('system/js/courses.js')); ?>" data-navigate-once></script>


    <style>
        /* Variáveis de Cores Customizadas para consistência */
        :root {
            --bs-primary-dark: #004d99;
            --bs-success-dark: #147444;
            --bs-info-dark: #007777;
            --bs-danger-dark: #b3394a;
        }

        /* Card do Plano Personalizado (INTOCADO) */
        .card-plano {
            background: linear-gradient(45deg, var(--bs-primary), #0056b3); 
        }

        /* Efeito de Brilho no Status (INTOCADO) */
        .status-badge {
            font-size: 1.1rem;
            padding: 0.6em 1em;
            letter-spacing: 0.5px;
        }
        .status-badge.bg-success {
            box-shadow: 0 0 15px rgba(25, 135, 84, 0.7);
        }
        .status-badge.bg-warning {
             box-shadow: 0 0 15px rgba(255, 193, 7, 0.7);
        }
         .status-badge.bg-danger {
             box-shadow: 0 0 15px rgba(220, 53, 69, 0.7);
        }

        /* Estilo das Abas (INTOCADO) */
        .nav-tabs-primary .nav-link {
            font-weight: 600;
            color: var(--bs-primary);
            border: 0;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs-primary .nav-link.active,
        .nav-tabs-primary .nav-item.show .nav-link {
            color: var(--bs-primary);
            background-color: transparent;
            border-color: var(--bs-primary);
        }
         .nav-tabs-primary .nav-link:hover {
            border-color: var(--bs-primary-subtle);
         }

        /* Estilo para Lista de Histórico (INTOCADO) */
        .history-list .list-group-item {
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }
        .history-list .list-group-item:last-child {
            border-bottom: 0;
        }
        .history-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1rem;
        }

        /* Ações Rápidas - Tiles Interativos (INTOCADO) */
        .action-tile {
            display: block;
            text-align: center;
            padding: 1.5rem 0.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            border: 1px solid;
            transition: transform 0.2s, box-shadow 0.2s;
            line-height: 1.3;
        }
        .action-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .action-tile small {
            opacity: 0.8;
            font-size: 0.75rem;
            display: block;
        }
        
        /* Permissões do Pacote - Lista de Módulos (NOVO ESTILO PARA LISTA) */
        .permissions-scroll-list {
            max-height: 350px; /* Limite de altura para torná-lo rolavel em muitos registros */
            overflow-y: auto;
        }
        .permissions-scroll-list .list-group-item {
            border-left: 5px solid transparent; /* Adiciona espaço para a barra de acento */
            transition: all 0.15s ease-in-out;
            cursor: pointer;
        }
        .permissions-scroll-list .list-group-item:hover {
            background-color: var(--bs-light);
            /* Borda lateral sutil no hover */
            border-left-color: var(--bs-primary-subtle); 
        }
        .permissions-scroll-list .list-group-item:active {
            background-color: var(--bs-primary-subtle);
        }
        /* Garantindo o visual da borda esquerda se necessário, embora list-group-item-action cubra a maioria */
        .permissions-scroll-list .text-success { border-left-color: var(--bs-success) !important; }
        .permissions-scroll-list .text-warning { border-left-color: var(--bs-warning) !important; }
        .permissions-scroll-list .text-danger { border-left-color: var(--bs-danger) !important; }


        /* Alertas Ativos - Banners de Urgência (INTOCADO) */
        .alert-banner {
            border-bottom: 1px solid var(--bs-border-color-translucent);
            position: relative;
            background-color: var(--bs-white);
        }
        .alert-banner:hover {
             background-color: var(--bs-light);
        }
        .alert-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 8px; /* Barra bem espessa para urgência */
        }

        .alert-banner-warning::before { background-color: var(--bs-warning); }
        .alert-banner-info::before { background-color: var(--bs-info); }
        .alert-banner-danger::before { background-color: var(--bs-danger); }

        /* Ajustes para a lista de alertas */
        .card-body .alert-banner:first-child { border-top: 0 !important; }
        .card-body .alert-banner:last-child { border-bottom: 0 !important; }

        @media print {
            .btn, .card-header, .toast-container, .card-plano, .action-tile {
                display: none !important;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }

            .tab-content > .tab-pane {
                display: block !important;
                opacity: 1 !important;
            }

            .container-fluid {
                padding: 0 !important;
            }
            
            /* Força o layout de 1 coluna na impressão */
            .col-lg-8, .col-lg-4 {
                width: 100% !important;
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
    </style>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/church/billing/subscription.blade.php ENDPATH**/ ?>