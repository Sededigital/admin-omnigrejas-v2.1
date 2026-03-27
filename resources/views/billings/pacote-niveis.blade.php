<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Níveis dos Pacotes</h1>
                            <p>Configure os níveis hierárquicos dos pacotes SaaS</p>
                        </div>
                        <div>
                            <button type="button" class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#nivelModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Nível
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar níveis..." wire:model.live.debounce.300ms="search">
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

        {{-- Lista de Níveis --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Níveis dos Pacotes ({{ $pacoteNiveis->total() }})</h4>
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
                                    <th>Nível</th>
                                    <th>Prioridade</th>
                                    <th>Recursos Extras</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pacoteNiveis as $nivel)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($nivel->pacote->nome ?? 'P', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $nivel->pacote->nome ?? 'Pacote' }}</h6>
                                                <small class="text-muted">AOA {{ number_format($nivel->pacote->preco ?? 0, 2, ',', '.') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $nivel->nivel }}</span>
                                    </td>
                                    <td>
                                        <strong class="text-info">{{ $nivel->prioridade }}</strong>
                                    </td>
                                    <td>
                                        @if($nivel->recursos_extras)
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ json_encode($nivel->recursos_extras, JSON_PRETTY_PRINT) }}">
                                                <small class="text-muted">{{ Str::limit(json_encode($nivel->recursos_extras), 50) }}</small>
                                            </div>
                                        @else
                                            <small class="text-muted">Nenhum</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $nivel->id }})" data-bs-toggle="modal" data-bs-target="#nivelModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteNivel({{ $nivel->id }})" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-layer-group fa-2x mb-2"></i>
                                            <p>Nenhum nível encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($pacoteNiveis->hasPages())
                <div class="card-footer">
                    {{ $pacoteNiveis->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Nível --}}
    <div class="modal fade" id="nivelModal" tabindex="-1" aria-labelledby="nivelModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="nivelModalLabel">
                        <i class="fas fa-layer-group text-info me-2"></i>
                        <span id="modal-title">{{ $editingNivel ? 'Editar Nível' : 'Novo Nível' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveNivel">

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

                            <!-- Nível e Prioridade -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('nivel') is-invalid @enderror"
                                           wire:model="nivel" placeholder="Ex: Básico, Premium, Enterprise">
                                    <label><i class="fas fa-tag text-info me-1"></i>Nível *</label>
                                    @error('nivel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control @error('prioridade') is-invalid @enderror"
                                           wire:model="prioridade" placeholder="1" min="1" max="10">
                                    <label><i class="fas fa-sort-numeric-up text-info me-1"></i>Prioridade *</label>
                                    @error('prioridade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Recursos Extras -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('recursos_extras') is-invalid @enderror"
                                              wire:model="recursos_extras" placeholder='{"suporte": "24/7", "backup": "diario"}' rows="4"></textarea>
                                    <label><i class="fas fa-cogs text-info me-1"></i>Recursos Extras (JSON)</label>
                                    @error('recursos_extras')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Formato JSON para recursos extras do nível</small>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn bg-info text-light" wire:click="saveNivel" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveNivel">
                            <i class="fas fa-save me-1"></i>{{ $editingNivel ? 'Atualizar Nível' : 'Salvar Nível' }}
                        </span>
                        <span wire:loading wire:target="saveNivel">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingNivel ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
