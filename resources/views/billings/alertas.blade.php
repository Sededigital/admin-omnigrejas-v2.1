<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Gestão de Alertas SaaS</h1>
                            <p>Gerencie alertas automáticos e manuais para assinaturas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#alertaModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Alerta
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
                                <input type="text"  autocomplete="new-password" autocomplete="new-password"  class="form-control" placeholder="Buscar alertas..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="tipoFilter">
                                <option value="">Todos os tipos</option>
                                @foreach($tipoOptions as $key => $value)
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

        {{-- Lista de Alertas --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Alertas ({{ $alertas->total() }})</h4>
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
                                    <th>Tipo</th>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alertas as $alerta)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($alerta->igreja->nome ?? 'I', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $alerta->igreja->nome ?? 'Igreja' }}</h6>
                                                <small class="text-muted">{{ $alerta->igreja->nif ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $tipoOptions[$alerta->tipo_alerta] ?? $alerta->tipo_alerta }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Str::limit($alerta->titulo, 30) }}</strong>
                                            <br>
                                            <small class="text-muted">{{ Str::limit($alerta->mensagem, 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $isExpired = $alerta->expires_at && $alerta->expires_at->isPast();
                                            $statusClass = $isExpired ? 'danger' : 'success';
                                            $statusText = $isExpired ? 'Expirado' : 'Ativo';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        @if($alerta->expires_at)
                                            <br>
                                            <small class="text-muted">Expira: {{ $alerta->expires_at->format('d/m/Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $alerta->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-success" wire:click="enviarAlertaAgora({{ $alerta->id }})" title="Enviar Agora">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $alerta->id }})" data-bs-toggle="modal" data-bs-target="#alertaModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteAlerta({{ $alerta->id }})" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                            <p>Nenhum alerta encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($alertas->hasPages())
                <div class="card-footer">
                    {{ $alertas->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Alerta --}}
    <div class="modal fade" id="alertaModal" tabindex="-1" aria-labelledby="alertaModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="alertaModalLabel">
                        <i class="fas fa-bell text-primary me-2"></i>
                        <span id="modal-title">{{ $editingAlerta ? 'Editar Alerta' : 'Novo Alerta' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-3">
                    <form wire:submit.prevent="saveAlerta">

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

                            <!-- Tipo e Título -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('tipo_alerta') is-invalid @enderror"
                                            wire:model="tipo_alerta">
                                        <option value="">Selecione o tipo</option>
                                        @foreach($tipoOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-tag text-primary me-1"></i>Tipo de Alerta *</label>
                                    @error('tipo_alerta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" autocomplete="new-password"  class="form-control @error('titulo') is-invalid @enderror"
                                           wire:model="titulo" placeholder="Título do alerta">
                                    <label><i class="fas fa-heading text-primary me-1"></i>Título *</label>
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Mensagem -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('mensagem') is-invalid @enderror"
                                              wire:model="mensagem" placeholder="Mensagem do alerta" rows="3"></textarea>
                                    <label><i class="fas fa-comment text-primary me-1"></i>Mensagem *</label>
                                    @error('mensagem')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Expiração -->
                            <div class="col-12">
                                <div class="form-floating mb-3" wire:ignore>
                                    <input type="datetime-local" class="form-control date_flatpicker @error('expires_at') is-invalid @enderror"
                                           wire:model="expires_at">
                                    <label><i class="fas fa-calendar-times text-primary me-1"></i>Data de Expiração (opcional)</label>
                                    @error('expires_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Deixe em branco para alerta permanente</small>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveAlerta" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveAlerta">
                            <i class="fas fa-save me-1"></i>{{ $editingAlerta ? 'Atualizar Alerta' : 'Salvar Alerta' }}
                        </span>
                        <span wire:loading wire:target="saveAlerta">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingAlerta ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
