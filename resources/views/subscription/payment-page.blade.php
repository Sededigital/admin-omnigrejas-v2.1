<div class="container-fluid py-5">
    <!-- Hero Section -->
    <div class="card bg-gradient-hero text-white border-0 shadow-lg mb-5">
        <div class="card-body p-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-credit-card me-3"></i>
                        Pagamento de Assinatura
                    </h1>
                    <p class="lead mb-4">
                        Complete seu <strong>{{ $acaoFormatada }}</strong> do pacote <strong>{{ $pacote->nome }}</strong>.
                        Envie o comprovativo de pagamento para ativar sua assinatura.
                    </p>

                    <div class="row g-4">
                        <div class="col-auto text-center">
                            <div class="h4 mb-1">{{ number_format($valorTotal, 2, ',', '.') }} AOA</div>
                            <div class="small opacity-75">Valor Total</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">
                                @if($duracaoMeses === 'vitalicio')
                                    Vitalício
                                @else
                                    {{ $duracaoMeses }} {{ $duracaoMeses == 1 ? 'mês' : 'meses' }}
                                @endif
                            </div>
                            <div class="small opacity-75">Duração</div>
                        </div>
                        <div class="col-auto text-center border-start border-white border-opacity-25 ps-4">
                            <div class="h4 mb-1">{{ number_format($valorMensal, 2, ',', '.') }} AOA/Mês</div>
                            <div class="small opacity-75">Valor Mensal (média)</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end">
                    <!-- Ícone maior para a seção -->
                    <i class="fas fa-sack-dollar fa-5x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagens de Validação -->
    @if($mensagensValidacao)
        @foreach($mensagensValidacao as $msg)
            <div class="alert alert-{{ $msg['tipo'] }} alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        @if($msg['tipo'] === 'warning')
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        @elseif($msg['tipo'] === 'info')
                            <i class="fas fa-info-circle fa-2x"></i>
                        @elseif($msg['tipo'] === 'success')
                            <i class="fas fa-check-circle fa-2x"></i>
                        @else
                            <i class="fas fa-bell fa-2x"></i>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-2">{{ $msg['titulo'] }}</h6>
                        <p class="mb-2">{{ $msg['mensagem'] }}</p>

                        <!-- Informações da Assinatura Atual -->
                        <!-- Informações da Assinatura Atual (Aprimorado) -->
                        @if(isset($msg['assinatura_info']) && $msg['assinatura_info'])
                            <div class="mt-4 p-4 alert alert-warning border-0 rounded-3 shadow-sm">
                                <h5 class="mb-3 text-warning-emphasis">
                                    <i class="fas fa-history me-2"></i> Detalhes da Assinatura Vigente
                                </h5>
                                
                                <p class="mb-1">
                                    <span class="text-muted small">Pacote:</span>
                                    <strong class="text-dark d-block">{{ $msg['assinatura_info']['pacote_nome'] }}</strong>
                                </p>

                                <p class="mb-1">
                                    <span class="text-muted small">Valor Mensal:</span>
                                    <strong class="text-dark d-block">{{ $msg['assinatura_info']['pacote_preco'] }}/mês</strong>
                                </p>

                                @if($msg['assinatura_info']['data_fim'])
                                <p class="mb-1">
                                    <span class="text-muted small">Válida até:</span>
                                    <strong class="text-dark d-block">{{ $msg['assinatura_info']['data_fim'] }}</strong>
                                </p>
                                @endif
                                
                                <div class="mt-3 pt-3 border-top border-warning-subtle">
                                    <span class="text-muted small me-3">Status:</span>
                                    <span class="badge fs-6 bg-{{ $msg['assinatura_info']['status'] === 'Ativa' ? 'success' : 'danger' }}">
                                        {{ $msg['assinatura_info']['status'] }}
                                    </span>
                                </div>
                            </div>
                        @endif


                        @if(isset($msg['acao_sugerida']))
                            <small class="text-muted">
                                Ação sugerida: <strong>
                                    @switch($msg['acao_sugerida'])
                                        @case('nova_assinatura')
                                            Nova Assinatura
                                            @break
                                        @case('renovar')
                                            Renovar Assinatura
                                            @break
                                        @case('upgrade')
                                            Fazer Upgrade
                                            @break
                                        @default
                                            {{ ucfirst($msg['acao_sugerida']) }}
                                    @endswitch
                                </strong>
                            </small>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforeach
    @endif

    <!-- Formulário Principal e Resumo -->
    <div class="row justify-content-center">
        <!-- Coluna do Formulário -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="section-header text-primary fw-bold mb-4">
                        <i class="fas fa-edit me-2"></i> Detalhes do Pagamento
                    </h3>

                    <form wire:submit="submitPagamento">

                        <!-- Seleção de Igreja (se múltiplas) -->
                        @if($igrejasDisponiveis->count() > 1)
                            <div class="mb-4">
                                <label for="igrejaSelecionada" class="form-label fw-semibold">Igreja para Assinatura</label>
                                <div class="position-relative">
                                    <select id="igrejaSelecionada" class="form-select" wire:model.live="igrejaSelecionada" >
                                        @foreach($igrejasDisponiveis as $igrejaDisp)
                                            <option value="{{ $igrejaDisp['id'] }}">
                                                {{ $igrejaDisp['nome'] }}
                                                @if($igrejaDisp['sigla'])
                                                    ({{ $igrejaDisp['sigla'] }})
                                                @endif
                                                - {{ $igrejaDisp['categoria'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div wire:loading wire:target="igrejaSelecionada" class="position-absolute top-50 end-0 translate-middle-y me-5">
                                        <i class="fas fa-spinner fa-spin text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </i>
                                    </div>
                                </div>
                                @error('igrejaSelecionada')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Duração da Assinatura -->
                        <div class="mb-4">
                            <label for="duracao" class="form-label fw-semibold">Duração da Assinatura</label>
                            <select id="duracao" class="form-select" wire:model.live="duracaoMeses" >
                                @foreach($duracaoOpcoes as $valor => $label)
                                    <option value="{{ $valor }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Seleção do Método de Pagamento -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">Método de Pagamento</label>
                            <div class="row g-3">
                                @foreach($metodosPagamento as $codigo => $metodo)
                                    <!-- Opção de Pagamento -->
                                    <div class="col-sm-6">
                                        <div class="card card-payment-option  @error('metodoPagamento') is-invalid @enderror {{ $metodoPagamento === $codigo ? 'selected' : '' }}"
                                             wire:click="$set('metodoPagamento', '{{ $codigo }}')">
                                            <div class="card-body py-3 text-center">
                                                <i class="fas {{ $metodo['icone'] }} fa-3x {{ $metodo['cor'] }} mb-3"></i>
                                                <p class="mb-0 fw-semibold">{{ $metodo['nome'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('metodoPagamento')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dados da Referência / Conta Bancária (Visível quando método requer referência) -->
                        @if($metodoPagamento && $metodosPagamento[$metodoPagamento]['requer_referencia'])
                            <div id="payment-details-{{ $metodoPagamento }}" class="alert alert-primary p-4 mb-4">
                                <h5 class="fw-bold text-primary"><i class="fas fa-money-check-alt me-2"></i> Dados para {{ $metodosPagamento[$metodoPagamento]['nome'] }}</h5>
                                <p class="mb-1"><strong>Banco:</strong> BAI</p>
                                <p class="mb-1"><strong>
                                    @if($metodosPagamento[$metodoPagamento]['nome'] == 'multicaixa_express')
                                    Nº Multicaixa Express:</strong> 940467779
                                    @else
                                    Nº da conta:</strong> 087687548675453
                                    @endif
                                </p>
                                <p class="mb-1"><strong>IBAN:</strong> AO06.0051.0000.2569.5685.1016.7</p>
                                <p class="mb-0"><strong>Titular:</strong> SEDE DIGITAL PRESTAÇÃO DE SERVIÇOS (SU) LDA</p>
                                <small class="d-block mt-2">Por favor, transfira o valor exato de <strong>{{ number_format($valorTotal, 2, ',', '.') }} AOA</strong>.</small>
                            </div>
                        @endif

                        <!-- Campo de Referência/Comprovativo -->
                        @if($referencia)
                            <div class="mb-4">
                                <label for="referencia" class="form-label fw-semibold">Referência do Pagamento</label>
                                <input type="text" id="referencia" class="form-control bg-white text-dark" wire:model.live="referencia" value="{{ $referencia }}" readonly style="background-color: white !important; color: #212529 !important;">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Esta referência foi gerada automaticamente. Use-a na sua transferência bancária.
                                </div>
                                @error('referencia')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Upload do Comprovativo -->
                        <div class="mb-4">
                            <label for="comprovativo" class="form-label fw-semibold">Comprovativo de Pagamento <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="file" id="comprovativo" class="form-control @error('comprovativo') is-invalid @enderror" wire:model="comprovativo" accept="image/*, application/pdf" wire:loading.attr="disabled" wire:target="comprovativo">
                                <div class="position-absolute top-50 end-0 translate-middle-y pe-3" wire:loading wire:target="comprovativo">
                                    <i class="fas fa-spinner fa-spin text-primary" role="status" aria-hidden="true"></i>
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                            <div class="form-text">
                                Anexe o talão da transferência ou captura de tela do pagamento. Máx. 5MB.
                            </div>
                            @error('comprovativo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <!-- Preview do arquivo -->
                            @if($comprovativo)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file me-3 text-primary"></i>
                                        <div>
                                            <div class="fw-bold">{{ $comprovativo->getClientOriginalName() }}</div>
                                            <small class="text-muted">
                                                {{ number_format($comprovativo->getSize() / 1024, 1) }} KB •
                                                {{ strtoupper($comprovativo->getClientOriginalExtension()) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Observações -->
                        <div class="mb-4">
                            <label for="observacoes" class="form-label fw-semibold">Observações (Opcional)</label>
                            <textarea id="observacoes" class="form-control" rows="3" wire:model="observacoes" placeholder="Adicione qualquer nota importante..." wire:loading.attr="disabled" wire:target="submitPagamento"></textarea>
                            @error('observacoes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botão de Envio -->
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-glow"
                                    wire:loading.attr="disabled"
                                    wire:target="submitPagamento,comprovativo">
                                <span wire:loading.remove wire:target="submitPagamento">
                                    <i class="fas fa-paper-plane me-2"></i> Enviar Comprovativo e Concluir
                                </span>
                                <span wire:loading wire:target="submitPagamento">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Processando...
                                </span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        <!-- Coluna do Resumo (Sticky) -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                <div class="card-body p-4 p-md-5">
                    <h4 class="section-header text-success fw-bold mb-4">
                        <i class="fas fa-receipt me-2"></i> Resumo da Assinatura
                    </h4>
                    <div class="card-summary">
                        <div class="card-summary-item">
                            <span class="fw-medium">Pacote Selecionado:</span>
                            <span class="text-end fw-bold text-dark">{{ $pacote->nome }}</span>
                        </div>
                        <div class="card-summary-item">
                            <span class="fw-medium">Tipo de Transação:</span>
                            <span class="text-end text-success fw-semibold">{{ $acaoFormatada }}</span>
                        </div>
                        <div class="card-summary-item">
                            <span class="fw-medium">Duração:</span>
                            <span class="text-end">
                                @if($duracaoMeses === 'vitalicio')
                                    Vitalício
                                @else
                                    {{ $duracaoMeses }} {{ $duracaoMeses == 1 ? 'mês' : 'meses' }}
                                @endif
                            </span>
                        </div>
                        <div class="card-summary-item">
                            <span class="fw-medium">Preço Base Mensal:</span>
                            <span class="text-end">{{ number_format($valorMensal, 2, ',', '.') }} AOA</span>
                        </div>
                        <div class="card-summary-item">
                            <span class="fw-medium">Desconto por Duração:</span>
                            <span class="text-end text-danger">
                                @if($duracaoMeses === 'vitalicio')
                                    50%
                                @elseif($duracaoMeses == 12)
                                    20%
                                @elseif($duracaoMeses == 6)
                                    10%
                                @elseif($duracaoMeses == 3)
                                    5%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                        <div class="card-summary-item card-summary-total">
                            <span class="fw-bold">TOTAL A PAGAR:</span>
                            <span class="fw-bold">{{ number_format($valorTotal, 2, ',', '.') }} AOA</span>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">A validade da sua assinatura será atualizada após a confirmação do pagamento.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

