<!-- Filtros para Funções -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" wire:model.live="filtroFuncaoStatus">
                    <option value="">Todas</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Buscar</label>
                <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroFuncaoBusca" placeholder="Nome ou descrição...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $funcoes->total() }}</strong> funções encontradas
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Funções -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-users-cog text-success me-2"></i>Funções
        </h5>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#funcaoModal" wire:click="abrirModalFuncao">
            <i class="fas fa-plus me-1"></i>Nova Função
        </button>
    </div>
    <div class="card-body">
        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            @if($funcoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Nível</th>
                                <th>Permissões</th>
                                <th>Membros</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($funcoes as $funcao)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $funcao->nome }}</strong>
                                            @if($funcao->descricao)
                                                <br><small class="text-muted">{{ Str::limit($funcao->descricao, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $nivelClasses = [
                                                'baixo' => 'success',
                                                'medio' => 'warning',
                                                'alto' => 'danger',
                                                'critico' => 'dark'
                                            ];
                                            $nivelLabels = [
                                                'baixo' => 'Baixo',
                                                'medio' => 'Médio',
                                                'alto' => 'Alto',
                                                'critico' => 'Crítico'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $nivelClasses[$funcao->nivel_hierarquia] ?? 'secondary' }}">
                                            {{ $nivelLabels[$funcao->nivel_hierarquia] ?? ucfirst($funcao->nivel_hierarquia) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-light">{{ $funcao->permissoes->count() }}</span>
                                        @if($funcao->permissoes->count() > 0)
                                            <small class="text-muted d-block">
                                                {{ $funcao->permissoes->take(2)->pluck('nome')->join(', ') }}
                                                @if($funcao->permissoes->count() > 2)
                                                    +{{ $funcao->permissoes->count() - 2 }} mais
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $funcao->contarMembrosAtivos() }}</span>
                                    </td>
                                    <td>
                                        @if($funcao->ativo)
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
                                            {{ $funcao->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#funcaoModal" wire:click="abrirModalFuncao('{{ $funcao->id }}')" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" wire:click="excluirFuncao('{{ $funcao->id }}')">
                                                            <i class="fas fa-trash me-2"></i>Excluir
                                                        </a>
                                                    </li>
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
                    {{ $funcoes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users-cog text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted">Nenhuma função encontrada</h4>
                    <p class="text-muted mb-4">
                        @if($filtroFuncaoStatus !== '' || $filtroFuncaoBusca)
                            Nenhuma função encontrada com os filtros aplicados.
                        @else
                            Ainda não há funções cadastradas na sua igreja.
                        @endif
                    </p>
                    @if($filtroFuncaoStatus !== '' || $filtroFuncaoBusca)
                        <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroFuncaoStatus', '')" wire:click="$set('filtroFuncaoBusca', '')">
                            <i class="fas fa-times me-1"></i>Limpar Filtros
                        </button>
                    @endif
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#funcaoModal" wire:click="abrirModalFuncao">
                        <i class="fas fa-plus me-1"></i>Criar Primeira Função
                    </button>
                </div>
            @endif
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            @if($funcoes->count() > 0)
                <div class="row g-3">
                    @foreach($funcoes as $funcao)
                    <div class="col-12 col-md-6">
                        <div class="card card-hover h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-success text-white me-3">
                                            <i class="fas fa-users-cog"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-1">{{ $funcao->nome }}</h6>
                                            @php
                                                $nivelClasses = [
                                                    'baixo' => 'success',
                                                    'medio' => 'warning',
                                                    'alto' => 'danger',
                                                    'critico' => 'dark'
                                                ];
                                                $nivelLabels = [
                                                    'baixo' => 'Baixo',
                                                    'medio' => 'Médio',
                                                    'alto' => 'Alto',
                                                    'critico' => 'Crítico'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $nivelClasses[$funcao->nivel_hierarquia] ?? 'secondary' }}">
                                                {{ $nivelLabels[$funcao->nivel_hierarquia] ?? ucfirst($funcao->nivel_hierarquia) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if($funcao->ativo)
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
                                    <i class="fas fa-shield-alt text-muted me-1"></i>
                                    <span class="badge bg-info text-light">{{ $funcao->permissoes->count() }} permissões</span>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-users text-muted me-1"></i>
                                    <span class="badge bg-warning">{{ $funcao->contarMembrosAtivos() }} membros</span>
                                </div>
                                @if($funcao->descricao)
                                <div class="mb-3">
                                    <small class="text-muted">{{ Str::limit($funcao->descricao, 80) }}</small>
                                </div>
                                @endif
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#funcaoModal" wire:click="abrirModalFuncao({{ $funcao->id }})">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" wire:click="excluirFuncao({{ $funcao->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginação Mobile -->
                @if($funcoes->hasPages())
                <div class="mt-4">
                    <nav aria-label="Paginação Mobile">
                        {{ $funcoes->links() }}
                    </nav>
                    <div class="text-center text-muted mt-2">
                        <small>Mostrando {{ $funcoes->firstItem() }}-{{ $funcoes->lastItem() }} de {{ $funcoes->total() }} registros</small>
                    </div>
                </div>
                @endif
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-users-cog text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhuma função encontrada</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
