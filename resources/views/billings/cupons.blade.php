<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Cupons de Desconto</h1>
                            <p>Gerencie cupons de desconto para assinaturas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#cupomModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Cupom
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
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar cupons..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Todos os status</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                <i class="fas fa-eraser me-1"></i>
                                Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de Cupons --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Cupons ({{ $cupons->total() }})</h4>
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
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>Desconto</th>
                                    <th>Validade</th>
                                    <th>Uso</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cupons as $cupom)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ substr($cupom->codigo, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $cupom->codigo }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($cupom->descricao ?? 'Sem descrição', 40) }}</small>
                                    </td>
                                    <td>
                                        @if($cupom->desconto_percentual)
                                            <span class="badge bg-success">{{ $cupom->desconto_percentual }}%</span>
                                        @elseif($cupom->desconto_valor)
                                            <span class="badge bg-info">{{ number_format($cupom->desconto_valor, 2, ',', '.') }} Kz</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cupom->valido_de && $cupom->valido_ate)
                                            <small class="text-muted">
                                                {{ $cupom->valido_de->format('d/m/Y') }}<br>
                                                até {{ $cupom->valido_ate->format('d/m/Y') }}
                                            </small>
                                        @elseif($cupom->valido_de)
                                            <small class="text-muted">
                                                A partir de<br>{{ $cupom->valido_de->format('d/m/Y') }}
                                            </small>
                                        @elseif($cupom->valido_ate)
                                            <small class="text-muted">
                                                Até<br>{{ $cupom->valido_ate->format('d/m/Y') }}
                                            </small>
                                        @else
                                            <small class="text-muted">Sem limite</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small class="text-muted">{{ $cupom->usado ?? 0 }}/{{ $cupom->uso_max }}</small>
                                            @if($cupom->usado > 0)
                                                <div class="progress" style="height: 4px;">
                                                    <div class="progress-bar bg-primary" role="progressbar"
                                                         style="width: {{ ($cupom->usado / $cupom->uso_max) * 100 }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($cupom->ativo)
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
                                        <small class="text-muted">{{ $cupom->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $cupom->id }})" data-bs-toggle="modal" data-bs-target="#cupomModal" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" wire:click="toggleStatus({{ $cupom->id }})" title="{{ $cupom->ativo ? 'Desativar' : 'Ativar' }}">
                                                <i class="fas fa-{{ $cupom->ativo ? 'ban' : 'check' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteCupom({{ $cupom->id }})" title="Excluir"
                                                    onclick="return confirm('Tem certeza que deseja excluir este cupom?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                            <p>Nenhum cupom encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cupons->hasPages())
                <div class="card-footer">
                    {{ $cupons->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Modal de Cupom --}}
        <div class="modal fade" id="cupomModal" tabindex="-1" aria-labelledby="cupomModalLabel" aria-hidden="true"
             data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <!-- Header do Modal -->
                    <div class="modal-header bg-light border-bottom">
                        <h5 class="modal-title fw-bold" id="cupomModalLabel">
                            <i class="fas fa-ticket-alt text-primary me-2"></i>
                            <span id="modal-title">{{ $editingCupom ? 'Editar Cupom' : 'Novo Cupom' }}</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>

                    <!-- Corpo do Modal -->
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveCupom">

                            <!-- Código e Descrição -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control text-uppercase @error('codigo') is-invalid @enderror"
                                               wire:model="codigo" placeholder="Código do cupom" required>
                                        <label><i class="fas fa-hashtag text-primary me-1"></i>Código *</label>
                                        @error('codigo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="generateCodigo">
                                        <i class="fas fa-magic me-1"></i>Gerar Código
                                    </button>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('uso_max') is-invalid @enderror"
                                               wire:model="uso_max" placeholder="1" min="1" required>
                                        <label><i class="fas fa-users text-primary me-1"></i>Uso Máximo *</label>
                                        @error('uso_max')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="2"
                                                  placeholder="Descrição do cupom"></textarea>
                                        <label><i class="fas fa-comment text-primary me-1"></i>Descrição</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Desconto -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('desconto_percentual') is-invalid @enderror"
                                               wire:model="desconto_percentual" placeholder="0" min="0" max="100">
                                        <label><i class="fas fa-percent text-primary me-1"></i>Desconto Percentual (%)</label>
                                        @error('desconto_percentual')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" step="0.01" class="form-control @error('desconto_valor') is-invalid @enderror"
                                               wire:model="desconto_valor" placeholder="0.00" min="0">
                                        <label><i class="fas fa-dollar-sign text-primary me-1"></i>Desconto em Valor (Kz)</label>
                                        @error('desconto_valor')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Validade -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control date_flatpicker @error('valido_de') is-invalid @enderror"
                                               wire:model="valido_de">
                                        <label><i class="fas fa-calendar-plus text-primary me-1"></i>Válido De</label>
                                        @error('valido_de')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control date_flatpicker @error('valido_ate') is-invalid @enderror"
                                               wire:model="valido_ate">
                                        <label><i class="fas fa-calendar-minus text-primary me-1"></i>Válido Até</label>
                                        @error('valido_ate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input @error('ativo') is-invalid @enderror"
                                               type="checkbox" wire:model="ativo" id="ativoSwitch">
                                        <label class="form-check-label" for="ativoSwitch">
                                            <i class="fas fa-toggle-on text-primary me-1"></i>Cupom Ativo
                                        </label>
                                        @error('ativo')
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
                                            {{ $editingCupom ? 'Editando Cupom' : 'Novo Cupom' }}
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
                        <button type="button" class="btn btn-primary" wire:click="saveCupom" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveCupom">
                                <i class="fas fa-save me-1"></i>{{ $editingCupom ? 'Atualizar Cupom' : 'Salvar Cupom' }}
                            </span>
                            <span wire:loading wire:target="saveCupom">
                                <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingCupom ? 'Atualizando...' : 'Salvando...' }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scripts para Cupons --}}
        <script src="{{ asset('system/js/assignatures.js') }}" data-navigate-once></script>
    </div>
</div>
