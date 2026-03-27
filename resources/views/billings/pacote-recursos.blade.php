<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Recursos dos Pacotes</h1>
                            <p>Configure os limites de recursos para cada pacote SaaS</p>
                        </div>
                        <div>
                            <button type="button" class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#recursoModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Limite
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
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar limites..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" wire:model.live="pacoteFilter">
                                <option value="">Todos os pacotes</option>
                                @foreach($pacotes as $pacote)
                                    <option value="{{ $pacote->id }}">{{ $pacote->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="recursoFilter">
                                <option value="">Todos os recursos</option>
                                @foreach($recursoOptions as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                <i class="fas fa-eraser me-1"></i>
                                Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de Limites de Recursos --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Limites de Recursos ({{ $pacoteRecursos->total() }})</h4>
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
                                    <th>Pacote</th>
                                    <th>Recurso</th>
                                    <th>Limite</th>
                                    <th>Unidade</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pacoteRecursos as $recurso)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($recurso->pacote->nome ?? 'P', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $recurso->pacote->nome ?? 'Pacote' }}</h6>
                                                <small class="text-muted">AOA {{ number_format($recurso->pacote->preco ?? 0, 2, ',', '.') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-light">{{ $recursoOptions[$recurso->recurso_tipo] ?? $recurso->recurso_tipo }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-info">{{ number_format($recurso->limite_valor, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $unidadeOptions[$recurso->unidade] ?? $recurso->unidade }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $recurso->id }})" data-bs-toggle="modal" data-bs-target="#recursoModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteRecurso({{ $recurso->id }})" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-cogs fa-2x mb-2"></i>
                                            <p>Nenhum limite de recurso encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($pacoteRecursos->hasPages())
                <div class="card-footer">
                    {{ $pacoteRecursos->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Recurso --}}
    <div class="modal fade" id="recursoModal" tabindex="-1" aria-labelledby="recursoModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="recursoModalLabel">
                        <i class="fas fa-cogs text-info me-2"></i>
                        <span id="modal-title">{{ $editingRecurso ? 'Editar Limite' : 'Novo Limite de Recurso' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveRecurso">

                        <!-- Seleção do Pacote -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('pacote_id') is-invalid @enderror"
                                            wire:model="pacote_id">
                                        <option value="">Selecione um pacote</option>
                                        @foreach($pacotes as $pacote)
                                            <option value="{{ $pacote->id }}">{{ $pacote->nome }} - AOA {{ number_format($pacote->preco, 2, ',', '.') }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-box text-info me-1"></i>Pacote *</label>
                                    @error('pacote_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tipo de Recurso e Limite -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('recurso_tipo') is-invalid @enderror"
                                            wire:model="recurso_tipo">
                                        <option value="">Selecione o recurso</option>
                                        @foreach($recursoOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-tags text-info me-1"></i>Tipo de Recurso *</label>
                                    @error('recurso_tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control @error('limite_valor') is-invalid @enderror"
                                           wire:model="limite_valor" placeholder="0" min="0" step="0.01">
                                    <label><i class="fas fa-tachometer-alt text-info me-1"></i>Limite </label>
                                    @error('limite_valor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Unidade -->
                            <div class="col-md-3">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('unidade') is-invalid @enderror"
                                            wire:model="unidade">
                                        <option value="">Selecione</option>
                                        @foreach($unidadeOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-balance-scale text-info me-1"></i>Unidade *</label>
                                    @error('unidade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                    <button type="button" class="btn bg-info text-light" wire:click="saveRecurso" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveRecurso">
                            <i class="fas fa-save me-1"></i>{{ $editingRecurso ? 'Atualizar Limite' : 'Salvar Limite' }}
                        </span>
                        <span wire:loading wire:target="saveRecurso">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingRecurso ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
