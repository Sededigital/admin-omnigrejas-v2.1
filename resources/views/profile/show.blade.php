<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-user me-2"></i>Meu Perfil
                        </h1>
                        <p class="mb-0 text-muted">Gerencie suas informações pessoais e configurações</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn bg-info text-light btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fas fa-edit me-1"></i>Editar Perfil
                            </button>
                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-1"></i>Alterar Senha
                            </button>
                            @if($user->isIgrejaAdmin() || $user->isSuperAdmin())
                            <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#editChurchModal">
                                <i class="fas fa-church me-1"></i>Editar Igreja
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Photo Section -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($user->photo_url)
                        <img src="{{ Storage::disk('supabase')->url($user->photo_url) }}" alt="Foto de perfil"
                             class="rounded-circle border border-3 border-primary"
                             style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-info text-light text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 120px; height: 120px; font-size: 3rem;">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                        <i class="fas fa-camera me-1"></i>Alterar Foto
                    </button>
                    <div class="text-muted small mt-1">
                        Clique para fazer upload de nova foto
                    </div>
                </div>
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB
                </div>
            </div>
        </div>

        <!-- Informações do Perfil (Modo Visualização) -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user text-info me-2"></i>Informações do Perfil
                </h5>
                <button class="btn bg-info text-light btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit me-1"></i>Editar Perfil
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Nome Completo</small>
                                    <span class="text-dark">{{ $user->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Email</small>
                                    <span class="text-dark">{{ $user->email }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Telefone</small>
                                    <span class="text-dark">{{ $user->phone ?: 'Não informado' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Função</small>
                                    <span class="badge bg-info text-light">{{ $user->getRoleLabel() }}</span>
                                </div>
                            </div>

                            <!-- Informações da Igreja -->
                            @if($cargo)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Cargo na Igreja</small>
                                    <span class="badge bg-info text-light">{{ ucfirst($cargo) }}</span>
                                </div>
                            </div>
                            @endif

                            @if($data_entrada)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Membro desde</small>
                                    <span class="text-dark">{{ \Carbon\Carbon::parse($data_entrada)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            @endif

                            @if($numero_membro)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Número do Membro</small>
                                    <span class="text-dark">{{ $numero_membro }}</span>
                                </div>
                            </div>
                            @endif

                            @if($membroPerfil && $membroPerfil->genero)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Gênero</small>
                                    <span class="text-dark">{{ ucfirst($membroPerfil->genero) }}</span>
                                </div>
                            </div>
                            @endif

                            @if($membroPerfil && $membroPerfil->data_nascimento)
                            <div class="col-md-6">
                                <div class="border rounded p-3 h-100">
                                    <small class="text-muted d-block fw-semibold">Data de Nascimento</small>
                                    <span class="text-dark">{{ \Carbon\Carbon::parse($membroPerfil->data_nascimento)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            @endif

                            @if($membroPerfil && $membroPerfil->endereco)
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <small class="text-muted d-block fw-semibold">Endereço</small>
                                    <span class="text-dark">{{ $membroPerfil->endereco }}</span>
                                </div>
                            </div>
                            @endif

                            @if($membroPerfil && $membroPerfil->observacoes)
                            <div class="col-12">
                                <div class="border rounded p-3">
                                    <small class="text-muted d-block fw-semibold">Observações</small>
                                    <span class="text-dark">{{ $membroPerfil->observacoes }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status da Conta -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-info text-light text-light">
                                <h6 class="mb-4 text-light">
                                    <i class="fas fa-shield-alt me-2"></i>Status da Conta
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Status da Conta -->
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-{{ $user->is_active ? 'check-circle text-success' : 'times-circle text-secondary' }} me-2 fs-5"></i>
                                        <div>
                                            <small class="text-muted d-block mb-0">Status</small>
                                            <span class="fw-semibold">{{ $user->is_active ? 'Ativa' : 'Inativa' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }} fs-6">
                                        <i class="fas fa-{{ $user->is_active ? 'check' : 'times' }} me-1"></i>
                                        {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>

                                <!-- Email Verificado -->
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-{{ $user->hasVerifiedEmail() ? 'envelope-open text-success' : 'envelope text-warning' }} me-2 fs-5"></i>
                                        <div>
                                            <small class="text-muted d-block mb-0">Email</small>
                                            <span class="fw-semibold">{{ $user->hasVerifiedEmail() ? 'Verificado' : 'Pendente' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $user->hasVerifiedEmail() ? 'success' : 'warning' }} fs-6">
                                        <i class="fas fa-{{ $user->hasVerifiedEmail() ? 'check' : 'clock' }} me-1"></i>
                                        {{ $user->hasVerifiedEmail() ? 'OK' : 'Aguardando' }}
                                    </span>
                                </div>

                                <!-- 2FA -->
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-{{ $user->two_factor_confirmed_at ? 'lock text-success' : 'unlock text-muted' }} me-2 fs-5"></i>
                                        <div>
                                            <small class="text-muted d-block mb-0">2FA</small>
                                            <span class="fw-semibold">{{ $user->two_factor_confirmed_at ? 'Ativado' : 'Desativado' }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $user->two_factor_confirmed_at ? 'success' : 'secondary' }} fs-6">
                                        <i class="fas fa-{{ $user->two_factor_confirmed_at ? 'shield-alt' : 'shield' }} me-1"></i>
                                        {{ $user->two_factor_confirmed_at ? 'Protegido' : 'Desprotegido' }}
                                    </span>
                                </div>

                                <!-- Membro desde -->
                                <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt text-info me-2 fs-5"></i>
                                        <div>
                                            <small class="text-muted d-block mb-0">Registrado no sistema desde</small>
                                            <span class="fw-semibold">{{ $user->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ $user->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Ações Rápidas -->
                        <div class="card mt-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i>Ações Rápidas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                        <i class="fas fa-edit me-1"></i>Editar Perfil
                                    </button>
                                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="fas fa-key me-1"></i>Alterar Senha
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" wire:navigate href="{{ route('two-factor.show') }}">
                                        <i class="fas fa-shield-alt me-1"></i>Segurança 2FA
                                    </button>
                                    @if($user->isIgrejaAdmin() || $user->isSuperAdmin())
                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#editChurchModal">
                                        <i class="fas fa-church me-1"></i>Editar Igreja
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Perfil -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light text-white">
                    <h5 class="modal-title" id="editProfileModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Editar Perfil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <form wire:submit.prevent="updateProfile">
                    <div class="modal-body p-4">
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills nav-fill mb-4" id="profileTabs" role="tablist" wire:ignore.self>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic" type="button" role="tab">
                                    <i class="fas fa-user me-1"></i>
                                    <span class="d-none d-sm-inline">Informações Básicas</span>
                                    <span class="d-inline d-sm-none">Básicas</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                                    <i class="fas fa-id-card me-1"></i>
                                    <span class="d-none d-sm-inline">Informações Pessoais</span>
                                    <span class="d-inline d-sm-none">Pessoais</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content" id="profileTabsContent" wire:ignore>
                            <!-- Aba Básica -->
                            <div class="tab-pane fade show active" id="basic" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"  autocomplete="new-password" class="form-control @error('name') is-invalid @enderror"
                                                   wire:model="name" placeholder="Nome completo" id="name">
                                            <label for="name">
                                                <i class="fas fa-user me-1"></i>Nome Completo *
                                            </label>
                                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="email" autocomplete="new-password"  class="form-control @error('email') is-invalid @enderror"
                                                   wire:model="email" placeholder="seu@email.com" id="email">
                                            <label for="email">
                                                <i class="fas fa-envelope me-1"></i>Email *
                                            </label>
                                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   wire:model="phone" placeholder="+244 900 000 000" id="phone">
                                            <label for="phone">
                                                <i class="fas fa-phone me-1"></i>Telefone
                                            </label>
                                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text"  autocomplete="new-password" class="form-control" value="{{ $user->getRoleLabel() }}" readonly id="role">
                                            <label for="role">
                                                <i class="fas fa-user-tag me-1"></i>Função
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aba Pessoal -->
                            <div class="tab-pane fade" id="personal" role="tabpanel">
                                @if($cargo)
                                <div class="alert alert-info py-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Como membro da igreja, você pode editar suas informações pessoais abaixo:</small>
                                </div>
                                @else
                                <div class="alert alert-light py-2 mb-3 border">
                                    <i class="fas fa-user-edit text-info me-2"></i>
                                    <small>Complete suas informações pessoais:</small>
                                </div>
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select @error('genero') is-invalid @enderror" wire:model="genero" id="genero">
                                                <option value="">Selecione...</option>
                                                <option value="masculino">Masculino</option>
                                                <option value="feminino">Feminino</option>
                                            </select>
                                            <label for="genero">
                                                <i class="fas fa-venus-mars me-1"></i>Gênero
                                            </label>
                                            @error('genero') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control @error('data_nascimento') is-invalid @enderror"
                                                   wire:model="data_nascimento" id="data_nascimento">
                                            <label for="data_nascimento">
                                                <i class="fas fa-birthday-cake me-1"></i>Data de Nascimento
                                            </label>
                                            @error('data_nascimento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('endereco') is-invalid @enderror"
                                                      wire:model="endereco" placeholder="Digite seu endereço completo"
                                                      id="endereco" style="height: 80px;"></textarea>
                                            <label for="endereco">
                                                <i class="fas fa-map-marker-alt me-1"></i>Endereço
                                            </label>
                                            @error('endereco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                      wire:model="observacoes" placeholder="Observações adicionais sobre seu perfil"
                                                      id="observacoes" style="height: 100px;"></textarea>
                                            <label for="observacoes">
                                                <i class="fas fa-comment me-1"></i>Observações
                                            </label>
                                            @error('observacoes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                @if($cargo)
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-info-circle me-1"></i>Informações da Igreja
                                    </h6>
                                    <div class="row g-2 text-sm">
                                        <div class="col-md-6">
                                            <small class="text-muted">Cargo:</small>
                                            <span class="ms-1 fw-semibold">{{ ucfirst($cargo) }}</span>
                                        </div>
                                        @if($data_entrada)
                                        <div class="col-md-6">
                                            <small class="text-muted">Membro desde:</small>
                                            <span class="ms-1 fw-semibold">{{ \Carbon\Carbon::parse($data_entrada)->format('d/m/Y') }}</span>
                                        </div>
                                        @endif
                                        @if($numero_membro)
                                        <div class="col-md-6">
                                            <small class="text-muted">Número:</small>
                                            <span class="ms-1 fw-semibold">{{ $numero_membro }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn bg-info text-light" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-2"></i>Salvando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Alteração de Senha -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true" wire:ignore.self >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-key me-2"></i>Alterar Senha
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <form wire:submit.prevent="updatePassword">
                    <div class="modal-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-key text-warning fs-3"></i>
                            </div>
                            <h6 class="mt-2 text-muted">Atualize sua senha de acesso</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" autocomplete="new-password"   class="form-control @error('current_password') is-invalid @enderror"
                                           wire:model="current_password" placeholder="Senha atual" id="current_password">
                                    <label for="current_password">
                                        <i class="fas fa-lock me-1"></i>Senha Atual *
                                    </label>
                                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password"  autocomplete="new-password"  class="form-control @error('password') is-invalid @enderror"
                                           wire:model="password" placeholder="Nova senha" id="password">
                                    <label for="password">
                                        <i class="fas fa-key me-1"></i>Nova Senha *
                                    </label>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" autocomplete="new-password"   class="form-control @error('password_confirmation') is-invalid @enderror"
                                           wire:model="password_confirmation" placeholder="Confirmar nova senha" id="password_confirmation">
                                    <label for="password_confirmation">
                                        <i class="fas fa-check-circle me-1"></i>Confirmar Nova Senha *
                                    </label>
                                    @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-light border border-warning mt-4">
                            <div class="d-flex">
                                <i class="fas fa-lightbulb text-warning me-2 mt-1"></i>
                                <div>
                                    <strong class="text-warning">Dicas para uma senha segura:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li>Use pelo menos 6 caracteres</li>
                                        <li>Inclua letras maiúsculas e minúsculas</li>
                                        <li>Adicione números e símbolos</li>
                                        <li>Evite usar informações pessoais</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-warning" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-save me-2"></i>Atualizar Senha
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-2"></i>Atualizando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Edição da Igreja (Admin Only) -->
    @if($user->isIgrejaAdmin() || $user->isSuperAdmin())
    <div class="modal fade" id="editChurchModal" tabindex="-1" aria-labelledby="editChurchModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light text-white">
                    <h5 class="modal-title" id="editChurchModalLabel">
                        <i class="fas fa-church me-2"></i>Editar Dados da Igreja
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <form wire:submit.prevent="updateIgreja">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nome da Igreja *</label>
                                <input type="text"  autocomplete="new-password" class="form-control @error('igreja_nome') is-invalid @enderror"
                                       wire:model="igreja_nome" placeholder="Nome completo da igreja">
                                @error('igreja_nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sigla</label>
                                <input type="text"  autocomplete="new-password" class="form-control @error('igreja_sigla') is-invalid @enderror"
                                       wire:model="igreja_sigla" placeholder="Ex: IBAL">
                                @error('igreja_sigla') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Descrição/Sobre</label>
                                <textarea class="form-control @error('igreja_descricao') is-invalid @enderror"
                                          rows="3" wire:model="igreja_descricao"
                                          placeholder="Breve descrição sobre a igreja"></textarea>
                                @error('igreja_descricao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contato</label>
                                <input type="text"  autocomplete="new-password" class="form-control @error('igreja_contacto') is-invalid @enderror"
                                       wire:model="igreja_contacto" placeholder="Telefone ou email">
                                @error('igreja_contacto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Localização</label>
                                <input type="text"  autocomplete="new-password" class="form-control @error('igreja_localizacao') is-invalid @enderror"
                                       wire:model="igreja_localizacao" placeholder="Cidade, Província">
                                @error('igreja_localizacao') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Logo da Igreja</label>
                                <input type="file" class="form-control @error('igreja_logo') is-invalid @enderror"
                                       wire:model="igreja_logo" accept="image/*">
                                @error('igreja_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">
                                    <small class="text-muted">
                                        Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB. Recomendado: 300x300px
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-info" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-save me-2"></i>Salvar Dados da Igreja
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-2"></i>Salvando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Upload de Foto -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-light text-white">
                    <h5 class="modal-title" id="uploadPhotoModalLabel">
                        <i class="fas fa-camera me-2"></i>Alterar Foto de Perfil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Foto Atual -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            @if($user->photo_url)
                                <img src="{{ Storage::disk('supabase')->url($user->photo_url) }}" alt="Foto atual"
                                     class="rounded-circle border border-primary"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-info text-light text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 120px; height: 120px; font-size: 3rem;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 end-0">
                                <div class="bg-success rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 30px; height: 30px;">
                                    <i class="fas fa-camera text-white" style="font-size: 12px;"></i>
                                </div>
                            </div>
                        </div>
                        <h6 class="mt-3 text-muted">Foto de Perfil Atual</h6>
                    </div>

                    <!-- Upload de Nova Foto -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-upload me-1"></i>Selecionar Nova Foto
                        </label>
                        <input type="file" class="form-control @error('upload_photo') is-invalid @enderror"
                               wire:model="upload_photo" accept="image/*">
                        @error('upload_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Especificações -->
                    <div class="alert alert-light border border-primary">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <strong class="text-info">Especificações da Foto:</strong>
                                <ul class="mb-0 mt-2 small">
                                    <li><strong>Formatos aceitos:</strong> JPG, PNG, GIF</li>
                                    <li><strong>Tamanho máximo:</strong> 2MB</li>
                                    <li><strong>Recomendado:</strong> 300x300px</li>
                                    <li><strong>Formato:</strong> Quadrado para melhor visualização</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn bg-info text-light" wire:click="uploadPhoto" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-upload me-2"></i>Fazer Upload
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-2"></i>Fazendo Upload...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script otimizado para Livewire 3 e SPA -->
    <script>
        // Função para inicializar listeners dos modais
        function initializeModalListeners() {
            // Verificar e configurar modais
            const modals = [
                { id: 'editProfileModal', name: 'Editar Perfil' },
                { id: 'uploadPhotoModal', name: 'Upload Foto' },
                { id: 'changePasswordModal', name: 'Alterar Senha' },
                { id: 'editChurchModal', name: 'Editar Igreja' }
            ];

            modals.forEach(modalConfig => {
                const modalElement = document.getElementById(modalConfig.id);
                if (modalElement) {
                    // Listener para abertura do modal
                    modalElement.addEventListener('shown.bs.modal', function() {
                        // Modal aberto
                    });

                    // Listener para fechamento do modal
                    modalElement.addEventListener('hidden.bs.modal', function() {
                        // Modal fechado
                    });
                }
            });

            // Verificar botões de ação
            const actionButtons = [
                { selector: '[data-bs-target="#editProfileModal"]', name: 'Editar Perfil' },
                { selector: '[data-bs-target="#uploadPhotoModal"]', name: 'Upload Foto' },
                { selector: '[data-bs-target="#changePasswordModal"]', name: 'Alterar Senha' },
                { selector: '[data-bs-target="#editChurchModal"]', name: 'Editar Igreja' }
            ];

            actionButtons.forEach(buttonConfig => {
                const buttons = document.querySelectorAll(buttonConfig.selector);
                buttons.forEach((button, index) => {
                    button.addEventListener('click', function() {
                        // Botão clicado
                    });
                });
            });
        }

        // Função para configurar listeners do Livewire
        function initializeLivewireListeners() {
            // Listener para fechar modal de upload de foto
            Livewire.on('close-upload-photo-modal', () => {
                const modalElement = document.getElementById('uploadPhotoModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Listener para fechar modal de editar perfil
            Livewire.on('close-edit-profile-modal', () => {
                const modalElement = document.getElementById('editProfileModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Listener para fechar modal de alterar senha
            Livewire.on('close-change-password-modal', () => {
                const modalElement = document.getElementById('changePasswordModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });

            // Listener para fechar modal de editar igreja
            Livewire.on('close-edit-church-modal', () => {
                const modalElement = document.getElementById('editChurchModal');
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) {
                        modal.hide();
                    }
                }
            });
        }

        // Inicialização para Livewire 3 e SPA
        document.addEventListener('livewire:navigated', function() {
            initializeModalListeners();
            initializeLivewireListeners();
        });

        // Fallback para carregamento inicial (não SPA)
        document.addEventListener('DOMContentLoaded', function() {
            initializeModalListeners();
            initializeLivewireListeners();
        });

        // Listener adicional para garantir funcionamento em todos os cenários
        document.addEventListener('livewire:init', function() {
            setTimeout(() => {
                initializeModalListeners();
                initializeLivewireListeners();
            }, 100);
        });
    </script>

</div>
