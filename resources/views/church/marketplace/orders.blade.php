<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-shopping-cart me-2"></i>Gestão de Pedidos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os pedidos do marketplace da sua igreja</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Pedidos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['pendente'] }}</div>
                        <div class="text-muted small">Pedidos Pendentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['concluido'] }}</div>
                        <div class="text-muted small">Pedidos Concluídos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-dollar-sign text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ number_format($stats['total_valor'], 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Valor Total</div>
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
                            <option value="pago">Pago</option>
                            <option value="enviado">Enviado</option>
                            <option value="concluido">Concluído</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar por Produto</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroProduto" placeholder="Nome do produto">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar por Comprador</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroComprador" placeholder="Nome do comprador">
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="$refresh">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Scripts para Orders --}}
                <script src="{{ asset('system/js/marketplace.js') }}" data-navigate-once></script>
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Pedidos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pedido</th>
                                <th>Produto</th>
                                <th>Comprador</th>
                                <th>Quantidade</th>
                                <th>Valor Total</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <div class="fw-semibold">#{{ substr($order->id, 0, 8) }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $order->produto->nome ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ number_format($order->produto->preco ?? 0, 2, ',', '.') }} AOA/un</small>
                                </td>
                                <td>{{ $order->comprador->name ?? 'N/A' }}</td>
                                <td>{{ $order->quantidade }}</td>
                                <td>{{ number_format(($order->produto->preco ?? 0) * $order->quantidade, 2, ',', '.') }} AOA</td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($order->status) }}">
                                        {{ $this->getStatusLabel($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->data_pedido->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if($order->status === 'pendente')
                                        <button class="btn btn-outline-success" wire:click="atualizarStatus({{ $order->id }}, 'pago')" title="Marcar como Pago">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        @if($order->status === 'pago')
                                        <button class="btn btn-outline-primary" wire:click="atualizarStatus({{ $order->id }}, 'enviado')" title="Marcar como Enviado">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                        @endif
                                        @if($order->status === 'enviado')
                                        <button class="btn btn-outline-success" wire:click="atualizarStatus({{ $order->id }}, 'concluido')" title="Marcar como Concluído">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-outline-danger" wire:click="excluirOrder({{ $order->id }})"
                                                onclick="return confirm('Tem certeza que deseja excluir este pedido?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-shopping-cart text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum pedido encontrado</div>
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
                @forelse($orders as $order)
                <div class="col-12">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h6 class="card-title mb-1">#{{ substr($order->id, 0, 8) }}</h6>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($order->status) }}">
                                        {{ $this->getStatusLabel($order->status) }}
                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary">{{ number_format(($order->produto->preco ?? 0) * $order->quantidade, 2, ',', '.') }} AOA</div>
                                    <small class="text-muted">{{ $order->data_pedido->format('d/m/Y') }}</small>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-box text-muted me-1"></i>
                                <small class="text-muted">{{ $order->produto->nome ?? 'N/A' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-muted me-1"></i>
                                <small class="text-muted">{{ $order->comprador->name ?? 'N/A' }}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-hashtag text-muted me-1"></i>
                                <small class="text-muted">{{ $order->quantidade }} unidade(s)</small>
                            </div>
                            <div class="d-flex gap-2">
                                @if($order->status === 'pendente')
                                <button class="btn btn-success btn-sm flex-fill" wire:click="atualizarStatus({{ $order->id }}, 'pago')">
                                    <i class="fas fa-check me-1"></i>Marcar Pago
                                </button>
                                @elseif($order->status === 'pago')
                                <button class="btn btn-primary btn-sm flex-fill" wire:click="atualizarStatus({{ $order->id }}, 'enviado')">
                                    <i class="fas fa-truck me-1"></i>Enviar
                                </button>
                                @elseif($order->status === 'enviado')
                                <button class="btn btn-success btn-sm flex-fill" wire:click="atualizarStatus({{ $order->id }}, 'concluido')">
                                    <i class="fas fa-check-double me-1"></i>Concluir
                                </button>
                                @endif
                                <button class="btn btn-outline-danger btn-sm" wire:click="excluirOrder({{ $order->id }})"
                                        onclick="return confirm('Tem certeza que deseja excluir este pedido?')">
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
                            <i class="fas fa-shopping-cart text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum pedido encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
