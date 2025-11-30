<!-- Resource Modal -->
<div class="modal fade" id="resourceModal" tabindex="-1" aria-labelledby="resourceModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title fw-bold" id="resourceModalLabel">
                    <i class="fas fa-{{ $isEditing ? 'edit' : 'plus' }} text-white me-2"></i>
                    {{ $isEditing ? 'Editar Recurso' : 'Adicionar Recurso' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="salvarRecurso">
                    <div class="text-center mb-4">
                        <i class="fas fa-boxes text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-bold text-center mb-3">
                        {{ $isEditing ? 'Atualize as informações do recurso' : 'Cadastre um novo recurso' }}
                    </h6>
                    <p class="text-muted mb-4">
                        Registre os recursos disponíveis na sua igreja para melhor organização.
                    </p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag text-primary me-1"></i>Nome do Recurso *
                            </label>
                            <input type="text"  autocomplete="new-password"
                                   class="form-control @error('nome') is-invalid @enderror"
                                   wire:model="nome"
                                   placeholder="Ex: Projetor, Sala de Reunião, Bíblia..."
                                   maxlength="255">
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-list text-primary me-1"></i>Tipo *
                            </label>
                            <select class="form-select @error('tipo') is-invalid @enderror"
                                    wire:model="tipo">
                                <option value="">Selecione o tipo...</option>
                                @if(isset($tiposRecursos))
                                    @foreach($tiposRecursos as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left text-primary me-1"></i>Descrição (opcional)
                        </label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                  wire:model="descricao"
                                  rows="3"
                                  placeholder="Descreva o recurso, suas características ou condições de uso..."
                                  maxlength="1000"></textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="disponivel" id="disponivelCheck">
                            <label class="form-check-label fw-semibold" for="disponivelCheck">
                                <i class="fas fa-toggle-on text-primary me-1"></i>Recurso disponível
                            </label>
                        </div>
                        <small class="text-muted">Marque para tornar o recurso disponível para agendamento e uso</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Recursos disponíveis podem ser agendados para eventos e cultos.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="salvarRecurso">
                        <span wire:loading.remove wire:target="salvarRecurso">
                            <i class="fas fa-{{ $isEditing ? 'save' : 'plus' }} me-1"></i>
                            {{ $isEditing ? 'Atualizar' : 'Adicionar' }}
                        </span>
                        <span wire:loading wire:target="salvarRecurso">
                            <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
                        </span>
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:initialized', () => {
    const modalElement = document.getElementById('resourceModal');

    Livewire.on('open-resource-modal', () => {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    Livewire.on('close-resource-modal', () => {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    });

    // Preencher campos quando o modal for mostrado
    modalElement.addEventListener('show.bs.modal', function () {
        // Pequeno delay para garantir que o componente Livewire atualizou
        setTimeout(() => {
            @if($isEditing)
                // Os campos serão preenchidos automaticamente pelo wire:model
                // Não precisamos fazer nada aqui
            @endif
        }, 100);
    });

    // Garantir que o modal seja completamente fechado
    modalElement.addEventListener('hidden.bs.modal', function () {
        // Forçar remoção de classes do Bootstrap que podem permanecer
        modalElement.classList.remove('show');
        modalElement.style.display = 'none';
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    });
});
</script>