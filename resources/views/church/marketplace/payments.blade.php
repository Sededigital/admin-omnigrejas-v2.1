<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-credit-card me-2"></i>Gestão de Pagamentos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os pagamentos do marketplace da sua igreja</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-credit-card text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Pagamentos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['confirmado'] }}</div>
                        <div class="text-muted small">Pagamentos Confirmados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['pendente'] }}</div>
                        <div class="text-muted small">Pagamentos Pendentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-dollar-sign text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ number_format($stats['total_valor'], 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Valor Confirmado</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="filtroStatus">
                            <option value="">Todos os status</option>
                            <option value="pendente">Pendente</option>
                            <option value="confirmado">Confirmado</option>
                            <option value="falhou">Falhou</option>
                            <option value="estornado">Estornado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Método</label>
                        <select class="form-select" wire:model.live="filtroMetodo">
                            <option value="">Todos os métodos</option>
                            <option value="multicaixa_express">Multicaixa Express</option>
                            <option value="bai_direto">BAI Direto</option>
                            <option value="tpa">TPA</option>
                            <option value="cash">Dinheiro</option>
                            <option value="deposito">Depósito</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar por Pedido</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroPedido" placeholder="ID do pedido">
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="$refresh">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Scripts para Payments --}}
                <script src="{{ asset('system/js/marketplace.js') }}" data-navigate-once></script>
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Pagamentos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pagamento</th>
                                <th>Pedido</th>
                                <th>Produto</th>
                                <th>Comprador</th>
                                <th>Método</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <div class="fw-semibold">#{{ substr($payment->id, 0, 8) }}</div>
                                    <small class="text-muted">{{ $payment->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">#{{ substr($payment->pedido->id, 0, 8) }}</div>
                                    <small class="text-muted">{{ $payment->pedido->quantidade }} un</small>
                                </td>
                                <td>{{ $payment->pedido->produto->nome ?? 'N/A' }}</td>
                                <td>{{ $payment->pedido->comprador->name ?? 'N/A' }}</td>
                                <td>{{ $this->getMetodoLabel($payment->metodo) }}</td>
                                <td>{{ number_format($payment->valor, 2, ',', '.') }} AOA</td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($payment->status) }}">
                                        {{ $this->getStatusLabel($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->data_pagamento->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if($payment->status === 'pendente')
                                        <button class="btn btn-outline-success" wire:click="atualizarStatusPagamento({{ $payment->id }}, 'confirmado')" title="Confirmar Pagamento">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        @if(in_array($payment->status, ['pendente', 'confirmado']))
                                        <button class="btn btn-outline-danger" wire:click="atualizarStatusPagamento({{ $payment->id }}, 'falhou')" title="Marcar como Falhou">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-outline-warning" wire:click="excluirPayment({{ $payment->id }})"
                                                onclick="return confirm('Tem certeza que deseja excluir este pagamento?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-credit-card text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum pagamento encontrado</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($payments as $payment)
                <div class="col-12">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h6 class="card-title mb-1">#{{ substr($payment->id, 0, 8) }}</h6>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($payment->status) }}">
                                        {{ $this->getStatusLabel($payment->status) }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">{{ number_format($payment->valor, 2, ',', '.') }} AOA</div>
                                    <small class="text-muted">{{ $payment->data_pagamento->format('d/m/Y') }}</small>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-shopping-cart text-muted me-1"></i>
                                <small class="text-muted">Pedido #{{ substr($payment->pedido->id, 0, 8) }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-box text-muted me-1"></i>
                                <small class="text-muted">{{ $payment->pedido->produto->nome ?? 'N/A' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-muted me-1"></i>
                                <small class="text-muted">{{ $payment->pedido->comprador->name ?? 'N/A' }}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-credit-card text-muted me-1"></i>
                                <small class="text-muted">{{ $this->getMetodoLabel($payment->metodo) }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                @if($payment->status === 'pendente')
                                <button class="btn btn-success btn-sm flex-fill" wire:click="atualizarStatusPagamento({{ $payment->id }}, 'confirmado')">
                                    <i class="fas fa-check me-1"></i>Confirmar
                                </button>
                                @elseif(in_array($payment->status, ['pendente', 'confirmado']))
                                <button class="btn btn-danger btn-sm flex-fill" wire:click="atualizarStatusPagamento({{ $payment->id }}, 'falhou')">
                                    <i class="fas fa-times me-1"></i>Falhou
                                </button>
                                @endif
                                <button class="btn btn-outline-warning btn-sm" wire:click="excluirPayment({{ $payment->id }})"
                                        onclick="return confirm('Tem certeza que deseja excluir este pagamento?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-credit-card text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum pagamento encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
