<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-church me-2"></i>Ministérios
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie os ministérios da sua igreja
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" wire:click="abrirModalMinisterio">
                                <i class="fas fa-plus me-1"></i>Novo Ministério
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush

        <!-- Filtros para Ministérios -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="filtroMinisterioStatus">
                            <option value="">Todos</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Buscar</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroMinisterioBusca" placeholder="Nome ou descrição...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Total</label>
                        <div class="form-control-plaintext pt-2">
                            <strong>{{ $ministerios->total() }}</strong> ministérios encontrados
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Ministérios -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-church text-success me-2"></i>Ministérios
                </h5>
                <button class="btn btn-success btn-sm" wire:click="abrirModalMinisterio">
                    <i class="fas fa-plus me-1"></i>Novo Ministério
                </button>
            </div>
            <div class="card-body">
                <!-- Desktop: Tabela -->
                <div class="d-none d-lg-block">
                    @if($ministerios->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Membros</th>
                                        <th>Status</th>
                                        <th>Criado em</th>
                                        <th width="120">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ministerios as $ministerio)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $ministerio->nome }}</strong>
                                                    @if($ministerio->descricao)
                                                        <br><small class="text-muted">{{ Str::limit($ministerio->descricao, 50) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $ministerio->membros_count }}</span>
                                            </td>
                                            <td>
                                                @if($ministerio->ativo)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Ativo
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times me-1"></i>Inativo
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $ministerio->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-success" wire:click="abrirModalMinisterio('{{ $ministerio->id }}')" title="Editar">
                                                        <i class="fas fa-edit me-1"></i>Editar
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger" wire:click="confirmarExclusao('{{ $ministerio->id }}')" title="Excluir">
                                                        <i class="fas fa-trash me-1"></i>Excluir
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $ministerios->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-church text-muted mb-4" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">Nenhum ministério encontrado</h4>
                            <p class="text-muted mb-4">
                                @if($filtroMinisterioStatus !== '' || $filtroMinisterioBusca)
                                    Nenhum ministério encontrado com os filtros aplicados.
                                @else
                                    Ainda não há ministérios cadastrados na sua igreja.
                                @endif
                            </p>
                            @if($filtroMinisterioStatus !== '' || $filtroMinisterioBusca)
                                <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroMinisterioStatus', '')" wire:click="$set('filtroMinisterioBusca', '')">
                                    <i class="fas fa-times me-1"></i>Limpar Filtros
                                </button>
                            @endif
                            <button class="btn btn-success" wire:click="abrirModalMinisterio">
                                <i class="fas fa-plus me-1"></i>Criar Primeiro Ministério
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Mobile/Tablet: Cards -->
                <div class="d-lg-none">
                    @if($ministerios->count() > 0)
                        <div class="row g-3">
                            @foreach($ministerios as $ministerio)
                            <div class="col-12 col-md-6">
                                <div class="card card-hover h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-start justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar bg-success text-white me-3">
                                                    <i class="fas fa-church"></i>
                                                </div>
                                                <div>
                                                    <h6 class="card-title mb-1">{{ $ministerio->nome }}</h6>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @if($ministerio->ativo)
                                                    <span class="badge bg-success mb-2">
                                                        <i class="fas fa-check me-1"></i>Ativo
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary mb-2">
                                                        <i class="fas fa-times me-1"></i>Inativo
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <i class="fas fa-users text-muted me-1"></i>
                                            <span class="badge bg-info">{{ $ministerio->membros_count }} membros</span>
                                        </div>
                                        @if($ministerio->descricao)
                                        <div class="mb-3">
                                            <small class="text-muted">{{ Str::limit($ministerio->descricao, 80) }}</small>
                                        </div>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-success btn-sm flex-fill" wire:click="abrirModalMinisterio({{ $ministerio->id }})">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" wire:click="confirmarExclusao({{ $ministerio->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Paginação Mobile -->
                        @if($ministerios->hasPages())
                        <div class="mt-4">
                            <nav aria-label="Paginação Mobile">
                                {{ $ministerios->links() }}
                            </nav>
                            <div class="text-center text-muted mt-2">
                                <small>Mostrando {{ $ministerios->firstItem() }}-{{ $ministerios->lastItem() }} de {{ $ministerios->total() }} registros</small>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-church text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum ministério encontrado</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- MODALS --}}
        @include('church.ministries.modals.ministerio-modal')
        @include('church.ministries.modals.confirmacao-modal')

    </div>
</div>
