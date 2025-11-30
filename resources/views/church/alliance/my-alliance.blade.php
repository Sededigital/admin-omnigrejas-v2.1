<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-star me-2"></i>Minhas Alianças
                        </h1>
                        <p class="mb-0 text-muted">Gerencie as alianças criadas pela sua igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <a href="{{ route('churches.alliance.tools') }}" wire:navigate class="btn btn-outline-secondary btn-md">
                                <i class="fas fa-arrow-left me-2"></i>Voltar
                            </a>
                            <button class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#allianceModal">
                                <i class="fas fa-plus-circle me-2"></i>Nova Aliança
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-handshake text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total Criadas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['aprovadas'] }}</div>
                        <div class="text-muted small">Aprovadas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['pendentes'] + $stats['prontas'] }}</div>
                        <div class="text-muted small">Em Análise</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['aderentes_total'] }}</div>
                        <div class="text-muted small">Total de Membros</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" wire:model.live="categoriaFilter">
                            <option value="">Todas as categorias</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">Todos os status</option>
                            <option value="rascunho">Rascunho</option>
                            <option value="pendente_validacao">Pendente Validação</option>
                            <option value="pronta_aprovacao">Pronta p/ Aprovação</option>
                            <option value="aprovada">Aprovada</option>
                            <option value="rejeitada">Rejeitada</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ordenar por</label>
                        <select class="form-select" wire:model.live="orderBy">
                            <option value="created_at">Data de Criação</option>
                            <option value="aderentes_count">Nº de Membros</option>
                            <option value="nome">Nome</option>
                            <option value="status">Status</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Alianças Criadas -->
        <div class="row g-4">
            @forelse($minhasAliancas as $alianca)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-hover h-100 my-alliance-card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-handshake text-primary me-2"></i>
                                    {{ $alianca->nome }}
                                    @if($alianca->sigla)
                                        <small class="text-muted">({{ $alianca->sigla }})</small>
                                    @endif
                                </h6>
                            </div>
                            <span class="badge bg-{{ match($alianca->status) {
                                'aprovada' => 'success',
                                'pronta_aprovacao' => 'info',
                                'pendente_validacao' => 'warning',
                                'rascunho' => 'secondary',
                                default => 'danger'
                            } }}">
                                {{ match($alianca->status) {
                                    'aprovada' => 'Aprovada',
                                    'pronta_aprovacao' => 'Pronta p/ Aprovação',
                                    'pendente_validacao' => 'Em Validação',
                                    'rascunho' => 'Rascunho',
                                    'rejeitada' => 'Rejeitada',
                                    default => 'Suspensa'
                                } }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted small mb-3">
                            {{ Str::limit($alianca->descricao, 120) }}
                        </p>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Categoria</small>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-tag me-1"></i>{{ $alianca->categoria->nome ?? 'Não definida' }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Membros</small>
                                <span class="fw-semibold text-primary">
                                    <i class="fas fa-users me-1"></i>{{ $alianca->aderentes_count }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Criada em</small>
                                <span class="text-dark small">
                                    {{ $alianca->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Status</small>
                                <span class="text-dark small">
                                    @if($alianca->status === 'aprovada' && $alianca->aprovado_em)
                                        Aprovada em {{ $alianca->aprovado_em->format('d/m/Y') }}
                                    @else
                                        {{ $alianca->status === 'aprovada' ? 'Ativa' : 'Em análise' }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        @if($alianca->status === 'pronta_aprovacao')
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Sua aliança tem {{ $alianca->aderentes_count }} membros interessados e está pronta para aprovação do Super Admin!</small>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary btn-sm flex-fill"
                                    data-bs-toggle="modal" data-bs-target="#allianceModal"
                                    wire:click="editarAlianca({{ $alianca->id }})">
                                <i class="fas fa-edit me-1"></i>Editar
                            </button>
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver
                            </button>
                            @if($alianca->aderentes_count === 0 && in_array($alianca->status, ['rascunho', 'rejeitada']))
                                <button class="btn btn-outline-danger btn-sm"
                                        wire:click="excluirAlianca({{ $alianca->id }})"
                                        wire:confirm="Tem certeza que deseja excluir esta aliança?">
                                    <i class="fas fa-trash me-1"></i>Excluir
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
                        <i class="fas fa-handshake text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Você ainda não criou nenhuma aliança</h5>
                        <p class="text-muted">Comece criando sua primeira aliança para conectar com outras igrejas</p>
                        <button class="btn btn-primary" wire:click="criarNovaAlianca">
                            <i class="fas fa-plus-circle me-2"></i>Criar Primeira Aliança
                        </button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($minhasAliancas->hasPages())
        <div class="mt-4">
            {{ $minhasAliancas->links() }}
            <div class="text-center text-muted mt-2">
                <small>Mostrando {{ $minhasAliancas->firstItem() }}-{{ $minhasAliancas->lastItem() }} de {{ $minhasAliancas->total() }} alianças</small>
            </div>
        </div>
        @endif
    </div>

    {{-- Modal de Aliança --}}
    <div class="modal fade" id="allianceModal" tabindex="-1" aria-labelledby="allianceModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="allianceModalLabel">
                        <i class="fas fa-handshake text-primary me-2"></i>
                        <span>{{ $isEditing ? 'Editar Aliança' : 'Criar Nova Aliança' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="salvarAlianca">

                        <!-- Navegação por Abas -->
                        <nav class="mb-4">
                            <div class="nav nav-tabs border-bottom-0" id="alliance-nav-tab" role="tablist">
                                <button class="nav-link active border-0 bg-transparent fw-semibold"
                                        id="alliance-nav-basic-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#alliance-nav-basic"
                                        type="button" role="tab">
                                    <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                                </button>
                                <button class="nav-link border-0 bg-transparent fw-semibold"
                                        id="alliance-nav-details-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#alliance-nav-details"
                                        type="button" role="tab">
                                    <i class="fas fa-file-alt text-primary me-1"></i>Detalhes
                                </button>
                            </div>
                        </nav>

                        <!-- Conteúdo das Abas -->
                        <div class="tab-content" id="alliance-nav-tabContent">

                            <!-- Aba: Informações Básicas -->
                            <div class="tab-pane fade show active" id="alliance-nav-basic" role="tabpanel">
                                <div class="row g-3">
                                    <!-- Nome da Aliança -->
                                    <div class="col-md-8">
                                        <div class="form-floating mb-3">
                                            <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control @error('nome') is-invalid @enderror"
                                                   wire:model="nome" placeholder="Digite o nome da aliança" required>
                                            <label><i class="fas fa-handshake text-primary me-1"></i>Nome da Aliança *</label>
                                            @error('nome')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Sigla -->
                                    <div class="col-md-4">
                                        <div class="form-floating mb-3">
                                            <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control text-uppercase @error('sigla') is-invalid @enderror"
                                                   wire:model="sigla" placeholder="Ex: AEA" maxlength="10">
                                            <label><i class="fas fa-tag text-primary me-1"></i>Sigla</label>
                                            @error('sigla')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Categoria -->
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-select @error('categoria_id') is-invalid @enderror"
                                                    wire:model.live="categoria_id" >
                                                    <option value="">{{ Auth::user()->getIgreja()->categoria->nome }}</option>
                                            </select>
                                            <label><i class="fas fa-tags text-primary me-1"></i>Categoria *</label>
                                            @error('categoria_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Limite de Membros -->
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" class="form-control @error('limite_membros') is-invalid @enderror"
                                                   wire:model="limite_membros" min="1" max="1000000" placeholder="Deixe vazio para ilimitado">
                                            <label><i class="fas fa-users text-primary me-1"></i>Limite de Membros</label>
                                            @error('limite_membros')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Deixe vazio para não limitar o número de membros</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba: Detalhes -->
                            <div class="tab-pane fade" id="alliance-nav-details" role="tabpanel">
                                <div class="row g-3">
                                    <!-- Descrição -->
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                      wire:model="descricao" rows="6" style="height: 150px;"
                                                      placeholder="Descreva os objetivos, missão e valores da aliança" required></textarea>
                                            <label><i class="fas fa-align-left text-primary me-1"></i>Descrição da Aliança *</label>
                                            @error('descricao')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Status Visual -->
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Status:</strong>
                                            <span class="text-muted">
                                                {{ $isEditing ? 'Editando Aliança' : 'Nova Aliança' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="salvarAlianca" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="salvarAlianca">
                            <i class="fas fa-save me-1"></i>{{ $isEditing ? 'Atualizar Aliança' : 'Criar Aliança' }}
                        </span>
                        <span wire:loading wire:target="salvarAlianca">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $isEditing ? 'Atualizando...' : 'Criando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .my-alliance-card {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }

        .my-alliance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: #0d6efd;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .metric-card {
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            transform: scale(1.05);
        }

        .icon-interactive {
            transition: all 0.3s ease;
        }

        .metric-card:hover .icon-interactive {
            transform: scale(1.1);
        }
    </style>
    @endpush
</div>
