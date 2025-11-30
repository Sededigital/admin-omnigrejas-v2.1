<!-- Modal para Cadastro/Edição de Pedido Especial -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false"  wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="requestModalLabel">
                    <i class="fas fa-hands-helping text-primary me-2"></i>
                    <span id="modal-title">{{ $editingRequest ? 'Editar Pedido Especial' : 'Novo Pedido Especial' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarPedido">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4" wire:ignore>
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-primary me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-file-text text-primary me-1"></i>Detalhes do Pedido
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-course-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-course" type="button" role="tab">
                                <i class="fas fa-graduation-cap text-primary me-1"></i>Curso Relacionado
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent" wire:ignore.self>

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel"  wire:ignore>
                            <div class="row g-3">
                                <!-- Membro Solicitante -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('membro_id') is-invalid @enderror"
                                                wire:model="membro_id">
                                            <option value="">Selecione um membro</option>
                                            @foreach($membrosDisponiveis as $membro)
                                                <option value="{{ $membro->id }}">{{ $membro->user->name }} - {{ $membro->numero_membro }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user text-primary me-1"></i>Membro Solicitante *</label>
                                        @error('membro_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Pedido -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('pedido_tipo_id') is-invalid @enderror"
                                                wire:model="pedido_tipo_id">
                                            <option value="">Selecione o tipo de pedido</option>
                                            @foreach($tiposPedidoDisponiveis as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-tag text-primary me-1"></i>Tipo de Pedido *</label>
                                        @error('pedido_tipo_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data do Pedido -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control date_flatpicker @error('data_pedido') is-invalid @enderror"
                                               wire:model="data_pedido" placeholder="Selecione a data">
                                        <label><i class="fas fa-calendar-day text-primary me-1"></i>Data do Pedido *</label>
                                        @error('data_pedido')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Responsável -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('responsavel_id') is-invalid @enderror"
                                                wire:model="responsavel_id">
                                            <option value="">Selecione um responsável (opcional)</option>
                                            @foreach($responsaveisDisponiveis as $responsavel)
                                                <option value="{{ $responsavel->id }}">{{ $responsavel->name }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-user-tie text-primary me-1"></i>Responsável</label>
                                        @error('responsavel_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Aba: Detalhes do Pedido -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel"  wire:ignore.self>
                            <div class="row g-3">
                                <!-- Descrição -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('descricao') is-invalid @enderror"
                                                  wire:model="descricao" rows="6"
                                                  placeholder="Descreva detalhadamente o pedido especial..."></textarea>
                                        <label><i class="fas fa-file-text text-primary me-1"></i>Descrição do Pedido *</label>
                                        @error('descricao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">
                                        Descreva o pedido de forma clara e detalhada para facilitar o processamento.
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="radio" name="status" id="status-pendente" value="pendente" wire:model="status" style="transform: scale(1.2);">
                                                    <label class="form-check-label fw-semibold" for="status-pendente" style="font-size: 1.1em;">
                                                        <i class="fas fa-clock text-warning me-1"></i>Pendente
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="radio" name="status" id="status-em_andamento" value="em_andamento" wire:model="status" style="transform: scale(1.2);">
                                                    <label class="form-check-label fw-semibold" for="status-em_andamento" style="font-size: 1.1em;">
                                                        <i class="fas fa-cogs text-info me-1"></i>Em Andamento
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        <strong>Status do Pedido:</strong>
                                        <span class="text-muted ms-2">
                                            @if($status === 'pendente')
                                                Pedido aguardando processamento
                                            @elseif($status === 'em_andamento')
                                                Pedido está sendo processado
                                            @elseif($status === 'aprovado')
                                                Pedido foi aprovado
                                            @elseif($status === 'rejeitado')
                                                Pedido foi rejeitado
                                            @elseif($status === 'concluido')
                                                Pedido foi concluído
                                            @else
                                                Status não definido
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Aba: Curso Relacionado -->
                        <div class="tab-pane fade" id="nav-course" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-graduation-cap me-2"></i>Curso Relacionado (Opcional)
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        Se este pedido estiver relacionado a um curso específico, selecione-o abaixo.
                                    </p>
                                </div>

                                <!-- Curso -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('curso_id') is-invalid @enderror"
                                                wire:model="curso_id">
                                            <option value="">Nenhum curso relacionado</option>
                                            @foreach($cursosDisponiveis as $curso)
                                                <option value="{{ $curso->id }}">{{ $curso->nome }} - {{ $curso->tipo }}</option>
                                            @endforeach
                                        </select>
                                        <label><i class="fas fa-graduation-cap text-primary me-1"></i>Curso Relacionado</label>
                                        @error('curso_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Informações do Curso Selecionado -->
                                @if($curso_id)
                                    <div class="col-12">
                                        <div class="alert alert-info border">
                                            <i class="fas fa-info-circle text-info me-2"></i>
                                            <strong>Curso Selecionado:</strong>
                                            @php
                                                $cursoSelecionado = collect($cursosDisponiveis)->firstWhere('id', $curso_id);
                                            @endphp
                                            @if($cursoSelecionado)
                                                <div class="mt-2">
                                                    <strong>{{ $cursoSelecionado->nome }}</strong><br>
                                                    <small class="text-muted">
                                                        Tipo: {{ $cursoSelecionado->tipo }} |
                                                        Carga Horária: {{ $cursoSelecionado->carga_horaria_total ?? 'N/A' }}h |
                                                        Status: {{ ucfirst($cursoSelecionado->status) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
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
                <button type="button" class="btn btn-primary" wire:click="salvarPedido" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarPedido">
                        <i class="fas fa-save me-1"></i>{{ $editingRequest ? 'Atualizar Pedido' : 'Salvar Pedido' }}
                    </span>
                    <span wire:loading wire:target="salvarPedido">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingRequest ? 'Atualizando...' : 'Salvando...' }}
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
