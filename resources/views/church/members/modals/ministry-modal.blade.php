<!-- Modal para Adicionar Ministério ao Membro -->
<div wire:ignore.self class="modal fade" id="ministryModal" tabindex="-1" aria-labelledby="ministryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ministryModalLabel">
                    <i class="fas fa-church text-success me-2"></i>Adicionar Ministério
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($selectedMemberForMinistry)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Membro Selecionado</label>
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-primary text-white me-3">
                                        {{ strtoupper(substr($selectedMemberForMinistry->user->name ?? 'N', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $selectedMemberForMinistry->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $selectedMemberForMinistry->user->email ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Selecionar Ministério</label>
                        <select class="form-select" wire:model="selectedMinistryToAdd">
                            <option value="">Selecione um ministério...</option>
                            @foreach($availableMinistries as $ministerio)
                                <option value="{{ $ministerio->id }}">{{ $ministerio->nome }}</option>
                            @endforeach
                        </select>
                        @error('selectedMinistryToAdd')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($availableMinistries->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Este membro já faz parte de todos os ministérios disponíveis/Nenhum ministério
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users text-muted display-4 mb-3"></i>
                        <div class="text-muted">Nenhum membro selecionado</div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                @if($selectedMemberForMinistry && $availableMinistries->isNotEmpty())
                    <button type="button" class="btn btn-success" wire:click="addMemberToMinistry" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-plus me-1"></i>Adicionar ao Ministério
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i>Adicionando...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
