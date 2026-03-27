<div>
{{-- Hero Section --}}
<div class="hero-section text-center py-5 mb-5">
    <div class="container">
        <div class="hero-icon mx-auto mb-4">
            <i class="fas fa-rocket fa-3x text-white"></i>
        </div>
        <h1 class="display-4 fw-bold text-white mb-3">{{ config('app.name') }}</h1>
        <p class="lead text-white-50 fs-4">Teste Gratuito por 10 Dias</p>
    </div>
</div>

{{-- Success Message --}}
@if($mostrar_sucesso)
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5 text-center bg-success text-white rounded-3">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </div>
                    <h2 class="h3 fw-bold mb-3">Solicitação Enviada com Sucesso!</h2>
                    <p class="mb-4 fs-5">
                        Sua solicitação de período de teste foi recebida! Em breve você receberá um email confirmando se foi aprovado ou não.
                    </p>

                    <div class="trial-info bg-white text-dark p-4 rounded-3 mb-4">
                        <h3 class="h5 fw-bold mb-3 text-info">📋 Dados da Solicitação</h3>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Nome:</strong> {{ $dados_trial['usuario']['nome'] }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $dados_trial['usuario']['email'] }}</p>
                                <p class="mb-2"><strong>Igreja:</strong> {{ $dados_trial['igreja']['nome'] }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Período Solicitado:</strong> <span class="text-info fw-bold">{{ $dados_trial['periodo']['dias'] }} dias</span></p>
                                <p class="mb-2"><strong>Status:</strong> <span class="badge bg-warning">Aguardando Aprovação</span></p>
                                <p class="mb-2"><strong>Data da Solicitação:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Próximos passos:</strong> Nossa equipe irá analisar sua solicitação e você receberá um email ou mensagem de telemóvel com a decisão em até 24 horas.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Form Section --}}
@if(!$mostrar_sucesso)
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h2 class="h1 fw-bold text-info mb-3">Solicitar Período de Teste</h2>
                        <p class="lead text-muted fs-5">
                            Preencha os dados abaixo para criar sua conta de teste gratuita. Você terá acesso completo a todas as funcionalidades por 10 dias.
                        </p>
                    </div>

                    {{-- Error Messages --}}
                    @if($errors->has('geral'))
                        <div class="alert alert-danger border-0 shadow-sm">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $errors->first('geral') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="solicitarTrial">
                        <div class="row g-4">
                            {{-- Nome --}}
                            <div class="col-md-6">
                                <label for="nome" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2 text-info"></i>Nome Completo *
                                </label>
                                <input
                                    type="text"
                                    id="nome"
                                    wire:model="nome"
                                    class="form-control form-control-lg @error('nome') is-invalid @enderror"
                                    autocomplete="new-password"
                                    placeholder="Digite seu nome completo"
                                >
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2 text-info"></i>Email *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    wire:model="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    autocomplete="new-password"
                                    placeholder="seu@email.com"

                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Senha --}}
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-info"></i>Senha *
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    wire:model="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    autocomplete="new-password"
                                    placeholder="Mínimo 8 caracteres"

                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirmar Senha --}}
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-info"></i>Confirmar Senha *
                                </label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    wire:model="password_confirmation"
                                    class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                    autocomplete="new-password"
                                    placeholder="Digite a senha novamente"

                                >
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nome da Igreja --}}
                            <div class="col-md-6">
                                <label for="igreja_nome" class="form-label fw-semibold">
                                    <i class="fas fa-church me-2 text-info"></i>Nome da Igreja *
                                </label>
                                <input
                                    type="text"
                                    id="igreja_nome"
                                    wire:model="igreja_nome"
                                    class="form-control form-control-lg @error('igreja_nome') is-invalid @enderror"
                                    autocomplete="new-password"
                                    placeholder="Nome da sua igreja"

                                >
                                @error('igreja_nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Denominação --}}
                            <div class="col-md-6">
                                <label for="denominacao" class="form-label fw-semibold">
                                    <i class="fas fa-building me-2 text-info"></i>Denominação *
                                </label>
                                <select
                                    id="denominacao"
                                    wire:model="denominacao"
                                    class="form-select form-select-lg"
                                >
                                    <option value="">Selecione uma denominação</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->nome }}">{{ $categoria->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Telefone --}}
                            <div class="col-md-6">
                                <label for="telefone" class="form-label fw-semibold">
                                    <i class="fas fa-phone me-2 text-info"></i>Telefone *
                                </label>
                                <input
                                    type="tel"
                                    id="telefone"
                                    wire:model="telefone"
                                    class="form-control form-control-lg"
                                    autocomplete="new-password"
                                    placeholder="+244 900 000 000"
                                >
                            </div>

                            {{-- Cidade --}}
                            <div class="col-md-6">
                                <label for="cidade" class="form-label fw-semibold">
                                    <i class="fas fa-map-marker-alt me-2 text-info"></i>Cidade
                                </label>
                                <input
                                    type="text"
                                    id="cidade"
                                    wire:model="cidade"
                                    class="form-control form-control-lg"
                                    autocomplete="new-password"
                                    placeholder="Sua cidade"
                                >
                            </div>

                            {{-- Província --}}
                            <div class="col-md-6">
                                <label for="provincia" class="form-label fw-semibold">
                                    <i class="fas fa-map me-2 text-info"></i>Província
                                </label>
                                <select
                                    id="provincia"
                                    wire:model="provincia"
                                    class="form-select form-select-lg"
                                >
                                    <option value="">Selecione uma província</option>
                                    <option value="Bengo">Bengo</option>
                                    <option value="Benguela">Benguela</option>
                                    <option value="Bié">Bié</option>
                                    <option value="Cabinda">Cabinda</option>
                                    <option value="Cuando">Cuando</option>
                                    <option value="Cubango">Cubango</option>
                                    <option value="Cunene">Cunene</option>
                                    <option value="Huambo">Huambo</option>
                                    <option value="Huíla">Huíla</option>
                                    <option value="Cuanza Norte">Cuanza Norte</option>
                                    <option value="Cuanza Sul">Cuanza Sul</option>
                                    <option value="Ícolo e Bengo">Ícolo e Bengo</option>
                                    <option value="Luanda">Luanda</option>
                                    <option value="Lunda Norte">Lunda Norte</option>
                                    <option value="Lunda Sul">Lunda Sul</option>
                                    <option value="Malanje">Malanje</option>
                                    <option value="Moxico">Moxico</option>
                                    <option value="Moxico Leste">Moxico Leste</option>
                                    <option value="Namibe">Namibe</option>
                                    <option value="Uíge">Uíge</option>
                                    <option value="Zaire">Zaire</option>
                                </select>
                            </div>
                        </div>

                        {{-- Termos de Uso --}}
                        <div class="form-check mt-4 p-3 bg-light rounded-3">
                            <input
                                type="checkbox"
                                id="aceitou_termos"
                                wire:model="aceitou_termos"
                                class="form-check-input"
                            >
                            <label for="aceitou_termos" class="form-check-label fw-semibold">
                                Li e aceito os <a href="#" target="_blank" class="text-info text-decoration-none">Termos de Uso</a> e <a href="#" target="_blank" class="text-info text-decoration-none">Política de Privacidade</a> do OmnIgrejas *
                            </label>
                        </div>
                        @error('aceitou_termos')
                            <div class="text-danger mt-2">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        {{-- Submit Button --}}
                        <div class="text-center mt-5">
                            <button
                                type="submit"
                                class="btn bg-info text-light btn-lg px-5 py-3 fw-bold"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                            >
                                <span wire:loading.remove>
                                    <i class="fas fa-rocket me-2"></i>
                                    Solicitar Teste Gratuito
                                </span>
                                <span wire:loading wire:target='solicitarTrial'>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Processando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Features Section --}}
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h3 class="h1 fw-bold text-info mb-4">O que você terá acesso durante o teste</h3>
                <p class="lead text-muted">Todas as funcionalidades premium por 10 dias completamente grátis</p>
            </div>

            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-users fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Gestão de Membros</h5>
                            <p class="text-muted mb-0">
                                Cadastre e gerencie todos os membros da sua igreja com perfis completos.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-calendar-alt fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Eventos e Cultos</h5>
                            <p class="text-muted mb-0">
                                Agende cultos, eventos e controle escalas de serviço.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-dollar-sign fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Finanças</h5>
                            <p class="text-muted mb-0">
                                Controle dízimos, ofertas e gere relatórios financeiros completos.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-comments fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Comunicação</h5>
                            <p class="text-muted mb-0">
                                Chats, notificações e comunicação direta com os membros.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-graduation-cap fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Cursos Online</h5>
                            <p class="text-muted mb-0">
                                Plataforma completa de cursos e capacitação.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Relatórios</h5>
                            <p class="text-muted mb-0">
                                Dashboards e estatísticas detalhadas da sua igreja.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-handshake fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Alianças</h5>
                            <p class="text-muted mb-0">
                                Conecte-se com outras igrejas e fortaleça a comunidade.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-store fa-2x text-white"></i>
                            </div>
                            <h5 class="card-title fw-bold mb-3">Marketplace</h5>
                            <p class="text-muted mb-0">
                                Venda produtos e alcance mais pessoas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 0 0 50px 50px;
        margin: -2rem -2rem 3rem -2rem !important;
        padding: 4rem 2rem 6rem !important;
    }

    .hero-icon {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .trial-info {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    @media (max-width: 768px) {
        .hero-section {
            margin: -1rem -1rem 2rem -1rem !important;
            padding: 3rem 1rem 4rem !important;
            border-radius: 0 0 30px 30px;
        }

        .display-4 {
            font-size: 2.5rem;
        }

        .lead {
            font-size: 1.25rem;
        }

        .hero-icon {
            width: 80px;
            height: 80px;
        }

        .hero-icon i {
            font-size: 2rem;
        }
    }
</style>



</div>
