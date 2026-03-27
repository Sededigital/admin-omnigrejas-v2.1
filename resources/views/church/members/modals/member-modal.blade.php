<!-- Modal para Cadastro/Edição de Membro -->
<div class="modal fade" id="memberModal" tabindex="-1" aria-labelledby="memberModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="memberModalLabel">
                    <i class="fas fa-user text-info me-2"></i>
                    <span id="modal-title">{{ $editingMember ? 'Editar Membro' : 'Cadastrar Novo Membro' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="saveMember">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-info me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-comment text-info me-1"></i>Observações
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel">
                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('name') is-invalid @enderror"
                                               wire:model="name" placeholder="Nome completo">
                                        <label><i class="fas fa-user text-info me-1"></i>Nome *</label>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email"  autocomplete="new-password" class="form-control @error('email') is-invalid @enderror"
                                               wire:model="email" placeholder="email@exemplo.com">
                                        <label><i class="fas fa-envelope text-info me-1"></i>Email *</label>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Telefone -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               wire:model="phone" placeholder="+244 900 000 000">
                                        <label><i class="fas fa-phone text-info me-1"></i>Telefone</label>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data de Nascimento -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"
                                               class="form-control date_flatpicker @error('data_nascimento') is-invalid @enderror"
                                               wire:model.defer="data_nascimento"
                                               placeholder="dd/mm/aaaa"
                                               data-min-date="1960-01-01"
                                               data-max-date="{{ date('Y') }}-12-31"
                                               autocomplete="off"
                                               readonly
                                               style="border: 2px solid #007bff; border-radius: 0.375rem; cursor: pointer;">
                                        <label><i class="fas fa-birthday-cake text-info me-1"></i>Data de Nascimento</label>
                                        @error('data_nascimento')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Gênero -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-venus-mars text-info me-1"></i>Gênero</label>
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="genero" id="genero-masculino" value="masculino" wire:model="genero">
                                                    <label class="form-check-label" for="genero-masculino">
                                                        <i class="fas fa-mars text-info me-1"></i>Masculino
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="genero" id="genero-feminino" value="feminino" wire:model="genero">
                                                    <label class="form-check-label" for="genero-feminino">
                                                        <i class="fas fa-venus text-danger me-1"></i>Feminino
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('genero')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Cargo -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('cargo') is-invalid @enderror"
                                                wire:model="cargo">
                                            <option value="">Selecione o cargo</option>
                                            <option value="membro">Membro</option>
                                            <option value="diacono">Diácono</option>
                                            <option value="obreiro">Obreiro</option>
                                            <option value="ministro">Ministro</option>
                                            <option value="pastor">Pastor</option>
                                        </select>
                                        <label><i class="fas fa-user-tag text-info me-1"></i>Cargo *</label>
                                        @error('cargo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Endereço -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('endereco') is-invalid @enderror"
                                                  wire:model="endereco" rows="2" placeholder="Endereço completo"></textarea>
                                        <label><i class="fas fa-map-marker-alt text-info me-1"></i>Endereço</label>
                                        @error('endereco')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data de Entrada -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"
                                               class="form-control date_flatpicker @error('data_entrada') is-invalid @enderror"
                                               wire:model.defer="data_entrada"
                                               placeholder="dd/mm/aaaa"
                                               data-min-date="1990-01-01"
                                               data-max-date="{{ date('Y') }}-12-31"
                                               autocomplete="off"
                                               readonly
                                               style="cursor: pointer;">
                                        <label><i class="fas fa-calendar-plus text-info me-1"></i>Data de Entrada *</label>
                                        @error('data_entrada')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-toggle-on text-info me-1"></i>Status *</label>
                                        <div class="row g-2">
                                            <div class="col-6 col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status-ativo" value="ativo" wire:model="status">
                                                    <label class="form-check-label" for="status-ativo">
                                                        <i class="fas fa-check-circle text-success me-1"></i>Ativo
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status-inativo" value="inativo" wire:model="status">
                                                    <label class="form-check-label" for="status-inativo">
                                                        <i class="fas fa-times-circle text-danger me-1"></i>Inativo
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status-falecido" value="falecido" wire:model="status">
                                                    <label class="form-check-label" for="status-falecido">
                                                        <i class="fas fa-cross text-dark me-1"></i>Falecido
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6 col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status-transferido" value="transferido" wire:model="status">
                                                    <label class="form-check-label" for="status-transferido">
                                                        <i class="fas fa-exchange-alt text-warning me-1"></i>Transferido
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Aba: Observações -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel">
                            <div class="row g-3">
                                <!-- Observações -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                  wire:model="observacoes" rows="3"
                                                  placeholder="Observações sobre o membro"></textarea>
                                        <label><i class="fas fa-comment text-info me-1"></i>Observações</label>
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
                                            {{ $editingMember ? 'Editando Membro' : 'Novo Membro' }}
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
                <button type="button" class="btn bg-info text-light" wire:click="saveMember" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveMember">
                        <i class="fas fa-save me-1"></i>{{ $editingMember ? 'Atualizar Membro' : 'Salvar Membro' }}
                    </span>
                    <span wire:loading wire:target="saveMember">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingMember ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>


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
