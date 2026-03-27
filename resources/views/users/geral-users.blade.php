<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-users me-2"></i>Gestão de Usuários
                        </h1>
                        <p class="mb-0 text-muted">Gerencie todos os usuários da plataforma</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#userModal">
                            <i class="fas fa-user-plus me-2"></i>Adicionar Usuário
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
                        <i class="fas fa-users text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Usuários</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-check text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['active'] }}</div>
                        <div class="text-muted small">Usuários Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-times text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['inactive'] }}</div>
                        <div class="text-muted small">Usuários Inativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-plus text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['new_this_month'] }}</div>
                        <div class="text-muted small">Novos (Este Mês)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Role -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome, email ou telefone">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filtrar por Função</label>
                        <select class="form-select" wire:model.live="selectedRole">
                            <option value="">Todas as funções</option>
                            <option value="super_admin">Super Administradores</option>
                            <option value="admin">Administradores</option>
                            <option value="pastor">Pastores</option>
                            <option value="ministro">Ministros</option>
                            <option value="obreiro">Obreiros</option>
                            <option value="diacono">Diáconos</option>
                            <option value="membro">Membros</option>
                            <option value="anonymous">Anônimos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button class="btn bg-info text-light flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botões de Filtro Rápido -->
                <div class="row g-2 mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm" wire:click="setRoleFilter('')">
                                <i class="fas fa-users me-1"></i>Todos
                            </button>
                            <button class="btn btn-outline-danger btn-sm" wire:click="setRoleFilter('super_admin')">
                                <i class="fas fa-crown me-1"></i>Super Admins
                            </button>
                            <button class="btn btn-outline-warning btn-sm" wire:click="setRoleFilter('admin')">
                                <i class="fas fa-user-shield me-1"></i>Admins
                            </button>
                            <button class="btn btn-outline-info btn-sm" wire:click="setRoleFilter('pastor')">
                                <i class="fas fa-church me-1"></i>Pastores
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setRoleFilter('ministro')">
                                <i class="fas fa-hands-helping me-1"></i>Ministros
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="setRoleFilter('obreiro')">
                                <i class="fas fa-praying-hands me-1"></i>Obreiros
                            </button>
                            <button class="btn btn-outline-dark text-light text-muted btn-sm" wire:click="setRoleFilter('membro')">
                                <i class="fas fa-user me-1"></i>Membros
                            </button>
                            <button class="btn btn-outline-light btn-sm text-dark" wire:click="setRoleFilter('anonymous')">
                                <i class="fas fa-question-circle me-1"></i>Anônimos
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
                        <i class="fas fa-list-ul me-2"></i>Lista de Usuários
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuário</th>
                                <th>Telefone</th>
                                <th>Função</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-info text-light text-white me-3">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">ID: {{ Str::limit($user->id, 6, '') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->phone ?: 'Não informado' }}</td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">
                                            <span class="badge bg-{{ $user->getRoleBadgeClass() }}">
                                            {{ $user->getRoleLabel() }}
                                            </span>
                                        </div>
                                        @if($user->membros->first() && $user->membros->first()->igreja)
                                            <small class="badge bg-dark text-light">{{ Str::limit($user->membros->first()->igreja->nome, 20, '...') }}</small>
                                        @endif
                                    </div>

                                    
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $user->id }}')" data-bs-toggle="modal" data-bs-target="#userModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" wire:click="enviarCredenciais('{{ $user->id }}')" title="Enviar Credenciais">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        <button class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                wire:click="toggleUserStatus('{{ $user->id }}')"
                                                title="{{ $user->is_active ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $user->is_active ? 'user-times' : 'user-check' }}"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="confirmDelete('{{ $user->id }}')"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-users text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum usuário encontrado</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ $users->firstItem() }}-{{ $users->lastItem() }} de {{ $users->total() }} registros</span>
                        <nav aria-label="Paginação">
                            {{ $users->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($users as $user)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-info text-light text-white me-3">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $user->name }}</h6>
                                        <span class="badge bg-{{ $user->getRoleBadgeClass() }}">
                                            {{ $user->getRoleLabel() }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} mb-2">
                                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-envelope text-muted me-1"></i>
                                <small class="text-muted">{{ $user->email }}</small>
                            </div>
                            @if($user->phone)
                            <div class="mb-2">
                                <i class="fas fa-phone text-muted me-1"></i>
                                <small class="text-muted">{{ $user->phone }}</small>
                            </div>
                            @endif
                            <div class="mb-3">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $user->created_at->format('d/m/Y') }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-info btn-sm" wire:click="enviarCredenciais({{ $user->id }})">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete('{{ $user->id }}')">
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
                            <i class="fas fa-users text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum usuário encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($users->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $users->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $users->firstItem() }}-{{ $users->lastItem() }} de {{ $users->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal para Cadastro/Edição de Usuário -->
    @include('users.modals.user-modal')
    <!-- Scripts para o Modal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Tem certeza?',
                text: 'Esta ação não pode ser desfeita! O usuário será excluído permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deleteUser', userId);
                }
            });
        }
    </script>
    <script src="{{ asset('system/js/user.js') }}"></script>


</div>
