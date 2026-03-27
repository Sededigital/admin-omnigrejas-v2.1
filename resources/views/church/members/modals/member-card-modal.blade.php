<!-- Modal para Cadastro/Edição de Cartão -->
<div class="modal fade" id="memberCardModal" tabindex="-1" aria-labelledby="memberCardModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <!-- Header do Modal -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="memberCardModalLabel">
                    <i class="fas fa-id-card text-info me-2"></i>
                    <span id="modal-title">{{ $modoEdicao ? 'Editar Cartão' : 'Cadastrar Novo Cartão' }}</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <!-- Corpo do Modal -->
            <div class="modal-body p-4">
                <form wire:submit.prevent="salvarCartao" enctype="multipart/form-data">

                    <!-- Navegação por Abas (Bootstrap puro) -->
                    <nav class="mb-4"  wire:ignore>
                        <div class="nav nav-tabs border-bottom-0" id="nav-tab" role="tablist">
                            <button class="nav-link active border-0 bg-transparent fw-semibold" id="nav-basic-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-basic" type="button" role="tab">
                                <i class="fas fa-info-circle text-info me-1"></i>Informações Básicas
                            </button>
                            <button class="nav-link border-0 bg-transparent fw-semibold" id="nav-details-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-details" type="button" role="tab">
                                <i class="fas fa-cogs text-info me-1"></i>Detalhes Técnicos
                            </button>
                        </div>
                    </nav>

                    <!-- Conteúdo das Abas -->
                    <div class="tab-content" id="nav-tabContent">

                        <!-- Aba: Informações Básicas -->
                        <div class="tab-pane fade show active" id="nav-basic" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <!-- Membro -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        @if($membrosDisponiveis->count() > 0)
                                            <select class="form-select @error('membro_id') is-invalid @enderror" wire:model.lazy="membro_id" wire:change="gerarNumeroAutomaticamente">
                                                <option value="">Selecione um membro</option>
                                                @foreach($membrosDisponiveis as $membro)
                                                    <option value="{{ $membro['id'] }}">
                                                        {{ $membro['nome'] }} - {{ ucfirst($membro['cargo']) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-select" disabled>
                                                <option value="">Nenhum membro disponível</option>
                                            </select>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Todos os membros já possuem cartão ou não há membros ativos.
                                                </small>
                                            </div>
                                        @endif
                                        <label><i class="fas fa-user text-info me-1"></i>Membro *</label>
                                        @error('membro_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Número do Cartão -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control" wire:model="numero_cartao"
                                               placeholder="Será gerado automaticamente" readonly
                                               style="background-color: #f8f9fa;">
                                        <label><i class="fas fa-hashtag text-info me-1"></i>Número do Cartão</label>
                                        @if($numero_cartao)
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Número gerado automaticamente
                                                </small>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Preencha os campos obrigatórios para gerar
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Data de Emissão -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"
                                               class="form-control @error('data_emissao') is-invalid @enderror"
                                               wire:model.defer="data_emissao"
                                               wire:change="gerarNumeroAutomaticamente"
                                               value="{{ date('d/m/Y') }}"
                                               placeholder="dd/mm/aaaa"
                                               autocomplete="off"
                                               readonly
                                               style="cursor: not-allowed;">
                                        <label><i class="fas fa-calendar-plus text-info me-1"></i>Data de Emissão *</label>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Data atual (automática)
                                            </small>
                                        </div>
                                        @error('data_emissao')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data de Validade -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password"
                                               class="form-control date_flatpicker @error('data_validade') is-invalid @enderror"
                                               wire:model="data_validade"
                                               value="{{ $data_validade ? \Carbon\Carbon::parse($data_validade)->format('d/m/Y') : '' }}"
                                               placeholder="dd/mm/aaaa"
                                               data-min-date="{{ date('Y-m-d') }}"
                                               data-max-date="{{ date('Y') + 30 }}-12-31"
                                               autocomplete="off"
                                               readonly
                                               style="cursor: pointer;">
                                        <label><i class="fas fa-calendar-check text-info me-1"></i>Data de Validade</label>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Selecione uma data futura
                                            </small>
                                        </div>
                                        @error('data_validade')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fas fa-toggle-on text-info me-1"></i>Status *</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status-ativo" value="ativo" wire:model="status" wire:change="gerarNumeroAutomaticamente">
                                                    <label class="form-check-label" for="status-ativo">
                                                        <i class="fas fa-check-circle text-success me-1"></i>Ativo
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status-inativo" value="inativo" wire:model="status" wire:change="gerarNumeroAutomaticamente">
                                                    <label class="form-check-label" for="status-inativo">
                                                        <i class="fas fa-times-circle text-danger me-1"></i>Inativo
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Observações -->
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                                  wire:model="observacoes" rows="3"
                                                  placeholder="Observações sobre o cartão"></textarea>
                                        <label><i class="fas fa-comment text-info me-1"></i>Observações</label>
                                        @error('observacoes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if($status !== 'ativo')
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input type="text"  autocomplete="new-password" class="form-control" wire:model="motivo_inativacao"
                                                   placeholder="Motivo da inativação">
                                            <label><i class="fas fa-exclamation-triangle text-warning me-1"></i>Motivo da Inativação</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Aba: Detalhes Técnicos -->
                        <div class="tab-pane fade" id="nav-details" role="tabpanel" wire:ignore.self>
                            <div class="row g-3">
                                <!-- Custos -->
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" wire:model="custo_producao" step="0.01" min="0"
                                               placeholder="0.00">
                                        <label><i class="fas fa-print text-info me-1"></i>Custo de Produção (KZ)</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" wire:model="custo_entrega" step="0.01" min="0"
                                               placeholder="0.00">
                                        <label><i class="fas fa-truck text-info me-1"></i>Custo de Entrega (KZ)</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <input type="text"  autocomplete="new-password" class="form-control" wire:model="assinatura_digital"
                                               placeholder="Dados da assinatura digital">
                                        <label><i class="fas fa-signature text-info me-1"></i>Assinatura Digital</label>
                                    </div>
                                </div>
                                <!-- Campos Técnicos -->
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"><i class="fas fa-camera text-info me-1"></i>Foto do Membro</label>
                                                <input type="file" class="form-control @error('foto_arquivo') is-invalid @enderror"
                                                       wire:model="foto_arquivo" accept="image/*">
                                                <div class="form-text">
                                                    <small class="text-muted">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB</small>
                                                </div>
                                                @error('foto_arquivo')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @if($foto_url && !$foto_arquivo)
                                                    <div class="mt-2">
                                                        <small class="text-success">
                                                            <i class="fas fa-check-circle me-1"></i>Foto atual: {{ basename($foto_url) }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label"><i class="fas fa-eye text-info me-1"></i>Pré-visualização</label>
                                                <div class="border rounded p-2" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                                    @if($foto_arquivo)
                                                        <div class="text-center">
                                                            <div wire:loading wire:target="foto_arquivo" class="mb-2">
                                                                <i class="fas fa-spinner fa-spin text-info"></i>
                                                                <small class="text-muted d-block">Processando...</small>
                                                            </div>
                                                            <img src="{{ $foto_arquivo->temporaryUrl() }}" alt="Pré-visualização" class="img-thumbnail" style="max-width: 80px; max-height: 80px;" wire:loading.remove wire:target="foto_arquivo">
                                                        </div>
                                                    @elseif($foto_url)
                                                        <img src="{{ asset($foto_url) }}" alt="Foto atual" class="img-thumbnail" style="max-width: 80px; max-height: 80px;">
                                                    @else
                                                        <div class="text-center text-muted">
                                                            <i class="fas fa-image fa-lg mb-1"></i>
                                                            <p class="mb-0 small">Miniatura</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Status Visual -->
                                <div class="col-12">
                                    <div class="alert alert-light border">
                                        <i class="fas fa-info-circle text-info me-2"></i>
                                        <strong>Status:</strong>
                                        <span class="text-muted">
                                            {{ $modoEdicao ? 'Editando Cartão' : 'Novo Cartão' }}
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
                <button type="button" class="btn bg-info text-light" wire:click="salvarCartao" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarCartao">
                        <i class="fas fa-save me-1"></i>{{ $modoEdicao ? 'Atualizar Cartão' : 'Salvar Cartão' }}
                    </span>
                    <span wire:loading wire:target="salvarCartao">
                        <i class="fas fa-spinner fa-spin me-1"></i>{{ $modoEdicao ? 'Atualizando...' : 'Salvando...' }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Histórico -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-light text-white">
                <h5 class="modal-title">
                    <i class="fas fa-history me-2"></i>Histórico do Cartão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" wire:click="fecharHistorico"></button>
            </div>

            <div class="modal-body">
                @if(!empty($historicoCartao))
                    <div class="position-relative">
                        @foreach($historicoCartao as $historico)
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-{{ $historico['acao'] === 'solicitado' ? 'primary' : ($historico['acao'] === 'aprovado' ? 'success' : 'info') }} text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-{{ $historico['acao'] === 'solicitado' ? 'plus' : ($historico['acao'] === 'aprovado' ? 'check' : 'info') }} fa-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 border-start border-{{ $historico['acao'] === 'solicitado' ? 'primary' : ($historico['acao'] === 'aprovado' ? 'success' : 'info') }} ps-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $historico['acao'] }}</h6>
                                            <p class="mb-1 text-muted small">{{ $historico['descricao'] }}</p>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($historico['data_acao'])->format('d/m/Y H:i') }}
                                                @if(isset($historico['realizado_por']))
                                                    por {{ $historico['realizado_por']['name'] }}
                                                @endif
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $historico['acao'] === 'solicitado' ? 'primary' : ($historico['acao'] === 'aprovado' ? 'success' : 'info') }}">
                                            {{ ucfirst($historico['acao']) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle text-muted mb-3" style="font-size: 2rem;"></i>
                        <h6 class="text-muted">Nenhum histórico encontrado</h6>
                        <p class="text-muted small">Este cartão ainda não possui ações registradas.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" wire:click="fecharHistorico">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visualização do Cartão -->
<div class="modal fade" id="viewCardModal" tabindex="-1" aria-labelledby="viewCardModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Visualizar Cartão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                @if(isset($cartaoSelecionado) && $cartaoSelecionado)
                    <!-- Cartão de Membro Estilizado -->
                    <div class="member-card-container">
                        <div class="member-card">
                            <!-- Cabeçalho do Cartão -->
                            <div class="card-header-section">
                                <div class="church-info">
                                    <div class="church-logo">
                                        <i class="fas fa-church"></i>
                                    </div>
                                    <div class="church-details">
                                        <h3 class="church-name">{{ $igreja->nome ?? 'Igreja' }}</h3>
                                        <p class="church-phone">{{ $igreja->contacto ?? '(+244) 932-713-172' }}</p>
                                    </div>
                                </div>
                                <div class="card-type">
                                    <span class="card-type-label">CARTÃO DE MEMBRO</span>
                                </div>
                            </div>

                            <!-- Corpo do Cartão -->
                            <div class="card-body-section">
                                <div class="member-photo-section">
                                    @if($cartaoSelecionado->foto_url)
                                        <img src="{{ asset($cartaoSelecionado->foto_url) }}" alt="Foto do Membro" class="member-photo">
                                    @else
                                        <div class="member-photo-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="member-info-section">
                                    <div class="member-name">
                                        <h2>{{ $cartaoSelecionado->membro->user->name ?? 'Nome do Membro' }}</h2>
                                        <p class="member-role">{{ ucfirst($cartaoSelecionado->membro->cargo ?? 'membro') }}</p>
                                    </div>

                                    <div class="member-details">
                                        <div class="detail-row">
                                            <span class="label">Número do Cartão:</span>
                                            <span class="value">{{ $cartaoSelecionado->numero_cartao }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Data de Emissão:</span>
                                            <span class="value">{{ $cartaoSelecionado->data_emissao ? $cartaoSelecionado->data_emissao->format('d/m/Y') : '-' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Data de Validade:</span>
                                            <span class="value">{{ $cartaoSelecionado->data_validade ? $cartaoSelecionado->data_validade->format('d/m/Y') : '-' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Status:</span>
                                            <span class="value status-{{ $cartaoSelecionado->status }}">{{ $cartaoSelecionado->getStatusFormatado() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- QR Code e Assinatura -->
                            <div class="card-footer-section">
                                <div class="qr-signature-section">
                                    @if($cartaoSelecionado->qr_code)
                                        <div class="qr-code">
                                            {!! $cartaoSelecionado->qr_code !!}
                                        </div>
                                    @endif

                                    @if($cartaoSelecionado->assinatura_digital)
                                        <div class="signature">
                                            <p class="signature-label">Assinatura Digital:</p>
                                            <p class="signature-value">{{ $cartaoSelecionado->assinatura_digital }}</p>
                                        </div>
                                    @endif
                                </div>

                                <div class="card-notice">
                                    <p class="notice-text">
                                        <i class="fas fa-info-circle"></i>
                                        Este cartão é propriedade da igreja e deve ser apresentado quando solicitado.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-id-card text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Cartão não encontrado</h5>
                        <p class="text-muted">Não foi possível carregar as informações do cartão.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fechar
                </button>
                @if(isset($cartaoSelecionado) && $cartaoSelecionado && $cartaoSelecionado->status === 'ativo' && (!$cartaoSelecionado->data_validade || !$cartaoSelecionado->isExpirado()))
                    <button type="button" class="btn btn-outline-primary me-2" onclick="window.open('/churches/church-member-cards-print/{{ $cartaoSelecionado->id }}', '_blank')" wire:loading.attr="disabled">
                        <i class="fas fa-print me-1"></i>Imprimir Cartão
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>


