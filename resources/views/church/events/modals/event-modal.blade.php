<!-- Modal para Cadastro/Edição de Evento -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="eventModalLabel">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    <span id="modal-title">{{ $editingEvent ? 'Editar Evento' : 'Cadastrar Novo Evento' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="saveEvent">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-datetime-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-datetime" type="button" role="tab">
                                <i class="fas fa-clock text-primary me-1"></i>Data e Horário
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-cog text-primary me-1"></i>Detalhes e Status
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel">
                            <div class="row g-3">
                                <!-- Título -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('titulo') is-invalid @enderror"
                                               wire:model="titulo" placeholder="Título do evento" required>
                                        <label><i class="fas fa-heading text-primary me-1"></i>Título do Evento *</label>
                                        @error('titulo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="3"
                                                  placeholder="Descrição detalhada do evento"></textarea>
                                        <label><i class="fas fa-align-left text-primary me-1"></i>Descrição</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Evento -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('tipo') is-invalid @enderror"
                                                wire:model="tipo">
                                            <option value="outro">Outro</option>
                                            <option value="culto">Culto</option>
                                            <option value="reuniao">Reunião</option>
                                            <option value="ensaio">Ensaio</option>
                                            <option value="evento_social">Evento Social</option>
                                        </select>
                                        <label><i class="fas fa-tag text-primary me-1"></i>Tipo de Evento *</label>
                                        @error('tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Local do Evento -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('local_evento') is-invalid @enderror"
                                               wire:model="local_evento" placeholder="Local do evento">
                                        <label><i class="fas fa-map-marker-alt text-primary me-1"></i>Local do Evento</label>
                                        @error('local_evento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Data e Horário -->
                        <div class="tab-pane fade" id="nav-datetime" role="tabpanel">
                            <div class="row g-3">
                                <!-- Data do Evento -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"
                                               class="form-control date_flatpicker @error('data_evento') is-invalid @enderror"
                                               wire:model.defer="data_evento"
                                               placeholder="dd/mm/aaaa"
                                               data-min-date="today"
                                               autocomplete="off"
                                               readonly
                                               style="border: 2px solid #007bff; border-radius: 0.375rem; cursor: pointer;">
                                        <label><i class="fas fa-calendar text-primary me-1"></i>Data do Evento *</label>
                                        @error('data_evento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Hora de Início -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                               wire:model="hora_inicio" required>
                                        <label><i class="fas fa-clock text-primary me-1"></i>Hora de Início *</label>
                                        @error('hora_inicio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Hora de Fim -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control @error('hora_fim') is-invalid @enderror"
                                               wire:model="hora_fim">
                                        <label><i class="fas fa-clock text-primary me-1"></i>Hora de Fim</label>
                                        @error('hora_fim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Detalhes e Status -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel">
                            <div class="row g-3">
                                <!-- Responsável -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('responsavel') is-invalid @enderror"
                                                wire:model="responsavel">
                                            <option value="">Selecione um responsável</option>
                                            @foreach(\App\Models\User::where('is_active', true)
                                            ->whereHas('membros', function($membro){
                                                $membro->where('igreja_id', Auth::user()->getIgrejaId());
                                            })
                                            ->orderBy('name')->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user text-primary me-1"></i>Responsável</label>
                                        @error('responsavel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('status') is-invalid @enderror"
                                                wire:model="status">
                                            <option value="agendado">Agendado</option>
                                            <option value="realizado">Realizado</option>
                                            <option value="cancelado">Cancelado</option>
                                        </select>
                                        <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                        @error('status')
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
                                            {{ $editingEvent ? 'Editando Evento' : 'Novo Evento' }}
                                        </span>
                                    </div>
                                </div>
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
                <button type="button" class="btn btn-primary" wire:click="saveEvent" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveEvent">
                        <i class="fas fa-save me-1"></i>{{ $editingEvent ? 'Atualizar Evento' : 'Salvar Evento' }}
                    </span>
                    <span wire:loading wire:target="saveEvent">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingEvent ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
