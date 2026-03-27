<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-7">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-exchange-alt me-2"></i>Migração de Membros
                        </h1>
                        <p class="mb-0 text-muted">Transfira membros entre igrejas de forma organizada</p>
                    </div>
                    <div class="col-lg-6 col-md-5 mt-3 mt-md-0">
                        <!-- Desktop: Botões em linha -->
                        <div class="d-none d-lg-flex justify-content-end">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary btn-sm @if($abaAtiva === 'membros') active @endif" wire:click="$set('abaAtiva', 'membros')">
                                    <i class="fas fa-users me-1"></i>Membros da Igreja
                                </button>
                                <button class="btn btn-outline-info btn-sm @if($abaAtiva === 'historico') active @endif" wire:click="$set('abaAtiva', 'historico')">
                                    <i class="fas fa-history me-1"></i>Histórico Migrações
                                </button>
                            </div>
                        </div>

                        <!-- Tablet/Mobile: Botões dinâmicos -->
                        <div class="d-lg-none">
                            <div class="row g-1">
                                <div class="col-6">
                                    <button class="btn btn-outline-primary btn-sm w-100 @if($abaAtiva === 'membros') active @endif" wire:click="$set('abaAtiva', 'membros')">
                                        <i class="fas fa-users me-1"></i>Membros
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-outline-info btn-sm w-100 @if($abaAtiva === 'historico') active @endif" wire:click="$set('abaAtiva', 'historico')">
                                        <i class="fas fa-history me-1"></i>Histórico
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        @if($abaAtiva === 'membros')
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $members->total() }}</div>
                        <div class="text-muted small">Membros Elegíveis</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $migrationHistory->where('status', 'concluida')->count() }}</div>
                        <div class="text-muted small">Migrações Concluídas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $migrationHistory->where('status', 'pendente')->count() }}</div>
                        <div class="text-muted small">Migrações Pendentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-church text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $churches->count() }}</div>
                        <div class="text-muted small">Igrejas Disponíveis</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Conteúdo das Abas -->
        @if($abaAtiva === 'membros')
            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Buscar Membro</label>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="searchMember" placeholder="Nome ou email">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" wire:model.live="filterStatus">
                                <option value="ativo">Ativos</option>
                                <option value="inativo">Inativos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">&nbsp;</label>
                            <button class="btn btn-outline-secondary w-100" wire:click="$set('searchMember', ''); $set('filterStatus', 'ativo')" wire:loading.attr="disabled" wire:target="$set('searchMember', ''); $set('filterStatus', 'ativo')">
                                <span wire:loading.remove wire:target="$set('searchMember', ''); $set('filterStatus', 'ativo')">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </span>
                                <span wire:loading wire:target="$set('searchMember', ''); $set('filterStatus', 'ativo')">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Limpar
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Membros -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-users me-2"></i>Membros da Igreja
                    </h5>
                </div>
                <div class="card-body">
                    @if($members->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Membro</th>
                                        <th>Cargo</th>
                                        <th>Data Entrada</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($member->user->photo_url)
                                                        <img src="{{ Storage::disk('supabase')->url($member->user->photo_url) }}"
                                                              class="me-3 rounded-circle border"
                                                              alt="Foto {{ $member->user->name }}"
                                                              style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <div class="user-avatar bg-info text-light text-white me-3">
                                                            {{ strtoupper(substr($member->user->name ?? 'N', 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold">{{ $member->user->name ?? 'N/A' }}</div>
                                                        <small class="text-muted">{{ $member->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ ucfirst($member->cargo) }}</td>
                                            <td>{{ $member->data_entrada ? $member->data_entrada->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $member->status === 'ativo' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($member->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($member->status === 'ativo')
                                                    <button class="btn bg-info text-light btn-sm" data-bs-toggle="modal" data-bs-target="#migrationModal" wire:click="openMigrationModal('{{ $member->id }}')">
                                                        <i class="fas fa-exchange-alt me-1"></i>Migrar
                                                    </button>
                                                @else
                                                    <span class="text-muted small">Inativo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $members->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">Nenhum membro encontrado</h5>
                            <p class="text-muted">Não há membros com os filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        @elseif($abaAtiva === 'historico')
            <!-- Histórico de Migrações -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-history me-2"></i>Histórico de Migrações
                    </h5>
                    <button class="btn btn-success btn-sm" wire:click="printTransferStats" wire:loading.attr="disabled" wire:target="printTransferStats">
                        <span wire:loading.remove wire:target="printTransferStats">
                            <i class="fas fa-chart-bar me-1"></i>Imprimir Estatísticas
                        </span>
                        <span wire:loading wire:target="printTransferStats">
                            <i class="fas fa-spinner fa-spin me-1"></i>Gerando...
                        </span>
                    </button>
                </div>
                <div class="card-body">
                    @if($migrationHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Membro</th>
                                        <th>Origem → Destino</th>
                                        <th>Tipo</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($migrationHistory as $migration)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $migration->membro_nome }}</div>
                                                <small class="text-muted">{{ $migration->numero_membro_origem ?? $migration->numero_membro_destino }}</small>
                                            </td>
                                            <td>
                                                @if($migration->igreja_origem_nome)
                                                    <span class="badge bg-danger">{{ $migration->igreja_origem_nome }}</span>
                                                    <i class="fas fa-arrow-right mx-2"></i>
                                                    <span class="badge bg-success">{{ $migration->igreja_destino_nome }}</span>
                                                @else
                                                    <span class="badge bg-info text-light">Nova Adesão</span>
                                                    <i class="fas fa-arrow-right mx-2"></i>
                                                    <span class="badge bg-success">{{ $migration->igreja_destino_nome }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ match($migration->tipo_migracao) {
                                                    'transferencia' => 'warning',
                                                    'reintegracao' => 'info',
                                                    'mudanca_cargo' => 'secondary',
                                                    'nova_adesao' => 'success',
                                                    default => 'light'
                                                } }}">
                                                    {{ match($migration->tipo_migracao) {
                                                        'transferencia' => 'Transferência',
                                                        'reintegracao' => 'Reintegração',
                                                        'mudanca_cargo' => 'Mudança de Cargo',
                                                        'nova_adesao' => 'Nova Adesão',
                                                        default => ucfirst($migration->tipo_migracao)
                                                    } }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($migration->data_migracao)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-{{ $migration->status === 'concluida' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($migration->status) }}
                                                    </span>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                            wire:click="printTransferFormForMigration('{{ $migration->id }}')"
                                                            wire:loading.attr="disabled"
                                                            wire:target="printTransferFormForMigration('{{ $migration->id }}')"
                                                            title="Imprimir ficha de transferência">
                                                        <span wire:loading.remove wire:target="printTransferFormForMigration('{{ $migration->id }}')">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </span>
                                                        <span wire:loading wire:target="printTransferFormForMigration('{{ $migration->id }}')">
                                                            <i class="fas fa-spinner fa-spin"></i>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history text-muted mb-3" style="font-size: 2rem;"></i>
                            <h6 class="text-muted">Nenhuma migração registrada</h6>
                            <p class="text-muted small">As migrações realizadas aparecerão aqui.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Migração -->
    <div class="modal fade" id="migrationModal" tabindex="-1" aria-labelledby="migrationModalLabel" aria-hidden="true"
        data-bs-backdrop="false" data-bs-keyboard="true" data-bs-focus="false"  wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="migrationModalLabel">
                        <i class="fas fa-exchange-alt me-2"></i>Migrar Membro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="migrateMember">


                    <div class="modal-body" >
                        @if($selectedMember)
                            @php
                                $member = \App\Models\Igrejas\IgrejaMembro::with(['user', 'igreja'])->find($selectedMember);
                            @endphp
                            @if($member)
                                <!-- Informações do Membro -->
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Informações do Membro</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Nome:</strong> {{ $member->user->name }}<br>
                                                <strong>Email:</strong> {{ $member->user->email }}<br>
                                                <strong>Cargo Atual:</strong> {{ ucfirst($member->cargo) }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Igreja Atual:</strong> {{ $member->igreja->nome }}<br>
                                                <strong>Data Entrada:</strong> {{ $member->data_entrada ? $member->data_entrada->format('d/m/Y') : 'N/A' }}<br>
                                                <strong>Número:</strong> {{ $member->numero_membro }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tipo de Migração -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Tipo de Migração</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" wire:model.live="migrationType" id="existingChurch" value="existing_church">
                                                    <label class="form-check-label" for="existingChurch">
                                                        <strong>Igreja Cadastrada</strong><br>
                                                        <small class="text-muted">Migrar para uma igreja já cadastrada no sistema</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" wire:model.live="migrationType" id="newChurch" value="new_church">
                                                    <label class="form-check-label" for="newChurch">
                                                        <strong>Igreja Externa</strong><br>
                                                        <small class="text-muted">Migrar para uma igreja não cadastrada no sistema</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('migrationType')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Configurações da Migração -->
                                <div class="row g-3">
                                    @if($migrationType === 'existing_church')
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Igreja de Destino *</label>
                                            <select class="form-select @error('targetChurch') is-invalid @enderror" wire:model="targetChurch">
                                                <option value="">Selecione uma igreja...</option>
                                                @foreach($churches as $church)
                                                    <option value="{{ $church->id }}">{{ $church->nome }} @if($church->sigla) ({{ $church->sigla }}) @endif</option>
                                                @endforeach
                                            </select>
                                            @error('targetChurch')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nome da Igreja de Destino *</label>
                                            <input type="text"  autocomplete="new-password" class="form-control @error('targetChurchName') is-invalid @enderror" wire:model="targetChurchName" placeholder="Digite o nome da igreja...">
                                            @error('targetChurchName')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Igreja não cadastrada no sistema Omnigreja</small>
                                        </div>
                                    @endif
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Novo Cargo (Opcional)</label>
                                        <select class="form-select" wire:model="newRole">
                                            <option value="">Manter cargo atual ({{ ucfirst($member->cargo) }})</option>
                                            <option value="membro">Membro</option>
                                            <option value="obreiro">Obreiro</option>
                                            <option value="diacono">Diácono</option>
                                            <option value="ministro">Ministro</option>
                                            <option value="pastor">Pastor</option>
                                            <option value="admin">Administrador</option>
                                        </select>
                                        @if($migrationType === 'new_church')
                                            <small class="text-muted">Cargo que o membro terá na nova igreja</small>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Motivo da Migração</label>
                                        <textarea class="form-control @error('reason') is-invalid @enderror" wire:model="reason" rows="3" placeholder="Descreva o motivo da migração..."></textarea>
                                        @error('reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" wire:model="printTransferForm" id="printTransferForm">
                                            <label class="form-check-label fw-semibold" for="printTransferForm">
                                                Imprimir ficha de transferência
                                            </label>
                                            <small class="text-muted d-block">Será gerado um documento PDF profissional com os dados da migração</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                            <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center"
                                    style="min-height: 200px;"
                                    wire:loading
                                    wire:target="openMigrationModal">
                                    <i class="fas fa-spinner fa-spin text-info mb-3" style="font-size: 3rem;"></i>
                                    <p class="text-muted">Carregando informações do membro...</p>
                                </div>
                            @endif
                        @else
                        <div class="text-center py-4 d-flex flex-column align-items-center justify-content-center"
                            style="min-height: 200px;"
                            wire:loading
                            wire:target="openMigrationModal">
                            <i class="fas fa-spinner fa-spin text-info mb-3" style="font-size: 3rem;"></i>
                            <p class="text-muted">Carregando informações do membro...</p>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-info text-light" wire:loading.attr="disabled"  wire:target="migrateMember">
                            <span wire:loading.remove>
                                <i class="fas fa-exchange-alt me-1"></i>Migrar Membro
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-1"></i>Processando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script>
        // Aguardar Livewire estar disponível
        function waitForLivewire(callback) {
            if (typeof Livewire !== 'undefined') {
                callback();
            } else {
                setTimeout(() => waitForLivewire(callback), 100);
            }
        }

        document.addEventListener('livewire:navigated', function () {
            // Re-inicializar listeners após navegação SPA
            waitForLivewire(() => initMigrationModalListeners());
        });

        document.addEventListener('livewire:updated', function () {
            // Re-inicializar listeners após atualização do componente
            waitForLivewire(() => initMigrationModalListeners());
        });

        function initMigrationModalListeners() {

            // Listener para abrir modal de migração
            Livewire.on('open-migration-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('migrationModal'));
                modal.show();
            });


            // Listener para quando o modal é mostrado
            document.getElementById('migrationModal').addEventListener('shown.bs.modal', function () {
                // Resetar estado quando modal abre
                const modal = document.getElementById('migrationModal');
                modal.classList.add('show');
                modal.style.display = 'block';
                document.body.classList.add('modal-open');
            });

            // Listener para quando o modal é fechado (qualquer forma)
            document.getElementById('migrationModal').addEventListener('hidden.bs.modal', function () {
                // Resetar estado quando modal fecha
                setTimeout(() => {
                    // Forçar remoção de classes do modal que podem causar problemas de foco
                    const modal = document.getElementById('migrationModal');
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    // Remover todos os backdrops possíveis
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    // Reabilitar scroll da página
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                }, 300);
            });

        }

        // Inicializar listeners na primeira carga
        waitForLivewire(() => initMigrationModalListeners());
    </script>

    <!-- Estilos -->
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .metric-card:hover .icon-interactive {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</div>
