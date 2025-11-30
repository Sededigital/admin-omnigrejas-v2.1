<div class="container-fluid py-1">
    <!-- Hero Section -->
    <div class="card bg-gradient-hero text-white border-0 shadow-lg mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-check-circle me-3"></i>
                        Bem-vindo ao {{ config('app.name') }}!
                    </h1>
                    <p class="lead mb-4">
                        Seu período de teste gratuito foi ativado com sucesso. Explore todas as funcionalidades disponíveis.
                    </p>

                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h4 mb-1">{{ $periodoDias }}</div>
                            <div class="small opacity-75">Dias de Teste</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">{{ $dataFim }}</div>
                            <div class="small opacity-75">Expira em</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">{{ $igrejaNome }}</div>
                            <div class="small opacity-75">Igreja</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-center d-none d-lg-block">
                    <i class="fas fa-rocket hero-icon opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Trial -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-primary fw-bold mb-4">
                        <i class="fas fa-user me-2"></i> Seus Dados
                    </h3>

                    <div class="user-info">
                        <div class="info-item mb-3">
                            <div class="info-label">Nome:</div>
                            <div class="info-value fw-bold">{{ $nomeUsuario }}</div>
                        </div>

                        <div class="info-item mb-3">
                            <div class="info-label">E-mail:</div>
                            <div class="info-value">{{ $emailUsuario }}</div>
                        </div>

                        <div class="info-item mb-3">
                            <div class="info-label">Igreja:</div>
                            <div class="info-value">{{ $igrejaNome }}</div>
                        </div>

                        <div class="info-item">
                            <div class="info-label">Status do Trial:</div>
                            <div class="info-value">
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
                        <i class="fas fa-calendar-alt me-2"></i> Período de Teste
                    </h3>

                    <div class="trial-period">
                        <div class="period-item mb-3">
                            <div class="period-label">Data de Início:</div>
                            <div class="period-value fw-bold text-primary">{{ $dataInicio }}</div>
                        </div>

                        <div class="period-item mb-3">
                            <div class="period-label">Data de Expiração:</div>
                            <div class="period-value fw-bold text-warning">{{ $dataFim }}</div>
                        </div>

                        <div class="period-item mb-3">
                            <div class="period-label">Dias Restantes:</div>
                            <div class="period-value fw-bold text-info">{{ $trial->diasRestantes() }}</div>
                        </div>

                        <div class="period-item mb-3">
                            <div class="period-label">Período Total:</div>
                            <div class="period-value">{{ $periodoDias }} dias</div>
                        </div>

                        <div class="period-item mb-3">
                            <div class="period-label">Dias de Trial:</div>
                            <div class="period-value">{{ $totalDiasTrial }} dias</div>
                        </div>

                        <div class="period-item">
                            <div class="period-label">Último Acesso:</div>
                            <div class="period-value">{{ $ultimoAcesso }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Funcionalidades Disponíveis -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-5">
                        <h2 class="section-header text-primary fw-bold display-5 mb-3">
                            <i class="fas fa-star me-3"></i>O que você pode fazer agora
                        </h2>
                        <p class="text-muted lead fs-5">Explore todas as funcionalidades disponíveis no seu período de teste</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-users fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Gerenciar Membros</h5>
                                    <p class="card-text small text-muted mb-3">Cadastre e organize os membros da sua igreja</p>
                                    <a href="{{ url('/membros') }}" class="btn btn-outline-primary btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-calendar-alt fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Criar Eventos</h5>
                                    <p class="card-text small text-muted mb-3">Agende cultos, reuniões e eventos especiais</p>
                                    <a href="{{ url('/eventos') }}" class="btn btn-outline-success btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-dollar-sign fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Controle Financeiro</h5>
                                    <p class="card-text small text-muted mb-3">Gerencie dízimos, ofertas e relatórios</p>
                                    <a href="{{ url('/financeiro') }}" class="btn btn-outline-warning btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-comments fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Comunicação</h5>
                                    <p class="card-text small text-muted mb-3">Chats, posts e notificações da igreja</p>
                                    <a href="{{ url('/social') }}" class="btn btn-outline-info btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-graduation-cap fa-3x text-danger"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Cursos Online</h5>
                                    <p class="card-text small text-muted mb-3">Acesse cursos de capacitação e certificados</p>
                                    <a href="{{ url('/cursos') }}" class="btn btn-outline-danger btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-chart-line fa-3x text-secondary"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Relatórios</h5>
                                    <p class="card-text small text-muted mb-3">Dashboards e estatísticas completas</p>
                                    <a href="{{ url('/relatorios') }}" class="btn btn-outline-secondary btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-handshake fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Alianças</h5>
                                    <p class="card-text small text-muted mb-3">Conecte-se com outras igrejas</p>
                                    <a href="{{ url('/aliancas') }}" class="btn btn-outline-primary btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <div class="card feature-available-card border-0 shadow-sm h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-store fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">Marketplace</h5>
                                    <p class="card-text small text-muted mb-3">Venda produtos da sua igreja</p>
                                    <a href="{{ url('/marketplace') }}" class="btn btn-outline-success btn-sm">Acessar</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dicas de Uso -->
    <div class="row mb-5">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-info fw-bold mb-4">
                        <i class="fas fa-lightbulb me-2"></i> Dicas para Aproveitar Melhor
                    </h3>

                    <div class="tips-list">
                        <div class="tip-item mb-3">
                            <div class="tip-icon">
                                <i class="fas fa-user-plus text-primary"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="fw-bold mb-1">Comece Cadastrando Membros</h6>
                                <p class="small text-muted mb-0">Adicione os membros da sua igreja para ter uma base sólida.</p>
                            </div>
                        </div>

                        <div class="tip-item mb-3">
                            <div class="tip-icon">
                                <i class="fas fa-calendar-plus text-success"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="fw-bold mb-1">Crie Seu Primeiro Evento</h6>
                                <p class="small text-muted mb-0">Agende um culto ou reunião para testar as funcionalidades.</p>
                            </div>
                        </div>

                        <div class="tip-item">
                            <div class="tip-icon">
                                <i class="fas fa-share-alt text-info"></i>
                            </div>
                            <div class="tip-content">
                                <h6 class="fw-bold mb-1">Convide Outros Líderes</h6>
                                <p class="small text-muted mb-0">Adicione pastores e obreiros para trabalhar em equipe.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-warning fw-bold mb-4">
                        <i class="fas fa-chart-bar me-2"></i> Estatísticas de Uso
                    </h3>

                    <div class="usage-stats">
                        <div class="stat-item mb-3">
                            <div class="stat-icon">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div class="stat-content">
                                <h6 class="fw-bold mb-1">Membros Criados</h6>
                                <p class="mb-0">{{ $estatisticasUso['membros_criados'] }} membros cadastrados</p>
                            </div>
                        </div>

                        <div class="stat-item mb-3">
                            <div class="stat-icon">
                                <i class="fas fa-edit text-success"></i>
                            </div>
                            <div class="stat-content">
                                <h6 class="fw-bold mb-1">Posts Criados</h6>
                                <p class="mb-0">{{ $estatisticasUso['posts_criados'] }} posts publicados</p>
                            </div>
                        </div>

                        <div class="stat-item mb-3">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-alt text-warning"></i>
                            </div>
                            <div class="stat-content">
                                <h6 class="fw-bold mb-1">Eventos Criados</h6>
                                <p class="mb-0">{{ $estatisticasUso['eventos_criados'] }} eventos agendados</p>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line text-info"></i>
                            </div>
                            <div class="stat-content">
                                <h6 class="fw-bold mb-1">Total de Itens</h6>
                                <p class="mb-0">{{ $estatisticasUso['total_itens'] }} itens criados no total</p>
                            </div>
                        </div>
                    </div>

                    <div class="reminder-actions mt-4">
                        <div class="d-grid gap-2">
                            <a href="{{ route('ecommerce.subscription.upgrade') }}" class="btn btn-primary">
                                <i class="fas fa-rocket me-2"></i>Ver Planos de Upgrade
                            </a>
                            <a href="{{ route('ecommerce.contact') }}" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>Tirar Dúvidas
                            </a>
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
                Comece a Explorar Agora!
            </h3>

            <p class="lead mb-4 fs-5">
                Seu período de teste está ativo. Aproveite todas as funcionalidades disponíveis.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                <button wire:click="irParaDashboard" class="btn btn-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-home me-2"></i>Ir para Dashboard
                </button>
                <a href="{{ route('ecommerce.contact') }}" class="btn btn-outline-light btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-envelope me-2"></i>Suporte
                </a>
                <a href="{{ route('ecommerce.who.we') }}" class="btn btn-success btn-lg px-4 fw-bold shadow-sm">
                    <i class="fas fa-info-circle me-2"></i>Sobre Nós
                </a>
            </div>
        </div>
    </div>
</div>