<div class="modal fade" id="meetingModal" tabindex="-1" aria-labelledby="meetingModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-info text-light text-white border-bottom">
                <h5 class="modal-title fw-bold" id="meetingModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>
                    <span>{{ $isEditing ? 'Editar Reunião' : 'Agendar Nova Reunião' }}</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarReuniao">

                    <!-- Navegação por Abas -->
                    <nav class="mb-4" wire:ignore>
                        <div class="nav nav-tabs border-bottom-0" id="meeting-nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold"
                                    id="meeting-nav-basic-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#meeting-nav-basic"
                                    type="button" role="tab">
                                <i class="fas fa-info-circle text-info me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold"
                                    id="meeting-nav-details-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#meeting-nav-details"
                                    type="button" role="tab">
                                <i class="fas fa-cogs text-info me-1"></i>Detalhes
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="meeting-nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="meeting-nav-basic" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Título da Reunião -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control @error('titulo') is-invalid @enderror"
                                               wire:model="titulo" placeholder="Digite o título da reunião" required>
                                        <label><i class="fas fa-heading text-info me-1"></i>Título da Reunião *</label>
                                        @error('titulo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data e Hora -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password"
                                               class="form-control date_flatpicker @error('data_agendamento') is-invalid @enderror"
                                               wire:model.defer="data_agendamento"
                                               placeholder="dd/mm/aaaa"
                                               data-min-date="{{ date('Y-m-d') }}"
                                               data-max-date=""
                                               autocomplete="off"
                                               readonly
                                               style="border: 2px solid #007bff; border-radius: 0.375rem; cursor: pointer;">
                                        <label><i class="fas fa-calendar text-info me-1"></i>Data *</label>
                                        @error('data_agendamento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                               wire:model="hora_inicio" required>
                                        <label><i class="fas fa-clock text-info me-1"></i>Início *</label>
                                        @error('hora_inicio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control @error('hora_fim') is-invalid @enderror"
                                               wire:model="hora_fim">
                                        <label><i class="fas fa-clock text-info me-1"></i>Fim</label>
                                        @error('hora_fim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Reunião -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('tipo') is-invalid @enderror"
                                                wire:model="tipo" required>
                                            <option value="reuniao">Reunião Geral</option>
                                            <option value="consulta">Consulta</option>
                                            <option value="acompanhamento">Acompanhamento</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                        <label><i class="fas fa-tag text-info me-1"></i>Tipo *</label>
                                        @error('tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Modalidade -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('modalidade') is-invalid @enderror"
                                                wire:model="modalidade" required>
                                            <option value="presencial">Presencial</option>
                                            <option value="online">Online</option>
                                            <option value="hibrido">Híbrido</option>
                                        </select>
                                        <label><i class="fas fa-globe text-info me-1"></i>Modalidade *</label>
                                        @error('modalidade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Local (condicional) -->
                                <div class="col-12" wire:ignore>
                                    <div class="form-floating mb-3" id="localField" style="display: {{ in_array($modalidade, ['presencial', 'hibrido']) ? 'block' : 'none' }}">
                                        <input type="text"  autocomplete="new-password"  autocomplete="new-password"  class="form-control @error('local') is-invalid @enderror"
                                               wire:model="local" placeholder="Digite o local da reunião">
                                        <label><i class="fas fa-map-marker-alt text-info me-1"></i>Local</label>
                                        @error('local')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Link da Reunião (condicional) -->
                                <div class="col-12" wire:ignore>
                                    <div class="form-floating mb-3" id="linkField" style="display: {{ in_array($modalidade, ['online', 'hibrido']) ? 'block' : 'none' }}">
                                        <input type="url" class="form-control @error('link_reuniao') is-invalid @enderror"
                                               wire:model="link_reuniao" placeholder="https://meet.google.com/...">
                                        <label><i class="fas fa-link text-info me-1"></i>Link da Reunião</label>
                                        @error('link_reuniao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Detalhes -->
                        <div class="tab-pane fade" id="meeting-nav-details" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="4"
                                                  placeholder="Descreva os objetivos e tópicos da reunião"></textarea>
                                        <label><i class="fas fa-align-left text-info me-1"></i>Descrição</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Aliança (obrigatório) -->
                                <div class="col-12" wire:ignore.self>
                                    <div class="form-floating mb-3" >
                                        <select class="form-select @error('aliancaSelecionada') is-invalid @enderror"
                                                wire:model.live="aliancaSelecionada" required>
                                            <option value="">Selecione uma aliança *</option>
                                            @if(isset($aliancasDisponiveis) && !empty($aliancasDisponiveis))
                                                @foreach($aliancasDisponiveis as $alianca)
                                                    <option value="{{ $alianca['id'] }}">
                                                        {{ $alianca['nome'] }}
                                                        @if($alianca['sigla']) ({{ $alianca['sigla'] }}) @endif
                                                        @if(isset($alianca['tipo']) && $alianca['tipo'] === 'criada')
                                                            <span class="text-info">★</span>
                                                        @else
                                                            <span class="text-muted">●</span>
                                                        @endif
                                                    </option>
                                                @endforeach
                                            @else
                                                <option value="" disabled>Nenhuma aliança disponível</option>
                                            @endif
                                        </select>
                                        <label><i class="fas fa-handshake text-info me-1"></i>Aliança *</label>
                                        @error('aliancaSelecionada')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <small class="text-muted">Selecione uma aliança para carregar líderes e membros específicos</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Responsável -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('responsavel_id') is-invalid @enderror"
                                                wire:model="responsavel_id">
                                            <option value="">Selecione um responsável</option>
                                            @if(isset($lideresDisponiveis) && !empty($lideresDisponiveis))
                                                @foreach($lideresDisponiveis as $lider)
                                                    @if($lider->membro && $lider->membro->user)
                                                        <option value="{{ $lider->membro->user_id }}">
                                                            {{ $lider->membro->user->name }} ({{ ucfirst($lider->cargo_na_alianca) }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <label><i class="fas fa-user-tie text-info me-1"></i>Responsável</label>
                                        @error('responsavel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Convidado Especial-->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('convidado_id') is-invalid @enderror"
                                                wire:model="convidado_id">
                                            <option value="">Selecione um convidado (opcional)</option>
                                            @if(isset($membrosDisponiveis) && !empty($membrosDisponiveis))
                                                @foreach($membrosDisponiveis as $membro)
                                                    <option value="{{ $membro->user_id }}">
                                                        {{ $membro->user->name }} ({{ ucfirst($membro->cargo) }})
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <label><i class="fas fa-user text-info me-1"></i>Convidado Especial</label>
                                        @error('convidado_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <small class="text-muted">Campo opcional - deixe vazio para reunião geral</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observações -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                  wire:model="observacoes" rows="3"
                                                  placeholder="Observações adicionais"></textarea>
                                        <label><i class="fas fa-sticky-note text-info me-1"></i>Observações</label>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <strong>Status:</strong>
                                        <span class="text-muted">
                                            {{ $isEditing ? 'Editando Reunião' : 'Nova Reunião' }}
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
                <button type="button" class="btn bg-info text-light" wire:click="salvarReuniao" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarReuniao">
                        <i class="fas fa-save me-1"></i>{{ $isEditing ? 'Atualizar Reunião' : 'Agendar Reunião' }}
                    </span>
                    <span wire:loading wire:target="salvarReuniao">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $isEditing ? 'Atualizando...' : 'Agendando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('livewire:init', () => {
    // Atualizar campos condicionais quando modalidade mudar
    Livewire.on('modalidadeChanged', (modalidade) => {
        const localField = document.getElementById('localField');
        const linkField = document.getElementById('linkField');

        if (localField && linkField) {
            if (modalidade === 'presencial' || modalidade === 'hibrido') {
                localField.style.display = 'block';
            } else {
                localField.style.display = 'none';
            }

            if (modalidade === 'online' || modalidade === 'hibrido') {
                linkField.style.display = 'block';
            } else {
                linkField.style.display = 'none';
            }
        }
    });
});

</script>

<!-- Estilos para Flatpickr - Forçar sempre desktop -->
<style>
    /* Forçar Flatpickr sempre visível */
    .flatpickr-calendar {
        z-index: 10000 !important;
        display: none;
    }

    .flatpickr-calendar.open {
        display: block !important;
    }

    /* Overlay para mobile */
    .flatpickr-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(0,0,0,0.5) !important;
        z-index: 9999 !important;
    }

    /* Responsivo para telas pequenas */
    @media (max-width: 768px) {
        .flatpickr-calendar {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: 320px !important;
        max-width: 90vw !important;
    }

    .flatpickr-calendar .flatpickr-month {
        height: 40px !important;
    }

    .flatpickr-calendar .flatpickr-day {
        height: 35px !important;
        line-height: 35px !important;
    }
    }

    /* Melhorar inputs de data */
    .date_flatpicker {
        cursor: pointer !important;
        background-color: white !important;
    }

    .date_flatpicker:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
</style>

