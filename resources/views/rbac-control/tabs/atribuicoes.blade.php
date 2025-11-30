<!-- Filtros para Atribuições -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Membro</label>
                <select class="form-select" wire:model.live="filtroAtribuicaoMembro">
                    <option value="">Todos os membros</option>
                    @foreach($membrosDisponiveis as $membro)
                        <option value="{{ $membro['id'] }}">{{ $membro['nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Função</label>
                <select class="form-select" wire:model.live="filtroAtribuicaoFuncao">
                    <option value="">Todas as funções</option>
                    @foreach($funcoesDisponiveis as $funcao)
                        <option value="{{ $funcao->id }}">{{ $funcao->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Status</label>
                <select class="form-select" wire:model.live="filtroAtribuicaoStatus">
                    <option value="">Todos</option>
                    <option value="ativo">Ativo</option>
                    <option value="inativo">Inativo</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total</label>
                <div class="form-control-plaintext pt-2">
                    <strong>{{ $atribuicoes->total() }}</strong> atribuições encontradas
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Atribuições -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-user-tag text-warning me-2"></i>Atribuições de Funções
        </h5>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#atribuicaoModal" wire:click="abrirModalAtribuicao">
            <i class="fas fa-plus me-1"></i>Atribuir Função
        </button>
    </div>
    <div class="card-body">
        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            @if($atribuicoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Membro</th>
                                <th>Função</th>
                                <th>Status</th>
                                <th>Validade</th>
                                <th>Atribuído em</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($atribuicoes as $atribuicao)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3">
                                                @if($atribuicao->membro->user->photo_url)
                                                <img src="{{ Storage::disk('supabase')->url($atribuicao->membro->user->photo_url) }}" alt="Avatar" class="rounded-circle">    
                                                @endif
                                                
                                            </div>
                                            <div>
                                                <strong>{{ $atribuicao->membro->user->name }}</strong>
                                                <br><small class="text-muted">{{ ucfirst($atribuicao->membro->cargo) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $atribuicao->funcao->nome }}</strong>
                                            @if($atribuicao->funcao->descricao)
                                                <br><small class="text-muted">{{ Str::limit($atribuicao->funcao->descricao, 40) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($atribuicao->status === 'ativo')
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
                                        @if($atribuicao->valido_ate)
                                            <span class="badge bg-info">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $atribuicao->valido_ate->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-infinity me-1"></i>Sem limite
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $atribuicao->created_at->format('d/m/Y') }}
                                            <br>por {{ $atribuicao->atribuidoPor ? $atribuicao->atribuidoPor->name : 'Sistema' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($atribuicao->status === 'ativo')
                                            <button class="btn btn-sm btn-outline-danger" wire:click="abrirModalConfirmacao('revogar_funcao', '{{ $atribuicao->id }}')" title="Revogar"  data-bs-toggle="modal" data-bs-target="#confirmacaoModal">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @elseif($atribuicao->status === 'revogado')
                                            <button class="btn btn-sm btn-outline-success" wire:click="abrirModalConfirmacao('reativar_funcao', '{{ $atribuicao->id }}')" title="Reativar"   data-bs-toggle="modal" data-bs-target="#confirmacaoModal">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small">
                                                @if($atribuicao->revogado_em)
                                                    Revogada em {{ $atribuicao->revogado_em->format('d/m/Y') }}
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $atribuicoes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-tag text-muted mb-4" style="font-size: 4rem;"></i>
                    <h4 class="text-muted">Nenhuma atribuição encontrada</h4>
                    <p class="text-muted mb-4">
                        @if($filtroAtribuicaoMembro || $filtroAtribuicaoFuncao || $filtroAtribuicaoStatus !== '')
                            Nenhuma atribuição encontrada com os filtros aplicados.
                        @else
                            Ainda não há atribuições de funções na sua igreja.
                        @endif
                    </p>
                    @if($filtroAtribuicaoMembro || $filtroAtribuicaoFuncao || $filtroAtribuicaoStatus !== '')
                        <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroAtribuicaoMembro', '')" wire:click="$set('filtroAtribuicaoFuncao', '')" wire:click="$set('filtroAtribuicaoStatus', '')">
                            <i class="fas fa-times me-1"></i>Limpar Filtros
                        </button>
                    @endif
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#atribuicaoModal" wire:click="abrirModalAtribuicao">
                        <i class="fas fa-plus me-1"></i>Fazer Primeira Atribuição
                    </button>
                </div>
            @endif
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            @if($atribuicoes->count() > 0)
                <div class="row g-3">
                    @foreach($atribuicoes as $atribuicao)
                    <div class="col-12">
                        <div class="card card-hover h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($atribuicao->membro->user->photo_url)
                                                <img src="{{ Storage::disk('supabase')->url($atribuicao->membro->user->photo_url) }}" alt="Avatar" class="rounded-circle"  style="width: 40px; height: 40px;">    
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="card-title mb-1">{{ $atribuicao->membro->user->name }}</h6>
                                            <small class="text-muted">{{ ucfirst($atribuicao->membro->cargo) }}</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        @if($atribuicao->status === 'ativo')
                                            <span class="badge bg-success mb-2">
                                                <i class="fas fa-check me-1"></i>Ativo
                                            </span>
                                        @elseif($atribuicao->status === 'revogado')
                                            <span class="badge bg-warning mb-2">
                                                <i class="fas fa-ban me-1"></i>Revogado
                                            </span>
                                        @else
                                            <span class="badge bg-secondary mb-2">
                                                <i class="fas fa-times me-1"></i>Inativo
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <i class="fas fa-users-cog text-warning me-1"></i>
                                    <strong>{{ $atribuicao->funcao->nome }}</strong>
                                </div>
                                <div class="mb-2">
                                    @if($atribuicao->valido_ate)
                                        <i class="fas fa-calendar text-info me-1"></i>
                                        <small class="text-muted">Válido até {{ $atribuicao->valido_ate->format('d/m/Y') }}</small>
                                    @else
                                        <i class="fas fa-infinity text-muted me-1"></i>
                                        <small class="text-muted">Sem limite de validade</small>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <i class="fas fa-clock text-muted me-1"></i>
                                    <small class="text-muted">
                                        Atribuído em {{ $atribuicao->created_at->format('d/m/Y') }}
                                        @if($atribuicao->atribuidoPor)
                                            por {{ $atribuicao->atribuidoPor->name }}
                                        @endif
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($atribuicao->status === 'ativo')
                                        <button class="btn btn-danger btn-sm flex-fill" wire:click="abrirModalConfirmacao('revogar_funcao', '{{ $atribuicao->id }}')">
                                            <i class="fas fa-ban me-1"></i>Revogar
                                        </button>
                                    @elseif($atribuicao->status === 'revogado')
                                        <button class="btn btn-success btn-sm flex-fill" wire:click="abrirModalConfirmacao('reativar_funcao', '{{ $atribuicao->id }}')">
                                            <i class="fas fa-check me-1"></i>Reativar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Paginação Mobile -->
                @if($atribuicoes->hasPages())
                <div class="mt-4">
                    <nav aria-label="Paginação Mobile">
                        {{ $atribuicoes->links() }}
                    </nav>
                    <div class="text-center text-muted mt-2">
                        <small>Mostrando {{ $atribuicoes->firstItem() }}-{{ $atribuicoes->lastItem() }} de {{ $atribuicoes->total() }} registros</small>
                    </div>
                </div>
                @endif
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-tag text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhuma atribuição encontrada</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
