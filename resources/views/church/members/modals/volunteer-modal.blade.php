<!-- Volunteer Modal -->
<div class="modal fade" id="volunteerModal" tabindex="-1" aria-labelledby="volunteerModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-light">
                <h5 class="modal-title fw-bold" id="volunteerModalLabel">
                    <i class="fas fa-{{ $isEditing ? 'edit' : 'plus' }} text-white me-2"></i>
                    {{ $isEditing ? 'Editar Voluntário' : 'Adicionar Voluntário' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="salvarVoluntario">
                    <div class="text-center mb-4">
                        <i class="fas fa-hands-helping text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-bold text-center mb-3">
                        {{ $isEditing ? 'Atualize as informações do voluntário' : 'Cadastre um novo voluntário' }}
                    </h6>
                    <p class="text-muted mb-4">
                        Preencha as informações do voluntário para ajudar na organização dos serviços da igreja.
                    </p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-info me-1"></i>Membro *
                            </label>
                            <select class="form-select @error('membro_id') is-invalid @enderror"
                                    wire:model="membro_id"
                                    {{ $isEditing ? 'disabled' : '' }}>
                                <option value="">Selecione um membro...</option>
                                @if(isset($membrosDisponiveis) && !empty($membrosDisponiveis))
                                    @foreach($membrosDisponiveis as $membro)
                                        <option value="{{ $membro['id'] }}">
                                            {{ $membro['nome'] }} ({{ ucfirst($membro['cargo']) }})
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>Nenhum membro disponível</option>
                                @endif
                                @if($isEditing && $voluntarioSelecionado)
                                    <option value="{{ $voluntarioSelecionado->membro_id }}" selected>
                                        {{ $voluntarioSelecionado->membro->user->name }} ({{ ucfirst($voluntarioSelecionado->membro->cargo) }})
                                    </option>
                                @endif
                            </select>
                            @error('membro_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-star text-info me-1"></i>Área de Interesse *
                            </label>
                            <input type="text"  autocomplete="new-password"
                                   class="form-control @error('area_interesse') is-invalid @enderror"
                                   wire:model="area_interesse"
                                   placeholder="Ex: Música, Ensino, Cuidados infantis..."
                                   maxlength="255">
                            @error('area_interesse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-clock text-info me-1"></i>Disponibilidade *
                        </label>
                        <textarea class="form-control @error('disponibilidade') is-invalid @enderror"
                                  wire:model="disponibilidade"
                                  rows="3"
                                  placeholder="Descreva os horários e dias disponíveis para voluntariado..."
                                  maxlength="255"></textarea>
                        @error('disponibilidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="ativo" id="ativoCheck">
                            <label class="form-check-label fw-semibold" for="ativoCheck">
                                <i class="fas fa-toggle-on text-info me-1"></i>Voluntário ativo
                            </label>
                        </div>
                        <small class="text-muted">Marque para ativar o voluntário e permitir sua participação nos serviços</small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Os voluntários ativos podem ser escalados para os serviços da igreja.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn bg-info text-light"
                            wire:loading.attr="disabled"
                            wire:target="salvarVoluntario">
                        <span wire:loading.remove wire:target="salvarVoluntario">
                            <i class="fas fa-{{ $isEditing ? 'save' : 'plus' }} me-1"></i>
                            {{ $isEditing ? 'Atualizar' : 'Adicionar' }}
                        </span>
                        <span wire:loading wire:target="salvarVoluntario">
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
    const modalElement = document.getElementById('volunteerModal');

    Livewire.on('open-volunteer-modal', () => {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    Livewire.on('close-volunteer-modal', () => {
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