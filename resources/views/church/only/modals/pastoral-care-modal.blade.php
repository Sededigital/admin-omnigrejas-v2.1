<!-- Pastoral Care Modal -->
<div class="modal fade" id="pastoralCareModal" tabindex="-1" aria-labelledby="pastoralCareModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title fw-bold" id="pastoralCareModalLabel">
                    <i class="fas fa-{{ $isEditing ? 'edit' : 'plus' }} text-white me-2"></i>
                    {{ $isEditing ? 'Editar Atendimento Pastoral' : 'Registrar Atendimento Pastoral' }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="salvarAtendimento">
                    <div class="text-center mb-4">
                        <i class="fas fa-praying-hands text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="fw-bold text-center mb-3">
                        {{ $isEditing ? 'Atualize as informações do atendimento' : 'Registre um novo atendimento pastoral' }}
                    </h6>
                    <p class="text-muted mb-4">
                        Documente os cuidados pastorais prestados aos membros da igreja.
                    </p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user text-primary me-1"></i>Membro *
                            </label>
                            <select class="form-select @error('membro_id') is-invalid @enderror"
                                    wire:model="membro_id">
                                <option value="">Selecione o membro...</option>
                                @if(isset($membrosDisponiveis))
                                    @foreach($membrosDisponiveis as $membro)
                                        <option value="{{ $membro['id'] }}">{{ $membro['nome'] }} ({{ ucfirst($membro['cargo']) }})</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('membro_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user-tie text-primary me-1"></i>Pastor *
                            </label>
                            <select class="form-select @error('pastor_id') is-invalid @enderror"
                                    wire:model="pastor_id">
                                <option value="">Selecione o pastor...</option>
                                @if(isset($pastoresDisponiveis))
                                    @foreach($pastoresDisponiveis as $pastor)
                                        <option value="{{ $pastor['id'] }}">{{ $pastor['nome'] }} ({{ ucfirst($pastor['cargo']) }})</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('pastor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-list text-primary me-1"></i>Tipo de Atendimento *
                            </label>
                            <select class="form-select @error('tipo') is-invalid @enderror"
                                    wire:model="tipo">
                                <option value="">Selecione o tipo...</option>
                                @if(isset($tiposAtendimento))
                                    @foreach($tiposAtendimento as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar text-primary me-1"></i>Data do Atendimento
                            </label>
                            <input type="date"
                                   class="form-control @error('data_atendimento') is-invalid @enderror"
                                   wire:model="data_atendimento">
                            @error('data_atendimento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left text-primary me-1"></i>Descrição do Atendimento (opcional)
                        </label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                  wire:model="descricao"
                                  rows="4"
                                  placeholder="Descreva o atendimento, orientações dadas, orações realizadas, etc..."
                                  maxlength="1000"></textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nota:</strong> Registre todos os detalhes importantes do atendimento pastoral para acompanhamento futuro.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="salvarAtendimento">
                        <span wire:loading.remove wire:target="salvarAtendimento">
                            <i class="fas fa-{{ $isEditing ? 'save' : 'plus' }} me-1"></i>
                            {{ $isEditing ? 'Atualizar' : 'Registrar' }}
                        </span>
                        <span wire:loading wire:target="salvarAtendimento">
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
    const modalElement = document.getElementById('pastoralCareModal');

    Livewire.on('open-pastoral-care-modal', () => {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });

    Livewire.on('close-pastoral-care-modal', () => {
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    });

    // Preencher campos quando o modal for mostrado
    modalElement.addEventListener('show.bs.modal', function () {
        // Pequeno delay para garantir que o componente Livewire atualizou
        setTimeout(() => {
            // Usar wire:model para sincronizar automaticamente
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