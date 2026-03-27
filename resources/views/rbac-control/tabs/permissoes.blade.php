<!-- Filtros para Permissões -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Categoria</label>
                <select class="form-select" wire:model.live="filtroPermissaoCategoria">
                    <option value="">Todas as categorias</option>
                    <option value="admin">Administração</option>
                    <option value="visualizacao">Visualização</option>
                    <option value="edicao">Edição</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" wire:model.live="filtroPermissaoStatus">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Buscar</label>
                <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroPermissaoBusca" placeholder="Nome, código ou descrição...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $permissoes->total() }}</strong> permissões encontradas
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Permissões -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-key text-info me-2"></i>Permissões
        </h5>
        <button class="btn bg-info text-light btn-sm" data-bs-toggle="modal" data-bs-target="#permissaoModal" wire:click="abrirModalPermissao">
            <i class="fas fa-plus me-1"></i>Nova Permissão
        </button>
    </div>
    <div class="card-body">
        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            @if($permissoes->count() > 0)
                    <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Código</th>
                                <th>Categoria</th>
                                <th>Nível</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissoes as $permissao)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $permissao->nome }}</strong>
                                            @if($permissao->descricao)
                                                <br><small class="text-muted">{{ Str::limit($permissao->descricao, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <code class="text-info">{{ $permissao->codigo }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $permissao->getCategoriaLabel() }}</span>
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
                                        <span class="badge bg-{{ $nivelClasses[$permissao->getNivelString()] ?? 'secondary' }}">
                                            {{ $nivelLabels[$permissao->getNivelString()] ?? ucfirst($permissao->getNivelString()) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($permissao->ativo)
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
                                            {{ $permissao->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#permissaoModal" wire:click="abrirModalPermissao('{{ $permissao->id }}')" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" wire:click="excluirPermissao('{{ $permissao->id }}')">
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
                    {{ $permissoes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-key text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted">Nenhuma permissão encontrada</h4>
                    <p class="text-muted mb-4">
                        @if($filtroPermissaoCategoria || $filtroPermissaoStatus !== '' || $filtroPermissaoBusca)
                            Nenhuma permissão encontrada com os filtros aplicados.
                        @else
                            Ainda não há permissões cadastradas na sua igreja.
                        @endif
                    </p>
                    @if($filtroPermissaoCategoria || $filtroPermissaoStatus !== '' || $filtroPermissaoBusca)
                        <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroPermissaoCategoria', '')" wire:click="$set('filtroPermissaoStatus', '')" wire:click="$set('filtroPermissaoBusca', '')">
                            <i class="fas fa-times me-1"></i>Limpar Filtros
                        </button>
                    @endif
                    <button class="btn bg-info text-light" data-bs-toggle="modal" data-bs-target="#permissaoModal" wire:click="abrirModalPermissao">
                        <i class="fas fa-plus me-1"></i>Criar Primeira Permissão
                    </button>
                </div>
            @endif
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            @if($permissoes->count() > 0)
                <div class="row g-3">
                    @foreach($permissoes as $permissao)
                    <div class="col-12 col-md-6">
                        <div class="card card-hover h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-info text-light text-white me-3">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-1">{{ $permissao->nome }}</h6>
                                            <span class="badge bg-secondary">{{ $permissao->getCategoriaLabel() }}</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if($permissao->ativo)
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
                                    <i class="fas fa-code text-muted me-1"></i>
                                    <code class="text-info">{{ $permissao->codigo }}</code>
                                </div>
                                <div class="mb-2">
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
                                    <i class="fas fa-exclamation-triangle text-muted me-1"></i>
                                    <span class="badge bg-{{ $nivelClasses[$permissao->getNivelString()] ?? 'secondary' }}">
                                        {{ $nivelLabels[$permissao->getNivelString()] ?? ucfirst($permissao->getNivelString()) }}
                                    </span>
                                </div>
                                @if($permissao->descricao)
                                <div class="mb-3">
                                    <small class="text-muted">{{ Str::limit($permissao->descricao, 80) }}</small>
                                </div>
                                @endif
                                <div class="d-flex gap-2">
                                    <button class="btn bg-info text-light btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#permissaoModal" wire:click="abrirModalPermissao({{ $permissao->id }})">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" wire:click="excluirPermissao({{ $permissao->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginação Mobile -->
                @if($permissoes->hasPages())
                <div class="mt-4">
                    <nav aria-label="Paginação Mobile">
                        {{ $permissoes->links() }}
                    </nav>
                    <div class="text-center text-muted mt-2">
                        <small>Mostrando {{ $permissoes->firstItem() }}-{{ $permissoes->lastItem() }} de {{ $permissoes->total() }} registros</small>
                    </div>
                </div>
                @endif
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-key text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhuma permissão encontrada</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
