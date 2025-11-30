<div>
    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                <div class="card-body z-3 px-md-0 px-lg-4">

                                    <!-- Logo -->
                                    <a href="{{ url('/') }}" wire:navigate class="navbar-brand d-flex align-items-center mb-3">
                                        <div class="logo-main">
                                            <div class="logo-mini">
                                                <img src="{{ asset('system/img/logo-system/icon.png') }}" alt="logo">
                                            </div>
                                            <div class="logo-mini">
                                                <img src="{{ asset('system/img/logo-system/icon.png') }}" alt="logo">
                                            </div>
                                        </div>
                                        <h1 class="logo-title fw-bold">
                                            <span class="text-primary">Omn</span><span class="text-success">Igrejas</span>
                                        </h1>
                                    </a>

                                    <!-- Título -->
                                    <div class="text-center mb-4">
                                        <h2 class="mb-1 d-inline">Verificação de Dois Fatores</h2>
                                        <p class="text-muted small mb-0 d-inline ms-2">
                                            Digite o código do seu app autenticador
                                        </p>
                                    </div>

                                    <!-- Alertas de Status -->
                                    @if(session()->has('status'))
                                        <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                                            <i class="fas fa-check-circle me-2"></i>
                                            {{ session('status') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    @if($emailSent)
                                        <div class="alert alert-info alert-dismissible fade show mb-2" role="alert">
                                            <i class="fas fa-envelope me-2"></i>
                                            Código de recuperação enviado para seu email!
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    @endif

                                    <!-- Dicas sobre os códigos -->
                                    <div class="alert alert-light border mb-3">
                                        <div class="d-flex align-items-start">
                                            <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                                            <div>
                                                <small class="fw-semibold">Código 2FA:</small> <small>6 dígitos do app autenticador</small><br>
                                                <small class="fw-semibold">Código Recuperação:</small> <small>Código longo por email</small><br>
                                                <small class="fw-semibold">Chave Secreta:</small> <small>Apenas para configurar</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Formulário Principal -->
                                    @if(!$showRecoveryCode && !$showEmailOption)
                                    <form wire:submit.prevent="verifyCode">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="code" class="form-label">Código 2FA</label>
                                                    <input type="text"  autocomplete="new-password" autocomplete="new-password"  class="form-control @error('code') is-invalid @enderror"
                                                           id="code" wire:model="code" maxlength="6"
                                                           placeholder="000000" autocomplete="off">
                                                    @error('code')
                                                        <div class="invalid-feedback d-block">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center mt-4">
                                            <button type="submit" class="btn btn-primary bg-primary border-0 d-flex align-items-center"
                                                    wire:loading.attr="disabled" wire:target="verifyCode">
                                                <span wire:loading wire:target="verifyCode" class="spinner-border spinner-border-sm me-2"></span>
                                                <i class="fas fa-sign-in-alt me-2"></i>Verificar e Entrar
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Opções Alternativas -->
                                    <div class="text-center mt-3">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <button class="btn btn-info bg-info btn-sm w-100" wire:click="toggleRecoveryCode">
                                                    <i class="fas fa-key me-1"></i>Usar Código de Recuperação
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <button class="btn btn-warning bg-warning btn-sm w-100" wire:click="enableEmailOption">
                                                    <i class="fas fa-envelope me-1"></i>Esqueci meu código
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @elseif($showRecoveryCode)
                                    <!-- Formulário Código de Recuperação -->
                                    <form wire:submit.prevent="verifyCode">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="recovery_code" class="form-label">Código de Recuperação</label>
                                                    <input type="text"  autocomplete="new-password" autocomplete="new-password"  class="form-control @error('recovery_code') is-invalid @enderror"
                                                           id="recovery_code" wire:model="recovery_code"
                                                           placeholder="Digite o código de recuperação" autocomplete="off">
                                                    @error('recovery_code')
                                                        <div class="invalid-feedback d-block">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center mt-4">
                                            <button type="submit" class="btn btn-success bg-success border-0 d-flex align-items-center"
                                                    wire:loading.attr="disabled" wire:target="verifyCode">
                                                <span wire:loading wire:target="verifyCode" class="spinner-border spinner-border-sm me-2"></span>
                                                <i class="fas fa-check-circle me-2"></i>Verificar Código
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Voltar -->
                                    <div class="text-center mt-3">
                                        <button class="btn btn-link text-muted" wire:click="backToMain">
                                            <i class="fas fa-arrow-left me-1"></i>Voltar para Código 2FA
                                        </button>
                                    </div>

                                    @elseif($showEmailOption)
                                    <!-- Opção de Envio por Email -->
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <i class="fas fa-envelope-open-text text-primary" style="font-size: 4rem;"></i>
                                        </div>
                                        <h5 class="mb-3">Enviar Código por Email</h5>
                                        <p class="text-muted mb-4">
                                            Enviaremos um código de recuperação para o email:
                                            <strong>{{ Auth::user()?->email }}</strong>
                                        </p>

                                        <div class="d-grid gap-2">
                                            @error('email')
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                </div>
                                            @enderror

                                            <button class="btn btn-primary" wire:click="sendRecoveryCodeByEmail"
                                                    wire:loading.attr="disabled" wire:target="sendRecoveryCodeByEmail">
                                                <span wire:loading wire:target="sendRecoveryCodeByEmail" class="spinner-border spinner-border-sm me-2"></span>
                                                <i class="fas fa-paper-plane me-1"></i>Enviar Código por Email
                                            </button>
                                            <button class="btn btn-secondary" wire:click="backToMain">
                                                <i class="fas fa-arrow-left me-1"></i>Voltar
                                            </button>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Botão Cancelar/Logout -->
                                    <div class="text-center mt-3 pt-2 border-top">
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger bg-danger btn-sm">
                                                <i class="fas fa-sign-out-alt me-1"></i>Cancelar e Fazer Logout
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sign-bg">
                        <img src="{{ asset('system/img/logo-system/icon.png') }}"
                            alt="logo"
                            class="img-fluid opacity-75"
                            width="400" height="330"
                            style="max-width: 200px; max-height: 200px;" >

                    </div>
                </div>

                <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                    <img src="{{ asset('assets/images/auth/01.png') }}" class="img-fluid gradient-main animated-scaleX" alt="images">
                </div>
            </div>
        </section>
    </div>

    <!-- Estilos para mensagens de erro -->
    <style>
        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback i {
            font-size: 0.75rem;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>

    <!-- Script para foco automático no input -->
    <script>
        document.addEventListener('livewire:loaded', () => {
            // Focar no input apropriado baseado no estado atual
            const focusInput = () => {
                if (!@this.showRecoveryCode && !@this.showEmailOption) {
                    document.getElementById('code')?.focus();
                } else if (@this.showRecoveryCode) {
                    document.getElementById('recovery_code')?.focus();
                }
            };

            // Focar quando o componente carregar
            focusInput();
        });
    </script>
</div>
