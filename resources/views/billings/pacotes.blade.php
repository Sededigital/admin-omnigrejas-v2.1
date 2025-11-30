<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Gestão de Pacotes</h1>
                            <p>Gerencie os pacotes de assinatura disponíveis no sistema</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#pacoteModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Pacote
                            </button>
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
        {{-- Filtros e busca --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar pacotes..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                <i class="fas fa-eraser me-1"></i>
                                Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de Pacotes --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Pacotes ({{ $pacotes->total() }})</h4>
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
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th>Preço</th>
                                    <th>Duração</th>
                                    <th>Trial Dias</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pacotes as $pacote)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($pacote->nome, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $pacote->nome }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($pacote->descricao ?? 'Sem descrição', 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ number_format($pacote->preco, 2, ',', '.') }} Kz</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $pacote->duracao_meses }} meses</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $pacote->trial_dias ?? 0 }} dias</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $pacote->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $pacote->id }})" data-bs-toggle="modal" data-bs-target="#pacoteModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePacote({{ $pacote->id }})" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este pacote?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-2x mb-2"></i>
                                            <p>Nenhum pacote encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($pacotes->hasPages())
                <div class="card-footer">
                    {{ $pacotes->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Modal de Pacote --}}
        <div class="modal fade" id="pacoteModal" tabindex="-1" aria-labelledby="pacoteModalLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="pacoteModalLabel">
                            <i class="fas fa-box text-primary me-2"></i>
                            <span id="modal-title">{{ $editingPacote ? 'Editar Pacote' : 'Novo Pacote' }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="savePacote">

                            <!-- Seleção do Nome -->
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('nome') is-invalid @enderror"
                                               wire:model="nome" placeholder="Nome do pacote" required>
                                        <label><i class="fas fa-tag text-primary me-1"></i>Nome *</label>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Preço e Duração -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" step="0.01" class="form-control @error('preco') is-invalid @enderror"
                                               wire:model="preco" placeholder="0.00" required>
                                        <label><i class="fas fa-dollar-sign text-primary me-1"></i>Preço (Kz) *</label>
                                        @error('preco')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('duracao_meses') is-invalid @enderror"
                                               wire:model="duracao_meses" placeholder="1" min="1" required>
                                        <label><i class="fas fa-calendar text-primary me-1"></i>Duração (meses) *</label>
                                        @error('duracao_meses')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('trial_dias') is-invalid @enderror"
                                               wire:model="trial_dias" placeholder="0" min="0">
                                        <label><i class="fas fa-clock text-primary me-1"></i>Trial (dias)</label>
                                        @error('trial_dias')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="3"
                                                  placeholder="Descrição do pacote"></textarea>
                                        <label><i class="fas fa-comment text-primary me-1"></i>Descrição</label>
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
                                            {{ $editingPacote ? 'Editando Pacote' : 'Novo Pacote' }}
                                        </span>
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
                        <button type="button" class="btn btn-primary" wire:click="savePacote" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="savePacote">
                                <i class="fas fa-save me-1"></i>{{ $editingPacote ? 'Atualizar Pacote' : 'Salvar Pacote' }}
                            </span>
                            <span wire:loading wire:target="savePacote">
                                <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingPacote ? 'Atualizando...' : 'Salvando...' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scripts para Assinaturas --}}
        <script src="{{ asset('system/js/assignatures.js') }}" data-navigate-once></script>
    </div>
</div>
