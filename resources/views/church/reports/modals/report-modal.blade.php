<!-- Modal para Cadastro/Edição de Relatório de Culto -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false"  wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="reportModalLabel">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    <span id="modal-title">{{ $editingReport ? 'Editar Relatório' : 'Novo Relatório de Culto' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarRelatorio">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4" wire:ignore>
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-chart-line text-primary me-1"></i>Dados do Culto
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-statistics-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-statistics" type="button" role="tab">
                                <i class="fas fa-chart-bar text-primary me-1"></i>Estatísticas Detalhadas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-finance-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-finance" type="button" role="tab">
                                <i class="fas fa-money-bill-wave text-primary me-1"></i>Valores Financeiros
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-info-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-info" type="button" role="tab">
                                <i class="fas fa-info text-primary me-1"></i>Informações do Culto
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-evaluation-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-evaluation" type="button" role="tab">
                                <i class="fas fa-check-circle text-primary me-1"></i>Avaliação
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-content-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-content" type="button" role="tab">
                                <i class="fas fa-file-text text-primary me-1"></i>Conteúdo
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent" wire:ignore.self>

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel"  wire:ignore>
                            <div class="row g-3">
                                <!-- Evento Relacionado -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('evento_id') is-invalid @enderror"
                                                wire:model="evento_id">
                                            <option value="">Selecione um evento (opcional)</option>
                                            @foreach($eventosDisponiveis as $evento)
                                                <option value="{{ $evento->id }}">{{ $evento->titulo }} - {{ $evento->data_evento->format('d/m/Y') }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-calendar-alt text-primary me-1"></i>Evento Relacionado</label>
                                        @error('evento_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Culto Padrão -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('culto_padrao_id') is-invalid @enderror"
                                                wire:model="culto_padrao_id">
                                            <option value="">Selecione um culto padrão (opcional)</option>
                                            @foreach($cultosPadraoDisponiveis as $culto)
                                                <option value="{{ $culto->id }}">{{ $culto->titulo }} - {{ $culto->hora_inicio }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-church text-primary me-1"></i>Culto Padrão</label>
                                        @error('culto_padrao_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Título -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('titulo') is-invalid @enderror"
                                               wire:model="titulo" placeholder="Título do relatório">
                                        <label><i class="fas fa-heading text-primary me-1"></i>Título (opcional)</label>
                                        @error('titulo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data do Relatório -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control date_flatpicker @error('data_relatorio') is-invalid @enderror"
                                               wire:model="data_relatorio" placeholder="Selecione a data">
                                        <label><i class="fas fa-calendar-day text-primary me-1"></i>Data do Relatório *</label>
                                        @error('data_relatorio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="radio" name="status" id="status-rascunho" value="rascunho" wire:model="status" style="transform: scale(1.2);">
                                                    <label class="form-check-label fw-semibold" for="status-rascunho" style="font-size: 1.1em;">
                                                        <i class="fas fa-edit text-warning me-1"></i>Rascunho
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="radio" name="status" id="status-finalizado" value="finalizado" wire:model="status" style="transform: scale(1.2);">
                                                    <label class="form-check-label fw-semibold" for="status-finalizado" style="font-size: 1.1em;">
                                                        <i class="fas fa-check-circle text-success me-1"></i>Finalizado
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

                        <!-- Aba: Dados do Culto -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Número de Participantes -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_participantes') is-invalid @enderror"
                                               wire:model="numero_participantes" placeholder="0" min="0">
                                        <label><i class="fas fa-users text-primary me-1"></i>Número de Participantes</label>
                                        @error('numero_participantes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Valor da Oferta -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('valor_oferta') is-invalid @enderror"
                                               wire:model="valor_oferta" placeholder="0.00" min="0" step="0.01">
                                        <label><i class="fas fa-money-bill-wave text-primary me-1"></i>Valor da Oferta (AOA)</label>
                                        @error('valor_oferta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Observações -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                  wire:model="observacoes" rows="3" placeholder="Observações adicionais"></textarea>
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
                                        <strong>Dados do Culto:</strong>
                                        <span class="text-muted ms-2">
                                            @if($numero_participantes)
                                                {{ $numero_participantes }} participantes
                                            @else
                                                Número de participantes não informado
                                            @endif
                                            @if($valor_oferta)
                                                • Oferta: {{ number_format($valor_oferta, 2, ',', '.') }} AOA
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Estatísticas Detalhadas -->
                        <div class="tab-pane fade" id="nav-statistics" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>Estatísticas de Participação e Eventos
                                    </h6>
                                </div>

                                <!-- Visitantes -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_visitantes') is-invalid @enderror"
                                               wire:model="numero_visitantes" placeholder="0" min="0">
                                        <label><i class="fas fa-user-friends text-primary me-1"></i>Número de Visitantes</label>
                                        @error('numero_visitantes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Decisões -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_decisoes') is-invalid @enderror"
                                               wire:model="numero_decisoes" placeholder="0" min="0">
                                        <label><i class="fas fa-hand-paper text-primary me-1"></i>Número de Decisões</label>
                                        @error('numero_decisoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Batismos -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_batismos') is-invalid @enderror"
                                               wire:model="numero_batismos" placeholder="0" min="0">
                                        <label><i class="fas fa-water text-primary me-1"></i>Número de Batismos</label>
                                        @error('numero_batismos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Conversões -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_conversoes') is-invalid @enderror"
                                               wire:model="numero_conversoes" placeholder="0" min="0">
                                        <label><i class="fas fa-heart text-primary me-1"></i>Número de Conversões</label>
                                        @error('numero_conversoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Reconciliações -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_reconciliacoes') is-invalid @enderror"
                                               wire:model="numero_reconciliacoes" placeholder="0" min="0">
                                        <label><i class="fas fa-handshake text-primary me-1"></i>Número de Reconciliações</label>
                                        @error('numero_reconciliacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Casamentos -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_casamentos') is-invalid @enderror"
                                               wire:model="numero_casamentos" placeholder="0" min="0">
                                        <label><i class="fas fa-ring text-primary me-1"></i>Número de Casamentos</label>
                                        @error('numero_casamentos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Funerais -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_funeral') is-invalid @enderror"
                                               wire:model="numero_funeral" placeholder="0" min="0">
                                        <label><i class="fas fa-cross text-primary me-1"></i>Número de Funerais</label>
                                        @error('numero_funeral')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Outros Eventos -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('numero_outros_eventos') is-invalid @enderror"
                                               wire:model="numero_outros_eventos" placeholder="0" min="0">
                                        <label><i class="fas fa-calendar-alt text-primary me-1"></i>Outros Eventos</label>
                                        @error('numero_outros_eventos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Valores Financeiros -->
                        <div class="tab-pane fade" id="nav-finance" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-money-bill-wave me-2"></i>Valores Financeiros Detalhados
                                    </h6>
                                </div>

                                <!-- Dízimos -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('valor_dizimos') is-invalid @enderror"
                                               wire:model.live="valor_dizimos" placeholder="0.00" min="0" step="0.01">
                                        <label><i class="fas fa-coins text-primary me-1"></i>Valor dos Dízimos (AOA)</label>
                                        @error('valor_dizimos')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Ofertas -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('valor_ofertas') is-invalid @enderror"
                                               wire:model.live="valor_ofertas" placeholder="0.00" min="0" step="0.01">
                                        <label><i class="fas fa-hand-holding-heart text-primary me-1"></i>Valor das Ofertas (AOA)</label>
                                        @error('valor_ofertas')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Doações -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('valor_doacoes') is-invalid @enderror"
                                               wire:model.live="valor_doacoes" placeholder="0.00" min="0" step="0.01">
                                        <label><i class="fas fa-gift text-primary me-1"></i>Valor das Doações (AOA)</label>
                                        @error('valor_doacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Outros -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('valor_outros') is-invalid @enderror"
                                               wire:model.live="valor_outros" placeholder="0.00" min="0" step="0.01">
                                        <label><i class="fas fa-plus-circle text-primary me-1"></i>Outros Valores (AOA)</label>
                                        @error('valor_outros')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Resumo Financeiro -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-calculator text-primary me-2"></i>
                                        <strong>Resumo Financeiro:</strong>
                                        <span class="text-muted ms-2">
                                            @php
                                                $totalFinanceiro = (float)($valor_oferta ?? 0) + (float)($valor_dizimos ?? 0) + (float)($valor_ofertas ?? 0) + (float)($valor_doacoes ?? 0) + (float)($valor_outros ?? 0);
                                            @endphp
                                            Total arrecadado: {{ number_format($totalFinanceiro, 2, ',', '.') }} AOA
                                            @if($valor_oferta) • <span class="text-success fw-bold">Oferta: {{ number_format($valor_oferta, 2, ',', '.') }}</span> @endif
                                            @if($valor_dizimos) • <span class="text-primary fw-bold">Dízimos: {{ number_format($valor_dizimos, 2, ',', '.') }}</span> @endif
                                            @if($valor_ofertas) • <span class="text-danger fw-bold">Ofertas: {{ number_format($valor_ofertas, 2, ',', '.') }}</span> @endif
                                            @if($valor_doacoes) • <span class="text-warning fw-bold">Doações: {{ number_format($valor_doacoes, 2, ',', '.') }}</span> @endif
                                            @if($valor_outros) • <span class="text-info fw-bold">Outros: {{ number_format($valor_outros, 2, ',', '.') }}</span> @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Informações do Culto -->
                        <div class="tab-pane fade" id="nav-info" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info me-2"></i>Informações Específicas do Culto
                                    </h6>
                                </div>

                                <!-- Tema do Culto -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('tema_culto') is-invalid @enderror"
                                               wire:model="tema_culto" placeholder="Tema do culto">
                                        <label><i class="fas fa-themeisle text-primary me-1"></i>Tema do Culto</label>
                                        @error('tema_culto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Pregador -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('pregador') is-invalid @enderror"
                                               wire:model="pregador" placeholder="Nome do pregador">
                                        <label><i class="fas fa-user-tie text-primary me-1"></i>Pregador</label>
                                        @error('pregador')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Pregador Convidado -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('pregador_convidado') is-invalid @enderror"
                                               wire:model="pregador_convidado" placeholder="Nome do pregador convidado">
                                        <label><i class="fas fa-user-friends text-primary me-1"></i>Pregador Convidado</label>
                                        @error('pregador_convidado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Texto Base -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('texto_base') is-invalid @enderror"
                                               wire:model="texto_base" placeholder="Ex: João 3:16">
                                        <label><i class="fas fa-book-open text-primary me-1"></i>Texto Base</label>
                                        @error('texto_base')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Resumo da Mensagem -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('resumo_mensagem') is-invalid @enderror"
                                                  wire:model="resumo_mensagem" rows="3" placeholder="Breve resumo da mensagem pregada"></textarea>
                                        <label><i class="fas fa-comment-dots text-primary me-1"></i>Resumo da Mensagem</label>
                                        @error('resumo_mensagem')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Culto -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('tipo_culto') is-invalid @enderror"
                                                wire:model="tipo_culto">
                                            <option value="outro">Outro</option>
                                            <option value="domingo">Domingo</option>
                                            <option value="sexta">Sexta-feira</option>
                                            <option value="vigilia">Vigília</option>
                                            <option value="especial">Especial</option>
                                        </select>
                                        <label><i class="fas fa-calendar-week text-primary me-1"></i>Tipo de Culto</label>
                                        @error('tipo_culto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dirigente -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('dirigente') is-invalid @enderror"
                                               wire:model="dirigente" placeholder="Nome do dirigente">
                                        <label><i class="fas fa-user-cog text-primary me-1"></i>Dirigente</label>
                                        @error('dirigente')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Responsável pela Música -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('musica_responsavel') is-invalid @enderror"
                                               wire:model="musica_responsavel" placeholder="Nome do responsável">
                                        <label><i class="fas fa-music text-primary me-1"></i>Responsável pela Música</label>
                                        @error('musica_responsavel')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Observações Gerais -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('observacoes_gerais') is-invalid @enderror"
                                                  wire:model="observacoes_gerais" rows="4"
                                                  placeholder="Observações gerais sobre o culto"></textarea>
                                        <label><i class="fas fa-sticky-note text-primary me-1"></i>Observações Gerais</label>
                                        @error('observacoes_gerais')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Avaliação -->
                        <div class="tab-pane fade" id="nav-evaluation" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-check-circle me-2"></i>Avaliação do Relatório
                                    </h6>
                                </div>

                                <!-- Avaliado Por -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control @error('avaliado_por') is-invalid @enderror"
                                               wire:model="avaliado_por" placeholder="Nome do avaliador"
                                               @if($avaliado_por) readonly @endif>
                                        <label><i class="fas fa-user-check text-primary me-1"></i>Avaliado Por</label>
                                        @error('avaliado_por')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($avaliado_por)
                                            <div class="form-text text-muted">
                                                <i class="fas fa-lock text-warning me-1"></i>Campo preenchido automaticamente ao finalizar relatório
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Data da Avaliação -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="datetime-local" class="form-control @error('data_avaliacao') is-invalid @enderror"
                                               wire:model="data_avaliacao"
                                               @if($data_avaliacao) readonly @endif>
                                        <label><i class="fas fa-calendar-check text-primary me-1"></i>Data da Avaliação</label>
                                        @error('data_avaliacao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($data_avaliacao)
                                            <div class="form-text text-muted">
                                                <i class="fas fa-lock text-warning me-1"></i>Campo preenchido automaticamente ao finalizar relatório
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status de Avaliação -->
                                <div class="col-12">
                                    <div class="alert @if($avaliado_por) alert-success @else alert-info @endif border">
                                        <i class="fas @if($avaliado_por) fa-check-circle @else fa-info-circle @endif text-primary me-2"></i>
                                        <strong>Avaliação:</strong>
                                        <span class="text-muted ms-2">
                                            @if($avaliado_por)
                                                Avaliado automaticamente por {{ $avaliado_por }}
                                                @if($data_avaliacao)
                                                    em {{ \Carbon\Carbon::parse($data_avaliacao)->format('d/m/Y H:i') }}
                                                @endif
                                            @else
                                                Relatório será avaliado automaticamente ao ser finalizado
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Conteúdo -->
                        <div class="tab-pane fade" id="nav-content" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Conteúdo do Relatório -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('conteudo') is-invalid @enderror"
                                                  wire:model="conteudo" rows="8"
                                                  placeholder="Descreva detalhadamente o relatório do culto..."></textarea>
                                        <label><i class="fas fa-file-text text-primary me-1"></i>Conteúdo do Relatório *</label>
                                        @error('conteudo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Descreva os principais acontecimentos, mensagens, atividades e reflexões do culto.
                                    </div>
                                </div>

                                <!-- Pré-visualização -->
                                <div class="col-12" wire:ignore>
                                    <div class="border rounded p-3 bg-light">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-eye me-1"></i>Pré-visualização
                                        </h6>
                                        <div class="content-preview">
                                            @if($conteudo)
                                                {!! nl2br(e($conteudo)) !!}
                                            @else
                                                <span class="text-muted">O conteúdo aparecerá aqui conforme você digita...</span>
                                            @endif
                                        </div>
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
                <button type="button" class="btn btn-primary" wire:click="salvarRelatorio" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarRelatorio">
                        <i class="fas fa-save me-1"></i>{{ $editingReport ? 'Atualizar Relatório' : 'Salvar Relatório' }}
                    </span>
                    <span wire:loading wire:target="salvarRelatorio">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingReport ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para o modal -->
<style>
/* Melhorar textarea */
.form-floating textarea {
    min-height: 120px;
}

/* Pré-visualização */
.content-preview {
    max-height: 200px;
    overflow-y: auto;
    line-height: 1.6;
}

/* Melhorar tabs */
.nav-tabs .nav-link {
    border: none;
    border-bottom: 2px solid transparent;
    color: #6c757d;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
    background-color: transparent;
}

.nav-tabs .nav-link:hover {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
}

/* Responsivo */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem;
    }

    .nav-tabs .nav-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Garantir que as abas funcionem corretamente */
.tab-content .tab-pane {
    display: none;
}

.tab-content .tab-pane.show {
    display: block;
}
</style>

