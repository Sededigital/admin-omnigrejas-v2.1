<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-chart-bar me-2"></i>Estatísticas da Igreja
                        </h1>
                        <p class="mb-0 text-muted">Acompanhe os dados e métricas da sua igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group">
                            <a href="#" class="btn btn-outline-primary btn-sm" wire:click.prevent="exportReport" wire:loading.attr="disabled" wire:target="exportReport">
                                <i class="fas fa-download me-1"></i>
                                <span wire:loading.remove wire:target="exportReport">Exportar PDF</span>
                                <span wire:loading wire:target="exportReport">Gerando...</span>
                            </a>
                            <button class="btn btn-primary btn-sm" wire:click="refreshData">
                                <i class="fas fa-sync-alt me-1"></i>Atualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas Principais -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $stats['total_members'] ?? 0 }}</div>
                        <div class="text-muted small">Total de Membros</div>
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>{{ $stats['members_growth'] ?? '+0' }}% este mês
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-user-check text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['active_members'] ?? 0 }}</div>
                        <div class="text-muted small">Membros Ativos</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                {{ $stats['active_percentage'] ?? 0 }}% do total
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-check text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['events_this_month'] ?? 0 }}</div>
                        <div class="text-muted small">Eventos Este Mês</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                {{ $stats['avg_attendance'] ?? 0 }} participantes/evento
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-graduation-cap text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['active_courses'] ?? 0 }}</div>
                        <div class="text-muted small">Cursos Ativos</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                {{ $stats['total_students'] ?? 0 }} alunos matriculados
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Novas Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-secondary metric-card">
                    <div class="card-body">
                        <i class="fas fa-retweet text-secondary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-secondary">{{ $stats['retention_rate'] ?? 0 }}%</div>
                        <div class="text-muted small">Taxa de Retenção</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Membros que permanecem ativos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-dark metric-card">
                    <div class="card-body">
                        <i class="fas fa-birthday-cake text-dark display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-dark">{{ $stats['average_age'] ?? 0 }}</div>
                        <div class="text-muted small">Idade Média</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Anos de idade
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-danger metric-card">
                    <div class="card-body">
                        <i class="fas fa-cross text-danger display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-danger">{{ $stats['conversion_rate'] ?? 0 }}%</div>
                        <div class="text-muted small">Taxa de Conversão</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Batismos/decisões
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-pink metric-card">
                    <div class="card-body">
                        <i class="fas fa-birthday-cake text-pink display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-pink">{{ count($stats['birthdays_this_month'] ?? []) }}</div>
                        <div class="text-muted small">Aniversariantes</div>
                        <div class="mt-2">
                            <small class="text-muted">
                                Este mês
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de Período -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Período</label>
                        <select class="form-select" wire:model.live="selectedPeriod">
                            <option value="7">Últimos 7 dias</option>
                            <option value="30">Últimos 30 dias</option>
                            <option value="90">Últimos 3 meses</option>
                            <option value="365">Último ano</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    @if($selectedPeriod === 'custom')
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Início</label>
                        <input type="date" class="form-control" wire:model.live="startDate">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Fim</label>
                        <input type="date" class="form-control" wire:model.live="endDate">
                    </div>
                    @endif
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tamanho da Folha</label>
                        <div class="d-flex align-items-center">
                            <select class="form-select" wire:model.live="paperSize">
                                <option value="a4">A4 (Padrão)</option>
                                <option value="a3">A3 (Maior - Menos Páginas)</option>
                            </select>
                            <div class="ms-2" style="width: 20px;">
                                @if($paperSize === 'a3')
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="applyFilters">
                                <i class="fas fa-filter me-1"></i>Aplicar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Estatísticas -->
        <div class="row g-4 mb-4">
            <!-- Crescimento de Membros -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Crescimento de Membros
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="membersGrowthChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribuição por Cargo -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-pie me-2"></i>Distribuição por Cargo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="rolesChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Detalhadas -->
        <div class="row g-4 mb-4">
            <!-- Distribuição por Gênero -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-venus-mars me-2"></i>Distribuição por Gênero
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="genderChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faixas Etárias -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-chart-bar me-2"></i>Faixas Etárias
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="ageGroupsChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Engajamento no Chat -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-comments me-2"></i>Engajamento no Chat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="chatActivityChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas Detalhadas - Parte 2 -->
        <div class="row g-4 mb-4">
            <!-- Frequência de Eventos -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Frequência de Eventos
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="eventsChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Engajamento por Ministério -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-hands-helping me-2"></i>Engajamento por Ministério
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="ministriesChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabelas de Dados -->
        <div class="row g-4">
            <!-- Aniversariantes do Mês -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-birthday-cake me-2"></i>Aniversariantes do Mês
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Dia</th>
                                        <th>Idade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['birthdays_this_month'] ?? [] as $birthday)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar bg-dark text-white me-3">
                                                    <i class="fas fa-birthday-cake"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $birthday['nome'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark text-light">{{ $birthday['dia'] }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $birthday['idade'] }} anos</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            Nenhum aniversariante este mês
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Membros Mais Ativos -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-star me-2"></i>Membros Mais Ativos
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Membro</th>
                                        <th>Pontos</th>
                                        <th>Atividades</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topMembers ?? [] as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar text-white me-3">
                                                    @if($member->user->photo_url)
                                                    <img src="{{ Storage::disk('supabase')->url($member->user->photo_url) }}"
                                                        class="w-100 h-100 rounded-circle" alt="">
                                                    @else
                                                    {{ strtoupper(substr($member->user->name ?? 'M', 0, 2)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $member->user->name ?? 'Nome do Membro' }}</div>
                                                    <small class="text-muted">{{ $member->cargo ?? 'Membro' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $member->total_points ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $member->activities_count ?? 0 }} atividades</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            Nenhum dado disponível
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Eventos Mais Populares -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-trophy me-2"></i>Eventos Mais Populares
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Evento</th>
                                        <th>Participantes</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($popularEvents ?? [] as $event)
                                    <tr>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $event->titulo ?? 'Nome do Evento' }}</div>
                                                <small class="text-muted">{{ $event->tipo ?? 'Tipo' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $event->participants_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $event->data_evento ? $event->data_evento->format('d/m/Y') : 'Data não definida' }}</small>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">
                                            Nenhum evento encontrado
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo Financeiro -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 text-primary">
                            <i class="fas fa-coins me-2"></i>Resumo Financeiro por Categoria
                        </h5>
                        <small class="text-muted">Dados do período selecionado</small>
                    </div>
                    <div class="card-body">
                        <!-- Gráfico de Receitas vs Despesas por Categoria -->
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0 text-primary">
                                            <i class="fas fa-chart-bar me-2"></i>Receitas e Despesas por Categoria
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container" style="height: 300px;">
                                            <canvas id="financialCategoriesChart" wire:ignore></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fw-bold h5 text-success">{{ $financialSummary['income'] ?? '0,00 AOA' }}</div>
                                    <small class="text-muted">Receitas</small>
                                    <div class="mt-1">
                                        <small class="text-success">
                                            <i class="fas fa-arrow-up me-1"></i>Entradas
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fw-bold h5 text-danger">{{ $financialSummary['expenses'] ?? '0,00 AOA' }}</div>
                                    <small class="text-muted">Despesas</small>
                                    <div class="mt-1">
                                        <small class="text-danger">
                                            <i class="fas fa-arrow-down me-1"></i>Saídas
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fw-bold h5 {{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $financialSummary['balance'] ?? '0,00 AOA' }}
                                    </div>
                                    <small class="text-muted">Saldo</small>
                                    <div class="mt-1">
                                        <small class="{{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-{{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? 'plus' : 'minus' }} me-1"></i>
                                            {{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? 'Positivo' : 'Negativo' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="fw-bold h5 text-info">{{ $financialSummary['donations'] ?? '0,00 AOA' }}</div>
                                    <small class="text-muted">Doações</small>
                                    <div class="mt-1">
                                        <small class="text-info">
                                            <i class="fas fa-heart me-1"></i>Ofertas
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhes por categoria (colapsável) -->
                        <div class="mt-4">
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#financialDetails" aria-expanded="false">
                                <i class="fas fa-chart-bar me-1"></i>Ver Detalhes por Categoria
                            </button>

                            <div class="collapse mt-3" id="financialDetails">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Categoria</th>
                                                <th class="text-end">Entradas</th>
                                                <th class="text-end">Saídas</th>
                                                <th class="text-end">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $categorias = \App\Models\Financeiro\FinanceiroCategoria::where('igreja_id', Auth::user()->getIgrejaId())->get();
                                            @endphp

                                            @forelse($categorias as $categoria)
                                                @php
                                                    $entradas = \App\Models\Financeiro\FinanceiroMovimento::where('igreja_id', Auth::user()->getIgrejaId())
                                                        ->where('categoria_id', $categoria->id)
                                                        ->where('tipo', 'entrada')
                                                        ->whereBetween('data_transacao', [Carbon\Carbon::parse($startDate), Carbon\Carbon::parse($endDate)])
                                                        ->sum('valor');

                                                    $saidas = \App\Models\Financeiro\FinanceiroMovimento::where('igreja_id', Auth::user()->getIgrejaId())
                                                        ->where('categoria_id', $categoria->id)
                                                        ->where('tipo', 'saida')
                                                        ->whereBetween('data_transacao', [Carbon\Carbon::parse($startDate), Carbon\Carbon::parse($endDate)])
                                                        ->sum('valor');

                                                    $saldo = $entradas - $saidas;
                                                @endphp
                                                <tr>
                                                    <td>{{ $categoria->nome }}</td>
                                                    <td class="text-end text-success">{{ number_format($entradas, 2, ',', '.') }} AOA</td>
                                                    <td class="text-end text-danger">{{ number_format($saidas, 2, ',', '.') }} AOA</td>
                                                    <td class="text-end fw-semibold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($saldo, 2, ',', '.') }} AOA
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        <i class="fas fa-info-circle me-2"></i>Nenhuma categoria financeira encontrada
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if($categorias->count() > 0)
                                        <tfoot class="table-light">
                                            <tr class="fw-bold">
                                                <td>TOTAL</td>
                                                <td class="text-end text-success">{{ $financialSummary['income'] ?? '0,00 AOA' }}</td>
                                                <td class="text-end text-danger">{{ $financialSummary['expenses'] ?? '0,00 AOA' }}</td>
                                                <td class="text-end {{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $financialSummary['balance'] ?? '0,00 AOA' }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .metric-card {
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .icon-interactive {
            transition: transform 0.3s ease;
        }

        .metric-card:hover .icon-interactive {
            transform: scale(1.1);
        }

        .chart-container {
            position: relative;
        }

        .chart-container canvas {
            max-height: 100%;
        }
    </style>

    @push('scripts')

    <!-- Dados dos gráficos passados diretamente do PHP -->
    <script data-navigate-once>
        window.chartData = @json($chartData ?? []);
        console.log('[PHP-Data] Chart data loaded from PHP:', window.chartData);

        // Função para atualizar dados quando o componente for atualizado
        window.updateChartData = function(newData) {
            console.log('[PHP-Data] Updating chart data:', newData);
            window.chartData = newData;

            // Notificar os gráficos para se atualizarem
            if (window.StatisticsManager && window.StatisticsManager.chartsInstance) {
                console.log('[PHP-Data] Notifying charts instance to update');
                window.StatisticsManager.chartsInstance.updateCharts();
            } else {
                console.log('[PHP-Data] Charts instance not available');
            }
        };

        // Inicializar quando o DOM estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[PHP-Data] DOM ready, initializing StatisticsManager');
            if (window.StatisticsManager) {
                window.StatisticsManager.init();
            }
        });

        // Forçar inicialização após um tempo
        setTimeout(function() {
            console.log('[PHP-Data] Force initializing StatisticsManager after timeout');
            if (window.StatisticsManager && !window.StatisticsManager.chartsInstance) {
                window.StatisticsManager.init();
            }
        }, 1000);
    </script>

    <!-- Carregar arquivo JavaScript dos gráficos -->
    <script src="{{ asset('system/js/statistics-charts.js') }}" data-navigate-once ></script>

    @endpush
</div>
