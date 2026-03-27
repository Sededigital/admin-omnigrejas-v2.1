<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-handshake me-2"></i>Alianças de Igrejas
                        </h1>
                        <p class="mb-0 text-muted">Visualize suas participações e explore novas alianças</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <a href="{{ route('churches.alliance.my') }}" wire:navigate class="btn btn-outline-primary btn-md">
                                <i class="fas fa-star me-2"></i>Gerenciar Minhas Alianças
                            </a>
                            @if(!$showMyAlliances)
                            <button class="btn bg-info text-light btn-md" wire:click="procurarAliancasCompativeis" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-search me-2"></i>Encontrar Compatíveis
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>Procurando...
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegação por Abas -->
        <div class="row mb-4">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill" id="allianceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $showMyAlliances ? 'active' : '' }}"
                                wire:click="mostrarMinhasAliancas"
                                type="button">
                            <i class="fas fa-star me-2"></i>Participando ({{ count($minhasAliancas) }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ !$showMyAlliances ? 'active' : '' }}"
                                wire:click="mostrarTodasAliancas"
                                type="button">
                            <i class="fas fa-search me-2"></i>Encontrar Alianças
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-handshake text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">
                            @if($showMyAlliances)
                                {{ count($minhasAliancas) }}
                            @elseif($showCompatibleOnly)
                                {{ count($compatibleAlliances) }}
                            @else
                                {{ $aliancas->total() }}
                            @endif
                        </div>
                        <div class="text-muted small">
                            @if($showMyAlliances)
                                Minhas Alianças
                            @elseif($showCompatibleOnly)
                                Compatíveis Encontradas
                            @else
                                Alianças Ativas
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ count($minhasAliancas) }}</div>
                        <div class="text-muted small">Participações Ativas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-search text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ count($compatibleAlliances) }}</div>
                        <div class="text-muted small">Compatíveis</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-tags text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $categorias->count() }}</div>
                        <div class="text-muted small">Categorias</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca (apenas quando não mostrando alianças próprias) -->
        @if(!$showMyAlliances)
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar Aliança</label>
                        <input type="text"  autocomplete="new-password"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome, sigla ou descrição...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" wire:model.live="categoriaFilter">
                            <option value="">Todas</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="aprovada">Aprovadas</option>
                            <option value="pronta_aprovacao">Prontas p/ Aprovação</option>
                            <option value="rascunho">Rascunhos</option>
                            <option value="pendente_validacao">Em Validação</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Ordenar por</label>
                        <select class="form-select" wire:model.live="orderBy">
                            <option value="created_at">Data de Criação</option>
                            <option value="aderentes_count">Nº de Membros</option>
                            <option value="nome">Nome</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($showCompatibleOnly)
                            <button class="btn btn-outline-secondary w-100" wire:click="limparBusca">
                                <i class="fas fa-times me-1"></i>Limpar
                            </button>
                        @else
                            <button class="btn btn-outline-primary w-100" disabled>
                                <i class="fas fa-filter me-1"></i>Filtros
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Spinner de Busca -->
        @if($isSearching)
        <div class="text-center py-5">
            <div class="spinner-border text-info" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Procurando alianças compatíveis...</span>
            </div>
            <h5 class="mt-3 text-muted">Procurando alianças compatíveis...</h5>
            <p class="text-muted">Analisando categorias e compatibilidade</p>
        </div>
        @endif

        <!-- Mensagem quando não há alianças compatíveis -->
        @if($showCompatibleOnly && !$isSearching && empty($compatibleAlliances))
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Nenhuma aliança compatível encontrada!</strong>
            <p class="mb-2">Não foram encontradas alianças da mesma categoria que a sua igreja.</p>
            <button class="btn btn-sm btn-outline-primary" wire:click="limparBusca">
                <i class="fas fa-search me-1"></i>Ver todas as alianças
            </button>
        </div>
        @endif


        <!-- Lista de Alianças -->
        <div class="row g-4">
            @forelse($showMyAlliances ? $minhasAliancas : ($showCompatibleOnly && !empty($compatibleAlliances) ? $compatibleAlliances : $aliancas) as $alianca)
            @php
                // Verificar status da participação no início do loop
                $participacao = \App\Models\Igrejas\IgrejaAlianca::where('igreja_id', $igreja->id ?? null)
                    ->where('alianca_id', $alianca->id)
                    ->first();

                $isParticipating = $participacao && $participacao->status === 'ativo';
                $hasInactiveParticipation = $participacao && $participacao->status === 'inativo';
                $isCreator = $alianca->criador_id === auth()->id();
                $canExit = $this->ehAdmin && !$isCreator && $isParticipating;
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-hover h-100 alliance-card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center m-2">
                            <div>
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-handshake text-info me-2"></i>
                                    {{ $alianca->nome }}
                                    @if($alianca->sigla)
                                        <small class="text-muted">({{ $alianca->sigla }})</small>
                                    @endif
                                </h6>
                            </div>
                            @if(!$showMyAlliances)
                            <span class="badge bg-{{
                                match($alianca->status) {
                                    'aprovada' => 'success',
                                    'pronta_aprovacao' => 'info',
                                    'rascunho' => 'secondary',
                                    'pendente_validacao' => 'warning',
                                    default => 'danger'
                                }
                            }}">
                                {{
                                    match($alianca->status) {
                                        'aprovada' => 'Ativa',
                                        'pronta_aprovacao' => 'Pronta p/ Aprovação',
                                        'rascunho' => 'Rascunho',
                                        'pendente_validacao' => 'Em Validação',
                                        default => 'Suspensa'
                                    }
                                }}
                            </span>
                            @else
                                @if($hasInactiveParticipation)
                                <span class="badge bg-warning">
                                    <i class="fas fa-pause me-1"></i>Saiu da Aliança
                                </span>
                                @else
                                <span class="badge bg-success">
                                    <i class="fas fa-star me-1"></i>Participando
                                </span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted small mb-3">
                            {{ Str::limit($alianca->descricao, 100) }}
                        </p>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                do<small class="text-muted d-block">Categoria</small>
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-tag me-1"></i>{{ $alianca->categoria->nome ?? 'Não definida' }}
                                </span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Membros</small>
                                <span class="fw-semibold text-info">
                                    <i class="fas fa-users me-1"></i>{{ $alianca->aderentes_count }}
                                </span>
                            </div>
                        </div>

                        @if($showMyAlliances)
                        <div class="mb-3">
                            @if($hasInactiveParticipation)
                                <small class="text-muted d-block">Data de Saída</small>
                                <span class="text-warning">
                                    <i class="fas fa-sign-out-alt me-1"></i>
                                    {{ $participacao ? $participacao->data_desligamento->format('d/m/Y') : 'N/A' }}
                                </span>
                            @else
                                <small class="text-muted d-block">Data de Entrada</small>
                                <span class="text-dark">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $participacao ? $participacao->data_adesao->format('d/m/Y') : 'N/A' }}
                                </span>
                            @endif
                        </div>
                        @else
                        <div class="mb-3">
                            <small class="text-muted d-block">Criada por</small>
                            <span class="text-dark">
                                <i class="fas fa-user me-1"></i>{{ $alianca->criador->name ?? 'Sistema' }}
                            </span>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-flex gap-2">

                            @if($showMyAlliances && $isParticipating)
                                <!-- Botões apenas para alianças com participação ATIVA -->
                                <div class="d-flex gap-2 flex-wrap">
                                    <button class="btn btn-outline-info btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#leadersModal"
                                            wire:click="$set('selectedAliancaId', {{ $alianca->id }})">
                                        <i class="fas fa-users me-1"></i>Ver Líderes
                                    </button>
                                    <a href="{{ route('churches.community', $alianca->id) }}" wire:navigate class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-comments me-1"></i>Comunidade
                                    </a>
                                    <button class="btn bg-info text-light btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addLeaderModal"
                                            wire:click="$set('selectedAliancaId', {{ $alianca->id }})">
                                        <i class="fas fa-user-plus me-1"></i>Adicionar Líder
                                    </button>
                                    @if($canExit)
                                    <button class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#exitAllianceModal"
                                            wire:click="solicitarSairDaAlianca({{ $alianca->id }})">
                                        <i class="fas fa-sign-out-alt me-1"></i>Sair
                                    </button>
                                    @endif
                                </div>
                            @elseif($showMyAlliances && $hasInactiveParticipation)
                                <!-- Botões para alianças com participação INATIVA (criadores que saíram) -->
                                <button class="btn btn-outline-primary btn-sm flex-fill"
                                        wire:click="solicitarEntrarNaAlianca({{ $alianca->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="solicitarEntrarNaAlianca"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal"
                                        >
                                    <i class="fas fa-sign-in-alt me-1"></i>Reentrar na Aliança
                                </button>
                            @elseif($isParticipating)
                                <!-- Usuário já participa desta aliança -->
                                <button class="btn btn-success btn-sm flex-fill" disabled>
                                    <i class="fas fa-check me-1"></i>Membro
                                </button>
                            @elseif($hasInactiveParticipation || in_array($alianca->status, ['aprovada', 'pronta_aprovacao', 'rascunho']))
                                <!-- Aliança disponível para entrada (incluindo criadores que saíram) -->
                                <button class="btn btn-outline-primary btn-sm flex-fill"
                                        wire:click="solicitarEntrarNaAlianca({{ $alianca->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="solicitarEntrarNaAlianca"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmModal"
                                        >
                                    <i class="fas fa-sign-in-alt me-1"></i>Entrar na Aliança
                                </button>
                            @else
                                <!-- Aliança em validação -->
                                <button class="btn btn-outline-secondary btn-sm flex-fill" disabled>
                                    <i class="fas fa-clock me-1"></i>Em Validação
                                </button>
                            @endif

                            @if(!$showMyAlliances)
                            <button class="btn btn-outline-info btn-sm">
                                <i class="fas fa-eye"></i>
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
                        @if($showMyAlliances)
                            <h5 class="text-muted">Você ainda não participa de nenhuma aliança</h5>
                            <p class="text-muted">Explore alianças compatíveis e fortaleça sua comunidade</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn bg-info text-light" wire:click="mostrarTodasAliancas">
                                    <i class="fas fa-search me-1"></i>Encontrar Alianças
                                </button>
                                <a href="{{ route('churches.alliance.my') }}" wire:navigate class="btn btn-outline-primary">
                                    <i class="fas fa-plus-circle me-1"></i>Criar Aliança
                                </a>
                            </div>
                        @else
                            <h5 class="text-muted">Nenhuma aliança encontrada</h5>
                            <p class="text-muted">Tente ajustar os filtros de busca</p>
                            @if($search || $categoriaFilter || $showCompatibleOnly)
                                <button class="btn btn-outline-primary" wire:click="limparBusca">
                                    <i class="fas fa-times me-1"></i>Limpar filtros
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($showMyAlliances)
        <div class="mt-4 text-center text-muted">
            <small>Mostrando {{ count($minhasAliancas) }} aliança(s) da sua igreja</small>
        </div>
        @elseif(!$showCompatibleOnly && $aliancas->hasPages())
        <div class="mt-4">
            {{ $aliancas->links() }}
            <div class="text-center text-muted mt-2">
                <small>Mostrando {{ $aliancas->firstItem() }}-{{ $aliancas->lastItem() }} de {{ $aliancas->total() }} alianças</small>
            </div>
        </div>
        @elseif($showCompatibleOnly && !empty($compatibleAlliances))
        <div class="mt-4 text-center text-muted">
            <small>Mostrando {{ count($compatibleAlliances) }} aliança(s) compatível(is) encontrada(s)</small>
        </div>
        @endif
    </div>

    {{-- Modal de Confirmação de Entrada --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold" id="confirmModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar Entrada na Aliança
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" wire:click="fecharModalConfirmacao"></button>
                </div>
                <div class="modal-body">
                    @if($selectedAlianca)
                        <div class="text-center mb-4">
                            <i class="fas fa-handshake text-info" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="fw-bold text-center mb-3">{{ $selectedAlianca->nome }}</h6>
                        <p class="text-muted mb-4">
                            Ao entrar nesta aliança, sua igreja se compromete a participar das atividades e seguir as regras estabelecidas.
                            Todos os líderes e administradores da sua igreja serão automaticamente adicionados como participantes.
                        </p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Esta ação não pode ser desfeita sem aprovação do administrador da aliança.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="fecharModalConfirmacao">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn bg-info text-light" wire:click="confirmarEntrarNaAlianca" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-check me-1"></i>Confirmar Entrada
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Entrando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Adicionar Líder --}}
    <div class="modal fade" id="addLeaderModal" tabindex="-1" aria-labelledby="addLeaderModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light text-white">
                    <h5 class="modal-title" id="addLeaderModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Adicionar Líder
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Selecione um membro para adicionar como líder</label>
                        <select class="form-select" wire:model="novoLiderId">
                            <option value="">Escolher membro...</option>
                            @if($this->membrosDisponiveis)
                                @foreach($this->membrosDisponiveis as $membro)
                                    <option value="{{ $membro->id }}">
                                        {{ $membro->user->name }} ({{ ucfirst($membro->cargo) }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn bg-info text-light"
                            wire:click="adicionarLider"
                            wire:loading.attr="disabled"
                            data-bs-dismiss="modal">
                        <span wire:loading.remove>
                            <i class="fas fa-plus me-1"></i>Adicionar
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Adicionando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Sair da Aliança --}}
    <div class="modal fade" id="exitAllianceModal" tabindex="-1" aria-labelledby="exitAllianceModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exitAllianceModalLabel">
                        <i class="fas fa-sign-out-alt me-2"></i>Sair da Aliança
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar" wire:click="fecharModalSaida"></button>
                </div>
                <div class="modal-body">
                    @if($selectedAlianca)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Ao sair da aliança "{{ $selectedAlianca->nome }}", todos os líderes da sua igreja serão removidos automaticamente.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirme sua senha para continuar</label>
                            <input type="password" autocomplete="new-password"   class="form-control" wire:model="senhaConfirmacao" placeholder="Digite sua senha">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="fecharModalSaida">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger"
                            wire:click="sairDaAlianca"
                            wire:loading.attr="disabled"
                            data-bs-dismiss="modal">
                        <span wire:loading.remove>
                            <i class="fas fa-sign-out-alt me-1"></i>Sair da Aliança
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Saindo...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Visualizar Líderes --}}
    <div class="modal fade" id="leadersModal" tabindex="-1" aria-labelledby="leadersModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light text-white">
                    <h5 class="modal-title" id="leadersModalLabel">
                        <i class="fas fa-users me-2"></i>Líderes das Minhas Alianças
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    @if($selectedAliancaId)
                        @php
                            $aliancaSelecionada = \App\Models\Igrejas\AliancaIgreja::find($selectedAliancaId);
                            $participacao = \App\Models\Igrejas\IgrejaAlianca::where('igreja_id', $igreja->id)
                                ->where('alianca_id', $selectedAliancaId)
                                ->where('status', 'ativo')
                                ->first();
                            // Buscar TODOS os líderes de TODAS as igrejas participantes da aliança
                            $participacoesAlianca = \App\Models\Igrejas\IgrejaAlianca::where('alianca_id', $selectedAliancaId)
                                ->where('status', 'ativo')
                                ->pluck('id');
                            $lideres = \App\Models\Igrejas\AliancaLider::whereIn('igreja_alianca_id', $participacoesAlianca)
                                ->where('ativo', true)
                                ->with(['membro.user', 'membro.igreja'])
                                ->get();
                        @endphp

                        @if($aliancaSelecionada)
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-handshake text-info me-2 fs-5"></i>
                                    <h6 class="mb-0 text-info fw-bold">{{ $aliancaSelecionada->nome }}</h6>
                                    <span class="badge bg-info text-light ms-2">
                                        {{ $lideres->count() }} líder{{ $lideres->count() !== 1 ? 'es' : '' }}
                                    </span>
                                </div>

                                @if($lideres->count() > 0)
                                    <div class="row g-3">
                                        @foreach($lideres as $lider)
                                            <div class="col-lg-6 col-xl-4">
                                                <div class="card border-primary h-100 shadow-sm">
                                                    <div class="card-body text-center">
                                                        <div class="position-relative mb-3">
                                                            <div class="text-center">
                                                                <i class="fas fa-user-circle text-info" style="font-size: 2rem;"></i>
                                                            </div>
                                                            @if($lider->membro->igreja_id === $igreja->id)
                                                                <div class="position-absolute top-0 end-0">
                                                                    <span class="badge bg-success">
                                                                        <i class="fas fa-home"></i>
                                                                    </span>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <h6 class="mb-1 fw-bold">{{ $lider->membro->user->name }}</h6>
                                                        <p class="text-muted mb-2 small">{{ ucfirst($lider->cargo_na_alianca) }}</p>

                                                        <div class="row g-2 mb-2">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Igreja</small>
                                                                <small class="fw-semibold">{{ $lider->membro->igreja->nome ?? 'N/A' }}</small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Adesão</small>
                                                                <small class="fw-semibold">{{ $lider->data_adesao->format('d/m/Y') }}</small>
                                                            </div>
                                                        </div>

                                                        @if($lider->membro->igreja_id === $igreja->id)
                                                            @if($lider->membro->user_id === auth()->id())
                                                                <button class="btn btn-outline-warning btn-sm w-100"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#confirmActionModal"
                                                                        wire:click="$set('selectedLiderId', {{ $lider->id }})">
                                                                    <i class="fas fa-user-minus me-1"></i>Abdicar Cargo
                                                                </button>
                                                            @else
                                                                <button class="btn btn-outline-danger btn-sm w-100"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#confirmActionModal"
                                                                        wire:click="$set('selectedLiderId', {{ $lider->id }})">
                                                                    <i class="fas fa-trash me-1"></i>Remover
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-user-slash fa-3x mb-3 opacity-50"></i>
                                        <h6>Nenhum líder adicionado ainda</h6>
                                        <p class="mb-0">Adicione líderes para gerenciar esta aliança</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-handshake fa-4x mb-3 opacity-25"></i>
                                <h5>Aliança não encontrada</h5>
                                <p class="mb-0">A aliança selecionada não foi encontrada</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-handshake fa-4x mb-3 opacity-25"></i>
                            <h5>Selecione uma aliança</h5>
                            <p class="mb-0">Clique em "Ver Líderes" em uma aliança para visualizar seus líderes</p>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmação de Ação --}}
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold" id="confirmActionModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar Ação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" wire:click="fecharModalConfirmacaoAcao"></button>
                </div>
                <div class="modal-body">
                    @if($selectedLiderId)
                        @php
                            $liderSelecionado = \App\Models\Igrejas\AliancaLider::with('membro.user')->find($selectedLiderId);
                            $isProprioUsuario = $liderSelecionado && $liderSelecionado->membro->user_id === auth()->id();
                        @endphp

                        @if($liderSelecionado)
                            <div class="text-center mb-4">
                                @if($isProprioUsuario)
                                    <i class="fas fa-user-minus text-warning" style="font-size: 3rem;"></i>
                                @else
                                    <i class="fas fa-user-times text-danger" style="font-size: 3rem;"></i>
                                @endif
                            </div>

                            <h6 class="fw-bold text-center mb-3">
                                @if($isProprioUsuario)
                                    Abdicar do Cargo
                                @else
                                    Remover Líder
                                @endif
                            </h6>

                            <div class="card border-light mb-3">
                                <div class="card-body text-center">
                                    <div class="text-center mb-2">
                                        <i class="fas fa-user-circle text-info" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h6 class="mb-1">{{ $liderSelecionado->membro->user->name }}</h6>
                                    <p class="text-muted small mb-0">{{ ucfirst($liderSelecionado->cargo_na_alianca) }}</p>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                @if($isProprioUsuario)
                                    Tem certeza que deseja <strong>abdicar</strong> do seu cargo nesta aliança?
                                    Esta ação não pode ser desfeita.
                                @else
                                    Tem certeza que deseja <strong>remover</strong> {{ $liderSelecionado->membro->user->name }} da aliança?
                                    Esta ação não pode ser desfeita.
                                @endif
                            </p>

                            @if($isProprioUsuario)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Atenção:</strong> Se você for o último administrador/pastor desta aliança,
                                    será necessário indicar um substituto antes de abdicar.
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="fecharModalConfirmacaoAcao">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    @if($selectedLiderId)
                        @php
                            $liderSelecionado = \App\Models\Igrejas\AliancaLider::with('membro.user')->find($selectedLiderId);
                            $isProprioUsuario = $liderSelecionado && $liderSelecionado->membro->user_id === auth()->id();
                        @endphp

                        @if($isProprioUsuario)
                            <button type="button" class="btn btn-warning"
                                    wire:click="confirmarAbdicarCargo"
                                    wire:loading.attr="disabled"
                                    data-bs-dismiss="modal">
                                <span wire:loading.remove>
                                    <i class="fas fa-user-minus me-1"></i>Abdicar Cargo
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-1"></i>Processando...
                                </span>
                            </button>
                        @else
                            <button type="button" class="btn btn-danger"
                                    wire:click="confirmarRemoverLider"
                                    wire:loading.attr="disabled"
                                    data-bs-dismiss="modal">
                                <span wire:loading.remove>
                                    <i class="fas fa-trash me-1"></i>Remover Líder
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-1"></i>Removendo...
                                </span>
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
            .alliance-card {
                transition: all 0.3s ease;
                border: 1px solid #e9ecef;
            }

            .alliance-card:hover {
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

    <script src="{{ asset('system/js/alliance.js') }}" data-navigate-once></script>

</div>
