<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Contas Financeiras</h1>
                            <p>Gerencie as contas bancárias e canais digitais da igreja</p>
                        </div>
                        <div>
                            @if($activeTab === 'accounts')
                                <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#accountModal">
                                    <i class="fas fa-plus me-2"></i>
                                    Nova Conta
                                </button>
                            @elseif($activeTab === 'digitalChannels')
                                <button type="button" class="btn btn-primary" wire:click="openDigitalModal" data-bs-toggle="modal" data-bs-target="#digitalChannelModal">
                                    <i class="fas fa-plus me-2"></i>
                                    Novo Canal Digital
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="iq-header-img">
            <img src="{{ asset('assets/images/dashboard/top-header.png') }}" alt="header" class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header1.png') }}" alt="header" class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header2.png') }}" alt="header" class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header3.png') }}" alt="header" class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header4.png') }}" alt="header" class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header5.png') }}" alt="header" class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="row">
        {{-- Abas de navegação --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"  wire:ignore>
                    <ul class="nav nav-tabs" id="financialAccountTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab === 'accounts' ? 'active' : '' }}" id="accounts-tab"
                               wire:click.prevent="setTab('accounts')" data-bs-toggle="tab"
                               href="#accounts" role="tab" aria-controls="accounts" aria-selected="{{ $activeTab === 'accounts' ? 'true' : 'false' }}">
                                <i class="fas fa-university me-2"></i>Contas Bancárias
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab === 'digitalChannels' ? 'active' : '' }}" id="digitalChannels-tab"
                               wire:click.prevent="setTab('digitalChannels')" data-bs-toggle="tab"
                               href="#digitalChannels" role="tab" aria-controls="digitalChannels" aria-selected="{{ $activeTab === 'digitalChannels' ? 'true' : 'false' }}">
                                <i class="fas fa-wallet me-2"></i>Canais Digitais
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Conteúdo das abas --}}
                <div class="card-body"  wire:ignore>
                    <div class="tab-content" id="financialAccountTabContent">
                        {{-- Aba Contas Bancárias --}}
                        <div class="tab-pane fade {{ $activeTab === 'accounts' ? 'show active' : '' }}" id="accounts" role="tabpanel" aria-labelledby="accounts-tab" tabindex="0" >
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar conta..." wire:model.live.debounce.300ms="search">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="selectedStatus">
                                        <option value="">Todos os status</option>
                                        <option value="ativo">Ativa</option>
                                        <option value="inativo">Inativa</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-end">
                                    <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                        <i class="fas fa-eraser me-1"></i>
                                        Limpar Filtros
                                    </button>
                                </div>
                            </div>

                            {{-- Tabela de Contas --}}
                            <div class="card"  wire:ignore.self>
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Contas Bancárias ({{ $accounts->total() }})</h4>
                                    <div class="dropdown">
                                        <select class="form-select" wire:model.live="perPage">
                                            <option value="10">10 por página</option>
                                            <option value="25">25 por página</option>
                                            <option value="50">50 por página</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Banco</th>
                                                    <th>Titular</th>
                                                    <th>Número da Conta</th>
                                                    <th>Moeda</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($accounts as $account)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title">{{ substr($account->banco, 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $account->banco }}</h6>
                                                                <small class="text-muted">{{ $account->iban ? 'IBAN: ' . $account->iban : '' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $account->titular }}</td>
                                                    <td>{{ $account->numero_conta }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $account->moeda }}</span>
                                                    </td>
                                                    <td>
                                                        @if($account->ativa)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Ativa
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times me-1"></i>Inativa
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click.prevent="openModal('{{ $account->id }}')" data-bs-toggle="modal" data-bs-target="#accountModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="openDeleteModal('account', '{{ $account->id }}')" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-university fa-2x mb-2"></i>
                                                            <p>Nenhuma conta bancária encontrada.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($accounts->hasPages())
                                <div class="card-footer">
                                    {{ $accounts->links() }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Aba Canais Digitais --}}
                        <div class="tab-pane fade {{ $activeTab === 'digitalChannels' ? 'show active' : '' }}" id="digitalChannels" role="tabpanel" aria-labelledby="digitalChannels-tab" tabindex="0">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar canal..." wire:model.live.debounce.300ms="searchDigital">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="selectedType">
                                        <option value="">Todos os tipos</option>
                                        @foreach($digitalChannelTypes as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="selectedStatusDigital">
                                        <option value="">Todos os status</option>
                                        <option value="ativo">Ativo</option>
                                        <option value="inativo">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                        <i class="fas fa-eraser me-1"></i>
                                        Limpar
                                    </button>
                                </div>
                            </div>

                            {{-- Tabela de Canais Digitais --}}
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Canais Digitais ({{ $digitalChannels->total() }})</h4>
                                    <div class="dropdown">
                                        <select class="form-select" wire:model.live="perPage">
                                            <option value="10">10 por página</option>
                                            <option value="25">25 por página</option>
                                            <option value="50">50 por página</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Referência</th>
                                                    <th>Titular</th>
                                                    <th>Moeda</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($digitalChannels as $channel)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-success rounded">
                                                                <span class="avatar-title">{{ substr($digitalChannelTypes[$channel->tipo] ?? $channel->tipo, 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $digitalChannelTypes[$channel->tipo] ?? $channel->tipo }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $channel->referencia }}</td>
                                                    <td>{{ $channel->titular }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $channel->moeda }}</span>
                                                    </td>
                                                    <td>
                                                        @if($channel->ativo)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Ativo
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-times me-1"></i>Inativo
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openDigitalModal('{{ $channel->id }}')" data-bs-toggle="modal" data-bs-target="#digitalChannelModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="openDeleteModal('channel', '{{ $channel->id }}')" title="Excluir">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-wallet fa-2x mb-2"></i>
                                                            <p>Nenhum canal digital encontrado.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($digitalChannels->hasPages())
                                <div class="card-footer">
                                    {{ $digitalChannels->links() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        {{-- Modal de Confirmação de Exclusão --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Confirmar Exclusão
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        @if($deleteItem)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atenção!</strong> Esta ação não pode ser desfeita.
                            </div>
    
                            <div class="mb-3">
                                <h6>Você está prestes a excluir:</h6>
                                @if($deleteType === 'account')
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Banco:</strong><br>
                                                    {{ $deleteItem->banco }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Titular:</strong><br>
                                                    {{ $deleteItem->titular }}
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <strong>Número da Conta:</strong><br>
                                                    {{ $deleteItem->numero_conta }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($deleteType === 'channel')
                                    <div class="card border-warning">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Tipo:</strong><br>
                                                    {{ $digitalChannelTypes[$deleteItem->tipo] ?? $deleteItem->tipo }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Referência:</strong><br>
                                                    {{ $deleteItem->referencia }}
                                                </div>
                                                <div class="col-12 mt-2">
                                                    <strong>Titular:</strong><br>
                                                    {{ $deleteItem->titular }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
    
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-lock text-danger me-1"></i>
                                    Digite sua senha para confirmar:
                                </label>
                                <input type="password"  autocomplete="new-password"  class="form-control @error('deletePassword') is-invalid @enderror"
                                       wire:model="deletePassword" placeholder="Digite sua senha">
                                @error('deletePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($deleteError)
                                    <div class="text-danger small mt-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $deleteError }}
                                    </div>
                                @endif
                            </div>
    
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Permissões:</strong> Apenas usuários com cargo de Admin, Pastor ou Ministro podem excluir itens financeiros.
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x mb-3"></i>
                                <div class="text-muted">Item não encontrado para exclusão.</div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                        <button type="button" class="btn btn-danger" wire:click="confirmDelete" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmDelete">
                                <i class="fas fa-trash me-1"></i>Excluir
                            </span>
                            <span wire:loading wire:target="confirmDelete">
                                <i class="fas fa-spinner fa-spin me-1"></i>Excluindo...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    {{-- Modal de Conta Bancária --}}
    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="accountModalLabel">
                        <i class="fas fa-university text-primary me-2"></i>
                        <span id="modal-title">{{ $editingAccount ? 'Editar Conta Bancária' : 'Nova Conta Bancária' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveAccount">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('account_banco') is-invalid @enderror"
                                           wire:model="account_banco" placeholder="Nome do banco" required>
                                    <label><i class="fas fa-building text-primary me-1"></i>Banco *</label>
                                    @error('account_banco')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('account_titular') is-invalid @enderror"
                                           wire:model="account_titular" placeholder="Nome do titular" required>
                                    <label><i class="fas fa-user text-primary me-1"></i>Titular *</label>
                                    @error('account_titular')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('account_iban') is-invalid @enderror"
                                           wire:model="account_iban" placeholder="Código IBAN">
                                    <label><i class="fas fa-hashtag text-primary me-1"></i>IBAN</label>
                                    @error('account_iban')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('account_swift') is-invalid @enderror"
                                           wire:model="account_swift" placeholder="Código SWIFT">
                                    <label><i class="fas fa-code text-primary me-1"></i>SWIFT</label>
                                    @error('account_swift')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('account_numero_conta') is-invalid @enderror"
                                           wire:model="account_numero_conta" placeholder="Número da conta" required>
                                    <label><i class="fas fa-credit-card text-primary me-1"></i>Número da Conta *</label>
                                    @error('account_numero_conta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('account_moeda') is-invalid @enderror"
                                            wire:model="account_moeda">
                                        <option value="AOA" {{ $account_moeda === 'AOA' ? 'selected' : '' }}>AOA - Kwanza Angolano</option>
                                        <option value="EUR" {{ $account_moeda === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="USD" {{ $account_moeda === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                        <option value="BRL" {{ $account_moeda === 'BRL' ? 'selected' : '' }}>BRL - Real Brasileiro</option>
                                    </select>
                                    <label><i class="fas fa-dollar-sign text-primary me-1"></i>Moeda *</label>
                                    @error('account_moeda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('account_observacoes') is-invalid @enderror"
                                              wire:model="account_observacoes" rows="3"
                                              placeholder="Observações adicionais"></textarea>
                                    <label><i class="fas fa-comment text-primary me-1"></i>Observações</label>
                                    @error('account_observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('account_ativa') is-invalid @enderror"
                                           type="checkbox" wire:model="account_ativa" id="accountAtivaSwitch">
                                    <label class="form-check-label" for="accountAtivaSwitch">
                                        <i class="fas fa-toggle-on text-primary me-1"></i>Conta Ativa
                                    </label>
                                    @error('account_ativa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong>
                                    <span class="text-muted">
                                        {{ $editingAccount ? 'Editando Conta Bancária' : 'Nova Conta Bancária' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveAccount" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAccount">
                            <i class="fas fa-save me-1"></i>{{ $editingAccount ? 'Atualizar Conta' : 'Salvar Conta' }}
                        </span>
                        <span wire:loading wire:target="saveAccount">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingAccount ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Canal Digital --}}
    <div class="modal fade" id="digitalChannelModal" tabindex="-1" aria-labelledby="digitalChannelModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="digitalChannelModalLabel">
                        <i class="fas fa-wallet text-primary me-2"></i>
                        <span id="modal-title">{{ $editingDigitalChannel ? 'Editar Canal Digital' : 'Novo Canal Digital' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveDigitalChannel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('digital_tipo') is-invalid @enderror"
                                            wire:model="digital_tipo">
                                        <option value="">Selecione um tipo</option>
                                        @foreach($digitalChannelTypes as $key => $value)
                                            <option value="{{ $key }}" {{ $digital_tipo === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-tags text-primary me-1"></i>Tipo *</label>
                                    @error('digital_tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('digital_referencia') is-invalid @enderror"
                                           wire:model="digital_referencia" placeholder="Referência do canal" required>
                                    <label><i class="fas fa-hashtag text-primary me-1"></i>Referência *</label>
                                    @error('digital_referencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('digital_titular') is-invalid @enderror"
                                           wire:model="digital_titular" placeholder="Nome do titular" required>
                                    <label><i class="fas fa-user text-primary me-1"></i>Titular *</label>
                                    @error('digital_titular')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('digital_moeda') is-invalid @enderror"
                                            wire:model="digital_moeda">
                                        <option value="AOA" {{ $digital_moeda === 'AOA' ? 'selected' : '' }}>AOA - Kwanza Angolano</option>
                                        <option value="EUR" {{ $digital_moeda === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="USD" {{ $digital_moeda === 'USD' ? 'selected' : '' }}>USD - Dólar Americano</option>
                                        <option value="BRL" {{ $digital_moeda === 'BRL' ? 'selected' : '' }}>BRL - Real Brasileiro</option>
                                    </select>
                                    <label><i class="fas fa-dollar-sign text-primary me-1"></i>Moeda *</label>
                                    @error('digital_moeda')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('digital_observacoes') is-invalid @enderror"
                                              wire:model="digital_observacoes" rows="3"
                                              placeholder="Observações adicionais"></textarea>
                                    <label><i class="fas fa-comment text-primary me-1"></i>Observações</label>
                                    @error('digital_observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input @error('digital_ativo') is-invalid @enderror"
                                           type="checkbox" wire:model="digital_ativo" id="digitalChannelAtivoSwitch">
                                    <label class="form-check-label" for="digitalChannelAtivoSwitch">
                                        <i class="fas fa-toggle-on text-primary me-1"></i>Canal Ativo
                                    </label>
                                    @error('digital_ativo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong>
                                    <span class="text-muted">
                                        {{ $editingDigitalChannel ? 'Editando Canal Digital' : 'Novo Canal Digital' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveDigitalChannel" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveDigitalChannel">
                            <i class="fas fa-save me-1"></i>{{ $editingDigitalChannel ? 'Atualizar Canal' : 'Salvar Canal' }}
                        </span>
                        <span wire:loading wire:target="saveDigitalChannel">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingDigitalChannel ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
