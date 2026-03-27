<div>
    {{-- Injetar variáveis CSS dinâmicas para cores configuráveis --}}
    @php
        $config = \App\Models\CartaoConfig::getConfiguracaoIgreja(auth()->user()->getIgreja()->id);
        $cores = $config ? $config->getConfiguracaoCompleta() : \App\Models\CartaoConfig::getCoresPadrao();
    @endphp
    <style>
        :root {
            --card-header-bg: {{ $cores['cor_fundo_header'] }};
            --card-header-text: {{ $cores['cor_texto_header'] }};
            --card-text-info: {{ $cores['cor_texto_principal'] }};
            --card-text-secondary: {{ $cores['cor_texto_secundario'] }};
            --card-accent: {{ $cores['cor_acento'] }};
            --card-status-active: {{ $cores['cor_status_ativo'] }};
            --card-status-inactive: {{ $cores['cor_status_inativo'] }};
            --card-status-lost: {{ $cores['cor_status_perdido'] }};
            --card-status-damaged: {{ $cores['cor_status_danificado'] }};
            --card-status-renewed: {{ $cores['cor_status_renovado'] }};
            --card-status-cancelled: {{ $cores['cor_status_cancelado'] }};
        }
    </style>

    {{-- Incluir CSS do cartão de membro --}}
    <link rel="stylesheet" href="{{ asset('system/css/member-card.css') }}">
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-id-card me-2"></i>Cartões de Membro
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie os cartões de identificação dos membros da igreja
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-info btn-md" wire:click="abrirModalConfig" data-bs-toggle="modal" data-bs-target="#cardConfigModal" title="Configurar Cores">
                                <i class="fas fa-palette"></i>
                            </button>
                            <button class="btn bg-info text-light btn-md" wire:click="abrirModalNovo" data-bs-toggle="modal" data-bs-target="#memberCardModal">
                                <i class="fas fa-plus me-2"></i>Adicionar Cartão
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-id-card text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $estatisticas['total'] }}</div>
                        <div class="text-muted small">Total de Cartões</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $estatisticas['ativos'] }}</div>
                        <div class="text-muted small">Cartões Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $estatisticas['expirando'] }}</div>
                        <div class="text-muted small">Expirando em 30 dias</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-danger metric-card">
                    <div class="card-body">
                        <i class="fas fa-exclamation-triangle text-danger display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-danger">{{ $estatisticas['expirados'] }}</div>
                        <div class="text-muted small">Cartões Expirados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="busca"
                               placeholder="Buscar por nome ou número do cartão...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="filtroStatus">
                            <option value="">Todos os status</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                            <option value="perdido">Perdido</option>
                            <option value="danificado">Danificado</option>
                            <option value="renovado">Renovado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="filtroData">
                            <option value="">Todas as datas</option>
                            <option value="expirando">Expirando em 30 dias</option>
                            <option value="expirados">Expirados</option>
                            <option value="recentes">Criados recentemente</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" wire:click="$set('busca', ''); $set('filtroStatus', ''); $set('filtroData', '')">
                            <i class="fas fa-times me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Cartões -->
        <div class="card">
            <div class="card-body">
                @if($cartoes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Membro</th>
                                    <th>Número do Cartão</th>
                                    <th>Data de Emissão</th>
                                    <th>Data de Validade</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartoes as $cartao)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($cartao->membro && $cartao->membro->user)
                                                    <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                        <i class="fas fa-user fa-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $cartao->membro->user->name }}</div>
                                                        <small class="text-muted">{{ ucfirst($cartao->membro->cargo) }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Membro não encontrado</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-light">{{ $cartao->numero_cartao }}</span>
                                        </td>
                                        <td>{{ $cartao->data_emissao ? $cartao->data_emissao->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            @if($cartao->data_validade)
                                                <span class="{{ $cartao->isExpirado() ? 'text-danger' : ($cartao->precisaRenovar() ? 'text-warning' : 'text-success') }}">
                                                    {{ $cartao->data_validade->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $cartao->getStatusBadgeClass() }}">
                                                {{ $cartao->getStatusFormatado() }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" wire:click="abrirModalEditar('{{ $cartao->id }}')" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-success" wire:click="selecionarCartao('{{ $cartao->id }}')" data-bs-toggle="modal" data-bs-target="#viewCardModal" title="Visualizar Cartão">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($cartao->status === 'ativo' && (!$cartao->data_validade || !$cartao->isExpirado()))
                                                    <button class="btn btn-sm btn-outline-warning" onclick="window.open('/churches/church-member-cards-print/{{ $cartao->id }}', '_blank')" title="Imprimir Cartão: {{ $cartao->id }}" wire:loading.attr="disabled" wire:target="print-{{ $cartao->id }}">
                                                        <span wire:loading.remove wire:target="print-{{ $cartao->id }}">
                                                            <i class="fas fa-print"></i>
                                                        </span>
                                                        <span wire:loading wire:target="print-{{ $cartao->id }}">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-outline-info" wire:click="verHistorico('{{ $cartao->id }}')" data-bs-toggle="modal" data-bs-target="#historyModal" title="Ver Histórico">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                                <div class="dropdown dropend">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                                        @if($cartao->status === 'ativo')
                                                            @if(!$cartao->impresso_em)
                                                                <li><a class="dropdown-item" href="#" wire:click="marcarComoImpresso('{{ $cartao->id }}')">
                                                                    <i class="fas fa-print me-2"></i>Marcar como Impresso
                                                                </a></li>
                                                            @endif
                                                            @if(!$cartao->entregue_em)
                                                                <li><a class="dropdown-item" href="#" wire:click="marcarComoEntregue('{{ $cartao->id }}')">
                                                                    <i class="fas fa-hand-holding me-2"></i>Marcar como Entregue
                                                                </a></li>
                                                            @endif
                                                            @if($cartao->isExpirado())
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item" href="#" wire:click="renovarCartao('{{ $cartao->id }}')">
                                                                    <i class="fas fa-sync-alt me-2"></i>Renovar Cartão
                                                                </a></li>
                                                            @endif
                                                        @endif
                                                        @if($cartao->status !== 'cancelado')
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="#" wire:click="cancelarCartao('{{ $cartao->id }}')">
                                                                <i class="fas fa-times me-2"></i>Cancelar Cartão
                                                            </a></li>
                                                        @endif
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" wire:click="excluirCartao('{{ $cartao->id }}')" onclick="return confirm('Tem certeza que deseja excluir este cartão?')">
                                                            <i class="fas fa-trash me-2"></i>Excluir Cartão
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $cartoes->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-id-card text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Nenhum cartão encontrado</h5>
                        <p class="text-muted">Não há cartões cadastrados com os filtros atuais.</p>
                        <button class="btn bg-info text-light" wire:click="abrirModalNovo" data-bs-toggle="modal" data-bs-target="#memberCardModal">
                            <i class="fas fa-plus me-1"></i>Criar Primeiro Cartão
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>



    {{-- Incluir modais --}}
    @include('church.members.modals.member-card-modal')
    @include('church.members.modals.card-config-modal')

    <!-- Scripts para Member Cards -->
    <script src="{{ asset('system/js/members.js') }}" ></script>



    <!-- Estilos para Flatpickr - Forçar sempre desktop -->
    <style>
    /* Forçar Flatpickr sempre visível */
    .flatpickr-calendar {
        z-index: 10000 !important;
        display: none;
    }

    .flatpickr-calendar.open {
        display: block !important;
    }

    /* Overlay para mobile */
    .flatpickr-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(0,0,0,0.5) !important;
        z-index: 9999 !important;
    }

    /* Responsivo para telas pequenas */
    @media (max-width: 768px) {
        .flatpickr-calendar {
            position: fixed !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 320px !important;
            max-width: 90vw !important;
        }

        .flatpickr-calendar .flatpickr-month {
            height: 40px !important;
        }

        .flatpickr-calendar .flatpickr-day {
            height: 35px !important;
            line-height: 35px !important;
        }
    }

    /* Melhorar inputs de data */
    .date_flatpicker {
        cursor: pointer !important;
        background-color: white !important;
    }

    .date_flatpicker:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }

    </style>

</div>

