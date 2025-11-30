<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Gestão de Módulos</h1>
                            <p>Gerencie os módulos disponíveis no sistema</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#moduloModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Módulo
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar módulos..." wire:model.live.debounce.300ms="search">
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

        {{-- Lista de Módulos --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Módulos ({{ $modulos->total() }})</h4>
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
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($modulos as $modulo)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-info rounded">
                                                <span class="avatar-title">{{ substr($modulo->nome, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $modulo->nome }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($modulo->descricao ?? 'Sem descrição', 50) }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $modulo->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $modulo->id }})" data-bs-toggle="modal" data-bs-target="#moduloModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteModulo({{ $modulo->id }})" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este módulo?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-cubes fa-2x mb-2"></i>
                                            <p>Nenhum módulo encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($modulos->hasPages())
                <div class="card-footer">
                    {{ $modulos->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Scripts para Assinaturas --}}
        <script src="{{ asset('system/js/assignatures.js') }}" data-navigate-once></script>

    </div>

    {{-- Modal de Módulo --}}
    <div class="modal fade" id="moduloModal" tabindex="-1" aria-labelledby="moduloModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="moduloModalLabel">
                        <i class="fas fa-cube text-primary me-2"></i>
                        <span id="modal-title">{{ $editingModulo ? 'Editar Módulo' : 'Novo Módulo' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveModulo">

                        <!-- Seleção do Nome -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('nome') is-invalid @enderror"
                                           wire:model="nome" placeholder="Nome do módulo" required>
                                    <label><i class="fas fa-tag text-primary me-1"></i>Nome *</label>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                              wire:model="descricao" rows="3"
                                              placeholder="Descrição do módulo"></textarea>
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
                                        {{ $editingModulo ? 'Editando Módulo' : 'Novo Módulo' }}
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
                    <button type="button" class="btn btn-primary" wire:click="saveModulo" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveModulo">
                            <i class="fas fa-save me-1"></i>{{ $editingModulo ? 'Atualizar Módulo' : 'Salvar Módulo' }}
                        </span>
                        <span wire:loading wire:target="saveModulo">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingModulo ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
