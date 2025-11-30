<!-- Modal para Exclusão de Relatório de Culto -->
<div class="modal fade" id="deleteReportModal" tabindex="-1" aria-labelledby="deleteReportModalLabel" aria-hidden="true"
data-bs-backdrop="static" data-bs-keyboard="false"  wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="deleteReportModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white"  data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-3">
                @if($showDeleteModal && $relatorioParaExcluir)
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt text-danger display-4 mb-2"></i>
                    <h5 class="text-danger mb-2">Excluir Relatório</h5>
                </div>

                <div class="alert alert-warning py-2 mb-3" role="alert">
                    <strong>Esta ação não pode ser desfeita!</strong>
                </div>

                <!-- Informações do Relatório -->
                <div class="border rounded p-3 bg-light">
                    <div class="row g-2 text-sm">
                        <div class="col-12 fw-semibold">{{ $relatorioParaExcluir->titulo ?: 'Relatório sem título' }}</div>
                        <div class="col-md-6"><strong>Data:</strong> {{ $relatorioParaExcluir->data_relatorio->format('d/m/Y') }}</div>
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $this->getStatusBadgeClass($relatorioParaExcluir->status) }} ms-1">
                                {{ $this->getStatusLabel($relatorioParaExcluir->status) }}
                            </span>
                        </div>
                        @if($relatorioParaExcluir->evento || $relatorioParaExcluir->cultoPadrao)
                        <div class="col-12"><strong>Relacionamento:</strong> {{ $relatorioParaExcluir->evento ? $relatorioParaExcluir->evento->titulo : $relatorioParaExcluir->cultoPadrao->titulo }}</div>
                        @endif
                    </div>
                </div>
                @else
                <div class="text-center py-3">
                    <i class="fas fa-file-alt text-muted display-4 mb-2"></i>
                    <div class="text-muted">Relatório não encontrado</div>
                </div>
                @endif
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary"  data-bs-dismiss="modal" aria-label="Fechar">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                @if($showDeleteModal && $relatorioParaExcluir)
                <button type="button" class="btn btn-danger" wire:click="confirmarExclusao">
                    <i class="fas fa-trash me-1"></i>Excluir Permanentemente
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
