<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Movimentos Financeiros</h1>
                            <p>Registre entradas e saídas financeiras da igreja</p>
                        </div>
                        <div>
                            <button type="button" class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#movementModal">
                                <i class="fas fa-plus me-2"></i>Novo Movimento
                            </button>
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

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-arrow-up text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ number_format($stats['total_entradas'] ?? 0, 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Total Entradas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-danger metric-card">
                    <div class="card-body">
                        <i class="fas fa-arrow-down text-danger display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-danger">{{ number_format($stats['total_saidas'] ?? 0, 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Total Saídas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-balance-scale text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ number_format($stats['saldo_liquido'] ?? 0, 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Saldo Líquido</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-list-ul text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total_movimentos'] ?? 0 }}</div>
                        <div class="text-muted small">Total Movimentos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar Movimento</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Descrição ou categoria">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" wire:model.live="selectedType">
                            <option value="">Todos</option>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Conta</label>
                        <select class="form-select" wire:model.live="selectedAccount">
                            <option value="">Todas as contas</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->banco }}</option>
                            @endforeach
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
                            <button class="btn bg-info text-light flex-fill" wire:click="clearFilters">
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
                        <i class="fas fa-list-ul me-2"></i>Lista de Movimentos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Conta</th>
                                <th>Categoria</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                            <tr>
                                <td>
                                    <div>{{ $movement->data_transacao->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $movement->data_transacao->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $movement->descricao }}</div>
                                    <small class="text-muted">{{ Str::limit($movement->observacoes ?? '', 30) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $movement->tipo === 'entrada' ? 'success' : 'danger' }}">
                                        <i class="fas fa-{{ $movement->tipo === 'entrada' ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                                        {{ ucfirst($movement->tipo) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold {{ $movement->tipo === 'entrada' ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($movement->valor, 2, ',', '.') }} AOA
                                    </div>
                                </td>
                                <td>{{ $movement->conta->banco ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $movement->categoria->nome ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $movement->id }}')" data-bs-toggle="modal" data-bs-target="#movementModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" wire:click="viewDetails('{{ $movement->id }}')" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @php
                                            $canDelete = $this->canDeleteMovement($movement);
                                        @endphp
                                        <button class="btn btn-outline-danger {{ !$canDelete ? 'disabled' : '' }}"
                                                wire:click="openDeleteModal('movement', '{{ $movement->id }}')"
                                                title="{{ !$canDelete ? 'Não é possível excluir este movimento' : 'Excluir' }}"
                                                {{ !$canDelete ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-exchange-alt text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum movimento encontrado</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ $movements->firstItem() }}-{{ $movements->lastItem() }} de {{ $movements->total() }} registros</span>
                        <nav aria-label="Paginação">
                            {{ $movements->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($movements as $movement)
                <div class="col-12">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="movement-avatar bg-{{ $movement->tipo === 'entrada' ? 'success' : 'danger' }} text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        <i class="fas fa-{{ $movement->tipo === 'entrada' ? 'arrow-up' : 'arrow-down' }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $movement->descricao }}</h6>
                                        <span class="badge bg-{{ $movement->tipo === 'entrada' ? 'success' : 'danger' }}">
                                            {{ ucfirst($movement->tipo) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold {{ $movement->tipo === 'entrada' ? 'text-success' : 'text-danger' }} h5 mb-0">
                                        {{ number_format($movement->valor, 2, ',', '.') }} AOA
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $movement->data_transacao->format('d/m/Y') }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-university text-muted me-1"></i>
                                <small class="text-muted">{{ $movement->conta->banco ?? 'N/A' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-tag text-muted me-1"></i>
                                <small class="text-muted">{{ $movement->categoria->nome ?? 'N/A' }}</small>
                            </div>
                            @if($movement->observacoes)
                            <div class="mb-3">
                                <i class="fas fa-comment text-muted me-1"></i>
                                <small class="text-muted">{{ Str::limit($movement->observacoes, 50) }}</small>
                            </div>
                            @endif
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $movement->id }}')" data-bs-toggle="modal" data-bs-target="#movementModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-info btn-sm" wire:click="viewDetails('{{ $movement->id }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @php
                                    $canDelete = $this->canDeleteMovement($movement);
                                @endphp
                                <button class="btn btn-outline-danger btn-sm {{ !$canDelete ? 'disabled' : '' }}"
                                        wire:click="openDeleteModal('movement', '{{ $movement->id }}')"
                                        title="{{ !$canDelete ? 'Não é possível excluir este movimento' : 'Excluir' }}"
                                        {{ !$canDelete ? 'disabled' : '' }}>
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
                            <i class="fas fa-exchange-alt text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum movimento encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($movements->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $movements->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $movements->firstItem() }}-{{ $movements->lastItem() }} de {{ $movements->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>

        {{-- FINANCIAL MOVEMENTS MODALS --}}
        @include('church.finance.modals.movement-modal')

        {{-- Modal de Confirmação de Exclusão --}}
        @if($showDeleteModal)
        <div class="modal fade show" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="false" style="display: block;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Confirmar Exclusão
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        @if($deleteItem)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                            </div>

                            <div class="mb-3">
                                <h6>Você está prestes a excluir:</h6>
                                @if($deleteType === 'movement')
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Descrição:</strong><br>
                                                    {{ $deleteItem->descricao }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Valor:</strong><br>
                                                    <span class="text-{{ $deleteItem->tipo === 'entrada' ? 'success' : 'danger' }}">
                                                        {{ number_format($deleteItem->valor, 2, ',', '.') }} AOA
                                                    </span>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Data:</strong><br>
                                                    {{ $deleteItem->data_transacao->format('d/m/Y') }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Tipo:</strong><br>
                                                    <span class="badge bg-{{ $deleteItem->tipo === 'entrada' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($deleteItem->tipo) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Verificar vínculos --}}
                                    @php
                                        $dependencies = $this->getMovementDependencies($deleteItem);
                                    @endphp
                                    @if(count($dependencies) > 0)
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Atenção:</strong> Este movimento possui vínculos com {{ implode(', ', $dependencies) }}.
                                            A exclusão pode afetar o histórico financeiro.
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-lock text-danger me-1"></i>
                                    Digite sua senha para confirmar:
                                </label>
                                <input type="password"  autocomplete="new-password"  class="form-control @error('deletePassword') is-invalid @enderror"
                                       wire:model="deletePassword" placeholder="Digite sua senha">
                                @error('deletePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($deleteError)
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $deleteError }}
                                    </div>
                                @endif
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Permissões:</strong> Apenas usuários com cargo de Admin, Pastor ou Ministro podem excluir movimentos financeiros.
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x mb-3"></i>
                                <div class="text-muted">Item não encontrado para exclusão.</div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="confirmDelete" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmDelete">
                                <i class="fas fa-trash me-1"></i>Excluir
                            </span>
                            <span wire:loading wire:target="confirmDelete">
                                <i class="fas fa-spinner fa-spin me-1"></i>Excluindo...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Backdrop --}}
            <div class="modal-backdrop fade show"></div>
        @endif

        {{-- Scripts para Financial Movements --}}
        <script src="{{ asset('system/js/financial-movements.js') }}" data-navigate-once></script>
    </div>
