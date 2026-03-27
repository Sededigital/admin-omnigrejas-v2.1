<div>
    {{-- Card de Boas-vindas (mantido como está) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-60 rounded-circle me-3 d-flex align-items-center justify-content-center overflow-hidden border border-white border-2">
                                    @if(Auth::user()->photo_url)
                                        <img src="{{ Storage::disk('supabase')->url(Auth::user()->photo_url) }}"
                                             alt="{{ Auth::user()->name }}"
                                             class="w-100 h-100 object-fit-cover">
                                    @else
                                        <span class="text-white fw-bold fs-4">
                                            {{ substr(Auth::user()->name, 0, 1) . substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1) }}
                                        </span>
                                    @endif
                                </div>
                                @php
                                    use Illuminate\Support\Facades\Storage;
                                @endphp

                                <div>
                                    <h2 class="text-white mb-1 fw-bold">Olá, {{ Auth::user()->name }}! 👋</h2>
                                    <p class="text-white-50 mb-0 fs-5">Bem-vindo ao seu painel <strong>{{ $funcaoMembro ?? 'membro' }}</strong></p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ri-building-line text-white-75 me-2"></i>
                                <span class="text-white"><strong>{{ $igreja?->nome ?? 'Igreja não selecionada' }}</strong></span>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                            <div class="d-flex flex-column align-items-lg-end">
                                <small class="text-white-75 mb-1">Último acesso</small>
                                <span class="text-white fw-semibold">{{ now()->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dashboard Principal --}}
    <div class="row mb-4">
        {{-- Atividades Recentes --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Atividades Recentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        {{-- Eventos Próximos --}}
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                    <h6 class="mb-0">Próximos Eventos</h6>
                                </div>
                                @if($proximosEventos->count() > 0)
                                    <div class="small">
                                        @foreach($proximosEventos->take(2) as $evento)
                                            <div class="mb-2">
                                                <strong>{{ $evento->titulo }}</strong><br>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m') }}
                                                    @if($evento->hora_inicio)
                                                        às {{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}
                                                    @endif
                                                </small>
                                            </div>
                                        @endforeach
                                        @if($proximosEventos->count() > 2)
                                            <small class="text-muted">+{{ $proximosEventos->count() - 2 }} mais...</small>
                                        @endif
                                    </div>
                                @else
                                    <small class="text-muted">Nenhum evento próximo</small>
                                @endif
                            </div>
                        </div>

                        {{-- Minhas Escalas --}}
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-success me-2"></i>
                                    <h6 class="mb-0">Minhas Escalas</h6>
                                </div>
                                @if($minhasProximasEscalas->count() > 0)
                                    <div class="small">
                                        @foreach($minhasProximasEscalas->take(2) as $escala)
                                            <div class="mb-2">
                                                <strong>{{ $escala->evento?->titulo ?? 'Evento' }}</strong><br>
                                                <small class="text-muted">
                                                    {{ $escala->funcao }} - {{ $escala->evento ? \Carbon\Carbon::parse($escala->evento->data_evento)->format('d/m') : 'Data N/A' }}
                                                </small>
                                            </div>
                                        @endforeach
                                        @if($minhasProximasEscalas->count() > 2)
                                            <small class="text-muted">+{{ $minhasProximasEscalas->count() - 2 }} mais...</small>
                                        @endif
                                    </div>
                                @else
                                    <small class="text-muted">Nenhuma escala próxima</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estatísticas Rápidas --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-info">{{ $totalEventos }}</div>
                                <small class="text-muted">Eventos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-success">{{ $minhasEscalas }}</div>
                                <small class="text-muted">Escalas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-warning">{{ $pedidosOracao }}</div>
                                <small class="text-muted">Orações</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="fs-4 fw-bold text-info">{{ $pontosEngajamento }}</div>
                                <small class="text-muted">Pontos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Seção de Informações --}}
    <div class="row">
        {{-- Informações Pessoais --}}
        @if($membro)
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Minhas Informações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Cargo</small>
                            <strong>{{ ucfirst($membro->cargo) }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge {{ $membro->status === 'ativo' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($membro->status) }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Data de Entrada</small>
                            <strong>{{ \Carbon\Carbon::parse($membro->data_entrada)->format('d/m/Y') }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Nº Membro</small>
                            <strong>{{ $membro->numero_membro ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Cursos e Notificações --}}
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Aprendizado & Comunicação
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-book-open fs-3 text-info mb-1"></i>
                                <div class="fw-bold">{{ $cursosAtivos }}</div>
                                <small class="text-muted">Cursos Ativos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-bell fs-3 text-warning mb-1"></i>
                                <div class="fw-bold">{{ $notificacoesNaoLidas }}</div>
                                <small class="text-muted">Não Lidas</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="text-center p-2 border rounded">
                                <i class="fas fa-newspaper fs-3 text-info mb-1"></i>
                                <div class="fw-bold">{{ $postsRecentes }}</div>
                                <small class="text-muted">Posts Recentes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notificações Recentes --}}
    @if($ultimasNotificacoes->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Notificações Recentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($ultimasNotificacoes->take(3) as $notificacao)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 {{ !$notificacao->lida ? 'border-primary' : '' }}">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $notificacao->titulo }}</h6>
                                        <p class="mb-2 small text-muted">{{ Str::limit($notificacao->mensagem ?? '', 80) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($notificacao->created_at)->diffForHumans() }}
                                            </small>
                                            @if(!$notificacao->lida)
                                                <span class="badge bg-info text-lightry">Nova</span>
                                            @endif
                                        </div>
                                        @if(!$notificacao->lida)
                                            <button
                                                wire:click="marcarNotificacaoComoLida({{ $notificacao->id }})"
                                                class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-check me-1"></i>
                                                Marcar como Lida
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
