@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div>
<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center border border-danger h-100">
            <div class="card-body">
                <i class="fas fa-exclamation-triangle text-danger display-6 mb-2"></i>
                <div class="fw-bold h4 mb-1 text-danger">{{ $denuncias->total() }}</div>
                <div class="text-muted small">Total de Denúncias</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border border-warning h-100">
            <div class="card-body">
                <i class="fas fa-calendar-day text-warning display-6 mb-2"></i>
                <div class="fw-bold h4 mb-1 text-warning">{{ $denuncias->where('data', '>=', today())->count() }}</div>
                <div class="text-muted small">Denúncias Hoje</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border border-info h-100">
            <div class="card-body">
                <i class="fas fa-calendar-week text-info display-6 mb-2"></i>
                <div class="fw-bold h4 mb-1 text-info">{{ $denuncias->where('data', '>=', now()->startOfWeek())->count() }}</div>
                <div class="text-muted small">Denúncias na Semana</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border border-secondary h-100">
            <div class="card-body">
                <i class="fas fa-users text-secondary display-6 mb-2"></i>
                <div class="fw-bold h4 mb-1 text-secondary">{{ $denuncias->unique('criado_por')->count() }}</div>
                <div class="text-muted small">Membros que Denunciaram</div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros para Denúncias -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Buscar</label>
                <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="filtroDenunciaBusca" placeholder="Nome do membro ou texto da denúncia...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Data Inicial</label>
                <input type="date" class="form-control" wire:model.live="filtroDenunciaDataInicio">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Data Final</label>
                <input type="date" class="form-control" wire:model.live="filtroDenunciaDataFim">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-secondary w-100" wire:click="$set('filtroDenunciaBusca', ''); $set('filtroDenunciaDataInicio', ''); $set('filtroDenunciaDataFim', '')">
                    <i class="fas fa-times me-1"></i>Limpar
                </button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="text-muted small">
                    <strong>{{ $denuncias->total() }}</strong> denúncias encontradas
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Denúncias -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-exclamation-triangle text-danger me-2"></i>Denúncias Recebidas
        </h5>
    </div>
    <div class="card-body">
        @if($denuncias->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Membro</th>
                            <th>Denúncia</th>
                            <th>Data</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($denuncias as $denuncia)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($denuncia->criadoPor->photo_url)
                                                <img src="{{ Storage::disk('supabase')->url($denuncia->criadoPor->photo_url) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <span class="fw-bold">{{ substr($denuncia->criadoPor->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $denuncia->criadoPor->name }}</div>
                                            <small class="text-muted">{{ $denuncia->criadoPor->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $denuncia->texto }}">
                                        {{ Str::limit($denuncia->texto, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <i class="fas fa-calendar me-1"></i>{{ $denuncia->data->format('d/m/Y') }}
                                        <br>
                                        <i class="fas fa-clock me-1"></i>{{ $denuncia->data->format('H:i') }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" wire:click="abrirModalDenuncia({{ $denuncia->id }})" title="Visualizar Denúncia">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="abrirModalConfirmacao('excluir_denuncia', {{ $denuncia->id }})" title="Excluir Denúncia">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $denuncias->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-exclamation-triangle text-muted mb-4" style="font-size: 4rem;"></i>
                <h4 class="text-muted">Nenhuma denúncia encontrada</h4>
                <p class="text-muted mb-4">
                    @if($filtroDenunciaBusca || $filtroDenunciaDataInicio || $filtroDenunciaDataFim)
                        Nenhuma denúncia encontrada com os filtros aplicados.
                    @else
                        Ainda não há denúncias recebidas na sua igreja.
                    @endif
                </p>
                @if($filtroDenunciaBusca || $filtroDenunciaDataInicio || $filtroDenunciaDataFim)
                    <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroDenunciaBusca', ''); $set('filtroDenunciaDataInicio', ''); $set('filtroDenunciaDataFim', '')">
                        <i class="fas fa-times me-1"></i>Limpar Filtros
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>
</div>