<div>



<div class="container-fluid py-1">

    <!-- Hero Section -->
    <div class="card bg-gradient-hero text-white border-0 shadow-lg mb-0">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-rocket me-3"></i>
                        Potencialize Sua Igreja
                    </h1>
                    <p class="lead mb-4">
                        Desbloqueie todo o potencial do OMNIGREJAS com nossos planos premium.
                        Gestão completa, relatórios avançados e muito mais!
                    </p>

                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h3 mb-1">500+</div>
                            <div class="small opacity-75">Igrejas Ativas</div>
                        </div>
                        <div class="col-auto text-center">
                            <div class="h3 mb-1">50k+</div>
                            <div class="small opacity-75">Membros Gerenciados</div>
                        </div>
                        <div class="col-auto text-center">
                            <div class="h3 mb-1">99.9%</div>
                            <div class="small opacity-75">Uptime</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-church hero-icon opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Wave Divider SVG - TOP (para o Hero Section) -->
    <div class="wave-divider" style="color: var(--light-bg);">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="currentColor" fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,122.7C384,117,480,75,576,69.3C672,64,768,96,864,106.7C960,117,1056,107,1152,106.7C1248,107,1344,117,1392,122.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->check()): ?>
    <!-- Status da Assinatura Dinâmico -->
    <!--[if BLOCK]><![endif]--><?php if($statusAssinatura['status'] !== 'ativa'): ?>
        <div class="alert alert-<?php echo e($statusAssinatura['tipo_alerta']); ?> border-0 shadow-sm mb-5 p-4 container-fluid" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1 fw-bold"><?php echo e($statusAssinatura['titulo']); ?></h5>
                    <p class="mb-0">
                        <?php echo e($statusAssinatura['mensagem']); ?>

                        <!--[if BLOCK]><![endif]--><?php if($statusAssinatura['acao_sugerida']): ?>
                            <strong><?php echo e($statusAssinatura['acao_sugerida']); ?></strong>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Alertas Ativos -->
    <!--[if BLOCK]><![endif]--><?php if($alertasAtivos->count() > 0): ?>
        <div class="row mb-4">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $alertasAtivos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 mb-3">
                    <div class="alert alert-<?php echo e($alerta->getBadgeClass()); ?> border-0 shadow-sm p-3" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-<?php echo e($alerta->getTipoIcone()); ?> fa-lg me-3"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold"><?php echo e($alerta->titulo); ?></h6>
                                <p class="mb-0 small"><?php echo e($alerta->mensagem); ?></p>
                                <small class="text-muted"><?php echo e($alerta->getCriadoEmRelativo()); ?></small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary ms-3"
                                    wire:click="marcarAlertaLido(<?php echo e($alerta->id); ?>)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<div class="container-fluid">
    <div class="container-fluid px-0">

    <!--
    /
    / SEÇÃO DE PLANOS ATUALIZADA (com base no seu exemplo)
    /
    -->

    <!-- Header dos Pacotes -->
    <div class="pricing-header text-center position-relative">
        <h2 class="fw-bold mb-3 gradient-text display-5">
            <!--[if BLOCK]><![endif]--><?php if($modoFormatado === 'Nova Assinatura'): ?>
                <i class="fas fa-rocket me-3"></i>Escolha Seu Primeiro Plano
            <?php elseif($modoFormatado === 'Renovar Assinatura'): ?>
                <i class="fas fa-refresh me-3"></i>Renove Sua Assinatura
            <?php else: ?>
                <i class="fas fa-arrow-up me-3"></i><?php echo e($modoFormatado); ?>

            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </h2>

        <p class="text-muted lead mb-4 fs-5">
            <!--[if BLOCK]><![endif]--><?php if($pacoteAtual): ?>
                Seu plano atual: <strong class="text-primary"><?php echo e($pacoteAtual->nome); ?></strong> (<?php echo e($pacoteAtual->getPrecoFormatado()); ?>/mês)
            <?php else: ?>
                Planos flexíveis para igrejas de todos os tamanhos
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </p>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button wire:click.prevent="toggleComparison"
                    class="btn btn-compare"
                    wire:loading.attr="disabled"
                    wire:target="toggleComparison">
                <i class="fas fa-balance-scale me-2"></i>
                <span wire:loading.remove wire:target="toggleComparison">
                    <?php echo e($showComparison ? 'Ocultar Comparação' : 'Comparar Planos'); ?>

                </span>
                <span wire:loading wire:target="toggleComparison">
                    <i class="fas fa-spinner fa-spin me-2"></i>Carregando...
                </span>
            </button>
            <button class="btn btn-ocean btn-glow">
                <i class="fas fa-question-circle me-2"></i>
                Dúvidas Frequentes
            </button>
        </div>
    </div>

    <!-- Cards de Pacotes Dinâmicos -->
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('subscription.pacote-cards', ['pacotes' => $pacotesDisponiveis,'pacoteAtual' => $pacoteAtual,'assinaturaAtual' => $assinaturaAtual]);

$__html = app('livewire')->mount($__name, $__params, 'pacote-cards', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>


    <!-- Tabela de Comparação -->
    <div id="comparisonTable"
         style="<?php echo e($showComparison ? '' : 'display: none;'); ?>">

            <div class="container-fluid py-5">
                <?php echo $__env->make('subscription.partials.comparison-table', ['pacotes' => $pacotesDisponiveis], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

    </div>
    <!-- FIM DA SEÇÃO DE PLANOS ATUALIZADA -->

    <!-- FAQ -->
    <div class="card shadow-sm mb-0">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h3 class="fw-bold display-5">Perguntas Frequentes</h3>
                <p class="text-muted lead fs-5">Tire suas dúvidas sobre nossos planos</p>
            </div>

            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                            Posso alterar meu plano a qualquer momento?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Sim! Você pode fazer upgrade ou downgrade do seu plano a qualquer momento diretamente pela sua área de cliente.
                            As mudanças entram em vigor no próximo ciclo de cobrança.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                            Como funciona o período de trial?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Oferecemos 7 - 14 dias gratuitos para você testar todos os recursos do plano Prata, sem compromisso.
                            Não é necessário cartão de crédito para começar, basta se cadastrar e aproveitar!
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                            Quais são as formas de pagamento aceitas?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Aceitamos pagamentos via Multicaixa Express (Angola), Transferência Bancária.
                            Para planos anuais, oferecemos descontos especiais.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                            Preciso instalar algum software?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Não! O OMNIGREJAS é uma solução 100% baseada na nuvem. Você pode acessá-lo de qualquer dispositivo
                            com internet, diretamente pelo navegador, sem necessidade de instalações.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Wave Divider SVG - BOTTOM (para a seção FAQ) -->
    <div class="wave-divider wave-divider-bottom" style="color: var(--light-bg);">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="currentColor" fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,122.7C384,117,480,75,576,69.3C672,64,768,96,864,106.7C960,117,1056,107,1152,106.7C1248,107,1344,117,1392,122.7L1440,128L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
        </svg>
    </div>


    <!-- Histórico de Assinaturas (se existir) -->
    <!--[if BLOCK]><![endif]--><?php if($historicoAssinaturas->count() > 0): ?>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-history m-3"></i>
                    Histórico de Assinaturas
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pacote</th>
                                <th>Período</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $historicoAssinaturas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assinatura): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong class="text-secondary"><?php echo e(Str::ucfirst($assinatura->pacote->nome)); ?></strong>
                                    </td>
                                    <td>
                                        <b class="text-danger"><?php echo e($assinatura->data_inicio->format('d/m/Y')); ?></b> -
                                        <b class="text-warning"><?php echo e($assinatura->data_fim->format('d/m/Y')); ?></b>
                                    </td>
                                    <td class="text-success fw-bold">
                                        <?php echo e($assinatura->getValorFormatado()); ?>

                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($assinatura->status === 'Ativo' ? 'success' : 'secondary'); ?>">
                                            <?php echo e($assinatura->status); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- CTA Final Dinâmico -->
    <div class="card cta-gradient text-white border-0 shadow-lg mt-5">
        <div class="card-body p-5 text-center">
            <h3 class="fw-bold display-5 mb-3">
                <!--[if BLOCK]><![endif]--><?php if($modoFormatado === 'Nova Assinatura'): ?>
                    Pronto para Começar?
                <?php elseif($modoFormatado === 'Renovar Assinatura'): ?>
                    Pronto para Continuar?
                <?php else: ?>
                    Pronto para Evoluir?
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </h3>

            <p class="lead mb-4 fs-5">
                <!--[if BLOCK]><![endif]--><?php if($pacoteAtual): ?>
                    Mantenha sua igreja sempre conectada e organizada com o OMNIGREJAS.
                <?php else: ?>
                    Junte-se a centenas de igrejas que já confiam no OMNIGREJAS e otimize sua gestão.
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                <button class="btn btn-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-envelope me-2"></i>Falar com Suporte
                </button>
                <!--[if BLOCK]><![endif]--><?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('ecommerce.trial.solicitar')); ?>" class="btn btn-outline-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-play me-2"></i>Ver Demonstração
                </a>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
    </div>

</div> <!-- Fim do container-lg -->

 
</div><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/subscription/upgrade-page.blade.php ENDPATH**/ ?>