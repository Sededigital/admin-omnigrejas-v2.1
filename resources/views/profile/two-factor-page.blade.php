<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-shield-alt me-2"></i>Autenticação de Dois Fatores (2FA)
                        </h1>
                        <p class="mb-0 text-muted">Proteja sua conta com uma camada extra de segurança</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button class="btn btn-outline-secondary" wire:navigate href="{{ route('profile.show') }}">
                            <i class="fas fa-arrow-left me-1"></i>Voltar ao Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status do 2FA -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header border-0 position-relative overflow-hidden"
                 style="background: @if($hasTwoFactor) linear-gradient(135deg, #28a745 0%, #20c997 100%) @else linear-gradient(135deg, #6c757d 0%, #495057 100%) @endif;">
                <div class="position-absolute top-0 end-0 opacity-25">
                    @if($hasTwoFactor)
                        <i class="fas fa-shield-alt fs-1 me-3 mt-2"></i>
                    @else
                        <i class="fas fa-shield fs-1 me-3 mt-2"></i>
                    @endif
                </div>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center text-white">
                            <div class="me-3">
                                @if($hasTwoFactor)
                                    <i class="fas fa-check-circle fs-2"></i>
                                @else
                                    <i class="fas fa-times-circle fs-2"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="mb-1 fw-bold">
                                    @if($hasTwoFactor)
                                        <i class="fas fa-shield-alt me-2"></i>2FA Ativado
                                    @else
                                        <i class="fas fa-shield me-2"></i>2FA Desativado
                                    @endif
                                </h4>
                                <p class="mb-0 opacity-75">
                                    @if($hasTwoFactor)
                                        ✅ Sua conta está protegida com autenticação de dois fatores
                                    @else
                                        ⚠️ Adicione uma camada extra de segurança à sua conta
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        @if($hasTwoFactor)
                            <button class="btn btn-light text-danger fw-semibold" data-bs-toggle="modal" data-bs-target="#disable2FAModal">
                                <i class="fas fa-times me-1"></i>Desativar 2FA
                            </button>
                        @else
                            <button class="btn btn-light text-primary fw-semibold" wire:click="generateNewSecret">
                                <i class="fas fa-play me-1"></i>Ativar Agora
                            </button>
                        @endif
                    </div>
                </div>
                <div class="progress mt-3" style="height: 4px;">
                    <div class="progress-bar @if($hasTwoFactor) bg-success @else bg-secondary @endif"
                         role="progressbar" style="width: @if($hasTwoFactor) 100% @else 0% @endif"></div>
                </div>
            </div>
        </div>

        @if(!$hasTwoFactor)
        <!-- Dicas para Ativar 2FA -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-gradient-info text-white border-0 position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 opacity-25">
                    <i class="fas fa-shield-alt fs-1 me-3 mt-2"></i>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-lightbulb fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">
                            <i class="fas fa-route me-2"></i>Como Ativar o 2FA - Passo a Passo
                        </h5>
                        <p class="mb-0 small opacity-75">
                            Siga estes passos simples para proteger sua conta
                        </p>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 3px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 25%"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h6 class="text-primary mb-3 fw-bold">
                            <i class="fas fa-list-check me-2"></i>Passos para Configuração:
                        </h6>
                        <div class="timeline">
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-success">
                                    <i class="fas fa-download text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">1. Instale um Aplicativo Autenticador</h6>
                                    <p class="text-muted small mb-0">
                                        Baixe e instale um app como Google Authenticator, Authy ou Microsoft Authenticator
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-primary">
                                    <i class="fas fa-qrcode text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">2. Escaneie o QR Code</h6>
                                    <p class="text-muted small mb-0">
                                        Abra o app e escaneie o código QR ou digite a chave secreta manualmente
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item mb-3">
                                <div class="timeline-marker bg-info">
                                    <i class="fas fa-cogs text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">3. Escolha o Método de Ativação</h6>
                                    <p class="text-muted small mb-0">
                                        Digite o código de 6 dígitos ou use a chave secreta completa
                                    </p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning">
                                    <i class="fas fa-save text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">4. Ative e Salve os Códigos</h6>
                                    <p class="text-muted small mb-0">
                                        Clique em "Ativar 2FA" e guarde os códigos de recuperação em local seguro
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-primary mb-3 fw-bold">
                            <i class="fas fa-mobile-alt me-2"></i>Apps Recomendados:
                        </h6>
                        <div class="d-grid gap-2">
                            <div class="app-card p-3 border rounded text-center bg-light">
                                <i class="fab fa-google text-danger fs-2 mb-2"></i>
                                <div class="fw-semibold">Google Authenticator</div>
                                <small class="text-muted">Grátis e confiável</small>
                            </div>
                            <div class="app-card p-3 border rounded text-center bg-light">
                                <i class="fas fa-mobile-alt text-primary fs-2 mb-2"></i>
                                <div class="fw-semibold">Authy</div>
                                <small class="text-muted">Backup na nuvem</small>
                            </div>
                            <div class="app-card p-3 border rounded text-center bg-light">
                                <i class="fab fa-microsoft text-info fs-2 mb-2"></i>
                                <div class="fw-semibold">Microsoft Authenticator</div>
                                <small class="text-muted">Integração Microsoft</small>
                            </div>
                        </div>
                        <div class="alert alert-light border mt-3">
                            <i class="fas fa-info-circle text-info me-2"></i>
                            <small>Todos os apps são gratuitos e seguros</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuração do 2FA -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-cogs text-primary fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">
                            <i class="fas fa-shield-alt me-2"></i>Configuração da Autenticação 2FA
                        </h5>
                        <p class="mb-0 small text-muted">
                            Configure sua autenticação de dois fatores para maior segurança
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($showQrCode)
                <!-- QR Code e Chave Secreta -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-center">
                            <h6 class="mb-3">Escaneie o QR Code</h6>
                            <div class="border rounded p-3 bg-light">
                                {!! $qrCode !!}
                            </div>
                            <small class="text-muted mt-2 d-block">
                                Abra seu aplicativo autenticador e escaneie este código
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Chave Secreta</label>
                            <div class="input-group">
                                <input type="text"  autocomplete="new-password" class="form-control" value="{{ $secretKey }}" readonly id="secretKey">
                                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $secretKey }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                Você também pode digitar esta chave manualmente no aplicativo
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Método de Ativação -->
                <div class="mb-4">
                    <h6 class="mb-3">Escolha como ativar o 2FA:</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="activationMethod" id="codeMethod"
                                       wire:model.live="activationMethod" value="code" checked>
                                <label class="form-check-label fw-semibold" for="codeMethod">
                                    <i class="fas fa-mobile-alt me-1"></i>Código de Verificação
                                </label>
                                <br>
                                <small class="text-muted">Digite o código de 6 dígitos do seu aplicativo</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="activationMethod" id="secretMethod"
                                       wire:model.live="activationMethod" value="secret">
                                <label class="form-check-label fw-semibold" for="secretMethod">
                                    <i class="fas fa-key me-1"></i>Chave Secreta
                                </label>
                                <br>
                                <small class="text-muted">Digite a chave secreta completa</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulário de Ativação -->
                <form wire:submit.prevent="enableTwoFactor">
                    <div class="row g-3">
                        @if($activationMethod === 'code')
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Código de Verificação *</label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('code') is-invalid @enderror"
                                   wire:model="code" placeholder="000000" maxlength="6">
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Digite o código de 6 dígitos do seu aplicativo autenticador</small>
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chave Secreta *</label>
                            <input type="text"  autocomplete="new-password" class="form-control @error('secretKeyInput') is-invalid @enderror"
                                   wire:model="secretKeyInput" placeholder="Digite a chave secreta">
                            @error('secretKeyInput') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Digite exatamente a chave secreta mostrada acima</small>
                        </div>
                        @endif

                        <div class="col-12">
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-shield-alt me-2"></i>Ativar 2FA
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>Ativando...
                                </span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary ms-2" wire:click="generateNewSecret">
                                <i class="fas fa-refresh me-1"></i>Gerar Nova Chave
                            </button>
                        </div>
                    </div>
                </form>
                @else
                <!-- Botão para Iniciar Configuração -->
                <div class="text-center py-4">
                    <button class="btn btn-primary btn-lg" wire:click="generateNewSecret">
                        <i class="fas fa-qrcode me-2"></i>Iniciar Configuração do 2FA
                    </button>
                    <p class="text-muted mt-2 mb-0">
                        Clique para gerar o QR Code e começar a configuração
                    </p>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($hasTwoFactor)
        <!-- Códigos de Recuperação -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-key text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-warning">
                            <i class="fas fa-shield-alt me-2"></i>Códigos de Recuperação
                        </h5>
                        <p class="mb-0 small text-muted">
                            Códigos de backup para emergências
                        </p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm" wire:click="downloadRecoveryCodesPDF"
                            wire:loading.attr="disabled" wire:target="downloadRecoveryCodesPDF">
                        <span wire:loading.remove wire:target="downloadRecoveryCodesPDF">
                            <i class="fas fa-download me-1"></i>Baixar PDF
                        </span>
                        <span wire:loading wire:target="downloadRecoveryCodesPDF">
                            <i class="fas fa-spinner fa-spin me-1"></i>Gerando PDF...
                        </span>
                    </button>
                    <button class="btn btn-outline-warning btn-sm" wire:click="generateNewRecoveryCodes"
                            wire:loading.attr="disabled" wire:target="generateNewRecoveryCodes">
                        <span wire:loading.remove wire:target="generateNewRecoveryCodes">
                            <i class="fas fa-refresh me-1"></i>Gerar Novos
                        </span>
                        <span wire:loading wire:target="generateNewRecoveryCodes">
                            <i class="fas fa-spinner fa-spin me-1"></i>Gerando...
                        </span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($showRecoveryCodes || !empty($recoveryCodes))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Importante:</strong> Salve estes códigos em um local seguro.
                    Eles serão necessários se você perder acesso ao seu aplicativo autenticador.
                </div>

                <div class="row">
                    @foreach($recoveryCodes as $index => $code)
                    <div class="col-md-3 col-sm-6 mb-2">
                        <div class="input-group">
                            <input type="text"  autocomplete="new-password" class="form-control text-center fw-bold" value="{{ $code }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $code }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Cada código pode ser usado apenas uma vez. Guarde-os em local seguro.
                    </small>
                </div>
                @else
                <div class="text-center py-4">
                    <button class="btn btn-outline-info" wire:click="generateNewRecoveryCodes">
                        <i class="fas fa-eye me-1"></i>Mostrar Códigos de Recuperação
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Informações de Segurança -->
        <div class="card shadow-sm">
            <div class="card-header bg-light border-0">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-info-circle text-info fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-info">
                            <i class="fas fa-shield-alt me-2"></i>Informações de Segurança
                        </h5>
                        <p class="mb-0 small text-muted">
                            Saiba mais sobre os benefícios e cuidados com o 2FA
                        </p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success mb-2">
                            <i class="fas fa-check-circle me-1"></i>Benefícios do 2FA:
                        </h6>
                        <ul class="small mb-0">
                            <li>Proteção extra contra acesso não autorizado</li>
                            <li>Códigos únicos gerados a cada 30 segundos</li>
                            <li>Compatível com diversos aplicativos</li>
                            <li>Códigos de recuperação para emergências</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-exclamation-triangle me-1"></i>Lembretes:
                        </h6>
                        <ul class="small mb-0">
                            <li>Não compartilhe seus códigos com ninguém</li>
                            <li>Guarde os códigos de recuperação em local seguro</li>
                            <li>Mantenha seu aplicativo autenticador atualizado</li>
                            <li>Configure backup do aplicativo se possível</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal de Confirmação para Desativar 2FA -->
    <div class="modal fade" id="disable2FAModal" tabindex="-1" aria-labelledby="disable2FAModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold" id="disable2FAModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Desativar Autenticação 2FA
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-shield-alt text-danger fs-2"></i>
                        </div>
                        <h5 class="text-danger mb-3">Atenção! Ação Irreversível</h5>
                        <p class="text-muted mb-4">
                            Você está prestes a desativar a autenticação de dois fatores da sua conta.
                            Esta ação reduzirá significativamente a segurança da sua conta.
                        </p>
                    </div>

                    <div class="alert alert-danger border-danger">
                        <div class="d-flex">
                            <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                            <div>
                                <strong>Impactos da desativação:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Sua conta ficará vulnerável a acessos não autorizados</li>
                                    <li>Você perderá a proteção extra de segurança</li>
                                    <li>Os códigos de recuperação serão removidos permanentemente</li>
                                    <li>Recomendamos manter o 2FA ativado</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="confirmDisable" wire:model.live="confirmDisable">
                        <label class="form-check-label fw-semibold" for="confirmDisable">
                            <i class="fas fa-check-circle text-danger me-1"></i>
                            Entendo os riscos e desejo desativar o 2FA mesmo assim
                        </label>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="disableTwoFactor"
                            wire:loading.attr="disabled" @if($confirmDisable == false) disabled @endif>
                        <span wire:loading.remove>
                            <i class="fas fa-times me-2"></i>Desativar 2FA
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-2"></i>Desativando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos Personalizados -->
    <style>
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #17a2b8, #6c757d);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #17a2b8;
        }

        .app-card {
            transition: all 0.3s ease;
        }

        .app-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .progress {
            background-color: rgba(255,255,255,0.3);
        }

        .progress-bar {
            background: linear-gradient(90deg, #ffc107, #fd7e14);
        }

        /* Estilos para o PDF */
        .pdf-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }

        .recovery-codes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .recovery-code-item {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
        }

        .recovery-code-number {
            color: #6c757d;
            margin-right: 10px;
            font-weight: normal;
        }

        .recovery-code-value {
            color: #007bff;
            letter-spacing: 2px;
        }

        .pdf-footer {
            color: #6c757d;
            font-size: 12px;
        }

        /* Responsividade para PDF */
        @media print {
            .recovery-codes-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .recovery-code-item {
                break-inside: avoid;
            }
        }
    </style>

    <!-- Template oculto para PDF dos códigos de recuperação -->
    <div id="recoveryCodesPDF" class="d-none">
        <div class="pdf-header text-center mb-4">
            <h2 class="text-primary mb-2">
                <i class="fas fa-shield-alt me-2"></i>Códigos de Recuperação 2FA
            </h2>
            <p class="text-muted mb-0">Guarde estes códigos em local seguro</p>
            <hr class="my-4">
        </div>

        <div class="pdf-content">
            <div class="row mb-4">
                <div class="col-6">
                    <strong>Usuário:</strong> {{ Auth::user()->name }}
                </div>
                <div class="col-6 text-end">
                    <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Importante:</strong> Estes códigos podem ser usados apenas uma vez cada.
                Guarde-os em local seguro e não os compartilhe com ninguém.
            </div>

            <h4 class="mb-3">Seus Códigos de Recuperação:</h4>

            <div class="recovery-codes-grid mb-4">
                @if($showRecoveryCodes || !empty($recoveryCodes))
                    @foreach($recoveryCodes as $index => $code)
                        <div class="recovery-code-item">
                            <span class="recovery-code-number">{{ $index + 1 }}.</span>
                            <span class="recovery-code-value">{{ $code }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="alert alert-info">
                <h5><i class="fas fa-info-circle me-2"></i>Instruções de Uso:</h5>
                <ol class="mb-0">
                    <li>Guarde este documento em local seguro (impressora, cofre digital, etc.)</li>
                    <li>Use um código por vez apenas quando necessário</li>
                    <li>Após usar um código, risque-o da lista</li>
                    <li>Se perder acesso ao aplicativo autenticador, use estes códigos</li>
                    <li>Recomendamos gerar novos códigos após usar alguns deles</li>
                </ol>
            </div>

            <div class="pdf-footer text-center mt-5 pt-4 border-top">
                <small class="text-muted">
                    Sistema de Autenticação 2FA - {{ config('app.name') }}<br>
                    Gerado em {{ now()->format('d/m/Y \à\s H:i') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Feedback visual poderia ser adicionado aqui
                console.log('Texto copiado: ' + text);
            }).catch(function(err) {
                console.error('Erro ao copiar: ', err);
            });
        }

        async function downloadRecoveryCodesPDF() {
            const { jsPDF } = window.jspdf;
            const pdfElement = document.getElementById('recoveryCodesPDF');

            // Temporariamente mostrar o elemento para captura
            pdfElement.classList.remove('d-none');

            try {
                // Capturar o conteúdo como imagem
                const canvas = await html2canvas(pdfElement, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff'
                });

                // Criar PDF
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');

                // Calcular dimensões para caber na página
                const imgWidth = 210; // Largura A4 em mm
                const pageHeight = 295; // Altura A4 em mm
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                let heightLeft = imgHeight;

                let position = 0;

                // Adicionar primeira página
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;

                // Adicionar páginas adicionais se necessário
                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                // Salvar PDF
                const fileName = 'codigos-recuperacao-2fa-' + new Date().toISOString().split('T')[0] + '.pdf';
                pdf.save(fileName);

                // Esconder novamente o elemento
                pdfElement.classList.add('d-none');

                // Dispatch de sucesso via Livewire
                @this.dispatch('toast', {
                    type: 'success',
                    message: 'PDF dos códigos de recuperação foi baixado com sucesso!'
                });

            } catch (error) {
                console.error('Erro ao gerar PDF:', error);

                // Dispatch de erro via Livewire
                @this.dispatch('toast', {
                    type: 'error',
                    message: 'Erro ao gerar PDF. Tente novamente.'
                });

                // Esconder o elemento em caso de erro
                pdfElement.classList.add('d-none');
            }
        }

        // Resetar checkbox do modal quando for fechado
        document.getElementById('disable2FAModal')?.addEventListener('hidden.bs.modal', function () {
            // Resetar o checkbox via Livewire
            @this.set('confirmDisable', false);
        });

        // Listener para gerar PDF via Livewire
        document.addEventListener('livewire:loaded', () => {
            Livewire.on('generate-pdf', () => {
                downloadRecoveryCodesPDF();
            });
        });
    </script>
</div>
