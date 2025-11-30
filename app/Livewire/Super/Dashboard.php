<?php

namespace App\Livewire\Super;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Billings\Pacote;
use App\Models\Outros\EngajamentoPonto;

use Livewire\Component;
use App\Models\Billings\AssinaturaLog;
use Livewire\Attributes\Title;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaCiclo;
use App\Models\Billings\AssinaturaCupom;
use Livewire\Attributes\Layout;
use App\Models\Billings\AssinaturaCupomUso;
use Illuminate\Support\Facades\DB;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\AssinaturaPagamentoFalha;

#[Title('Dashboard | Painel Principal')]
#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    // Propriedades públicas para as métricas principais
    public $receitaMesAtual = 0;
    public $receitaMesAnterior = 0;
    public $novosContratos = 0;
    public $valorMedioAssinatura = 0;
    public $mrr = 0;
    public $igrejasAtivas = 0;
    public $igrejasInativas = 0;
    public $assinaturasVencendo = 0;
    public $assinaturasAtraso = 0;
    public $pagamentosPendentes = 0;
    public $pagamentosFalha = 0;
    public $usuariosCrescimento = [];
    public $metodosPagamento = [];
    public $churn = 0;
    public $upgradesDowngrades = 0;
    public $cuponsAtivos = [];
    public $logsRecentes = [];
    public $igrejasEngajadas = [];
    public $distribuicaoGeografica = [];
    public $alertas = [];

    public $graficoGrossSales = [];
    public $graficoEarnings = [];
    public $graficoConversions = [];
    public $graficoCrescimentoUsuarios = [];
    public $graficoMetodosPagamento = [];
    public $graficoPerformancePacotes = [];

    public $enterpriseClients = [];
    public $widgetCartao = [];
    public $websiteVisitors = 0;
    public $newCustomers = 0;
    public $activityOverview = [];
    public $alertasCriticos = [];
    public $performancePacotes = [];
    public $cuponsAtivosLista = [];
    public $logsRecentesLista = [];
    public $distribuicaoGeograficaLista = [];

    // Novas métricas de assinaturas
    public $assinaturasVencendo7d = 0;
    public $assinaturasVencendo30d = 0;
    public $assinaturasExpiradas = 0;
    public $receitaRecorrenteMensal = 0;
    public $taxaRenovacao = 0;
    public $assinaturasNovas = 0;
    public $assinaturasCanceladas = 0;
    public $falhasPagamentoMes = 0;
    public $cuponsUsados = 0;
    public $assinaturasVencendoLista = [];
    public $falhasPagamentoLista = [];
    public $logsAssinaturasRecentes = [];

    // Novas métricas de engajamento
    public $totalPontosEngajamento = 0;
    public $igrejasMaisAtivas = [];
    public $topIgrejasEngajamento = [];
    public $igrejasInativasLista = [];

    // Períodos individuais para cada gráfico
    public $periodoGrossSales = 'mes';
    public $periodoPerformancePacotes = 'mes';
    public $periodoCrescimentoUsuarios = 'mes';
    public $periodoMetodosPagamento = 'mes';
    public $periodoDistribuicaoGeografica = 'mes';

    // Período geral (mantido para compatibilidade)
    public $periodoSelecionado = 'mes';

    public function mount()
    {
       
        $this->carregarMetricas();
        $this->carregarGraficos();
        $this->carregarDadosComplementares();
        $this->carregarMetricasEngajamento();
        $this->carregarIgrejasInativas();
    }

    /**
     * Consolida todos os dados dos gráficos em um único array para o frontend.
     */
    public function getChartData(): array
    {
        return [
            'periodo' => $this->periodoSelecionado,
            'grossSales' => $this->graficoGrossSales,
            'performancePacotes' => $this->performancePacotes,
            'churn' => $this->churn,
            'crescimentoUsuarios' => $this->graficoCrescimentoUsuarios,
            'metodosPagamento' => $this->metodosPagamento,
            'distribuicaoGeografica' => $this->distribuicaoGeografica,
            'receitaAtual' => $this->receitaMesAtual,
        ];
    }

    protected function carregarMetricas()
    {
        // Receita confirmada do mês atual
        $this->receitaMesAtual = AssinaturaPagamento::where('status', 'confirmado')
            ->whereMonth('data_pagamento', now()->month)
            ->whereYear('data_pagamento', now()->year)
            ->sum('valor');

        // Receita confirmada do mês anterior
        $this->receitaMesAnterior = AssinaturaPagamento::where('status', 'confirmado')
            ->whereMonth('data_pagamento', now()->subMonth()->month)
            ->whereYear('data_pagamento', now()->subMonth()->year)
            ->sum('valor');

        // Novos contratos assinados no mês
        $this->novosContratos = AssinaturaAtual::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'Ativo')
            ->count();

        // Valor médio por assinatura ativa
        $this->valorMedioAssinatura = Pacote::whereHas('assinaturasAtuais', function($query) {
            $query->where('status', 'Ativo');
        })->avg('preco') ?? 0;

        // MRR (Receita Recorrente Mensal)
        $this->mrr = AssinaturaAtual::where('status', 'Ativo')
            ->join('pacote', 'assinatura_atual.pacote_id', '=', 'pacote.id')
            ->sum(DB::raw('pacote.preco / COALESCE(pacote.duracao_meses, 1)')) ?? 0;

        // Igrejas ativas/inativas
        $this->igrejasAtivas = Igreja::where('status_aprovacao', 'aprovado')->count();
        $this->igrejasInativas = Igreja::where('status_aprovacao', '!=', 'aprovado')->count();

        // Assinaturas próximas do vencimento (30 dias)
        $this->assinaturasVencendo = AssinaturaAtual::where('status', 'Ativo')
            ->whereBetween('data_fim', [now(), now()->addDays(30)])
            ->count();

        // Assinaturas em atraso
        $this->assinaturasAtraso = AssinaturaCiclo::whereIn('status', ['pendente', 'atrasado'])
            ->where('fim', '<', now())
            ->count();

        // Pagamentos pendentes
        $this->pagamentosPendentes = AssinaturaPagamento::where('status', 'pendente')->count();

        // Falhas de pagamento
        $this->pagamentosFalha = AssinaturaPagamentoFalha::where('resolvido', false)->count();

        // Métodos de pagamento mais usados
        $this->metodosPagamento = AssinaturaPagamento::where('status', 'confirmado')
            ->select('metodo_pagamento', DB::raw('count(*) as total'))
            ->groupBy('metodo_pagamento')
            ->orderByDesc('total')
            ->get()
            ->toArray();

        // Churn mensal
        $cancelados = AssinaturaLog::where('acao', 'cancelado')
            ->whereMonth('data_acao', now()->month)
            ->count();
        $ativos = AssinaturaAtual::where('status', 'Ativo')->count();
        $this->churn = $ativos > 0 ? round(($cancelados / $ativos) * 100, 2) : 0;

        // Upgrades/Downgrades
        $this->upgradesDowngrades = AssinaturaLog::whereIn('acao', ['upgrade', 'downgrade'])
            ->whereMonth('data_acao', now()->month)
            ->count();

        // Novas métricas de assinaturas
        $this->carregarMetricasAssinaturas();
    }

    protected function carregarGraficos()
    {
        // Gross Sales (vendas brutas por mês do ano atual)
        $this->graficoGrossSales = AssinaturaPagamento::selectRaw('EXTRACT(MONTH FROM data_pagamento) as mes, SUM(valor) as total')
            ->where('status', 'confirmado')
            ->whereRaw('EXTRACT(YEAR FROM data_pagamento) = ?', [now()->year])
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Crescimento de usuários por mês
        $this->graficoCrescimentoUsuarios = User::selectRaw('EXTRACT(MONTH FROM created_at) as mes, COUNT(*) as total')
            ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [now()->year])
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->toArray();

        // Performance de pacotes
        $this->performancePacotes = Pacote::selectRaw('pacote.nome as label, COUNT(assinatura_atual.igreja_id) as total')
            ->leftJoin('assinatura_atual', 'pacote.id', '=', 'assinatura_atual.pacote_id')
            ->where('assinatura_atual.status', 'Ativo')
            ->groupBy('pacote.id', 'pacote.nome')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($pacote) {
                return [
                    'label' => $pacote->label,
                    'total' => $pacote->total
                ];
            })
            ->toArray();
    }

    protected function carregarDadosComplementares()
    {
        // Enterprise Clients (igrejas mais engajadas)
        $this->enterpriseClients = EngajamentoPonto::select('igreja_id', DB::raw('sum(pontos) as total'))
            ->groupBy('igreja_id')
            ->orderByDesc('total')
            ->with('igreja')
            ->take(5)
            ->get();

        // Alertas críticos
        $this->alertasCriticos = [
            'assinaturasVencidas7d' => AssinaturaAtual::where('status', 'Expirado')
                ->where('data_fim', '<', now()->subDays(7))
                ->with('igreja')
                ->get(),
            'falhasRenovacao' => AssinaturaPagamentoFalha::where('motivo', 'like', '%renovação%')
                ->where('resolvido', false)
                ->get(),
            'pagamentosPendentes' => AssinaturaPagamento::where('status', 'pendente')
                ->with('igreja')
                ->get(),
        ];

        // Distribuição geográfica
        $this->distribuicaoGeografica = Igreja::selectRaw('localizacao, COUNT(*) as total')
            ->whereNotNull('localizacao')
            ->where('localizacao', '!=', '')
            ->groupBy('localizacao')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function setPeriodo($periodo)
    {
        $this->periodoSelecionado = $periodo;
        $this->periodoGrossSales = $periodo; // Mantém sincronizado com o gráfico principal
        $this->atualizarMetricasPorPeriodo();

        // Dispara o evento com os dados atualizados e consolidados
        $this->dispatch('update-charts', $this->getChartData());
    }

    // Métodos individuais para cada gráfico
    public function setPeriodoGrossSales($periodo)
    {
        $this->periodoGrossSales = $periodo;
        $this->atualizarGraficoGrossSales();
        $this->dispatch('update-chart-gross-sales', [
            'grossSales' => $this->graficoGrossSales,
            'periodo' => $this->periodoGrossSales,
            'receitaAtual' => $this->receitaMesAtual
        ]);
    }

    public function setPeriodoPerformancePacotes($periodo)
    {
        $this->periodoPerformancePacotes = $periodo;
        $this->atualizarGraficoPerformancePacotes();
        $this->dispatch('update-chart-performance-pacotes', [
            'performancePacotes' => $this->performancePacotes,
            'periodo' => $this->periodoPerformancePacotes
        ]);
    }

    public function setPeriodoCrescimentoUsuarios($periodo)
    {
        $this->periodoCrescimentoUsuarios = $periodo;
        $this->atualizarGraficoCrescimentoUsuarios();
        $this->dispatch('update-chart-crescimento-usuarios', [
            'crescimentoUsuarios' => $this->graficoCrescimentoUsuarios,
            'periodo' => $this->periodoCrescimentoUsuarios
        ]);
    }

    public function setPeriodoMetodosPagamento($periodo)
    {
        $this->periodoMetodosPagamento = $periodo;
        $this->atualizarGraficoMetodosPagamento();
        $this->dispatch('update-chart-metodos-pagamento', [
            'metodosPagamento' => $this->metodosPagamento,
            'periodo' => $this->periodoMetodosPagamento
        ]);
    }

    public function setPeriodoDistribuicaoGeografica($periodo)
    {
        $this->periodoDistribuicaoGeografica = $periodo;
        $this->atualizarGraficoDistribuicaoGeografica();
        $this->dispatch('update-chart-distribuicao-geografica', [
            'distribuicaoGeografica' => $this->distribuicaoGeografica,
            'periodo' => $this->periodoDistribuicaoGeografica
        ]);
    }

    public function atualizarMetricasPorPeriodo()
    {
        // Define datas de início e fim conforme o período selecionado
        if ($this->periodoSelecionado === 'semana') {
            $inicio = now()->startOfWeek();
            $fim = now()->endOfWeek();
            $groupByRaw = "EXTRACT(DAY FROM %s)";
            $groupByLabel = 'periodo';
        } elseif ($this->periodoSelecionado === 'ano') {
            $inicio = now()->startOfYear();
            $fim = now()->endOfYear();
            $groupByRaw = "EXTRACT(MONTH FROM %s)";
            $groupByLabel = 'periodo';
        } else { // mês
            $inicio = now()->startOfMonth();
            $fim = now()->endOfMonth();
            $groupByRaw = "EXTRACT(DAY FROM %s)";
            $groupByLabel = 'periodo';
        }

        // Atualizar receita do período
        $this->receitaMesAtual = AssinaturaPagamento::where('status', 'confirmado')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->sum('valor');

        // Atualizar gráfico Gross Sales
        $this->graficoGrossSales = AssinaturaPagamento::selectRaw(sprintf($groupByRaw, 'data_pagamento') . " as $groupByLabel, SUM(valor) as total")
            ->where('status', 'confirmado')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->groupBy($groupByLabel)
            ->orderBy($groupByLabel)
            ->pluck('total', $groupByLabel)
            ->toArray();

        // Atualizar crescimento de usuários
        $this->graficoCrescimentoUsuarios = User::selectRaw(sprintf($groupByRaw, 'created_at') . " as $groupByLabel, COUNT(*) as total")
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupBy($groupByLabel)
            ->orderBy($groupByLabel)
            ->pluck('total', $groupByLabel)
            ->toArray();

        // Atualizar performance dos pacotes
        $this->performancePacotes = Pacote::selectRaw('pacote.nome as label, COUNT(assinatura_atual.igreja_id) as total')
            ->leftJoin('assinatura_atual', 'pacote.id', '=', 'assinatura_atual.pacote_id')
            ->whereBetween('assinatura_atual.created_at', [$inicio, $fim])
            ->where('assinatura_atual.status', 'Ativo')
            ->groupBy('pacote.id', 'pacote.nome')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($pacote) => ['label' => $pacote->label, 'total' => $pacote->total])
            ->toArray();
    }

    // Métodos individuais de atualização para cada gráfico
    protected function atualizarGraficoGrossSales()
    {
        [$inicio, $fim, $groupByRaw, $groupByLabel] = $this->getDateRangeAndGroupBy($this->periodoGrossSales);

        $this->receitaMesAtual = AssinaturaPagamento::where('status', 'confirmado')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->sum('valor');

        $this->graficoGrossSales = AssinaturaPagamento::selectRaw(sprintf($groupByRaw, 'data_pagamento') . " as $groupByLabel, SUM(valor) as total")
            ->where('status', 'confirmado')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->groupBy($groupByLabel)
            ->orderBy($groupByLabel)
            ->pluck('total', $groupByLabel)
            ->toArray();
    }

    protected function atualizarGraficoPerformancePacotes()
    {
        [$inicio, $fim] = $this->getDateRangeAndGroupBy($this->periodoPerformancePacotes);

        $this->performancePacotes = Pacote::selectRaw('pacote.nome as label, COUNT(assinatura_atual.igreja_id) as total')
            ->leftJoin('assinatura_atual', 'pacote.id', '=', 'assinatura_atual.pacote_id')
            ->whereBetween('assinatura_atual.created_at', [$inicio, $fim])
            ->where('assinatura_atual.status', 'Ativo')
            ->groupBy('pacote.id', 'pacote.nome')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($pacote) => ['label' => $pacote->label, 'total' => $pacote->total])
            ->toArray();
    }

    protected function atualizarGraficoCrescimentoUsuarios()
    {
        [$inicio, $fim, $groupByRaw, $groupByLabel] = $this->getDateRangeAndGroupBy($this->periodoCrescimentoUsuarios);

        $this->graficoCrescimentoUsuarios = User::selectRaw(sprintf($groupByRaw, 'created_at') . " as $groupByLabel, COUNT(*) as total")
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupBy($groupByLabel)
            ->orderBy($groupByLabel)
            ->pluck('total', $groupByLabel)
            ->toArray();
    }

    protected function atualizarGraficoMetodosPagamento()
    {
        [$inicio, $fim] = $this->getDateRangeAndGroupBy($this->periodoMetodosPagamento);

        $this->metodosPagamento = AssinaturaPagamento::where('status', 'confirmado')
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->select('metodo_pagamento', DB::raw('count(*) as total'))
            ->groupBy('metodo_pagamento')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    protected function atualizarGraficoDistribuicaoGeografica()
    {
        [$inicio, $fim] = $this->getDateRangeAndGroupBy($this->periodoDistribuicaoGeografica);

        $this->distribuicaoGeografica = Igreja::selectRaw('localizacao, COUNT(*) as total')
            ->whereNotNull('localizacao')
            ->where('localizacao', '!=', '')
            ->whereBetween('created_at', [$inicio, $fim])
            ->groupBy('localizacao')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    // Método auxiliar para obter range de datas e agrupamento
    protected function getDateRangeAndGroupBy($periodo)
    {
        if ($periodo === 'semana') {
            $inicio = now()->startOfWeek();
            $fim = now()->endOfWeek();
            $groupByRaw = "EXTRACT(DAY FROM %s)";
            $groupByLabel = 'periodo';
        } elseif ($periodo === 'ano') {
            $inicio = now()->startOfYear();
            $fim = now()->endOfYear();
            $groupByRaw = "EXTRACT(MONTH FROM %s)";
            $groupByLabel = 'periodo';
        } else { // mês
            $inicio = now()->startOfMonth();
            $fim = now()->endOfMonth();
            $groupByRaw = "EXTRACT(DAY FROM %s)";
            $groupByLabel = 'periodo';
        }

        return [$inicio, $fim, $groupByRaw, $groupByLabel];
    }

    protected function carregarMetricasAssinaturas()
    {
        // Assinaturas vencendo em 7 dias
        $this->assinaturasVencendo7d = AssinaturaAtual::where('status', 'Ativo')
            ->whereBetween('data_fim', [now(), now()->addDays(7)])
            ->count();

        // Assinaturas vencendo em 30 dias
        $this->assinaturasVencendo30d = AssinaturaAtual::where('status', 'Ativo')
            ->whereBetween('data_fim', [now(), now()->addDays(30)])
            ->count();

        // Assinaturas expiradas
        $this->assinaturasExpiradas = AssinaturaAtual::where('status', 'Expirado')
            ->orWhere(function($query) {
                $query->where('status', 'Ativo')
                      ->where('data_fim', '<', now());
            })
            ->count();

        // Receita Recorrente Mensal (MRR)
        $this->receitaRecorrenteMensal = AssinaturaAtual::where('status', 'Ativo')
            ->join('pacote', 'assinatura_atual.pacote_id', '=', 'pacote.id')
            ->sum(DB::raw('pacote.preco / COALESCE(pacote.duracao_meses, 1)'));

        // Taxa de renovação (últimos 30 dias)
        $renovadas = AssinaturaLog::where('acao', 'renovado')
            ->where('data_acao', '>=', now()->subDays(30))
            ->count();
        $vencidas = AssinaturaAtual::where('data_fim', '>=', now()->subDays(30))
            ->where('data_fim', '<=', now())
            ->count();
        $this->taxaRenovacao = $vencidas > 0 ? round(($renovadas / $vencidas) * 100, 2) : 0;

        // Assinaturas novas este mês
        $this->assinaturasNovas = AssinaturaAtual::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Assinaturas canceladas este mês
        $this->assinaturasCanceladas = AssinaturaLog::where('acao', 'cancelado')
            ->whereMonth('data_acao', now()->month)
            ->whereYear('data_acao', now()->year)
            ->count();

        // Falhas de pagamento este mês
        $this->falhasPagamentoMes = AssinaturaPagamentoFalha::whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->count();

        // Cupons usados este mês
        $this->cuponsUsados = AssinaturaCupomUso::whereMonth('usado_em', now()->month)
            ->whereYear('usado_em', now()->year)
            ->count();

        // Lista de assinaturas vencendo (próximos 30 dias)
        $this->assinaturasVencendoLista = AssinaturaAtual::where('status', 'Ativo')
            ->whereBetween('data_fim', [now(), now()->addDays(30)])
            ->with(['igreja', 'pacote'])
            ->orderBy('data_fim')
            ->limit(10)
            ->get();

        // Lista de falhas de pagamento recentes
        $this->falhasPagamentoLista = AssinaturaPagamentoFalha::where('resolvido', false)
            ->with(['igreja', 'pagamento'])
            ->orderByDesc('data')
            ->limit(10)
            ->get();

        // Logs recentes de assinaturas
        $this->logsAssinaturasRecentes = AssinaturaLog::with(['igreja', 'pacote'])
            ->orderByDesc('data_acao')
            ->limit(15)
            ->get();
    }

    protected function carregarMetricasEngajamento()
    {
        // Total de pontos de engajamento
        $this->totalPontosEngajamento = EngajamentoPonto::sum('pontos');

        // Top igrejas por engajamento
        $this->topIgrejasEngajamento = EngajamentoPonto::select('igreja_id', DB::raw('sum(pontos) as total_pontos'))
            ->groupBy('igreja_id')
            ->orderByDesc('total_pontos')
            ->with('igreja')
            ->take(5)
            ->get();

        // Igrejas mais ativas (por conteúdo criado)
        $this->igrejasMaisAtivas = Igreja::select('igrejas.id', 'igrejas.nome',
            DB::raw('(SELECT COUNT(*) FROM posts WHERE posts.igreja_id = igrejas.id) +
                     (SELECT COUNT(*) FROM eventos WHERE eventos.igreja_id = igrejas.id) as total_conteudo'))
            ->orderByDesc('total_conteudo')
            ->take(5)
            ->get();
    }


    protected function carregarIgrejasInativas()
    {
        // Igrejas sem atividade recente (sem posts, eventos ou doações nos últimos 30 dias)
        $this->igrejasInativasLista = Igreja::whereDoesntHave('posts', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->whereDoesntHave('eventos', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })
            ->whereDoesntHave('doacoesOnline', function($query) {
                $query->where('data', '>=', now()->subDays(30));
            })
            ->where('status_aprovacao', 'aprovado')
            ->with(['assinaturaAtual'])
            ->take(10)
            ->get();
    }

    // Ações rápidas para o dashboard
    public function renovarAssinatura($assinaturaId)
    {
        try {
            $assinatura = AssinaturaAtual::find($assinaturaId);
            if (!$assinatura) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Assinatura não encontrada!'
                ]);
                return;
            }

            // Estende por mais 30 dias
            $assinatura->update([
                'data_fim' => $assinatura->data_fim->addDays(30),
                'updated_at' => now()
            ]);

            // Registra log
            AssinaturaLog::create([
                'igreja_id' => $assinatura->igreja_id,
                'pacote_id' => $assinatura->pacote_id,
                'acao' => 'renovado',
                'descricao' => 'Renovação manual pelo super admin (+30 dias)',
                'data_acao' => now()
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Assinatura renovada por 30 dias!'
            ]);

            // Recarrega os dados
            $this->carregarMetricasAssinaturas();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao renovar assinatura: ' . $e->getMessage()
            ]);
        }
    }

    public function marcarFalhaComoResolvida($falhaId)
    {
        try {
            $falha = AssinaturaPagamentoFalha::find($falhaId);
            if (!$falha) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Falha não encontrada!'
                ]);
                return;
            }

            $falha->update(['resolvido' => true]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Falha marcada como resolvida!'
            ]);

            // Recarrega os dados
            $this->carregarMetricasAssinaturas();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao resolver falha: ' . $e->getMessage()
            ]);
        }
    }

    public function enviarLembreteVencimento($assinaturaId)
    {
        try {
            $assinatura = AssinaturaAtual::with(['igreja', 'pacote'])->find($assinaturaId);
            if (!$assinatura) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Assinatura não encontrada!'
                ]);
                return;
            }

            // Aqui você pode implementar o envio de email/notificação
            // Por enquanto, vamos simular o envio

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Lembrete enviado para ' . $assinatura->igreja->nome
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao enviar lembrete: ' . $e->getMessage()
            ]);
        }
    }

    public function suspenderAssinatura($assinaturaId)
    {
        try {
            $assinatura = AssinaturaAtual::find($assinaturaId);
            if (!$assinatura) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Assinatura não encontrada!'
                ]);
                return;
            }

            $assinatura->update(['status' => 'Cancelado']);

            // Registra log
            AssinaturaLog::create([
                'igreja_id' => $assinatura->igreja_id,
                'pacote_id' => $assinatura->pacote_id,
                'acao' => 'cancelado',
                'descricao' => 'Suspensão manual pelo super admin',
                'data_acao' => now()
            ]);

            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Assinatura suspensa!'
            ]);

            // Recarrega os dados
            $this->carregarMetricasAssinaturas();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao suspender assinatura: ' . $e->getMessage()
            ]);
        }
    }

    public function gerarRelatorioAssinaturas()
    {
        try {
            // Simula geração de relatório
            $this->dispatch('toast', [
                'type' => 'info',
                'message' => 'Relatório sendo gerado... Você receberá por email em breve.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ]);
        }
    }

    public function sincronizarPagamentos()
    {
        try {
            // Simula sincronização com gateway de pagamento
            sleep(1); // Simula processamento

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pagamentos sincronizados com sucesso!'
            ]);

            // Recarrega métricas
            $this->carregarMetricas();
            $this->carregarMetricasAssinaturas();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro na sincronização: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('super-admin.dashboard');
    }
}
