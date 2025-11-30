<div class="modal fade" id="scaleModal" tabindex="-1" aria-labelledby="scaleModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="scaleModalLabel">
                        <i class="fas fa-user-plus text-primary me-2"></i>
                        <span id="modal-title">{{ $editingScale ? 'Editar Escala' : 'Escalar Membro' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveScale">

                        <!-- Seleção do Evento -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('culto_evento_id') is-invalid @enderror"
                                            wire:model="culto_evento_id">
                                        <option value="">Selecione um evento</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}">
                                                {{ $event->titulo }} - {{ $event->data_evento->format('d/m/Y') }} às {{ $event->hora_inicio->format('H:i') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-calendar-alt text-primary me-1"></i>Evento *</label>
                                    @error('culto_evento_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Seleção do Membro -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('membro_id') is-invalid @enderror"
                                            wire:model="membro_id">
                                        <option value="">Selecione um membro</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">
                                                {{ $member->user->name }} - {{ $member->cargo }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-user text-primary me-1"></i>Membro *</label>
                                    @error('membro_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Função -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('funcao') is-invalid @enderror"
                                           wire:model="funcao" placeholder="Ex: Cantor, Instrumentista, Diácono" required>
                                    <label><i class="fas fa-briefcase text-primary me-1"></i>Função *</label>
                                    @error('funcao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Observações -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                              wire:model="observacoes" rows="3"
                                              placeholder="Observações sobre a escala"></textarea>
                                    <label><i class="fas fa-comment text-primary me-1"></i>Observações</label>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Visual -->
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong>
                                    <span class="text-muted">
                                        {{ $editingScale ? 'Editando Escala' : 'Nova Escala' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="saveScale" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveScale">
                            <i class="fas fa-save me-1"></i>{{ $editingScale ? 'Atualizar Escala' : 'Salvar Escala' }}
                        </span>
                        <span wire:loading wire:target="saveScale">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingScale ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
