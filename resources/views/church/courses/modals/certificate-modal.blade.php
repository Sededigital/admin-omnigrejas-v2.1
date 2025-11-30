<!-- Modal para Cadastro/Edição de Certificado -->
<div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="certificateModalLabel">
                    <i class="fas fa-certificate text-primary me-2"></i>
                    <span>{{ $isEditing ? 'Editar Certificado' : 'Emitir Novo Certificado' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarCertificate">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4">
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab-certificate" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-certificate-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic-certificate" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações do Certificado
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-certificate-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details-certificate" type="button" role="tab">
                                <i class="fas fa-cog text-primary me-1"></i>Configurações Avançadas
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent-certificate">

                        <!-- Aba: Informações do Certificado -->
                        <div class="tab-pane fade show active" id="nav-basic-certificate" role="tabpanel">
                            <div class="row g-3">
                                <!-- Matrícula -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('matricula_id') is-invalid @enderror" wire:model="matricula_id">
                                            <option value="">Selecione a matrícula</option>
                                            @foreach(\App\Models\Cursos\CursoMatricula::with(['membro.user', 'turma.curso'])->whereHas('turma.curso', function($q) {
                                                $q->where('igreja_id', auth()->user()->getIgrejaId());
                                            })->where('status', 'concluido')->get() as $matricula)
                                                <option value="{{ $matricula->id }}">
                                                    {{ $matricula->membro->user->name }} - {{ $matricula->turma->curso->nome }} ({{ $matricula->turma->nome }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user-graduate text-primary me-1"></i>Matrícula *</label>
                                        @error('matricula_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Número do Certificado -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('numero_certificado') is-invalid @enderror"
                                               wire:model="numero_certificado" placeholder="Número do certificado">
                                        <label><i class="fas fa-hashtag text-primary me-1"></i>Número do Certificado</label>
                                        @error('numero_certificado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data Emissão -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_emissao') is-invalid @enderror"
                                               wire:model="data_emissao">
                                        <label><i class="fas fa-calendar-plus text-primary me-1"></i>Data Emissão</label>
                                        @error('data_emissao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data Conclusão -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('data_conclusao') is-invalid @enderror"
                                               wire:model="data_conclusao">
                                        <label><i class="fas fa-calendar-check text-primary me-1"></i>Data Conclusão</label>
                                        @error('data_conclusao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Frequência Final -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" min="0" max="100" step="0.1" class="form-control @error('frequencia_final') is-invalid @enderror"
                                               wire:model="frequencia_final" placeholder="0.0">
                                        <label><i class="fas fa-percentage text-primary me-1"></i>Frequência Final (%)</label>
                                        @error('frequencia_final')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Template Usado -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('template_usado') is-invalid @enderror"
                                               wire:model="template_usado" placeholder="Template usado">
                                        <label><i class="fas fa-file-alt text-primary me-1"></i>Template Usado</label>
                                        @error('template_usado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Código Verificação -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('codigo_verificacao') is-invalid @enderror"
                                               wire:model="codigo_verificacao" placeholder="Código de verificação">
                                        <label><i class="fas fa-qrcode text-primary me-1"></i>Código Verificação</label>
                                        @error('codigo_verificacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Válido Até -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('valido_ate') is-invalid @enderror"
                                               wire:model="valido_ate">
                                        <label><i class="fas fa-calendar-times text-primary me-1"></i>Válido Até</label>
                                        @error('valido_ate')
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
                                            {{ $isEditing ? 'Editando Certificado' : 'Novo Certificado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Configurações Avançadas -->
                        <div class="tab-pane fade" id="nav-details-certificate" role="tabpanel">
                            <div class="row g-3">
                                <!-- Campos avançados podem ser adicionados aqui -->
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Configurações Avançadas</strong><br>
                                        <small>Esta seção pode conter configurações adicionais como notificações, validações, etc.</small>
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
                <button type="button" class="btn btn-primary" wire:click="salvarCertificate" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarCertificate">
                        <i class="fas fa-save me-1"></i>{{ $isEditing ? 'Atualizar Certificado' : 'Emitir Certificado' }}
                    </span>
                    <span wire:loading wire:target="salvarCertificate">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $isEditing ? 'Atualizando...' : 'Emitindo...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
