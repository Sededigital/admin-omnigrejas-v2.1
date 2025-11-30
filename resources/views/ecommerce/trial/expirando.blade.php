<div class="container-fluid py-1">
    <!-- Hero Section -->
    <div class="card bg-gradient-hero text-white border-0 shadow-lg mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-clock me-3"></i>
                        Período de Teste Expirando
                    </h1>
                    <p class="lead mb-4">
                        @if($diasRestantes === 1)
                            Amanhã será o último dia do seu período de teste gratuito do <strong>{{ config('app.name') }}</strong>.
                        @else
                            Faltam apenas <strong>{{ $diasRestantes }} dias</strong> para o fim do seu período de teste gratuito do <strong>{{ config('app.name') }}</strong>.
                        @endif
                    </p>

                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h4 mb-1">{{ $diasRestantes }}</div>
                            <div class="small opacity-75">Dias Restantes</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">{{ $dataExpiracao }}</div>
                            <div class="small opacity-75">Data de Expiração</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">{{ $igrejaNome }}</div>
                            <div class="small opacity-75">Igreja</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-exclamation-triangle hero-icon opacity-75 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagem de Aviso -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-warning border-2 shadow-sm">
                <div class="card-body p-4">
                    <div class="alert alert-warning border-0 mb-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-2">Ação Necessária!</h5>
                                <p class="mb-0">Para não perder seus dados e continuar usando todas as funcionalidades do sistema, faça o upgrade antes da expiração do período de teste.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo do Trial -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-primary fw-bold mb-4">
                        <i class="fas fa-info-circle me-2"></i> Resumo do Seu Período de Teste
                    </h3>

                    <div class="trial-summary">
                        <div class="summary-item mb-3">
                            <div class="summary-label">Olá,</div>
                            <div class="summary-value fw-bold text-primary">{{ $nomeUsuario }}!</div>
                        </div>

                        <div class="summary-item mb-3">
                            <div class="summary-label">Igreja:</div>
                            <div class="summary-value">{{ $igrejaNome }}</div>
                        </div>

                        <div class="summary-item mb-3">
                            <div class="summary-label">Data de Expiração:</div>
                            <div class="summary-value text-danger fw-bold">{{ $dataExpiracao }}</div>
                        </div>

                        <div class="summary-item mb-3">
                            <div class="summary-label">Dias Restantes:</div>
                            <div class="summary-value text-warning fw-bold">{{ $diasRestantes }}</div>
                        </div>

                        <div class="summary-item">
                            <div class="summary-label">Status:</div>
                            <div class="summary-value">
                                <span class="badge bg-success fs-6">Ativo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-success fw-bold mb-4">
                        <i class="fas fa-rocket me-2"></i> Próximos Passos
                    </h3>

                    <div class="steps-list">
                        <div class="step-item mb-3">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h6 class="fw-bold">Escolha um Plano</h6>
                                <p class="small text-muted mb-0">Selecione o plano que melhor atende às necessidades da sua igreja.</p>
                            </div>
                        </div>

                        <div class="step-item mb-3">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h6 class="fw-bold">Faça o Pagamento</h6>
                                <p class="small text-muted mb-0">Processo seguro com múltiplas formas de pagamento.</p>
                            </div>
                        </div>

                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h6 class="fw-bold">Continue Usando</h6>
                                <p class="small text-muted mb-0">Acesse todas as funcionalidades sem limitações.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Funcionalidades que Serão Perdidas -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-5">
                <h2 class="section-header text-primary fw-bold display-5 mb-3">
                    <i class="fas fa-exclamation-triangle me-3"></i>O que você perderá se não renovar
                </h2>
                <p class="text-muted lead fs-5">Mantenha o acesso completo fazendo upgrade agora</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card feature-loss-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-users fa-3x text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold">Gestão de Membros</h5>
                            <p class="card-text small text-muted mb-0">Cadastro, perfis e controle de membros serão limitados</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card feature-loss-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-calendar-alt fa-3x text-success"></i>
                            </div>
                            <h5 class="card-title fw-bold">Eventos e Escalas</h5>
                            <p class="card-text small text-muted mb-0">Agendamento e organização de eventos serão desabilitados</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card feature-loss-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-dollar-sign fa-3x text-warning"></i>
                            </div>
                            <h5 class="card-title fw-bold">Relatórios Financeiros</h5>
                            <p class="card-text small text-muted mb-0">Controle financeiro e relatórios serão bloqueados</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card feature-loss-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-comments fa-3x text-info"></i>
                            </div>
                            <h5 class="card-title fw-bold">Comunicação</h5>
                            <p class="card-text small text-muted mb-0">Chats e notificações serão limitados</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card feature-loss-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-graduation-cap fa-3x text-danger"></i>
                            </div>
                            <h5 class="card-title fw-bold">Cursos Online</h5>
                            <p class="card-text small text-muted mb-0">Acesso aos cursos será interrompido</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card feature-loss-card border-0 shadow-sm h-100">
                        <div class="card-body p-4 text-center">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-chart-line fa-3x text-secondary"></i>
                            </div>
                            <h5 class="card-title fw-bold">Relatórios Avançados</h5>
                            <p class="card-text small text-muted mb-0">Dashboards e estatísticas serão desabilitados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Final -->
    <div class="card cta-gradient text-white border-0 shadow-lg">
        <div class="card-body p-5 text-center">
            <h3 class="fw-bold display-5 mb-3">
                Não Perca Tempo!
            </h3>

            <p class="lead mb-4 fs-5">
                Faça upgrade agora e continue aproveitando todas as funcionalidades do {{ config('app.name') }}.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                <a href="{{ route('ecommerce.subscription.upgrade') }}" class="btn btn-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-rocket me-2"></i>Fazer Upgrade Agora
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-home me-2"></i>Ir para Dashboard
                </a>
                <a href="{{ route('ecommerce.contact') }}" class="btn btn-success btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-envelope me-2"></i>Falar com Suporte
                </a>
            </div>
        </div>
    </div>
</div>