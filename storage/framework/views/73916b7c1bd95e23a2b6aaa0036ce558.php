<div class="comparison-container">
    <div class="comparison-header">
        <h3 class="text-center mb-4">
            <i class="fas fa-balance-scale text-primary me-2"></i>
            <span class="gradient-text">Comparação Detalhada</span>
        </h3>
        <p class="text-center text-muted mb-4">
            Compare todos os recursos e encontre o plano ideal para sua igreja
        </p>
    </div>

    <div class="comparison-table-wrapper">
        <table class="comparison-table">
            <thead>
                <tr class="table-header">
                    <th class="feature-column">
                        <div class="feature-header">
                            <i class="fas fa-list-check text-primary"></i>
                            <span>Recursos</span>
                        </div>
                    </th>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="plan-column <?php echo e($pacote->isPopular() ? 'popular-plan' : ''); ?>">
                            <div class="plan-card">
                                <!--[if BLOCK]><![endif]--><?php if($pacote->isPopular()): ?>
                                    <div class="popular-badge">
                                        <i class="fas fa-star"></i>
                                        <span>Mais Popular</span>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="plan-name"><?php echo e($pacote->nome); ?></div>
                                <div class="plan-price">
                                    <span class="price-amount"><?php echo e($pacote->getPrecoFormatado()); ?></span>
                                    <span class="price-period">/mês</span>
                                </div>

                                <!--[if BLOCK]><![endif]--><?php if($pacote->preco_vitalicio): ?>
                                    <div class="plan-lifetime">
                                        <small>Ou <?php echo e($pacote->getPrecoVitalicioFormatado()); ?> vitalício</small>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                                <div class="plan-trial">
                                    <i class="fas fa-gift text-success"></i>
                                    <span><?php echo e($pacote->trial_dias); ?> dias grátis</span>
                                </div>
                            </div>
                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tr>
            </thead>
            <tbody>
                <!-- Recursos Básicos -->
                <tr class="resource-section">
                    <td colspan="<?php echo e(count($pacotes) + 1); ?>" class="section-header">
                        <div class="section-title">
                            <i class="fas fa-cubes text-primary"></i>
                            <span>Recursos Básicos</span>
                        </div>
                    </td>
                </tr>

                <!-- Loop dinâmico pelos recursos de cada pacote -->
                <?php
                    // Buscar todos os tipos de recursos únicos disponíveis nos pacotes
                    $tiposRecursos = collect();
                    foreach($pacotes as $pacote) {
                        foreach($pacote->getRecursosAtivos() as $recurso) {
                            $tiposRecursos->push($recurso->recurso_tipo);
                        }
                    }
                    $tiposRecursos = $tiposRecursos->unique()->values();
                ?>

                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tiposRecursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipoRecurso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="feature-row">
                        <td class="feature-cell">
                            <div class="feature-content">
                                <?php
                                    // Usar o primeiro recurso encontrado para obter informações de exibição
                                    $exemploRecurso = null;
                                    foreach($pacotes as $pacote) {
                                        $exemploRecurso = $pacote->recursos->where('recurso_tipo', $tipoRecurso)->first();
                                        if($exemploRecurso) break;
                                    }
                                ?>
                                <!--[if BLOCK]><![endif]--><?php if($exemploRecurso): ?>
                                    <i class="<?php echo e($exemploRecurso->getIcone()); ?> feature-icon text-primary"></i>
                                    <div class="feature-text">
                                        <div class="feature-name"><?php echo e($exemploRecurso->getTipoFormatado()); ?></div>
                                        <div class="feature-desc"><?php echo e($exemploRecurso->getDescricao()); ?></div>
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </td>
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="value-cell">
                                <?php
                                    $limiteValor = $pacote->getLimiteRecurso($tipoRecurso);
                                    $temRecurso = $pacote->temRecurso($tipoRecurso);
                                    $recurso = $pacote->recursos->where('recurso_tipo', $tipoRecurso)->first();
                                ?>
                                <!--[if BLOCK]><![endif]--><?php if($temRecurso && $recurso): ?>
                                    <div class="value-content">
                                        <!--[if BLOCK]><![endif]--><?php if($recurso->isIlimitado()): ?>
                                            <i class="fas fa-infinity text-success"></i>
                                            <span class="value-text">Ilimitado</span>
                                        <?php else: ?>
                                            <span class="value-number"><?php echo e(number_format($limiteValor, 0, '.', '.')); ?></span>
                                            <!--[if BLOCK]><![endif]--><?php if($recurso->unidade !== 'quantidade'): ?>
                                                <span class="value-unit"><?php echo e($recurso->unidade); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                <!-- Funcionalidades Avançadas -->
                <tr class="resource-section">
                    <td colspan="<?php echo e(count($pacotes) + 1); ?>" class="section-header">
                        <div class="section-title">
                            <i class="fas fa-rocket text-warning"></i>
                            <span>Funcionalidades Avançadas</span>
                        </div>
                    </td>
                </tr>

                <!-- Relatórios -->
                <tr class="feature-row">
                    <td class="feature-cell">
                        <div class="feature-content">
                            <i class="fas fa-chart-bar feature-icon text-success"></i>
                            <div class="feature-text">
                                <div class="feature-name">Relatórios Avançados</div>
                                <div class="feature-desc">Dashboards e analytics completos</div>
                            </div>
                        </div>
                    </td>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="value-cell">
                            <!--[if BLOCK]><![endif]--><?php if($pacote->getNivelMaximo() && in_array($pacote->getNivelMaximo()->nivel, ['premium', 'enterprise'])): ?>
                                <div class="check-content">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span class="check-text">Incluído</span>
                                </div>
                            <?php else: ?>
                                <div class="cross-content">
                                    <i class="fas fa-times text-muted"></i>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tr>

                <!-- API Access -->
                <tr class="feature-row">
                    <td class="feature-cell">
                        <div class="feature-content">
                            <i class="fas fa-code feature-icon text-danger"></i>
                            <div class="feature-text">
                                <div class="feature-name">Acesso à API</div>
                                <div class="feature-desc">Integração com sistemas externos</div>
                            </div>
                        </div>
                    </td>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="value-cell">
                            <?php if($pacote->getNivelMaximo() && $pacote->getNivelMaximo()->nivel === 'enterprise'): ?>
                                <div class="check-content">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span class="check-text">Completo</span>
                                </div>
                            <?php else: ?>
                                <div class="cross-content">
                                    <i class="fas fa-times text-muted"></i>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tr>

                <!-- Suporte Prioritário -->
                <tr class="feature-row">
                    <td class="feature-cell">
                        <div class="feature-content">
                            <i class="fas fa-headset feature-icon text-info"></i>
                            <div class="feature-text">
                                <div class="feature-name">Suporte Prioritário</div>
                                <div class="feature-desc">Atendimento prioritário 24/7</div>
                            </div>
                        </div>
                    </td>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="value-cell">
                            <!--[if BLOCK]><![endif]--><?php if($pacote->getNivelMaximo() && in_array($pacote->getNivelMaximo()->nivel, ['premium', 'enterprise'])): ?>
                                <div class="check-content">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span class="check-text">24/7</span>
                                </div>
                            <?php else: ?>
                                <div class="cross-content">
                                    <i class="fas fa-times text-muted"></i>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tr>

                <!-- Backup Automático -->
                <tr class="feature-row">
                    <td class="feature-cell">
                        <div class="feature-content">
                            <i class="fas fa-shield-alt feature-icon text-warning"></i>
                            <div class="feature-text">
                                <div class="feature-name">Backup Automático</div>
                                <div class="feature-desc">Backup diário dos dados</div>
                            </div>
                        </div>
                    </td>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="value-cell">
                            <!--[if BLOCK]><![endif]--><?php if($pacote->getNivelMaximo() && in_array($pacote->getNivelMaximo()->nivel, ['premium', 'enterprise'])): ?>
                                <div class="check-content">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span class="check-text">Diário</span>
                                </div>
                            <?php else: ?>
                                <div class="cross-content">
                                    <i class="fas fa-times text-muted"></i>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tr>

                <!-- Período de Teste -->
                <tr class="resource-section">
                    <td colspan="<?php echo e(count($pacotes) + 1); ?>" class="section-header">
                        <div class="section-title">
                            <i class="fas fa-gift text-success"></i>
                            <span>Período de Teste</span>
                        </div>
                    </td>
                </tr>

                <tr class="feature-row trial-row">
                    <td class="feature-cell">
                        <div class="feature-content">
                            <i class="fas fa-clock feature-icon text-success"></i>
                            <div class="feature-text">
                                <div class="feature-name">Dias de Trial</div>
                                <div class="feature-desc">Teste gratuito sem compromisso</div>
                            </div>
                        </div>
                    </td>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $pacotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pacote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <td class="value-cell">
                            <div class="trial-content">
                                <span class="trial-days"><?php echo e($pacote->trial_dias); ?></span>
                                <span class="trial-text">dias grátis</span>
                            </div>
                        </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Call to Action -->
    <div class="comparison-cta">
        <div class="cta-content">
            <h4 class="cta-title">Pronto para escolher seu plano?</h4>
            <p class="cta-text">Todos os planos incluem suporte técnico e atualizações gratuitas.</p>
            <div class="cta-buttons">
                <button class="btn btn-primary btn-lg me-3">
                    <i class="fas fa-credit-card me-2"></i>
                    Escolher Plano
                </button>
                <button class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-question-circle me-2"></i>
                    Falar com Consultor
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Container principal */
.comparison-container {
    background: white;
    border-radius: 1.5rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin: 2rem 0;
}

/* Header da comparação */
.comparison-header {
    background: linear-gradient(135deg, var(--primary-blue), var(--ocean-blue));
    color: white;
    padding: 2rem;
    text-align: center;
}

.comparison-header h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.comparison-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

/* Wrapper da tabela */
.comparison-table-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

/* Tabela principal */
.comparison-table {
    width: 100%;
    min-width: 800px;
    border-collapse: collapse;
    font-size: 0.9rem;
}

/* Header da tabela */
.table-header {
    background: #f8fafc;
    border-bottom: 2px solid #e9ecef;
}

.table-header th {
    padding: 1.5rem;
    text-align: center;
    border-right: 1px solid #e9ecef;
}

.table-header th:last-child {
    border-right: none;
}

/* Coluna de recursos */
.feature-column {
    width: 280px;
    text-align: left;
    background: #f8fafc;
    border-right: 2px solid #dee2e6;
}

.feature-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: var(--primary-blue);
}

/* Colunas dos planos */
.plan-column {
    min-width: 200px;
    position: relative;
}

.plan-column.popular-plan {
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.05), rgba(245, 158, 11, 0.05));
}

.plan-card {
    text-align: center;
    padding: 1rem;
    position: relative;
}

.plan-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-blue);
    margin-bottom: 0.5rem;
}

.plan-price {
    margin-bottom: 0.5rem;
}

.price-amount {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--primary-blue);
}

.price-period {
    font-size: 0.9rem;
    color: #6c757d;
}

.plan-lifetime {
    margin-bottom: 0.5rem;
}

.plan-lifetime small {
    color: #6c757d;
    font-size: 0.8rem;
}

.plan-trial {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    font-size: 0.85rem;
    color: #28a745;
    font-weight: 500;
}

/* Badge Popular */
.popular-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, var(--gold), var(--warning));
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
    z-index: 10;
}

/* Seções de recursos */
.resource-section .section-header {
    background: linear-gradient(135deg, #f8fafc, #e9ecef);
    border: none;
    padding: 1rem;
}

.section-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-weight: 700;
    color: var(--primary-blue);
    font-size: 1.1rem;
}

/* Linhas de recursos */
.feature-row {
    border-bottom: 1px solid #e9ecef;
}

.feature-row:hover {
    background: rgba(13, 110, 253, 0.02);
}

.feature-row.trial-row {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(34, 197, 94, 0.05));
}

.feature-cell {
    padding: 1rem 1.5rem;
    border-right: 1px solid #e9ecef;
}

.feature-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.feature-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.feature-text {
    flex: 1;
}

.feature-name {
    font-weight: 600;
    color: #343a40;
    margin-bottom: 0.2rem;
}

.feature-desc {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Células de valor */
.value-cell {
    text-align: center;
    padding: 1rem 1.5rem;
    border-right: 1px solid #e9ecef;
    vertical-align: middle;
}

.value-cell:last-child {
    border-right: none;
}

.value-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.value-number {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--primary-blue);
}

.value-unit {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

.value-text {
    font-size: 0.9rem;
    color: #28a745;
    font-weight: 600;
}

/* Conteúdo de check/cross */
.check-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.check-content .fa-check-circle {
    font-size: 1.3rem;
}

.check-text {
    font-size: 0.8rem;
    color: #28a745;
    font-weight: 600;
}

.cross-content {
    display: flex;
    align-items: center;
    justify-content: center;
}

.cross-content .fa-times {
    font-size: 1.1rem;
}

/* Conteúdo de trial */
.trial-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.8rem 1rem;
    border-radius: 0.8rem;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.trial-days {
    font-size: 1.4rem;
    font-weight: 800;
}

.trial-text {
    font-size: 0.8rem;
    opacity: 0.9;
}

/* Call to Action */
.comparison-cta {
    background: linear-gradient(135deg, var(--primary-blue), var(--ocean-blue));
    padding: 2rem;
    text-align: center;
}

.cta-content {
    max-width: 600px;
    margin: 0 auto;
}

.cta-title {
    color: white;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.cta-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Responsividade */
@media (max-width: 768px) {
    .comparison-table {
        font-size: 0.8rem;
    }

    .feature-column {
        width: 200px;
    }

    .plan-column {
        min-width: 160px;
    }

    .feature-content {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }

    .feature-icon {
        width: 35px;
        height: 35px;
        font-size: 1rem;
    }

    .comparison-header {
        padding: 1.5rem 1rem;
    }

    .comparison-header h3 {
        font-size: 1.5rem;
    }

    .comparison-cta {
        padding: 1.5rem 1rem;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }

    .cta-buttons .btn {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 576px) {
    .feature-column {
        width: 150px;
    }

    .plan-column {
        min-width: 140px;
    }

    .plan-card {
        padding: 0.8rem 0.5rem;
    }

    .plan-name {
        font-size: 1rem;
    }

    .price-amount {
        font-size: 1.2rem;
    }

    .feature-cell,
    .value-cell {
        padding: 0.8rem 0.5rem;
    }
}
</style><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/subscription/partials/comparison-table.blade.php ENDPATH**/ ?>