<!-- Modal para Visualização de Relatório de Culto -->
<div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true"
 data-bs-backdrop="static" data-bs-keyboard="false"  wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="viewReportModalLabel">
                    <i class="fas fa-eye text-primary me-2"></i>Visualizar Relatório
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-3">
                @if($showViewModal && $relatorioVisualizacao)
                <!-- Header com Título e Status -->
                <div class="text-center mb-4">
                    <h4 class="mb-2">{{ $relatorioVisualizacao->titulo ?: 'Relatório sem título' }}</h4>
                    <div class="d-flex justify-content-center gap-3">
                        <span class="badge bg-{{ $this->getStatusBadgeClass($relatorioVisualizacao->status) }} fs-6">
                            {{ $this->getStatusLabel($relatorioVisualizacao->status) }}
                        </span>
                        <small class="text-muted align-self-center">
                            <i class="fas fa-calendar me-1"></i>{{ $relatorioVisualizacao->data_relatorio->format('d/m/Y') }}
                        </small>
                    </div>
                </div>

                <!-- Informações Principais -->
                <div class="row g-3 mb-3">
                    @if($relatorioVisualizacao->evento || $relatorioVisualizacao->cultoPadrao)
                    <div class="col-12">
                        <div class="border rounded p-2 bg-light">
                            <small class="text-muted d-block">Relacionamento</small>
                            <strong>{{ $relatorioVisualizacao->evento ? $relatorioVisualizacao->evento->titulo : $relatorioVisualizacao->cultoPadrao->titulo }}</strong>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <small class="text-muted d-block">Participantes</small>
                            <strong>{{ $relatorioVisualizacao->numero_participantes ?: 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-2">
                            <small class="text-muted d-block">Valor da Oferta</small>
                            <strong>{{ $relatorioVisualizacao->valor_oferta ? number_format($relatorioVisualizacao->valor_oferta, 2, ',', '.') . ' AOA' : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas (se existirem) -->
                @if($relatorioVisualizacao->numero_visitantes || $relatorioVisualizacao->numero_decisoes || $relatorioVisualizacao->numero_batismos || $relatorioVisualizacao->numero_conversoes)
                <div class="mb-3">
                    <h6 class="border-bottom pb-2 mb-2">Estatísticas do Culto</h6>
                    <div class="row g-2">
                        @if($relatorioVisualizacao->numero_visitantes)
                        <div class="col-6 col-md-3">
                            <div class="text-center border rounded p-2">
                                <div class="h5 mb-0">{{ $relatorioVisualizacao->numero_visitantes }}</div>
                                <small class="text-muted">Visitantes</small>
                            </div>
                        </div>
                        @endif
                        @if($relatorioVisualizacao->numero_decisoes)
                        <div class="col-6 col-md-3">
                            <div class="text-center border rounded p-2">
                                <div class="h5 mb-0">{{ $relatorioVisualizacao->numero_decisoes }}</div>
                                <small class="text-muted">Decisões</small>
                            </div>
                        </div>
                        @endif
                        @if($relatorioVisualizacao->numero_batismos)
                        <div class="col-6 col-md-3">
                            <div class="text-center border rounded p-2">
                                <div class="h5 mb-0">{{ $relatorioVisualizacao->numero_batismos }}</div>
                                <small class="text-muted">Batismos</small>
                            </div>
                        </div>
                        @endif
                        @if($relatorioVisualizacao->numero_conversoes)
                        <div class="col-6 col-md-3">
                            <div class="text-center border rounded p-2">
                                <div class="h5 mb-0">{{ $relatorioVisualizacao->numero_conversoes }}</div>
                                <small class="text-muted">Conversões</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Informações do Pregador (se existirem) -->
                @if($relatorioVisualizacao->pregador || $relatorioVisualizacao->pregador_convidado || $relatorioVisualizacao->tema_culto)
                <div class="mb-3">
                    <h6 class="border-bottom pb-2 mb-2">Informações do Culto</h6>
                    <div class="row g-2">
                        @if($relatorioVisualizacao->pregador)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Pregador</small>
                            <strong>{{ $relatorioVisualizacao->pregador }}</strong>
                        </div>
                        @endif
                        @if($relatorioVisualizacao->pregador_convidado)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Pregador Convidado</small>
                            <strong>{{ $relatorioVisualizacao->pregador_convidado }}</strong>
                        </div>
                        @endif
                        @if($relatorioVisualizacao->tema_culto)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Tema</small>
                            <strong>{{ $relatorioVisualizacao->tema_culto }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Conteúdo do Relatório -->
                <div class="mb-3">
                    <h6 class="border-bottom pb-2 mb-2">Conteúdo do Relatório</h6>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        @if($relatorioVisualizacao->conteudo)
                            {!! nl2br(e($relatorioVisualizacao->conteudo)) !!}
                        @else
                            <span class="text-muted">Conteúdo não disponível</span>
                        @endif
                    </div>
                </div>

                <!-- Observações (se existirem) -->
                @if($relatorioVisualizacao->observacoes)
                <div class="mb-3">
                    <h6 class="border-bottom pb-2 mb-2">Observações</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($relatorioVisualizacao->observacoes)) !!}
                    </div>
                </div>
                @endif

                @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt text-muted display-4 mb-3"></i>
                    <div class="text-muted">Relatório não encontrado</div>
                </div>
                @endif
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                @if($showViewModal && $relatorioVisualizacao)
                <button type="button" class="btn btn-primary" wire:click="exportReport('{{ $relatorioVisualizacao->id }}')" wire:loading.class="btn-loading" title="Imprimir" wire:target="exportingReport">
                    <span wire:loading.remove  wire:target="exportReport('{{ $relatorio->id }}')"><i class="fas fa-print"></i></span>
                    <span wire:loading  wire:target="exportReport('{{ $relatorio->id }}')"><i class="fas fa-spinner fa-spin"></i></span>
                    Imprimir
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
