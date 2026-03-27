<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-boxes me-2"></i>Gestão de Produtos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os produtos do marketplace da sua igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="fas fa-plus me-2"></i>Adicionar Produto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-boxes text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Produtos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['active'] }}</div>
                        <div class="text-muted small">Produtos Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-times-circle text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['inactive'] }}</div>
                        <div class="text-muted small">Produtos Inativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total_orders'] }}</div>
                        <div class="text-muted small">Total de Pedidos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Produto</label>
                        <div class="input-group">
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroNome" placeholder="Nome do produto">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="filtroAtivo">
                            <option value="">Todos os status</option>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn bg-info text-light flex-fill" wire:click="$refresh">
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
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-list-ul me-2"></i>Lista de Produtos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Preço</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th>Pedidos</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-info text-light text-white me-3">
                                            {{ strtoupper(substr($product->nome ?? 'P', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $product->nome ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ Str::limit($product->descricao ?? '', 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($product->preco, 2, ',', '.') }} AOA</td>
                                <td>{{ $product->estoque }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->ativo ? 'success' : 'danger' }}">
                                        {{ $product->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>{{ $product->pedidos->count() }}</td>
                                <td>
                                    <div>{{ $product->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $product->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal({{ $product->id }})" data-bs-toggle="modal" data-bs-target="#productModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-{{ $product->ativo ? 'warning' : 'success' }}"
                                                wire:click="toggleProductStatus({{ $product->id }})"
                                                title="{{ $product->ativo ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $product->ativo ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="excluirProduct({{ $product->id }})"
                                                onclick="return confirm('Tem certeza que deseja excluir este produto?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-boxes text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum produto encontrado</div>
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
                @forelse($products as $product)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-info text-light text-white me-3">
                                        {{ strtoupper(substr($product->nome ?? 'P', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $product->nome ?? 'N/A' }}</h6>
                                        <span class="badge bg-{{ $product->ativo ? 'success' : 'secondary' }}">
                                            {{ $product->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-dollar-sign text-muted me-1"></i>
                                <small class="text-muted">{{ number_format($product->preco, 2, ',', '.') }} AOA</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-warehouse text-muted me-1"></i>
                                <small class="text-muted">{{ $product->estoque }} em estoque</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-shopping-cart text-muted me-1"></i>
                                <small class="text-muted">{{ $product->pedidos->count() }} pedidos</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal({{ $product->id }})" data-bs-toggle="modal" data-bs-target="#productModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="excluirProduct({{ $product->id }})"
                                        onclick="return confirm('Tem certeza que deseja excluir este produto?')">
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
                            <i class="fas fa-boxes text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum produto encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- PRODUCT MODALS --}}
        @include('church.marketplace.modals.product-modal')

        {{-- Scripts para Products --}}
        <script src="{{ asset('system/js/marketplace.js') }}" data-navigate-once></script>
    </div>
</div>
