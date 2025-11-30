<!-- Modal de Cancelamento de Reunião -->
<div class="modal fade" id="cancelMeetingModal" tabindex="-1" aria-labelledby="cancelMeetingModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-danger text-white border-bottom">
                <h5 class="modal-title fw-bold" id="cancelMeetingModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Cancelar Reunião
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar" wire:click="fecharModalCancelamento"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                @if($reuniaoParaCancelar)
                    <div class="alert alert-danger border-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong> Esta ação não pode ser desfeita. Você está prestes a cancelar a reunião:
                    </div>

                    <div class="card border-warning mb-4">
                        <div class="card-body">
                            <h6 class="card-title text-warning mb-2">
                                <i class="fas fa-calendar-times me-2"></i>{{ $reuniaoParaCancelar->titulo }}
                            </h6>
                            <p class="card-text mb-1">
                                <i class="fas fa-calendar me-2"></i>{{ $reuniaoParaCancelar->data_agendamento->format('d/m/Y') }} às {{ $reuniaoParaCancelar->hora_inicio->format('H:i') }}
                            </p>
                            <p class="card-text mb-0">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $reuniaoParaCancelar->local ?? 'Local não informado' }}
                            </p>
                        </div>
                    </div>

                    <form wire:submit.prevent="confirmarCancelamentoReuniao">
                        <!-- Senha -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-lock text-danger me-1"></i>Sua Senha *
                            </label>
                            <input type="password"  autocomplete="new-password"  class="form-control @error('senhaCancelamento') is-invalid @enderror"
                                   wire:model="senhaCancelamento" placeholder="Digite sua senha" required>
                            @error('senhaCancelamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirmação Omnigrejas -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-exclamation-triangle text-danger me-1"></i>Digite "Omnigrejas" *
                            </label>
                            <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control @error('confirmacaoOmnigrejas') is-invalid @enderror"
                                   wire:model="confirmacaoOmnigrejas" placeholder="Digite exatamente: Omnigrejas" required>
                            @error('confirmacaoOmnigrejas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Digite exatamente "Omnigrejas" (sem espaços extras, maiúsculas/minúsculas corretas)
                            </div>
                        </div>

                        <!-- Confirmação do Título -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-heading text-danger me-1"></i>Digite o Título da Reunião *
                            </label>
                            <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control @error('confirmacaoTitulo') is-invalid @enderror"
                                   wire:model="confirmacaoTitulo"
                                   placeholder="Digite exatamente o título: {{ $reuniaoParaCancelar->titulo }}" required>
                            @error('confirmacaoTitulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Digite exatamente: <strong>"{{ $reuniaoParaCancelar->titulo }}"</strong> (sem espaços extras)
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Importante:</strong> Esta ação cancelará permanentemente a reunião e notificará todos os participantes.
                        </div>
                    </form>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Nenhuma reunião selecionada</h5>
                        <p class="text-muted">Selecione uma reunião para cancelar.</p>
                    </div>
                @endif
            </div>

            <!-- Footer do Modal -->
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="fecharModalCancelamento">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                @if($reuniaoParaCancelar)
                    <button type="button" class="btn btn-danger" wire:click="confirmarCancelamentoReuniao" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmarCancelamentoReuniao">
                            <i class="fas fa-times-circle me-1"></i>Confirmar Cancelamento
                        </span>
                        <span wire:loading wire:target="confirmarCancelamentoReuniao">
                            <i class="fas fa-spinner fa-spin me-1"></i>Cancelando...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Abrir modal de cancelamento
    Livewire.on('open-cancel-meeting-modal', () => {
        const cancelModal = document.getElementById('cancelMeetingModal');
        if (cancelModal) {
            const bsModal = new bootstrap.Modal(cancelModal, {
                backdrop: 'static',
                keyboard: false
            });
            bsModal.show();
        }
    });

    // Fechar modal de cancelamento
    Livewire.on('close-cancel-meeting-modal', () => {
        const cancelModal = document.getElementById('cancelMeetingModal');
        if (cancelModal) {
            const bsModal = bootstrap.Modal.getInstance(cancelModal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    });
});
</script>
