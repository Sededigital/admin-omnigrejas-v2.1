<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmacaoModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirmar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0" id="confirmacaoMensagem">Tem certeza que deseja executar esta ação?</p>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary btn-sm w-100" data-bs-dismiss="modal" wire:click="cancelarAcao">
                            <i class="fas fa-times me-1"></i>Não
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-danger btn-sm w-100" id="btnConfirmar" wire:click="confirmarAcao" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-check me-1"></i>Sim
                            </span>
                            <span wire:loading>
                                <i... class="fas fa-spinner fa-spin me-1"></i...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    let confirmacaoModal = null;

    Livewire.on('open-confirmacao-modal', (mensagem) => {
        // Atualizar mensagem se fornecida
        if (mensagem) {
            document.getElementById('confirmacaoMensagem').textContent = mensagem;
        }

        // Criar instância do modal se não existir
        if (!confirmacaoModal) {
            confirmacaoModal = new bootstrap.Modal(document.getElementById('confirmacaoModal'), {
                backdrop: 'static', // Impede fechar clicando no backdrop durante processamento
                keyboard: false     // Impede fechar com ESC durante processamento
            });
        }

        confirmacaoModal.show();
    });

    Livewire.on('close-confirmacao-modal', () => {
        if (confirmacaoModal) {
            // Limpar imediatamente o backdrop e focus
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }

            // Forçar fechamento do modal
            confirmacaoModal.hide();

            // Pequeno delay apenas para garantir que tudo seja limpo
            setTimeout(() => {
                // Resetar modal
                confirmacaoModal = null;
            }, 100);
        }
    });

    // Garantir que o modal seja limpo quando o componente Livewire for atualizado
    Livewire.on('component-updated', () => {
        if (confirmacaoModal && !document.getElementById('confirmacaoModal').classList.contains('show')) {
            confirmacaoModal = null;
        }
    });
});
</script>

