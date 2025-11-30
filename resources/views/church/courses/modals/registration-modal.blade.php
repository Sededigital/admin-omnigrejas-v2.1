<!-- Modal para Cadastro/Edição de Matrícula -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="registrationModalLabel">
                    <i class="fas fa-user-graduate text-primary me-2"></i>
                    <span>{{ $isEditing ? 'Editar Matrícula' : 'Cadastrar Nova Matrícula' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarRegistration">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab-registration" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-registration-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic-registration" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações da Matrícula
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-registration-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details-registration" type="button" role="tab">
                                <i class="fas fa-comment text-primary me-1"></i>Observações
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent-registration" >

                        <!-- Aba: Informações da Matrícula -->
                        <div class="tab-pane fade show active" id="nav-basic-registration" role="tabpanel">
                            <div class="row g-3">
                                <!-- Membro -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('membro_id') is-invalid @enderror" wire:model="membro_id">
                                            <option value="">Selecione o membro</option>
                                            @foreach($membrosDisponiveis as $membro)
                                                <option value="{{ $membro->id }}">{{ $membro->user->name }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user text-primary me-1"></i>Membro *</label>
                                        @error('membro_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data Matrícula -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_matricula') is-invalid @enderror"
                                               wire:model="data_matricula">
                                        <label><i class="fas fa-calendar-plus text-primary me-1"></i>Data Matrícula</label>
                                        @error('data_matricula')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                            <option value="">Selecione o status</option>
                                            <option value="ativo">Ativo</option>
                                            <option value="concluido">Concluído</option>
                                            <option value="desistente">Desistente</option>
                                            <option value="transferido">Transferido</option>
                                            <option value="suspenso">Suspenso</option>
                                        </select>
                                        <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Apto -->
                                <div class="col-md-3">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="apto" wire:model="apto">
                                        <label class="form-check-label" for="apto">
                                            <i class="fas fa-check-circle text-success me-1"></i>Apto
                                        </label>
                                    </div>
                                </div>

                                <!-- Data Apto -->
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_apto') is-invalid @enderror"
                                               wire:model="data_apto">
                                        <label><i class="fas fa-calendar-check text-primary me-1"></i>Data Apto</label>
                                        @error('data_apto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Certificado Emitido -->
                                <div class="col-md-3">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="certificado_emitido" wire:model="certificado_emitido">
                                        <label class="form-check-label" for="certificado_emitido">
                                            <i class="fas fa-certificate text-primary me-1"></i>Certificado Emitido
                                        </label>
                                    </div>
                                </div>

                                <!-- Data Certificado -->
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_certificado') is-invalid @enderror"
                                               wire:model="data_certificado">
                                        <label><i class="fas fa-calendar-alt text-primary me-1"></i>Data Certificado</label>
                                        @error('data_certificado')
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
                                            {{ $isEditing ? 'Editando Matrícula' : 'Nova Matrícula' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Observações -->
                        <div class="tab-pane fade" id="nav-details-registration" role="tabpanel">
                            <div class="row g-3">
                                <!-- Observações -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                  wire:model="observacoes" rows="4"
                                                  placeholder="Observações sobre a matrícula"></textarea>
                                        <label><i class="fas fa-comment text-primary me-1"></i>Observações</label>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                <button type="button" class="btn btn-primary" wire:click="salvarRegistration" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarRegistration">
                        <i class="fas fa-save me-1"></i>{{ $isEditing ? 'Atualizar Matrícula' : 'Salvar Matrícula' }}
                    </span>
                    <span wire:loading wire:target="salvarRegistration">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $isEditing ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
