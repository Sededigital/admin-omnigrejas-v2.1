    {{-- Modal de Confirmação de Status --}}
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title fw-bold" id="statusModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmar Ação
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-question-circle fa-2x text-warning mb-2"></i>
                    </div>
                    <p class="mb-2">
                        Tem certeza que deseja <strong>{{ $statusAction }}</strong> esta assinatura?
                    </p>
                    <small class="text-muted mb-2 d-block">
                        {{ $statusAction === 'desativar' ? 'A assinatura será cancelada e a data de cancelamento será registrada.' : 'A assinatura será reativada.' }}
                    </small>

                    {{-- Input de confirmação --}}
                    <div class="mb-3">
                        <label for="confirmacaoStatus" class="form-label fw-bold">
                            <i class="fas fa-shield-alt me-1"></i>
                            Confirme digitando o nome da igreja:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-church"></i></span>
                            <input type="text"  autocomplete="new-password"
                                   class="form-control"
                                   id="confirmacaoStatus"
                                   wire:model="confirmacaoNome"
                                   autocomplete="off">
                        </div>
                        <small class="text-muted">
                            Digite exatamente: <strong>{{ $selectedIgrejaAssinada->igreja->nome ?? '' }}</strong>
                        </small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" wire:click="confirmStatusChange" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmStatusChange">
                            <i class="fas fa-{{ $statusAction === 'desativar' ? 'ban' : 'check' }} me-1"></i>
                            {{ ucfirst($statusAction) }}
                        </span>
                        <span wire:loading wire:target="confirmStatusChange">
                            <i class="fas fa-spinner fa-spin me-1"></i>Processando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Confirmação de Exclusão --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true"  wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold" id="deleteModalLabel">
                        <i class="fas fa-trash-alt me-2"></i>
                        Confirmar Exclusão
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                    </div>
                    <p class="mb-1">
                        <strong>Atenção!</strong>
                    </p>
                    <p class="mb-2">
                        Esta ação não pode ser desfeita.<br>
                        Todos os dados relacionados serão perdidos.
                    </p>

                    {{-- Input de confirmação --}}
                    <div class="mb-3">
                        <label for="confirmacaoDelete" class="form-label fw-bold">
                            <i class="fas fa-shield-alt me-1"></i>
                            Confirme digitando o nome da igreja:
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-church"></i></span>
                            <input type="text"  autocomplete="new-password"
                                   class="form-control"
                                   id="confirmacaoDelete"
                                   wire:model="confirmacaoNome"

                                   autocomplete="off">
                        </div>
                        <small class="text-muted">
                            Digite exatamente: <strong>{{ $selectedIgrejaAssinada->igreja->nome ?? '' }}</strong>
                        </small>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" wire:click="confirmDelete" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmDelete">
                            <i class="fas fa-trash me-1"></i>Excluir
                        </span>
                        <span wire:loading wire:target="confirmDelete">
                            <i class="fas fa-spinner fa-spin me-1"></i>Excluindo...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
