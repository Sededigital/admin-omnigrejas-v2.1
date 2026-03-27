<!-- Modal para Cadastro/Edição de Turma -->
<div class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="classModalLabel">
                    <i class="fas fa-chalkboard-teacher text-info me-2"></i>
                    <span>{{ $editingClass ? 'Editar Turma' : 'Cadastrar Nova Turma' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarClass">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab-class" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-class-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic-class" type="button" role="tab">
                                <i class="fas fa-info-circle text-info me-1"></i>Informações da Turma
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-class-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details-class" type="button" role="tab">
                                <i class="fas fa-cog text-info me-1"></i>Configurações Avançadas
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent-class">

                        <!-- Aba: Informações da Turma -->
                        <div class="tab-pane fade show active" id="nav-basic-class" role="tabpanel">
                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('nome') is-invalid @enderror"
                                               wire:model="nome" placeholder="Nome da turma">
                                        <label><i class="fas fa-chalkboard-teacher text-info me-1"></i>Nome *</label>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Código -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('codigo') is-invalid @enderror"
                                               wire:model="codigo" placeholder="Código da turma">
                                        <label><i class="fas fa-hashtag text-info me-1"></i>Código</label>
                                        @error('codigo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data Início -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_inicio') is-invalid @enderror"
                                               wire:model="data_inicio">
                                        <label><i class="fas fa-calendar-plus text-info me-1"></i>Data Início</label>
                                        @error('data_inicio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data Fim -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_fim') is-invalid @enderror"
                                               wire:model="data_fim">
                                        <label><i class="fas fa-calendar-minus text-info me-1"></i>Data Fim</label>
                                        @error('data_fim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dia da Semana -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('dia_semana') is-invalid @enderror" wire:model="dia_semana">
                                            <option value="">Selecione o dia</option>
                                            <option value="0">Domingo</option>
                                            <option value="1">Segunda-feira</option>
                                            <option value="2">Terça-feira</option>
                                            <option value="3">Quarta-feira</option>
                                            <option value="4">Quinta-feira</option>
                                            <option value="5">Sexta-feira</option>
                                            <option value="6">Sábado</option>
                                        </select>
                                        <label><i class="fas fa-calendar-day text-info me-1"></i>Dia da Semana</label>
                                        @error('dia_semana')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Hora Início -->
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                               wire:model="hora_inicio">
                                        <label><i class="fas fa-clock text-info me-1"></i>Hora Início</label>
                                        @error('hora_inicio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Hora Fim -->
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="time" class="form-control @error('hora_fim') is-invalid @enderror"
                                               wire:model="hora_fim">
                                        <label><i class="fas fa-clock text-info me-1"></i>Hora Fim</label>
                                        @error('hora_fim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Local -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('local') is-invalid @enderror"
                                               wire:model="local" placeholder="Local da aula">
                                        <label><i class="fas fa-map-marker-alt text-info me-1"></i>Local</label>
                                        @error('local')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Vagas Máximo -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" min="1" class="form-control @error('vagas_maximo') is-invalid @enderror"
                                               wire:model="vagas_maximo" placeholder="0">
                                        <label><i class="fas fa-users text-info me-1"></i>Vagas Máximo</label>
                                        @error('vagas_maximo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                            <option value="">Selecione o status</option>
                                            <option value="planejado">Planejado</option>
                                            <option value="ativo">Ativo</option>
                                            <option value="concluido">Concluído</option>
                                            <option value="suspenso">Suspenso</option>
                                            <option value="cancelado">Cancelado</option>
                                        </select>
                                        <label><i class="fas fa-toggle-on text-info me-1"></i>Status *</label>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Instrutor -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('instrutor_id') is-invalid @enderror" wire:model="instrutor_id">
                                            <option value="">Selecione o instrutor</option>
                                            @foreach(\App\Models\User::whereHas('membros', function($q) {
                                                $q->where('igreja_id', auth()->user()->getIgrejaId())
                                                  ->where('cargo', '!=', 'membro');
                                            })->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user-tie text-info me-1"></i>Instrutor</label>
                                        @error('instrutor_id')
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
                                            {{ $editingClass ? 'Editando Turma' : 'Nova Turma' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Configurações Avançadas -->
                        <div class="tab-pane fade" id="nav-details-class" role="tabpanel">
                            <div class="row g-3">
                                <!-- Campos avançados podem ser adicionados aqui -->
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Configurações Avançadas</strong><br>
                                        <small>Esta seção pode conter configurações adicionais como notificações, materiais, etc.</small>
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
                <button type="button" class="btn bg-info text-light" wire:click="salvarClass" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarClass">
                        <i class="fas fa-save me-1"></i>{{ $editingClass ? 'Atualizar Turma' : 'Salvar Turma' }}
                    </span>
                    <span wire:loading wire:target="salvarClass">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingClass ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
