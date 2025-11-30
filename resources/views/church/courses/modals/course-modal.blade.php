<!-- Modal para Cadastro/Edição de Curso -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="courseModalLabel">
                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                    <span id="modal-title">{{ $editingCourse ? 'Editar Curso' : 'Cadastrar Novo Curso' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarCourse">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-cog text-primary me-1"></i>Configurações
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-team-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-team" type="button" role="tab">
                                <i class="fas fa-users text-primary me-1"></i>Equipe
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-advanced-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-advanced" type="button" role="tab">
                                <i class="fas fa-sliders-h text-primary me-1"></i>Avançado
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel">
                            <div class="row g-3">
                                <!-- Nome -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('nome') is-invalid @enderror"
                                               wire:model="nome" placeholder="Nome do curso" id="nome-input">
                                        <label><i class="fas fa-tag text-primary me-1"></i>Nome do Curso *</label>
                                        @error('nome')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('tipo') is-invalid @enderror" wire:model="tipo" id="tipo-select">
                                            <option value="">Selecione o tipo</option>
                                            <option value="escola_dominical">Escola Dominical</option>
                                            <option value="preparacao_batismo">Preparação para Batismo</option>
                                            <option value="curso_membros">Curso de Membros</option>
                                            <option value="lideranca">Liderança</option>
                                            <option value="ministerial">Ministerial</option>
                                            <option value="casais">Casais</option>
                                            <option value="jovens">Jovens</option>
                                            <option value="outro">Outro</option>
                                        </select>
                                        <label><i class="fas fa-list text-primary me-1"></i>Tipo *</label>
                                        @error('tipo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                            <option value="planejado">Planejado</option>
                                            <option value="ativo">Ativo</option>
                                            <option value="concluido">Concluído</option>
                                            <option value="suspenso">Suspenso</option>
                                            <option value="cancelado">Cancelado</option>
                                        </select>
                                        <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="3" placeholder="Descrição detalhada do curso"></textarea>
                                        <label><i class="fas fa-align-left text-primary me-1"></i>Descrição</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Objetivo -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('objetivo') is-invalid @enderror"
                                                  wire:model="objetivo" rows="2" placeholder="Objetivos do curso"></textarea>
                                        <label><i class="fas fa-bullseye text-primary me-1"></i>Objetivos</label>
                                        @error('objetivo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Configurações -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel">
                            <div class="row g-3">
                                <!-- Carga Horária e Duração -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" min="1" class="form-control @error('carga_horaria_total') is-invalid @enderror"
                                               wire:model="carga_horaria_total" placeholder="0">
                                        <label><i class="fas fa-clock text-primary me-1"></i>Carga Horária Total (horas)</label>
                                        @error('carga_horaria_total')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" min="1" class="form-control @error('duracao_semanas') is-invalid @enderror"
                                               wire:model="duracao_semanas" placeholder="0">
                                        <label><i class="fas fa-calendar-alt text-primary me-1"></i>Duração (semanas)</label>
                                        @error('duracao_semanas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Datas -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_inicio') is-invalid @enderror"
                                               wire:model="data_inicio">
                                        <label><i class="fas fa-calendar-plus text-primary me-1"></i>Data de Início</label>
                                        @error('data_inicio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_fim') is-invalid @enderror"
                                               wire:model="data_fim">
                                        <label><i class="fas fa-calendar-check text-primary me-1"></i>Data de Fim</label>
                                        @error('data_fim')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Vagas Máximo -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" min="1" class="form-control @error('vagas_maximo') is-invalid @enderror"
                                               wire:model="vagas_maximo" placeholder="0">
                                        <label><i class="fas fa-users text-primary me-1"></i>Vagas Máximo</label>
                                        @error('vagas_maximo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Frequência Mínima -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" min="0" max="100" class="form-control @error('frequencia_minima') is-invalid @enderror"
                                               wire:model="frequencia_minima" placeholder="75">
                                        <label><i class="fas fa-percentage text-primary me-1"></i>Frequência Mínima (%)</label>
                                        @error('frequencia_minima')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Equipe -->
                        <div class="tab-pane fade" id="nav-team" role="tabpanel">
                            <div class="row g-3">
                                <!-- Instrutor Principal -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('instrutor_principal') is-invalid @enderror" wire:model="instrutor_principal">
                                            <option value="">Selecione o instrutor</option>
                                            @foreach(\App\Models\User::whereHas('membros', function($q) {
                                                $q->where('igreja_id', auth()->user()->getIgrejaId())
                                                  ->where('cargo', '!=', 'membro');
                                            })->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user-graduate text-primary me-1"></i>Instrutor Principal</label>
                                        @error('instrutor_principal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Coordenador -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('coordenador') is-invalid @enderror" wire:model="coordenador">
                                            <option value="">Selecione o coordenador</option>
                                            @foreach(\App\Models\User::whereHas('membros', function($q) {
                                                $q->where('igreja_id', auth()->user()->getIgrejaId())
                                                  ->where('cargo', '!=', 'membro');
                                            })->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user-tie text-primary me-1"></i>Coordenador</label>
                                        @error('coordenador')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Avançado -->
                        <div class="tab-pane fade" id="nav-advanced" role="tabpanel">
                            <div class="row g-3">
                                <!-- Certificado Obrigatório -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-certificate text-primary me-1"></i>Certificado Obrigatório</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="certificado_obrigatorio" id="cert-sim" value="1" wire:model="certificado_obrigatorio">
                                                    <label class="form-check-label" for="cert-sim">
                                                        <i class="fas fa-check-circle text-success me-1"></i>Sim
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="certificado_obrigatorio" id="cert-nao" value="0" wire:model="certificado_obrigatorio">
                                                    <label class="form-check-label" for="cert-nao">
                                                        <i class="fas fa-times-circle text-danger me-1"></i>Não
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Visual -->
                                <div class="col-md-6">
                                    <div class="alert alert-light border h-100 d-flex align-items-center">
                                        <div>
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Status:</strong><br>
                                            <span class="text-muted">
                                                {{ $editingCourse ? 'Editando Curso' : 'Novo Curso' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Configurações Avançadas -->
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Configurações Avançadas</strong><br>
                                        <small>Esta seção pode conter configurações adicionais como permissões, notificações, etc.</small>
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
                <button type="button" class="btn btn-primary" wire:click="salvarCourse" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarCourse">
                        <i class="fas fa-save me-1"></i>{{ $editingCourse ? 'Atualizar Curso' : 'Salvar Curso' }}
                    </span>
                    <span wire:loading wire:target="salvarCourse">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingCourse ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
