<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-user-shield me-2"></i>Administradores de Igrejas
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie os administradores das igrejas
                            @if($preSelectedChurchObj && is_object($preSelectedChurchObj))
                                <span class="badge bg-info text-light ms-2">{{ $preSelectedChurchObj->nome }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openAddAdminModal" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="fas fa-user-plus me-2"></i>Adicionar Admin
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-shield text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Admins</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-check text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['active'] }}</div>
                        <div class="text-muted small">Admins Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-times text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['inactive'] }}</div>
                        <div class="text-muted small">Admins Inativos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Administrador</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome, email ou telefone">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filtrar por Igreja</label>
                        <select class="form-select" wire:model.live="selectedChurch">
                            <option value="">Todas as igrejas</option>
                            @foreach($churches as $church)
                                <option value="{{ $church->id }}" {{ $preSelectedChurchObj && $preSelectedChurchObj->id == $church->id ? 'selected' : '' }}>
                                    {{ $church->nome }}
                                </option>
                            @endforeach
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
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-list-ul me-2"></i>Lista de Administradores
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Administrador</th>
                                <th>Telefone</th>
                                <th>Igreja</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($admins as $admin)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-warning text-white me-3">
                                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $admin->name }}</div>
                                            <small class="text-muted">{{ $admin->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $admin->phone ?: 'Não informado' }}</td>
                                <td>
                                    @if($admin->membros->first() && $admin->membros->first()->igreja)
                                        <div class="fw-semibold">{{ $admin->membros->first()->igreja->nome }}</div>
                                        <small class="text-muted">{{ ucfirst($admin->membros->first()->igreja->tipo ?? 'Independente') }}</small>
                                    @else
                                        <span class="text-muted">Sem igreja</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $admin->is_active ? 'success' : 'secondary' }}">
                                        {{ $admin->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $admin->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $admin->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openEditAdminModal('{{ $admin->id }}')" data-bs-toggle="modal" data-bs-target="#editAdminModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-{{ $admin->is_active ? 'warning' : 'success' }}"
                                                wire:click="toggleAdminStatus('{{ $admin->id }}')"
                                                title="{{ $admin->is_active ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $admin->is_active ? 'user-times' : 'user-check' }}"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" wire:click="openDeleteModal('{{ $admin->id }}', 'soft')" data-bs-toggle="modal" data-bs-target="#deleteAdminModal" title="Desativar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="openDeleteModal('{{ $admin->id }}', 'hard')" data-bs-toggle="modal" data-bs-target="#deleteAdminModal" title="Excluir">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-user-shield text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum administrador encontrado</div>
                                    @if($search || $selectedChurch)
                                        <button class="btn btn-sm btn-outline-primary mt-2" wire:click="$set('search', '')">
                                            Limpar filtros
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($admins->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ $admins->firstItem() }}-{{ $admins->lastItem() }} de {{ $admins->total() }} registros</span>
                        <nav aria-label="Paginação">
                            {{ $admins->links() }}
                        </nav>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($admins as $admin)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-warning text-white me-3">
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $admin->name }}</h6>
                                        <span class="badge bg-{{ $admin->is_active ? 'success' : 'secondary' }}">
                                            {{ $admin->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-envelope text-muted me-1"></i>
                                <small class="text-muted">{{ $admin->email }}</small>
                            </div>
                            @if($admin->phone)
                            <div class="mb-2">
                                <i class="fas fa-phone text-muted me-1"></i>
                                <small class="text-muted">{{ $admin->phone }}</small>
                            </div>
                            @endif
                            <div class="mb-2">
                                <i class="fas fa-building text-muted me-1"></i>
                                <small class="text-muted">
                                    @if($admin->membros->first() && $admin->membros->first()->igreja)
                                        {{ $admin->membros->first()->igreja->nome }}
                                    @else
                                        Sem igreja
                                    @endif
                                </small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $admin->created_at->format('d/m/Y') }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openEditAdminModal('{{ $admin->id }}')" data-bs-toggle="modal" data-bs-target="#editAdminModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <div class="dropdown">
                                    <button class="btn btn-outline-danger btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item text-warning" wire:click="deleteAdmin('{{ $admin->id }}', 'soft')"
                                                    onclick="return confirm('Tem certeza que deseja DESATIVAR este administrador? Ele permanecerá no sistema como usuário anônimo.')">
                                                <i class="fas fa-user-times me-2"></i>Desativar (Manter no Sistema)
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger" wire:click="deleteAdmin('{{ $admin->id }}', 'hard')"
                                                onclick="return confirm('ATENÇÃO: Esta ação é IRREVERSÍVEL! O administrador será removido permanentemente do sistema. Tem certeza?')">
                                                <i class="fas fa-trash-alt me-2"></i>Excluir Permanentemente
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-shield text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum administrador encontrado</div>
                            @if($search || $selectedChurch)
                                <button class="btn btn-outline-primary mt-2" wire:click="$set('search', '')">
                                    <i class="fas fa-times me-1"></i>Limpar filtros
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($admins->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $admins->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $admins->firstItem() }}-{{ $admins->lastItem() }} de {{ $admins->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Adicionar Admin -->
    <div class="modal fade {{ $preSelectedChurch ? 'show' : '' }}" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="{{ !$preSelectedChurch }}" style="{{ $preSelectedChurch ? 'display: block;' : '' }}" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAdminModalLabel">
                        <i class="fas fa-user-plus me-2"></i>Adicionar Administrador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="addAdmin">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nome Completo *</label>
                                <input type="text"  autocomplete="new-password" class="form-control" wire:model="adminName" placeholder="Digite o nome completo">
                                @error('adminName') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email *</label>
                                <input type="email" autocomplete="new-password"  class="form-control" wire:model="adminEmail" placeholder="email@exemplo.com">
                                @error('adminEmail') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text"  autocomplete="new-password" class="form-control" wire:model="adminPhone" placeholder="(+244) 9**-***-***">
                                @error('adminPhone') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Igreja *</label>
                                {{-- Debug removido --}}
                                @if($preSelectedChurchObj)
                                    <!-- Igreja pré-selecionada: mostrar badge fixo -->
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info text-light fs-6 px-3 py-2">
                                            <i class="fas fa-building me-1"></i>{{ $preSelectedChurchObj->nome ?? 'Nome não disponível' }}
                                        </span>
                                    </div>
                                    <input type="hidden" wire:model="selectedChurchForAdmin" value="{{ $preSelectedChurchObj->id ?? '' }}">
                                @else
                                    {{-- Debug: Entrou no else --}}
                                    <!-- Seleção normal de igreja -->
                                    <select class="form-select" wire:model="selectedChurchForAdmin">
                                        <option value="">Selecione uma igreja</option>
                                        @foreach($churches as $church)
                                            <option value="{{ $church->id }}" {{ $selectedChurchForAdmin && $selectedChurchForAdmin == $church->id ? 'selected' : '' }}>
                                                {{ $church->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('selectedChurchForAdmin') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Uma senha será gerada automaticamente e enviada por email para o administrador.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @if($preSelectedChurchObj) wire:click="closeAddAdminModal" @else data-bs-dismiss="modal" @endif>Cancelar</button>
                        <button type="submit" class="btn bg-info text-light" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save me-1"></i>Adicionar Admin</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i>Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Admin -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdminModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Administrador
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit="updateAdmin">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nome Completo *</label>
                                <input type="text"  autocomplete="new-password" class="form-control" wire:model="editName" placeholder="Digite o nome completo">
                                @error('editName') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email *</label>
                                <input type="email" autocomplete="new-password"  class="form-control" wire:model="editEmail" placeholder="email@exemplo.com">
                                @error('editEmail') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text"  autocomplete="new-password" class="form-control" wire:model="editPhone" placeholder="(999) 999-999">
                                @error('editPhone') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="editIsActive" id="editIsActive">
                                    <label class="form-check-label" for="editIsActive">
                                        {{ $editIsActive ? 'Ativo' : 'Inativo' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn bg-info text-light" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save me-1"></i>Atualizar</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i>Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-labelledby="deleteAdminModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAdminModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar Ação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @if($deleteType === 'soft')
                        <i class="fas fa-user-times text-warning display-4 mb-3"></i>
                        <h6 class="fw-bold">Desativar Administrador</h6>
                        <p class="text-muted mb-0">
                            O administrador será desativado, mas permanecerá no sistema como usuário anônimo.
                        </p>
                    @else
                        <i class="fas fa-trash-alt text-danger display-4 mb-3"></i>
                        <h6 class="fw-bold text-danger">Excluir Permanentemente</h6>
                        <p class="text-danger mb-0">
                            <strong>ATENÇÃO:</strong> Esta ação é irreversível!<br>
                            O administrador será removido permanentemente do sistema.
                        </p>
                    @endif
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:loading.attr="disabled" wire:target="confirmDelete">
                        <span>Cancelar</span>
                    </button>
                    <button type="button" class="btn btn-{{ $deleteType === 'soft' ? 'warning' : 'danger' }}" wire:click="confirmDelete" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-{{ $deleteType === 'soft' ? 'ban' : 'trash-alt' }} me-1"></i>
                            {{ $deleteType === 'soft' ? 'Desativar' : 'Excluir' }}
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Processando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('system/js/user.js') }}"></script>

    @if($preSelectedChurch)
    <script>
        document.addEventListener('livewire:loaded', function () {
            // Auto-abrir modal quando vem com igreja pré-selecionada
            const modal = new bootstrap.Modal(document.getElementById('addAdminModal'));
            modal.show();

            // Impedir fechamento do modal ao clicar fora ou ESC
            document.getElementById('addAdminModal').addEventListener('hide.bs.modal', function (event) {
                // Só permitir fechar se não for via clique fora ou ESC
                if (event.trigger === 'backdrop' || event.trigger === 'keyboard') {
                    event.preventDefault();
                }
            });
        });
    </script>
    @endif

    <script>
        document.addEventListener('livewire:loaded', function () {
            // Fechar modal de exclusão quando solicitado
            Livewire.on('close-delete-modal', () => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAdminModal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>
</div>
