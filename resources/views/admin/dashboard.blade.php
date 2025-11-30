<div>
    {{-- Seção do cabeçalho responsivo --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Dashboard da Igreja</h1>
                            @if($igreja)
                                <h2 class="text-decoration-underline">{{ $igreja->nome }}</h2>
                            @endif
                            <p>Gerencie sua igreja com dados em tempo real</p>
                        </div>
                        <div>
                            <a href="#" class="btn btn-link btn-soft-light"
                               data-bs-toggle="offcanvas"
                               data-bs-target="#smsOffcanvas"
                               aria-controls="smsOffcanvas">
                                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.8251 15.2171H12.1748C14.0987 15.2171 15.731 13.985 16.3054 12.2764C16.3887 12.0276 16.1979 11.7713 15.9334 11.7713H14.8562C14.5133 11.7713 14.2362 11.4977 14.2362 11.16C14.2362 10.8213 14.5133 10.5467 14.8562 10.5467H15.9005C16.2463 10.5467 16.5263 10.2703 16.5263 9.92875C16.5263 9.58722 16.2463 9.31075 15.9005 9.31075H14.8562C14.5133 9.31075 14.2362 9.03619 14.2362 8.69849C14.2362 8.35984 14.5133 8.08528 14.8562 8.08528H15.9005C16.2463 8.08528 16.5263 7.8088 16.5263 7.46728C16.5263 7.12575 16.2463 6.84928 15.9005 6.84928H14.8562C14.5133 6.84928 14.2362 6.57472 14.2362 6.23606C14.2362 5.89837 14.5133 5.62381 14.8562 5.62381H15.9886C16.2483 5.62381 16.4343 5.3789 16.3645 5.13113C15.8501 3.32401 14.1694 2 12.1748 2H11.8251C9.42172 2 7.47363 3.92287 7.47363 6.29729V10.9198C7.47363 13.2933 9.42172 15.2171 11.8251 15.2171Z" fill="currentColor"></path>
                                    <path opacity="0.4" d="M19.5313 9.82568C18.9966 9.82568 18.5626 10.2533 18.5626 10.7823C18.5626 14.3554 15.6186 17.2627 12.0005 17.2627C8.38136 17.2627 5.43743 14.3554 5.43743 10.7823C5.43743 10.2533 5.00345 9.82568 4.46872 9.82568C3.93398 9.82568 3.5 10.2533 3.5 10.7823C3.5 15.0873 6.79945 18.6413 11.0318 19.1186V21.0434C11.0318 21.5715 11.4648 22.0001 12.0005 22.0001C12.5352 22.0001 12.9692 21.5715 12.9692 21.0434V19.1186C17.2006 18.6413 20.5 15.0873 20.5 10.7823C20.5 10.2533 20.066 9.82568 19.5313 9.82568Z" fill="currentColor"></path>
                                </svg>
                                Suporte técnico
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{ asset('assets/images/dashboard/top-header.png') }}" alt="header" class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header1.png') }}" alt="header" class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header2.png') }}" alt="header" class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header3.png') }}" alt="header" class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header4.png') }}" alt="header" class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header5.png') }}" alt="header" class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>

    {{-- Conteúdo do dashboard --}}
    <link rel="stylesheet" href="{{ asset('system/css/dashboard.css') }}">
    <div class="row">
        {{-- Cards de métricas --}}
        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
            <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-gradient-receita rounded p-3">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21.35L10.55 20.03C5.4 15.36 2 12.28 2 8.5C2 5.42 4.42 3 7.5 3C9.24 3 10.91 3.81 12 5.09C13.09 3.81 14.76 3 16.5 3C19.58 3 22 5.42 22 8.5C22 12.28 18.6 15.36 13.45 20.03L12 21.35Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ number_format($receitasMes, 2, ',', '.') }} Kz</h4>
                            <p class="mb-0 text-muted">Receitas do Mês</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
            <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-gradient-contratos rounded p-3">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="currentColor"/>
                                <path d="M15.5 12H13V9.5C13 9.22 12.78 9 12.5 9H12C11.72 9 11.5 9.22 11.5 9.5V13C11.5 13.28 11.72 13.5 12 13.5H15.5C15.78 13.5 16 13.28 16 13C16 12.72 15.78 12.5 15.5 12Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $totalMembros }}</h4>
                            <p class="mb-0 text-muted">Membros Ativos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
            <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-gradient-igrejas rounded p-3">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="currentColor"/>
                                <path d="M12 11H9V13H12V16H14V13H17V11H14V8H12V11Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $pedidosOracao }}</h4>
                            <p class="mb-0 text-muted">Pedidos de Oração</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
            <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-gradient-churn rounded p-3">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 10.5V7C22 6.45 21.55 6 21 6H15.5C15.22 6 15 5.78 15 5.5V3C15 2.45 14.55 2 14 2H10C9.45 2 9 2.45 9 3V5.5C9 5.78 8.78 6 8.5 6H3C2.45 6 2 6.45 2 7V10.5C2 11.05 2.45 11.5 3 11.5H8.5C8.78 11.5 9 11.72 9 12V14.5C9 14.78 8.78 15 8.5 15H3C2.45 15 2 15.45 2 16V19.5C2 20.05 2.45 20.5 3 20.5H8.5C8.78 20.5 9 20.72 9 21V22C9 22.55 9.45 23 10 23H14C14.55 23 15 22.55 15 22V21C15 20.72 15.22 20.5 15.5 20.5H21C21.55 20.5 22 20.05 22 19.5V16C22 15.45 21.55 15 21 15H15.5C15.22 15 15 14.78 15 14.5V12C15 11.72 15.22 11.5 15.5 11.5H21C21.55 11.5 22 11.05 22 10.5ZM10.5 10H13.5C13.78 10 14 10.22 14 10.5V11.5H10V10.5C10 10.22 10.22 10 10.5 10Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $eventosAtivos }}</h4>
                            <p class="mb-0 text-muted">Eventos Ativos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Novos Cards --}}
        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
            <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-gradient-vencendo rounded p-3">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="currentColor"/>
                                <path d="M12 7C11.45 7 11 7.45 11 8V12C11 12.55 11.45 13 12 13C12.55 13 13 12.55 13 12V8C13 7.45 12.55 7 12 7Z" fill="currentColor"/>
                                <path d="M12 15C11.45 15 11 15.45 11 16C11 16.55 11.45 17 12 17C12.55 17 13 16.55 13 16C13 15.45 12.55 15 12 15Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $cursosAtivos }}</h4>
                            <p class="mb-0 text-muted">Cursos Ativos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
            <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="icon-gradient-mrr rounded p-3">
                            <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z" fill="currentColor"/>
                                <path d="M7 7H17V9H7V7ZM7 11H17V13H7V11ZM7 15H13V17H7V15Z" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ number_format($engajamentoTotal, 0, ',', '.') }}</h4>
                            <p class="mb-0 text-muted">Pontos Engajamento</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico de Receitas --}}
        <div class="col-md-12 col-xl-8">
            <div class="card" data-aos="fade-up" data-aos-delay="800" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Receitas Mensais</h4>
                        <p class="mb-0">Últimos 6 meses</p>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownPeriodo" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($periodoSelecionado) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownPeriodo">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodo('mes')">Este Mês</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodo('ano')">Este Ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 280px;"><canvas id="receitasChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfico de Membros --}}
        <div class="col-md-12 col-xl-4">
            <div class="card" data-aos="fade-up" data-aos-delay="900" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Crescimento de Membros</h4>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 280px;"><canvas id="membrosChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfico de Engajamento --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1000" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Engajamento</h4>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 240px;"><canvas id="engajamentoChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfico de Pedidos --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1100" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Pedidos de Oração</h4>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 240px;"><canvas id="pedidosChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfico de Distribuição Geográfica Expandido --}}
        <div class="col-12">
            <div class="card" data-aos="fade-up" data-aos-delay="1200" wire:ignore>
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>Distribuição Geográfica dos Membros
                    </h4>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary me-2">{{ count($this->graficoDistribuicaoGeografica) }} regiões</span>
                        <span class="badge bg-success">{{ array_sum(array_column($this->graficoDistribuicaoGeografica, 'total')) }} membros mapeados</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Gráfico Principal --}}
                        <div class="col-lg-8">
                            <div wire:ignore style="height: 300px;"><canvas id="distribuicaoGeograficaChart"></canvas></div>
                        </div>

                        {{-- Informações Laterais --}}
                        <div class="col-lg-4">
                            <div class="border-start ps-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-users me-1"></i>Usuários Próximos da Igreja
                                </h6>

                                {{-- Estatísticas de proximidade --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Na mesma cidade</small>
                                        <span class="badge bg-success">{{ $this->usuariosProximos['mesma_cidade'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Na mesma província</small>
                                        <span class="badge bg-info">{{ $this->usuariosProximos['mesma_provincia'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">Em outras regiões</small>
                                        <span class="badge bg-secondary">{{ $this->usuariosProximos['outras_regioes'] ?? 0 }}</span>
                                    </div>
                                </div>

                                <hr>

                                {{-- Top 3 regiões --}}
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-trophy me-1"></i>Top 3 Regiões
                                </h6>
                                <div class="mb-3">
                                    @foreach(array_slice($this->graficoDistribuicaoGeografica, 0, 3) as $index => $regiao)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-truncate" style="max-width: 150px;" title="{{ $regiao['localizacao'] }}">
                                                {{ Str::limit($regiao['localizacao'], 20) }}
                                            </small>
                                            <span class="badge bg-primary">{{ $regiao['total'] }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <hr>

                                {{-- Estatísticas adicionais --}}
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-chart-bar me-1"></i>Estatísticas
                                </h6>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Região mais populosa</small>
                                        <small class="fw-semibold">
                                            {{ $this->estatisticasGeograficas['regiao_mais_populosa'] ?? 'N/A' }}
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Cobertura geográfica</small>
                                        <small class="fw-semibold">{{ $this->estatisticasGeograficas['cobertura_geografica'] ?? 0 }} regiões</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Média por região</small>
                                        <small class="fw-semibold">{{ $this->estatisticasGeograficas['media_por_regiao'] ?? 0 }} membros</small>
                                    </div>
                                </div>

                                {{-- Botão para ver detalhes --}}
                                <div class="mt-3">
                                    <button class="btn btn-outline-primary btn-sm w-100" data-bs-toggle="modal" data-bs-target="#distribuicaoGeograficaModal">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes Completos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Distribuição Geográfica --}}
    @include('admin.modals.distribuicao-geografica-modal')

    {{-- Componente SMS Manager --}}
    @livewire('sms.sms-manager')

</div>

@push('scripts')
<script data-navigate-once>
    const dashboardManager = {
        charts: {},

        destroyAllCharts() {
            Object.values(this.charts).forEach(chart => {
                if (chart instanceof Chart) chart.destroy();
            });
            this.charts = {};
        },

        init(chartData) {
            if (!chartData) return;

            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#6c757d';

            requestAnimationFrame(() => {
                this.destroyAllCharts();
                this.initReceitasChart(chartData);
                this.initMembrosChart(chartData);
                this.initEngajamentoChart(chartData);
                this.initPedidosChart(chartData);
                this.initDistribuicaoGeograficaChart(chartData);
            });
        },

        formatKwanza: (value) => `Kz ${Number(value || 0).toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
        formatNumber: (value) => Number(value || 0).toLocaleString('pt-AO'),

        initReceitasChart(chartData) {
            const ctx = document.getElementById('receitasChart')?.getContext('2d');
            if (!ctx) return;

            const receitas = chartData.receitas || [];
            if (receitas.length === 0) return;

            this.charts.receitas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: receitas.map(r => r.mes),
                    datasets: [{
                        label: 'Receitas',
                        data: receitas.map(r => r.valor),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `Receitas: ${this.formatKwanza(c.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => this.formatKwanza(v)
                            }
                        }
                    }
                }
            });
        },

        initMembrosChart(chartData) {
            const ctx = document.getElementById('membrosChart')?.getContext('2d');
            if (!ctx) return;

            const membros = chartData.membros || [];
            if (membros.length === 0) return;

            this.charts.membros = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: membros.map(m => m.mes),
                    datasets: [{
                        label: 'Membros',
                        data: membros.map(m => m.total),
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `Membros: ${this.formatNumber(c.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (v) => this.formatNumber(v)
                            }
                        }
                    }
                }
            });
        },

        initEngajamentoChart(chartData) {
            const ctx = document.getElementById('engajamentoChart')?.getContext('2d');
            if (!ctx) return;

            const engajamento = chartData.engajamento || [];
            if (engajamento.length === 0) return;

            this.charts.engajamento = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: engajamento.map(e => e.mes),
                    datasets: [{
                        label: 'Pontos',
                        data: engajamento.map(e => e.pontos),
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `Pontos: ${this.formatNumber(c.parsed.y)}`
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        initPedidosChart(chartData) {
            const ctx = document.getElementById('pedidosChart')?.getContext('2d');
            if (!ctx) return;

            const pedidos = chartData.pedidos || [];
            if (pedidos.length === 0) return;

            this.charts.pedidos = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: pedidos.map(p => p.mes),
                    datasets: [{
                        label: 'Pedidos',
                        data: pedidos.map(p => p.total),
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `Pedidos: ${this.formatNumber(c.parsed.y)}`
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        initDistribuicaoGeograficaChart(chartData) {
            const ctx = document.getElementById('distribuicaoGeograficaChart')?.getContext('2d');
            if (!ctx) return;

            const distribuicao = chartData.distribuicaoGeografica || [];
            if (distribuicao.length === 0) return;

            this.charts.distribuicaoGeografica = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: distribuicao.map(d => d.localizacao),
                    datasets: [{
                        label: 'Membros',
                        data: distribuicao.map(d => d.total),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)',
                            'rgba(6, 182, 212, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(251, 146, 60, 0.8)',
                            'rgba(244, 63, 94, 0.8)'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (c) => `${c.label}: ${this.formatNumber(c.parsed)} membros`
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    };

    document.addEventListener('livewire:init', () => {
        Livewire.on('update-charts', (event) => {
            const chartData = event.detail ? event.detail[0] : event;
            dashboardManager.init(chartData);
        });
    });

    document.addEventListener('livewire:navigated', () => {
        const chartData = @json($this->getChartData());
        dashboardManager.init(chartData);
    });

    // Função para inicializar gráfico do modal
    function initDistribuicaoGeograficaModalChart() {
        const ctx = document.getElementById('distribuicaoGeograficaModalChart');
        if (!ctx) return;

        const chartData = @json($this->getChartData());
        const distribuicao = chartData.distribuicaoGeografica || [];

        if (distribuicao.length === 0) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: distribuicao.map(d => d.localizacao),
                datasets: [{
                    label: 'Membros',
                    data: distribuicao.map(d => d.total),
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(244, 63, 94, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (c) => `${c.label}: ${dashboardManager.formatNumber(c.parsed)} membros`
                        }
                    }
                },
                cutout: '60%'
            }
        });
    }

    // Event listener para quando o modal for aberto
    document.getElementById('distribuicaoGeograficaModal')?.addEventListener('shown.bs.modal', function () {
        setTimeout(() => {
            initDistribuicaoGeograficaModalChart();
        }, 100);
    });
</script>
@endpush
