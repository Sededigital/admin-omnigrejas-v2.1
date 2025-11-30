<!-- Modal para Detalhes da Distribuição Geográfica -->
<div class="modal fade" id="distribuicaoGeograficaModal" tabindex="-1" aria-labelledby="distribuicaoGeograficaModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="distribuicaoGeograficaModalLabel">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    <span>Detalhes da Distribuição Geográfica</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <!-- Navegação por Abas -->
                <nav class="mb-4">
                    <div class="nav nav-tabs border-bottom-0" id="geo-nav-tab" role="tablist">
                        <button class="nav-link active border-0 bg-transparent fw-semibold" id="geo-nav-overview-tab"
                                data-bs-toggle="tab" data-bs-target="#geo-nav-overview" type="button" role="tab">
                            <i class="fas fa-chart-pie text-primary me-1"></i>Visão Geral
                        </button>
                        <button class="nav-link border-0 bg-transparent fw-semibold" id="geo-nav-details-tab"
                                data-bs-toggle="tab" data-bs-target="#geo-nav-details" type="button" role="tab">
                            <i class="fas fa-list text-primary me-1"></i>Detalhes por Região
                        </button>
                        <button class="nav-link border-0 bg-transparent fw-semibold" id="geo-nav-insights-tab"
                                data-bs-toggle="tab" data-bs-target="#geo-nav-insights" type="button" role="tab">
                            <i class="fas fa-lightbulb text-primary me-1"></i>Insights
                        </button>
                    </div>
                </nav>

                <!-- Conteúdo das Abas -->
                <div class="tab-content" id="geo-nav-tabContent">

                    <!-- Aba: Visão Geral -->
                    <div class="tab-pane fade show active" id="geo-nav-overview" role="tabpanel">
                        <div class="row">
                            <!-- Gráfico Principal -->
                            <div class="col-lg-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary mb-3">
                                            <i class="fas fa-chart-pie me-1"></i>Distribuição por Região
                                        </h6>
                                        <div wire:ignore style="height: 300px;">
                                            <canvas id="distribuicaoGeograficaModalChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estatísticas Resumidas -->
                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-chart-bar me-1"></i>Estatísticas Gerais
                                        </h6>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Total de Regiões</small>
                                                <span class="badge bg-primary"><?php echo e(count($this->graficoDistribuicaoGeografica)); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Total de Membros</small>
                                                <span class="badge bg-success"><?php echo e(array_sum(array_column($this->graficoDistribuicaoGeografica, 'total'))); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Média por Região</small>
                                                <span class="badge bg-info"><?php echo e($this->estatisticasGeograficas['media_por_regiao'] ?? 0); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-users me-1"></i>Usuários Próximos
                                        </h6>
                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Mesma Cidade</small>
                                                <span class="badge bg-success"><?php echo e($this->usuariosProximos['mesma_cidade'] ?? 0); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Mesma Província</small>
                                                <span class="badge bg-info"><?php echo e($this->usuariosProximos['mesma_provincia'] ?? 0); ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Outras Regiões</small>
                                                <span class="badge bg-secondary"><?php echo e($this->usuariosProximos['outras_regioes'] ?? 0); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aba: Detalhes por Região -->
                    <div class="tab-pane fade" id="geo-nav-details" role="tabpanel">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="fas fa-list me-1"></i>Detalhamento por Região
                                </h6>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th><i class="fas fa-map-marker-alt text-primary me-1"></i>Região</th>
                                                <th class="text-center"><i class="fas fa-users text-primary me-1"></i>Membros</th>
                                                <th class="text-center"><i class="fas fa-percentage text-primary me-1"></i>Percentual</th>
                                                <th class="text-center"><i class="fas fa-chart-line text-primary me-1"></i>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $totalMembros = array_sum(array_column($this->graficoDistribuicaoGeografica, 'total'));
                                            ?>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->graficoDistribuicaoGeografica; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $regiao): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $percentual = $totalMembros > 0 ? round(($regiao['total'] / $totalMembros) * 100, 1) : 0;
                                                    $statusClass = $percentual >= 20 ? 'success' : ($percentual >= 10 ? 'warning' : 'secondary');
                                                    $statusText = $percentual >= 20 ? 'Alta' : ($percentual >= 10 ? 'Média' : 'Baixa');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo e($regiao['localizacao']); ?></strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary"><?php echo e($regiao['total']); ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info"><?php echo e($percentual); ?>%</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aba: Insights -->
                    <div class="tab-pane fade" id="geo-nav-insights" role="tabpanel">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-lightbulb me-1"></i>Insights Estratégicos
                                        </h6>
                                        <div class="mb-3">
                                            <div class="alert alert-info border-0">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <strong>Região Principal:</strong> <?php echo e($this->estatisticasGeograficas['regiao_mais_populosa'] ?? 'N/A'); ?>

                                                concentra <?php echo e($this->graficoDistribuicaoGeografica[0]['total'] ?? 0); ?> membros
                                            </div>
                                            <div class="alert alert-success border-0">
                                                <i class="fas fa-users text-success me-2"></i>
                                                <strong>Oportunidade:</strong> <?php echo e($this->usuariosProximos['mesma_cidade'] ?? 0); ?> membros
                                                estão na mesma cidade da igreja
                                            </div>
                                            <div class="alert alert-warning border-0">
                                                <i class="fas fa-expand-arrows-alt text-warning me-2"></i>
                                                <strong>Expansão:</strong> Considere eventos em regiões com
                                                <?php echo e($this->estatisticasGeograficas['media_por_regiao'] ?? 0); ?> membros ou menos
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-chart-line me-1"></i>Recomendações
                                        </h6>
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Foque em regiões com alta concentração de membros
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Considere transporte para membros distantes
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Avalie abertura de pontos de encontro regionais
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                Monitore crescimento em regiões emergentes
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-calendar-alt me-1"></i>Próximas Ações
                                        </h6>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-plus me-1"></i>Planejar Evento Regional
                                            </button>
                                            <button class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-users me-1"></i>Contato com Líderes Locais
                                            </button>
                                            <button class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-chart-bar me-1"></i>Relatório Detalhado
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-download me-1"></i>Exportar Relatório
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/admin/modals/distribuicao-geografica-modal.blade.php ENDPATH**/ ?>