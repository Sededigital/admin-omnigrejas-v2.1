<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Recursos Bloqueados</h1>
                            <p>Gerencie recursos bloqueados manualmente para igrejas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#bloqueioModal">
                                <i class="fas fa-ban me-2"></i>
                                Bloquear Recurso
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar bloqueios..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="recursoFilter">
                                <option value="">Todos os recursos</option>
                                @foreach($recursoOptions as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Todos os status</option>
                                @foreach($statusOptions as $key => $value)
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

        {{-- Lista de Recursos Bloqueados --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Recursos Bloqueados ({{ $recursosBloqueados->total() }})</h4>
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
                                    <th>Igreja</th>
                                    <th>Recurso</th>
                                    <th>Motivo</th>
                                    <th>Status</th>
                                    <th>Bloqueado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recursosBloqueados as $bloqueio)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($bloqueio->igreja->nome ?? 'I', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $bloqueio->igreja->nome ?? 'Igreja' }}</h6>
                                                <small class="text-muted">{{ $bloqueio->igreja->nif ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $recursoOptions[$bloqueio->recurso_tipo] ?? $bloqueio->recurso_tipo }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Str::limit($bloqueio->motivo_bloqueio, 30) }}</strong>
                                            @if($bloqueio->observacoes)
                                                <br>
                                                <small class="text-muted">{{ Str::limit($bloqueio->observacoes, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $isAtivo = is_null($bloqueio->desbloqueado_em);
                                            $statusClass = $isAtivo ? 'danger' : 'success';
                                            $statusText = $isAtivo ? 'Bloqueado' : 'Desbloqueado';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        @if($bloqueio->desbloqueado_em)
                                            <br>
                                            <small class="text-muted">Em: {{ $bloqueio->desbloqueado_em->format('d/m/Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $bloqueio->created_at->format('d/m/Y H:i') }}</small>
                                        @if($bloqueio->bloqueadoPor)
                                            <br>
                                            <small class="text-muted">Por: {{ $bloqueio->bloqueadoPor->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(is_null($bloqueio->desbloqueado_em))
                                                <button type="button" class="btn btn-sm btn-outline-success" wire:click="desbloquearRecurso({{ $bloqueio->id }})" title="Desbloquear">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $bloqueio->id }})" data-bs-toggle="modal" data-bs-target="#bloqueioModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteBloqueio({{ $bloqueio->id }})" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-unlock fa-2x mb-2"></i>
                                            <p>Nenhum recurso bloqueado encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($recursosBloqueados->hasPages())
                <div class="card-footer">
                    {{ $recursosBloqueados->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Bloqueio --}}
    <div class="modal fade" id="bloqueioModal" tabindex="-1" aria-labelledby="bloqueioModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="bloqueioModalLabel">
                        <i class="fas fa-ban text-danger me-2"></i>
                        <span id="modal-title">{{ $editingBloqueio ? 'Editar Bloqueio' : 'Bloquear Recurso' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveBloqueio">

                        <!-- Seleção da Igreja -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('igreja_id') is-invalid @enderror"
                                            wire:model="igreja_id">
                                        <option value="">Selecione uma igreja</option>
                                        @foreach($igrejas as $igreja)
                                            <option value="{{ $igreja->id }}">{{ $igreja->nome }} ({{ $igreja->nif }})</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-church text-primary me-1"></i>Igreja *</label>
                                    @error('igreja_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tipo de Recurso -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('recurso_tipo') is-invalid @enderror"
                                            wire:model="recurso_tipo">
                                        <option value="">Selecione o recurso</option>
                                        @foreach($recursoOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-cogs text-primary me-1"></i>Recurso *</label>
                                    @error('recurso_tipo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Motivo -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('motivo_bloqueio') is-invalid @enderror"
                                           wire:model="motivo_bloqueio" placeholder="Motivo do bloqueio">
                                    <label><i class="fas fa-exclamation-triangle text-primary me-1"></i>Motivo *</label>
                                    @error('motivo_bloqueio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                              wire:model="observacoes" placeholder="Observações adicionais" rows="3"></textarea>
                                    <label><i class="fas fa-sticky-note text-primary me-1"></i>Observações</label>
                                    @error('observacoes')
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
                    <button type="button" class="btn btn-danger" wire:click="saveBloqueio" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveBloqueio">
                            <i class="fas fa-ban me-1"></i>{{ $editingBloqueio ? 'Atualizar Bloqueio' : 'Bloquear Recurso' }}
                        </span>
                        <span wire:loading wire:target="saveBloqueio">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingBloqueio ? 'Atualizando...' : 'Bloqueando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
