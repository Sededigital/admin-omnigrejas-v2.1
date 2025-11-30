@php
    use Illuminate\Support\Facades\Storage;
@endphp

<!-- Modal de Visualização de Denúncia -->
<div class="modal fade" id="denunciaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Visualizar Denúncia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($denunciaSelecionada)
                    <div class="row g-3">
                        <!-- Informações do Membro -->
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i>Informações do Membro
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg me-3">
                                            @if($denunciaSelecionada->criadoPor->photo_url)
                                                <img src="{{ Storage::disk('supabase')->url($denunciaSelecionada->criadoPor->photo_url) }}" alt="Avatar" class="rounded-circle" style="width: 60px; height: 60px;">
                                            @else
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                    <span class="fw-bold fs-4">{{ substr($denunciaSelecionada->criadoPor->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1">{{ $denunciaSelecionada->criadoPor->name }}</h5>
                                            <p class="mb-1 text-muted">{{ $denunciaSelecionada->criadoPor->email }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>Membro desde {{ $denunciaSelecionada->criadoPor->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Conteúdo da Denúncia -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-file-alt me-2"></i>Conteúdo da Denúncia
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="bg-light p-3 rounded">
                                        <p class="mb-0">{{ $denunciaSelecionada->texto }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações da Denúncia -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Informações da Denúncia
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="border-start border-danger border-4 ps-3">
                                                <small class="text-muted d-block">Data da Denúncia</small>
                                                <strong>{{ $denunciaSelecionada->data->format('d/m/Y') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border-start border-danger border-4 ps-3">
                                                <small class="text-muted d-block">Hora da Denúncia</small>
                                                <strong>{{ $denunciaSelecionada->data->format('H:i:s') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="border-start border-danger border-4 ps-3">
                                                <small class="text-muted d-block">ID da Denúncia</small>
                                                <strong>{{ $denunciaSelecionada->id }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Denúncia não encontrada</h5>
                        <p class="text-muted">A denúncia solicitada não foi encontrada ou foi removida.</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                @if($denunciaSelecionada)
                    <button type="button" class="btn btn-danger" wire:click="abrirModalConfirmacao('excluir_denuncia', {{ $denunciaSelecionada->id }})">
                        <i class="fas fa-trash me-1"></i>Excluir Denúncia
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-denuncia-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('denunciaModal'));
        modal.show();
    });

    Livewire.on('close-denuncia-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('denunciaModal'));
        if (modal) {
            modal.hide();
        }
    });
});
</script>