<!-- Donation Modal -->
<div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="donationModalLabel">
                    <i class="fas fa-hand-holding-heart me-2"></i>Detalhes da Doação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($selectedDonation)
                <div class="row g-3">
                    <!-- Informações da Doação -->
                    <div class="col-12">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informações da Doação
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Valor:</strong>
                                            <div class="h5 text-success">{{ number_format($selectedDonation->valor, 2, ',', '.') }} AOA</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Data:</strong>
                                            <div>{{ $selectedDonation->created_at->format('d/m/Y H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Gateway:</strong>
                                            <span class="badge bg-secondary">{{ ucfirst($selectedDonation->gateway) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Status:</strong>
                                            <span class="badge bg-{{ $this->getStatusBadgeClass($selectedDonation->status) }}">
                                                {{ $this->getStatusLabel($selectedDonation->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($selectedDonation->referencia)
                                    <div class="col-12">
                                        <div class="mb-2">
                                            <strong>Referência:</strong>
                                            <code>{{ $selectedDonation->referencia }}</code>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Doador -->
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Informações do Doador
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Nome:</strong>
                                            <div>{{ $selectedDonation->doador_nome ?? 'Anônimo' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Email:</strong>
                                            <div>{{ $selectedDonation->doador_email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    @if($selectedDonation->doador_telefone)
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Telefone:</strong>
                                            <div>{{ $selectedDonation->doador_telefone }}</div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($selectedDonation->doador_documento)
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <strong>Documento:</strong>
                                            <div>{{ $selectedDonation->doador_documento }}</div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes Técnicos -->
                    @if($selectedDonation->gateway_data)
                    <div class="col-12">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-cogs me-2"></i>Detalhes Técnicos
                                </h6>
                            </div>
                            <div class="card-body">
                                <pre class="bg-light p-3 rounded small">{{ json_encode($selectedDonation->gateway_data, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Observações -->
                    @if($selectedDonation->observacao)
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-comment me-2"></i>Observações
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $selectedDonation->observacao }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-hand-holding-heart text-muted display-4 mb-3"></i>
                    <div class="text-muted">Selecione uma doação para ver os detalhes</div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                @if($selectedDonation && $selectedDonation->status === 'pendente')
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" wire:click="approveDonation('{{ $selectedDonation->id }}')" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="fas fa-check me-2"></i>Aprovar</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Aprovando...</span>
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="cancelDonation('{{ $selectedDonation->id }}')" wire:loading.attr="disabled">
                        <span wire:loading.remove><i class="fas fa-times me-2"></i>Cancelar</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Cancelando...</span>
                    </button>
                </div>
                @endif
                @if($selectedDonation && $selectedDonation->status === 'aprovado')
                <button type="button" class="btn btn-warning" wire:click="refundDonation('{{ $selectedDonation->id }}')" wire:loading.attr="disabled">
                    <span wire:loading.remove><i class="fas fa-undo me-2"></i>Reembolsar</span>
                    <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Processando...</span>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
