<div>
    {{-- Seção do cabeçalho responsivo --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Admin Omnigrejas!</h1>
                            <p>Conectados, um só proposito e muitas ideias</p>
                        </div>
                        <div>
                            <a href="#" class="btn btn-link btn-soft-light">
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
                            <h4 class="mb-0" id="receita-value">{{ number_format($receitaMesAtual, 2, ',', '.') }} Kz</h4>
                            <p class="mb-0 text-muted">Receita</p>
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
                             <h4 class="mb-0">{{ $novosContratos }}</h4>
                             <p class="mb-0 text-muted">Novos Contratos</p>
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
                            <h4 class="mb-0">{{ $igrejasAtivas }}</h4>
                            <p class="mb-0 text-muted">Igrejas Ativas</p>
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
                            <h4 class="mb-0">{{ $churn }}%</h4>
                            <p class="mb-0 text-muted">Churn mensal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Novos Cards de Assinaturas --}}
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
                             <h4 class="mb-0">{{ $assinaturasVencendo7d }}</h4>
                             <p class="mb-0 text-muted">Vencendo em 7 dias</p>
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
                            <h4 class="mb-0">{{ number_format($receitaRecorrenteMensal, 0, ',', '.') }} Kz</h4>
                            <p class="mb-0 text-muted">MRR</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3" wire:ignore>
             <div class="card enhanced-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                         <div class="icon-gradient-falhas rounded p-3">
                             <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" fill="currentColor"/>
                             </svg>
                         </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $falhasPagamentoMes }}</h4>
                            <p class="mb-0 text-muted">Falhas Pagamento</p>
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
                                <path d="M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z" fill="currentColor"/>
                             </svg>
                         </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $taxaRenovacao }}%</h4>
                            <p class="mb-0 text-muted">Taxa Renovação</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloco de Vendas Brutas --}}
        <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="800" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">
                            <span>{{ number_format($receitaMesAtual, 2, ',', '.') }}</span> Kz
                        </h4>
                        <p class="mb-0">Vendas Brutas ({{ ucfirst($periodoSelecionado) }})</p>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownMenuButton22" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($periodoSelecionado) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton22">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodo('semana')">Esta Semana</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodo('mes')">Este Mês</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodo('ano')">Este Ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 250px;"><canvas id="grossSalesChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Tabela de Igrejas Mais Engajadas --}}
        <div class="col-md-12 col-lg-12">
            <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="600" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="mb-2 card-title">Igrejas Mais Engajadas</h4>
                        <p class="mb-0">
                            <svg class="me-2 text-primary icon-24" width="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                            </svg>
                            <span>{{ count($enterpriseClients) }}</span> novas este mês
                        </p>
                    </div>
                </div>
                <div class="p-0 card-body">
                    <div class="mt-4 table-responsive">
                        <table id="basic-table" class="table mb-0 table-striped" role="grid">
                            <thead>
                                <tr>
                                    <th>IGREJA</th>
                                    <th>CONTATOS</th>
                                    <th>PEDIDOS/ENGAJAMENTO</th>
                                    <th>COMPLETUDE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enterpriseClients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded bg-primary-subtle img-fluid avatar-40 me-3" src="../assets/images/shapes/01.png" alt="profile">
                                            <h6>{{ $client->igreja->nome ?? 'Igreja' }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="iq-media-group iq-media-group-1">
                                            <span class="icon iq-icon-box-3 rounded-pill">{{ $client->igreja->contacto ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($client->total, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="mb-2 d-flex align-items-center">
                                            <h6>{{ $client->percentual ?? rand(25,100) }}%</h6>
                                        </div>
                                        <div class="shadow-none progress bg-primary-subtle w-100" style="height: 4px">
                                            <div class="progress-bar bg-primary" data-toggle="progress-bar" role="progressbar" aria-valuenow="{{ $client->percentual ?? rand(25,100) }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $client->percentual ?? rand(25,100) }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nenhuma igreja engajada encontrada.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nova Seção: Igrejas Mais Ativas --}}
        <div class="col-md-12 col-lg-6">
            <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="700" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="mb-2 card-title">Igrejas Mais Ativas</h4>
                        <p class="mb-0">
                            <svg class="me-2 text-success icon-24" width="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" />
                                <path fill="currentColor" d="M12 11H9V13H12V16H14V13H17V11H14V8H12V11Z" />
                            </svg>
                            <span>{{ count($igrejasMaisAtivas) }}</span> igrejas com mais conteúdo
                        </p>
                    </div>
                </div>
                <div class="p-0 card-body">
                    <div class="mt-4 table-responsive">
                        <table class="table mb-0 table-striped" role="grid">
                            <thead>
                                <tr>
                                    <th>IGREJA</th>
                                    <th>CONTATOS</th>
                                    <th>TOTAL CONTEÚDO</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($igrejasMaisAtivas as $igreja)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded bg-success-subtle img-fluid avatar-40 me-3" src="../assets/images/shapes/01.png" alt="profile">
                                            <h6>{{ $igreja->nome ?? 'Igreja' }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="iq-media-group iq-media-group-1">
                                            <span class="icon iq-icon-box-3 rounded-pill">{{ $igreja->contacto ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($igreja->total_conteudo ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-success">Ativa</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nenhuma igreja ativa encontrada.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nova Seção: Top Engajamento --}}
        <div class="col-md-12 col-lg-6">
            <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="800" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="mb-2 card-title">Top Engajamento</h4>
                        <p class="mb-0">
                            <svg class="me-2 text-warning icon-24" width="24" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" />
                            </svg>
                            <span>{{ count($topIgrejasEngajamento) }}</span> igrejas com mais pontos
                        </p>
                    </div>
                </div>
                <div class="p-0 card-body">
                    <div class="mt-4 table-responsive">
                        <table class="table mb-0 table-striped" role="grid">
                            <thead>
                                <tr>
                                    <th>IGREJA</th>
                                    <th>CONTATOS</th>
                                    <th>PONTOS</th>
                                    <th>RANKING</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topIgrejasEngajamento as $index => $engajamento)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded bg-warning-subtle img-fluid avatar-40 me-3" src="../assets/images/shapes/01.png" alt="profile">
                                            <h6>{{ $engajamento->igreja->nome ?? 'Igreja' }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="iq-media-group iq-media-group-1">
                                            <span class="icon iq-icon-box-3 rounded-pill">{{ $engajamento->igreja->contacto ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ number_format($engajamento->total_pontos ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-warning">#{{ $index + 1 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nenhum engajamento encontrado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloco de Performance dos Pacotes --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="900" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Performance dos Pacotes</h4>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownPerformancePacotes" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($periodoPerformancePacotes) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownPerformancePacotes">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoPerformancePacotes('semana')">Esta Semana</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoPerformancePacotes('mes')">Este Mês</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoPerformancePacotes('ano')">Este Ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="flex-wrap d-flex align-items-center justify-content-between">
                        <div class="col-md-8 col-lg-8">
                            <div wire:ignore style="height: 250px;"><canvas id="performancePacotesChart"></canvas></div>
                        </div>
                        <div class="d-grid gap col-md-4 col-lg-4">
                            @foreach($performancePacotes as $pacote)
                            <div class="d-flex align-items-start">
                                <svg class="mt-2 icon-14" xmlns="http://www.w3.org/2000/svg" width="14" viewBox="0 0 24 24" fill="#3a57e8">
                                    <g><circle cx="12" cy="12" r="8" fill="#3a57e8"></circle></g>
                                </svg>
                                <div class="ms-3">
                                    <span class="text-gray">{{ $pacote['label'] ?? 'Pacote' }}</span>
                                    <h6>{{ number_format($pacote['total'], 0, ',', '.') }}</h6>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bloco de Churn vs Retenção --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1000" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Taxa de Churn vs Retenção</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 250px;"><canvas id="churnChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Gráfico Crescimento de Usuários --}}
        <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="1100" wire:ignore>
                <div class="flex-wrap card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Crescimento de Usuários</h4>
                    </div>
                    <div class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownCrescimentoUsuarios" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($periodoCrescimentoUsuarios) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownCrescimentoUsuarios">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoCrescimentoUsuarios('semana')">Esta Semana</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoCrescimentoUsuarios('mes')">Este Mês</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoCrescimentoUsuarios('ano')">Este Ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 250px;"><canvas id="crescimentoUsuariosChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Métodos de Pagamento --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1200" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Métodos de Pagamento</h4>
                    <div class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownMetodosPagamento" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($periodoMetodosPagamento) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMetodosPagamento">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoMetodosPagamento('semana')">Esta Semana</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoMetodosPagamento('mes')">Este Mês</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoMetodosPagamento('ano')">Este Ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 250px;"><canvas id="metodosPagamentoChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Distribuição Geográfica --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1300" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Distribuição Geográfica</h4>
                    <div class="dropdown">
                        <a href="#" class="text-gray dropdown-toggle" id="dropdownDistribuicaoGeografica" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ ucfirst($periodoDistribuicaoGeografica) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownDistribuicaoGeografica">
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoDistribuicaoGeografica('semana')">Esta Semana</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoDistribuicaoGeografica('mes')">Este Mês</a></li>
                            <li><a class="dropdown-item" href="#" wire:click.prevent="setPeriodoDistribuicaoGeografica('ano')">Este Ano</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div wire:ignore style="height: 250px;"><canvas id="distribuicaoGeograficaChart"></canvas></div>
                </div>
            </div>
        </div>

        {{-- Tabela de Assinaturas Vencendo --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1400" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Assinaturas Vencendo</h4>
                        <p class="mb-0 text-muted">Próximos 30 dias</p>
                    </div>
                    <span class="badge bg-warning m-2"><h6 class="m-1 mt-1 text-light">{{ count($assinaturasVencendoLista) }}</h6></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Igreja</th>
                                    <th>Pacote</th>
                                    <th>Vencimento</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assinaturasVencendoLista as $assinatura)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-32 me-2 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($assinatura->igreja->nome ?? 'I', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $assinatura->igreja->nome ?? 'Igreja' }}</h6>
                                                <small class="text-muted">{{ $assinatura->igreja->nif ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $assinatura->pacote->nome ?? 'Pacote' }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted">{{ $assinatura->data_fim ? $assinatura->data_fim->format('d/m/Y') : '-' }}</small><br>
                                            <small class="text-danger">{{ $assinatura->data_fim ? $assinatura->data_fim->diffForHumans() : '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $diasRestantes = $assinatura->data_fim ? $assinatura->data_fim->diffInDays(now()) : 0;
                                        @endphp
                                        @if($diasRestantes <= 7)
                                            <span class="badge bg-danger">Crítico</span>
                                        @elseif($diasRestantes <= 15)
                                            <span class="badge bg-warning">Atenção</span>
                                        @else
                                            <span class="badge bg-info">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        <div class="text-muted">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Nenhuma assinatura vencendo nos próximos 30 dias
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela de Falhas de Pagamento --}}
        <div class="col-md-12 col-xl-6">
            <div class="card" data-aos="fade-up" data-aos-delay="1500" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Falhas de Pagamento</h4>
                        <p class="mb-0 text-muted">Pendentes de resolução</p>
                    </div>
                    <span class="badge bg-danger m-2"><h6 class="m-1 mt-1 text-light">{{ count($falhasPagamentoLista) }}</h6></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Igreja</th>
                                    <th>Motivo</th>
                                    <th>Data</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($falhasPagamentoLista as $falha)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-32 me-2 bg-soft-danger rounded">
                                                <span class="avatar-title">{{ substr($falha->igreja->nome ?? 'I', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $falha->igreja->nome ?? 'Igreja' }}</h6>
                                                <small class="text-muted">{{ $falha->igreja->nif ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($falha->motivo, 30) }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted">{{ $falha->data ? $falha->data->format('d/m/Y') : '-' }}</small><br>
                                            <small class="text-danger">{{ $falha->data ? $falha->data->diffForHumans() : '-' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Resolver">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3">
                                        <div class="text-muted">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Nenhuma falha de pagamento pendente
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Logs Recentes de Assinaturas --}}
        <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="1600" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Atividade Recente de Assinaturas</h4>
                        <p class="mb-0 text-muted">Últimas 15 ações</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Igreja</th>
                                    <th>Pacote</th>
                                    <th>Ação</th>
                                    <th>Descrição</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logsAssinaturasRecentes as $log)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-32 me-2 bg-soft-secondary rounded">
                                                <span class="avatar-title">{{ substr($log->igreja->nome ?? 'I', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $log->igreja->nome ?? 'Igreja' }}</h6>
                                                <small class="text-muted">{{ $log->igreja->nif ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->pacote)
                                            <span class="badge bg-secondary">{{ $log->pacote->nome }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $acaoClass = match($log->acao) {
                                                'criado', 'renovado' => 'success',
                                                'cancelado', 'expirado' => 'danger',
                                                'upgrade' => 'info',
                                                'downgrade' => 'warning',
                                                'pagamento' => 'primary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $acaoClass }}">{{ ucfirst($log->acao) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($log->descricao ?? 'Sem descrição', 40) }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted">{{ $log->data_acao ? $log->data_acao->format('d/m/Y H:i') : '-' }}</small><br>
                                            <small class="text-muted">{{ $log->data_acao ? $log->data_acao->diffForHumans() : '-' }}</small>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <div class="text-muted">
                                            <i class="fas fa-history me-2"></i>
                                            Nenhuma atividade recente encontrada
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Nova Seção: Igrejas Inativas --}}
        <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="1700" wire:ignore>
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Igrejas Inativas</h4>
                        <p class="mb-0 text-muted">Sem atividade nos últimos 30 dias</p>
                    </div>
                    <span class="badge bg-danger m-2"><h6 class="m-1 mt-1 text-light">{{ count($igrejasInativasLista) }}</h6></span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Igreja</th>
                                    <th>Contato</th>
                                    <th>Status Assinatura</th>
                                    <th>Última Atividade</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($igrejasInativasLista as $igreja)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-32 me-2 bg-soft-danger rounded">
                                                <span class="avatar-title">{{ substr($igreja->nome ?? 'I', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $igreja->nome ?? 'Igreja' }}</h6>
                                                <small class="text-muted">{{ $igreja->nif ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="iq-media-group iq-media-group-1">
                                            <span class="icon iq-icon-box-3 rounded-pill">{{ $igreja->contacto ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($igreja->assinaturaAtual)
                                            @if($igreja->assinaturaAtual->status === 'Ativo')
                                                <span class="badge bg-success">Ativa</span>
                                            @elseif($igreja->assinaturaAtual->status === 'Expirado')
                                                <span class="badge bg-danger">Expirada</span>
                                            @else
                                                <span class="badge bg-warning">{{ $igreja->assinaturaAtual->status }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Sem Assinatura</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <small class="text-muted">Sem atividade recente</small><br>
                                            <small class="text-danger">Últimos 30 dias</small>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Enviar lembrete">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <div class="text-muted">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            Todas as igrejas estão ativas!
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')

<script  data-navigate-once>
    // Cria um objeto "manager" para organizar o código e evitar poluir o escopo global.
    const dashboardManager = {
        charts: {},

        // Destroi todas as instâncias de gráficos existentes para evitar duplicatas e vazamentos de memória.
        destroyAllCharts() {
            Object.values(this.charts).forEach(chart => {
                if (chart instanceof Chart) chart.destroy();
            });
            this.charts = {};
        },

        // Função principal que inicializa todos os gráficos.
        init(chartData) {
            if (!chartData) return;

            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#6c757d';

            // Garante que o DOM esteja pronto antes de tentar criar os gráficos.
            requestAnimationFrame(() => {
                this.destroyAllCharts();
                this.initGrossSalesChart(chartData);
                this.initPerformancePacotesChart(chartData);
                this.initChurnChart(chartData);
                this.initCrescimentoUsuariosChart(chartData);
                this.initMetodosPagamentoChart(chartData);
                this.initDistribuicaoGeograficaChart(chartData);
            });
        },

        // --- Funções de formatação e utilitários ---
        formatKwanza: (value) => `Kz ${Number(value || 0).toLocaleString('pt-AO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`,
        formatNumber: (value) => Number(value || 0).toLocaleString('pt-AO'),
        getLabels(data, periodo = 'mes') {
            const keys = Object.keys(data);
            const monthNames = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
            if (periodo === 'mes' || periodo === 'ano') {
                return keys.map(key => monthNames[parseInt(key) - 1] || key);
            }
            return (periodo === 'semana') ? keys.map(key => `Dia ${key}`) : keys;
        },

        // --- Funções de inicialização para cada gráfico ---
        initGrossSalesChart(chartData) {
            const ctx = document.getElementById('grossSalesChart')?.getContext('2d');
            if (!ctx) return;

            // Verifica se há dados válidos
            const grossSales = chartData.grossSales || {};
            const hasData = Object.keys(grossSales).length > 0;

            if (!hasData) {
                // Se não há dados, cria um gráfico vazio
                this.charts.grossSales = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Sem dados'],
                        datasets: [{
                            label: 'Vendas Brutas',
                            data: [0],
                            backgroundColor: 'rgba(200, 200, 200, 0.5)',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        },
                        scales: { y: { beginAtZero: true } }
                    }
                });
                return;
            }

            this.charts.grossSales = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: this.getLabels(grossSales, chartData.periodo),
                    datasets: [{
                        label: 'Vendas Brutas',
                        data: Object.values(grossSales),
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
                                label: (c) => `Vendas: ${this.formatKwanza(c.parsed.y)}`
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

        initPerformancePacotesChart(chartData) {
            const ctx = document.getElementById('performancePacotesChart')?.getContext('2d');
            if (!ctx) return;

            // Verifica se há dados válidos
            const performancePacotes = Array.isArray(chartData.performancePacotes) ? chartData.performancePacotes : [];
            if (performancePacotes.length === 0) {
                // Se não há dados, cria um gráfico vazio
                this.charts.performancePacotes = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Sem dados'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['rgba(200, 200, 200, 0.5)'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        }
                    }
                });
                return;
            }

            this.charts.performancePacotes = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: performancePacotes.map(p => p.label || 'N/A'),
                    datasets: [{
                        data: performancePacotes.map(p => p.total || 0),
                        backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(139, 92, 246, 0.8)'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `${c.label}: ${this.formatNumber(c.parsed)}`
                            }
                        }
                    }
                }
            });
        },

        initChurnChart(chartData) {
            const ctx = document.getElementById('churnChart')?.getContext('2d');
            if (!ctx) return;
            const churn = chartData.churn || 0;
            this.charts.churn = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Churn', 'Retenção'],
                    datasets: [{ data: [churn, 100 - churn], backgroundColor: ['rgba(239, 68, 68, 0.8)', 'rgba(16, 185, 129, 0.8)'], borderWidth: 2 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: (c) => `${c.label}: ${c.parsed.toFixed(1)}%` } } } }
            });
        },

        initCrescimentoUsuariosChart(chartData) {
            const ctx = document.getElementById('crescimentoUsuariosChart')?.getContext('2d');
            if (!ctx) return;

            // Verifica se há dados válidos
            const crescimentoUsuarios = chartData.crescimentoUsuarios || {};
            const hasData = Object.keys(crescimentoUsuarios).length > 0;

            if (!hasData) {
                // Se não há dados, cria um gráfico vazio
                this.charts.crescimentoUsuarios = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Sem dados'],
                        datasets: [{
                            label: 'Novos Usuários',
                            data: [0],
                            backgroundColor: 'rgba(200, 200, 200, 0.1)',
                            borderColor: 'rgba(200, 200, 200, 1)',
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
                            tooltip: { enabled: false }
                        },
                        scales: { y: { beginAtZero: true } }
                    }
                });
                return;
            }

            this.charts.crescimentoUsuarios = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.getLabels(crescimentoUsuarios, chartData.periodo),
                    datasets: [{
                        label: 'Novos Usuários',
                        data: Object.values(crescimentoUsuarios),
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
                                label: (c) => `Novos usuários: ${this.formatNumber(c.parsed.y)}`
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

        initMetodosPagamentoChart(chartData) {
            const ctx = document.getElementById('metodosPagamentoChart')?.getContext('2d');
            if (!ctx) return;

            // Verifica se há dados válidos
            const metodosPagamento = Array.isArray(chartData.metodosPagamento) ? chartData.metodosPagamento : [];
            if (metodosPagamento.length === 0) {
                // Se não há dados, cria um gráfico vazio ou com dados padrão
                this.charts.metodosPagamento = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Sem dados'],
                        datasets: [{
                            label: 'Quantidade',
                            data: [0],
                            backgroundColor: ['rgba(200, 200, 200, 0.5)'],
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        },
                        scales: { x: { beginAtZero: true } }
                    }
                });
                return;
            }

            this.charts.metodosPagamento = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: metodosPagamento.map(m => m.metodo_pagamento || 'N/A'),
                    datasets: [{
                        label: 'Quantidade',
                        data: metodosPagamento.map(m => m.total || 0),
                        backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (c) => `Quantidade: ${this.formatNumber(c.parsed.x)}`
                            }
                        }
                    },
                    scales: { x: { beginAtZero: true } }
                }
            });
        },

        initDistribuicaoGeograficaChart(chartData) {
            const ctx = document.getElementById('distribuicaoGeograficaChart')?.getContext('2d');
            if (!ctx) return;

            // Verifica se há dados válidos
            const distribuicaoGeografica = Array.isArray(chartData.distribuicaoGeografica) ? chartData.distribuicaoGeografica : [];
            if (distribuicaoGeografica.length === 0) {
                // Se não há dados, cria um gráfico vazio ou com dados padrão
                this.charts.distribuicaoGeografica = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Sem dados'],
                        datasets: [{
                            data: [1],
                            backgroundColor: ['rgba(200, 200, 200, 0.5)'],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: { enabled: false }
                        }
                    }
                });
                return;
            }

            this.charts.distribuicaoGeografica = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: distribuicaoGeografica.map(g => g.localizacao || 'N/A'),
                    datasets: [{
                        data: distribuicaoGeografica.map(g => g.total || 0),
                        backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(139, 92, 246, 0.8)'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (c) => `${c.label}: ${this.formatNumber(c.parsed)}`
                            }
                        }
                    }
                }
            });
        }
    };

    // Este evento é disparado uma vez quando o Livewire carrega.
    // Usamos para registrar listeners que precisam persistir.
    // Função para formatar texto do período
    function formatPeriodText(periodo) {
        const periodMap = {
            'semana': 'Semana',
            'mes': 'Mês',
            'ano': 'Ano'
        };
        return periodMap[periodo] || 'Mês';
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('update-charts', (event) => {
            const chartData = event.detail ? event.detail[0] : event;
            dashboardManager.init(chartData);

            // Atualiza apenas o valor da receita no card sem recarregar o card
            if (chartData.receitaAtual !== undefined) {
                const receitaElement = document.getElementById('receita-value');
                if (receitaElement) {
                    const formatter = new Intl.NumberFormat('pt-AO', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    receitaElement.textContent = `${formatter.format(chartData.receitaAtual)} Kz`;
                }
            }
        });

        // Listeners individuais para cada gráfico
        Livewire.on('update-chart-performance-pacotes', (event) => {
            const data = event.detail ? event.detail[0] : event;
            dashboardManager.initPerformancePacotesChart(data);

            // Atualiza texto do dropdown
            const dropdown = document.getElementById('dropdownPerformancePacotes');
            if (dropdown && data.periodo) {
                dropdown.textContent = formatPeriodText(data.periodo);
            }
        });

        Livewire.on('update-chart-crescimento-usuarios', (event) => {
            const data = event.detail ? event.detail[0] : event;
            dashboardManager.initCrescimentoUsuariosChart(data);

            // Atualiza texto do dropdown
            const dropdown = document.getElementById('dropdownCrescimentoUsuarios');
            if (dropdown && data.periodo) {
                dropdown.textContent = formatPeriodText(data.periodo);
            }
        });

        Livewire.on('update-chart-metodos-pagamento', (event) => {
            const data = event.detail ? event.detail[0] : event;
            dashboardManager.initMetodosPagamentoChart(data);

            // Atualiza texto do dropdown
            const dropdown = document.getElementById('dropdownMetodosPagamento');
            if (dropdown && data.periodo) {
                dropdown.textContent = formatPeriodText(data.periodo);
            }
        });

        Livewire.on('update-chart-distribuicao-geografica', (event) => {
            const data = event.detail ? event.detail[0] : event;
            dashboardManager.initDistribuicaoGeograficaChart(data);

            // Atualiza texto do dropdown
            const dropdown = document.getElementById('dropdownDistribuicaoGeografica');
            if (dropdown && data.periodo) {
                dropdown.textContent = formatPeriodText(data.periodo);
            }
        });

        Livewire.on('update-chart-gross-sales', (event) => {
            const data = event.detail ? event.detail[0] : event;
            dashboardManager.initGrossSalesChart(data);

            // Atualiza valor da receita se disponível
            if (data.receitaAtual !== undefined) {
                const receitaElement = document.getElementById('receita-value');
                if (receitaElement) {
                    const formatter = new Intl.NumberFormat('pt-AO', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    receitaElement.textContent = `${formatter.format(data.receitaAtual)} Kz`;
                }
            }
        });
    });

    // Este evento é disparado na carga inicial da página E toda vez que
    // você navega para esta página usando `wire:navigate`.
    document.addEventListener('livewire:navigated', () => {
        // Pega os dados mais recentes que foram renderizados com a página.
        const chartData = @json($this->getChartData());
        dashboardManager.init(chartData);
    });
</script>
@endpush

