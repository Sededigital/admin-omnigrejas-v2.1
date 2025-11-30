<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-hand-holding-heart me-2"></i>Doações Online
                        </h1>
                        <p class="mb-0 text-muted">Gerencie doações digitais e pagamentos online da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary btn-md" wire:click="configurePaymentGateway">
                            <i class="fas fa-cogs me-2"></i>Configurar Gateway
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-dollar-sign text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">AOA {{ number_format($stats['total_doacoes'] ?? 0, 2, ',', '.') }}</div>
                        <div class="text-muted small">Total Doações</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total_doadores'] ?? 0 }}</div>
                        <div class="text-muted small">Total Doadores</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['doacoes_pendentes'] ?? 0 }}</div>
                        <div class="text-muted small">Doações Pendentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">AOA {{ number_format($stats['media_mensal'] ?? 0, 2, ',', '.') }}</div>
                        <div class="text-muted small">Média Mensal</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status do Gateway de Pagamento -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-credit-card me-2"></i>Status do Gateway de Pagamento
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-{{ ($gatewayStatus['paypal']['ativo'] ?? false) ? 'success' : 'danger' }} me-3"></div>
                            <div>
                                <div class="fw-semibold">
                                    <i class="{{ $gatewayStatus['paypal']['icone'] ?? 'fas fa-credit-card' }} me-2"></i>
                                    {{ $gatewayStatus['paypal']['nome'] ?? 'PayPal' }}
                                </div>
                                <small class="text-muted">
                                    {{ ($gatewayStatus['paypal']['ativo'] ?? false) ? 'Ativo' : 'Inativo' }}
                                    @if($gatewayStatus['paypal']['configurado'] ?? false)
                                        <span class="badge bg-success ms-1">Configurado</span>
                                    @else
                                        <span class="badge bg-warning ms-1">Não Configurado</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-{{ ($gatewayStatus['stripe']['ativo'] ?? false) ? 'success' : 'danger' }} me-3"></div>
                            <div>
                                <div class="fw-semibold">
                                    <i class="{{ $gatewayStatus['stripe']['icone'] ?? 'fas fa-credit-card' }} me-2"></i>
                                    {{ $gatewayStatus['stripe']['nome'] ?? 'Stripe' }}
                                </div>
                                <small class="text-muted">
                                    {{ ($gatewayStatus['stripe']['ativo'] ?? false) ? 'Ativo' : 'Inativo' }}
                                    @if($gatewayStatus['stripe']['configurado'] ?? false)
                                        <span class="badge bg-success ms-1">Configurado</span>
                                    @else
                                        <span class="badge bg-warning ms-1">Não Configurado</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-{{ ($gatewayStatus['pagseguro']['ativo'] ?? false) ? 'success' : 'danger' }} me-3"></div>
                            <div>
                                <div class="fw-semibold">
                                    <i class="{{ $gatewayStatus['pagseguro']['icone'] ?? 'fas fa-credit-card' }} me-2"></i>
                                    {{ $gatewayStatus['pagseguro']['nome'] ?? 'PagSeguro' }}
                                </div>
                                <small class="text-muted">
                                    {{ ($gatewayStatus['pagseguro']['ativo'] ?? false) ? 'Ativo' : 'Inativo' }}
                                    @if($gatewayStatus['pagseguro']['configurado'] ?? false)
                                        <span class="badge bg-success ms-1">Configurado</span>
                                    @else
                                        <span class="badge bg-warning ms-1">Não Configurado</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="status-indicator bg-{{ ($gatewayStatus['mercadopago']['ativo'] ?? false) ? 'success' : 'danger' }} me-3"></div>
                            <div>
                                <div class="fw-semibold">
                                    <i class="{{ $gatewayStatus['mercadopago']['icone'] ?? 'fas fa-credit-card' }} me-2"></i>
                                    {{ $gatewayStatus['mercadopago']['nome'] ?? 'Mercado Pago' }}
                                </div>
                                <small class="text-muted">
                                    {{ ($gatewayStatus['mercadopago']['ativo'] ?? false) ? 'Ativo' : 'Inativo' }}
                                    @if($gatewayStatus['mercadopago']['configurado'] ?? false)
                                        <span class="badge bg-success ms-1">Configurado</span>
                                    @else
                                        <span class="badge bg-warning ms-1">Não Configurado</span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar Doação</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome do doador ou referência">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="pendente">Pendente</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="reembolsado">Reembolsado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Gateway</label>
                        <select class="form-select" wire:model.live="selectedGateway">
                            <option value="">Todos</option>
                            <option value="paypal">PayPal</option>
                            <option value="stripe">Stripe</option>
                            <option value="pagseguro">PagSeguro</option>
                            <option value="mercadopago">Mercado Pago</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <input type="date" class="form-control" wire:model.live="dateFrom">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Data Final</label>
                        <input type="date" class="form-control" wire:model.live="dateTo">
                    </div>
                    <div class="col-md-1">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Doações
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Doador</th>
                                <th>Valor</th>
                                <th>Gateway</th>
                                <th>Status</th>
                                <th>Referência</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $donation)
                            <tr>
                                <td>
                                    <div>{{ $donation->data->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $donation->data->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $donation->usuario->name ?? 'Anônimo' }}</div>
                                    <small class="text-muted">{{ $donation->usuario->email ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-success">
                                        AOA {{ number_format($donation->valor, 2, ',', '.') }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($donation->metodo) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($donation->status) }}">
                                        {{ $this->getStatusLabel($donation->status) }}
                                    </span>
                                </td>
                                <td>
                                    <code class="small">{{ $donation->referencia ?? 'N/A' }}</code>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info" wire:click="viewDetails('{{ $donation->id }}')" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($donation->status === 'pendente')
                                        <button class="btn btn-outline-success" wire:click="approveDonation('{{ $donation->id }}')" title="Aprovar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="cancelDonation('{{ $donation->id }}')" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                        @if($donation->status === 'aprovado')
                                        <button class="btn btn-outline-warning" wire:click="refundDonation('{{ $donation->id }}')" title="Reembolsar">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-hand-holding-heart text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhuma doação encontrada</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ $donations->firstItem() }}-{{ $donations->lastItem() }} de {{ $donations->total() }} registros</span>
                        <nav aria-label="Paginação">
                            {{ $donations->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($donations as $donation)
                <div class="col-12">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="donation-avatar bg-success text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        <i class="fas fa-hand-holding-heart"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $donation->usuario->name ?? 'Anônimo' }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($donation->status) }}">
                                            {{ $this->getStatusLabel($donation->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success h5 mb-0">
                                    AOA {{ number_format($donation->valor, 2, ',', '.') }}
                                </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $donation->data->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-credit-card text-muted me-1"></i>
                                <small class="text-muted">{{ ucfirst($donation->metodo) }}</small>
                            </div>
                            @if($donation->referencia)
                            <div class="mb-2">
                                <i class="fas fa-hashtag text-muted me-1"></i>
                                <small class="text-muted">{{ $donation->referencia }}</small>
                            </div>
                            @endif
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-info btn-sm flex-fill" wire:click="viewDetails('{{ $donation->id }}')">
                                    <i class="fas fa-eye me-1"></i>Detalhes
                                </button>
                                @if($donation->status === 'pendente')
                                <button class="btn btn-outline-success btn-sm" wire:click="approveDonation('{{ $donation->id }}')">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-hand-holding-heart text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhuma doação encontrada</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($donations->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $donations->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $donations->firstItem() }}-{{ $donations->lastItem() }} de {{ $donations->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>

        <!-- Link para Página de Doação -->
        <div class="card mt-4">
            <div class="card-body text-center">
                <h5 class="text-primary mb-3">Página de Doações Online</h5>
                <p class="text-muted mb-3">Compartilhe este link para receber doações online</p>
                <div class="input-group mb-3">
                    <input type="text"  autocomplete="new-password" class="form-control" value="{{ url('doacoes') }}" readonly>
                    <button class="btn btn-outline-primary" onclick="copyToClipboard('{{ url('doacoes') }}')">
                        <i class="fas fa-copy me-1"></i>Copiar
                    </button>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ url('doacoes') }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Página
                    </a>
                    <button class="btn btn-outline-primary" wire:click="shareDonationPage">
                        <i class="fas fa-share-alt me-2"></i>Compartilhar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para Online Donations -->
    <script src="{{ asset('system/js/online-donations.js') }}" data-navigate-once></script>

    {{-- ONLINE DONATIONS MODALS --}}
    @include('church.finance.modals.donation-modal')

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Feedback visual poderia ser adicionado aqui
                console.log('Link copiado: ' + text);
            });
        }
    </script>
</div>
