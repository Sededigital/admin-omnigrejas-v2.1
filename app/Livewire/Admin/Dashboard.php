<?php

namespace App\Livewire\Admin;
use Livewire\Component;


use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Title('Dashboard | Painel da Igreja')]
#[Layout('components.layouts.app')]
class Dashboard extends Component
{
    // Propriedades públicas para as métricas principais
    public $totalMembros = 0;
    public $receitasMes = 0;
    public $pedidosOracao = 0;
    public $eventosAtivos = 0;
    public $cursosAtivos = 0;
    public $engajamentoTotal = 0;
    public $pedidosMarketplace = 0;
    public $postsRecentes = 0;
    public $comentariosRecentes = 0;
    public $movimentosFinanceiros = 0;
    public $doacoesOnline = 0;

    // Propriedades para gráficos
    public $graficoReceitas = [];
    public $graficoMembros = [];
    public $graficoEngajamento = [];
    public $graficoPedidos = [];
    public $graficoDistribuicaoGeografica = [];

    // Propriedades para informações geográficas
    public $usuariosProximos = [];
    public $estatisticasGeograficas = [];

    public $periodoSelecionado = 'mes';
    public $igreja;

    public function mount()
    {   
        

        $this->carregarIgreja();
        if ($this->igreja) {
            $this->carregarMetricas();
            $this->carregarGraficos();
        } else {
            // Se não houver igreja, definir valores padrão
            $this->totalMembros = 0;
            $this->receitasMes = 0;
            $this->pedidosOracao = 0;
            $this->eventosAtivos = 0;
            $this->cursosAtivos = 0;
            $this->engajamentoTotal = 0;
            $this->pedidosMarketplace = 0;
            $this->postsRecentes = 0;
            $this->comentariosRecentes = 0;
            $this->movimentosFinanceiros = 0;
            $this->doacoesOnline = 0;
        }
    }

    protected function carregarIgreja()
    {
        // Obter a igreja principal do admin logado
        $this->igreja = Auth::user()->getIgreja();
        $this->garantirPermissoesPadrao($this->igreja->id);
    }


    public function garantirPermissoesPadrao(int $igrejaId): bool
    {
        // Chama a função diretamente no PostgreSQL
        $result = DB::selectOne('SELECT garantir_permissoes_padrao(?) AS created', [$igrejaId]);

        // Retorna true se criou permissões novas
        return (bool) $result->created;
    }

    protected function carregarMetricas()
    {
        // Total de membros ativos
        $this->totalMembros = $this->igreja->membrosAtivos()->count();

        // Receitas do mês atual (doações + movimentos financeiros)
        $this->receitasMes = $this->igreja->doacoesOnline()
            ->whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->sum('valor') +
            $this->igreja->financeiroMovimentos()
            ->where('tipo', 'entrada')
            ->whereMonth('data_transacao', now()->month)
            ->whereYear('data_transacao', now()->year)
            ->sum('valor');

        // Pedidos de oração não atendidos
        $this->pedidosOracao = $this->igreja->pedidosOracao()
            ->where('atendido', false)
            ->count();

        // Eventos ativos (futuros)
        $this->eventosAtivos = $this->igreja->eventos()
            ->where('data_evento', '>=', now())
            ->count();

        // Cursos ativos da igreja
        $this->cursosAtivos = $this->igreja->cursos()
            ->where('status', 'ativo')
            ->count();

        // Total de pontos de engajamento
        $this->engajamentoTotal = $this->igreja->engajamentoPontos()->sum('pontos');

        // Pedidos do marketplace (removido temporariamente - relacionamento não existe)
        $this->pedidosMarketplace = 0;

        // Posts e comentários recentes (últimos 30 dias)
        $this->postsRecentes = $this->igreja->posts()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Comentários recentes (não podemos filtrar por igreja pois a tabela não tem igreja_id)
        $this->comentariosRecentes = 0;

        // Movimentos financeiros do mês
        $this->movimentosFinanceiros = $this->igreja->financeiroMovimentos()
            ->whereMonth('data_transacao', now()->month)
            ->whereYear('data_transacao', now()->year)
            ->count();

        // Doações online do mês
        $this->doacoesOnline = $this->igreja->doacoesOnline()
            ->whereMonth('data', now()->month)
            ->whereYear('data', now()->year)
            ->count();
    }

    protected function carregarGraficos()
    {
        // Gráfico de receitas por mês (últimos 6 meses)
        $this->graficoReceitas = collect(range(0, 5))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $receitas = $this->igreja->doacoesOnline()
                ->whereMonth('data', $date->month)
                ->whereYear('data', $date->year)
                ->sum('valor') +
                $this->igreja->financeiroMovimentos()
                ->where('tipo', 'entrada')
                ->whereMonth('data_transacao', $date->month)
                ->whereYear('data_transacao', $date->year)
                ->sum('valor');

            return [
                'mes' => $date->format('M'),
                'valor' => $receitas
            ];
        })->reverse()->values()->toArray();

        // Gráfico de crescimento de membros (simulado com dados históricos)
        $this->graficoMembros = collect(range(0, 5))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            // Como não temos histórico de membros, usar contagem atual
            $membros = $this->igreja->membros()
                ->where('created_at', '<=', $date->endOfMonth())
                ->count();

            return [
                'mes' => $date->format('M'),
                'total' => $membros
            ];
        })->reverse()->values()->toArray();

        // Gráfico de engajamento (pontos por mês)
        $this->graficoEngajamento = collect(range(0, 5))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $pontos = $this->igreja->engajamentoPontos()
                ->whereMonth('data', $date->month)
                ->whereYear('data', $date->year)
                ->sum('pontos');

            return [
                'mes' => $date->format('M'),
                'pontos' => $pontos
            ];
        })->reverse()->values()->toArray();

        // Gráfico de pedidos de oração
        $this->graficoPedidos = collect(range(0, 5))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            $pedidos = $this->igreja->pedidosOracao()
                ->whereMonth('data_pedido', $date->month)
                ->whereYear('data_pedido', $date->year)
                ->count();

            return [
                'mes' => $date->format('M'),
                'total' => $pedidos
            ];
        })->reverse()->values()->toArray();

        // Gráfico de distribuição geográfica dos membros
        $this->carregarDistribuicaoGeografica();
        $this->carregarUsuariosProximos();
        $this->calcularEstatisticasGeograficas();
    }

    public function getChartData(): array
    {
        return [
            'periodo' => $this->periodoSelecionado,
            'receitas' => $this->graficoReceitas,
            'membros' => $this->graficoMembros,
            'engajamento' => $this->graficoEngajamento,
            'pedidos' => $this->graficoPedidos,
            'distribuicaoGeografica' => $this->graficoDistribuicaoGeografica,
            'usuariosProximos' => $this->usuariosProximos,
            'estatisticasGeograficas' => $this->estatisticasGeograficas,
            'receitasMes' => $this->receitasMes,
        ];
    }

    protected function carregarDistribuicaoGeografica()
    {
        // Distribuição geográfica dos membros baseada no endereço dos perfis dos membros
        $this->graficoDistribuicaoGeografica = DB::table('igreja_membros')
            ->join('membro_perfis', 'igreja_membros.id', '=', 'membro_perfis.igreja_membro_id')
            ->select('membro_perfis.endereco', DB::raw('COUNT(igreja_membros.id) as total_membros'))
            ->where('igreja_membros.status', 'ativo')
            ->where('igreja_membros.igreja_id', $this->igreja->id)
            ->whereNotNull('membro_perfis.endereco')
            ->where('membro_perfis.endereco', '!=', '')
            ->groupBy('membro_perfis.endereco')
            ->orderBy('total_membros', 'desc')
            ->limit(10) // Top 10 localizações
            ->get()
            ->map(function ($item) {
                return [
                    'localizacao' => $item->endereco,
                    'total' => $item->total_membros
                ];
            })
            ->toArray();
    }

    protected function carregarUsuariosProximos()
    {
        if (!$this->igreja || !$this->igreja->localizacao) {
            $this->usuariosProximos = [
                'mesma_cidade' => 0,
                'mesma_provincia' => 0,
                'outras_regioes' => 0
            ];
            return;
        }

        $localizacaoIgreja = strtolower($this->igreja->localizacao);

        // Buscar membros com endereços similares à localização da igreja
        $membrosProximos = DB::table('igreja_membros')
            ->join('membro_perfis', 'igreja_membros.id', '=', 'membro_perfis.igreja_membro_id')
            ->where('igreja_membros.status', 'ativo')
            ->where('igreja_membros.igreja_id', $this->igreja->id)
            ->whereNotNull('membro_perfis.endereco')
            ->where('membro_perfis.endereco', '!=', '')
            ->select('membro_perfis.endereco')
            ->get();

        $mesmaCidade = 0;
        $mesmaProvincia = 0;
        $outrasRegioes = 0;

        foreach ($membrosProximos as $membro) {
            $enderecoMembro = strtolower($membro->endereco);

            // Verificar se contém palavras da localização da igreja
            $palavrasIgreja = explode(',', $localizacaoIgreja);
            $encontrouSimilaridade = false;

            foreach ($palavrasIgreja as $palavra) {
                $palavra = trim($palavra);
                if (strlen($palavra) > 2 && str_contains($enderecoMembro, $palavra)) {
                    if (str_contains($enderecoMembro, 'luanda') && str_contains($localizacaoIgreja, 'luanda')) {
                        $mesmaCidade++;
                    } elseif (str_contains($enderecoMembro, 'benguela') && str_contains($localizacaoIgreja, 'benguela')) {
                        $mesmaCidade++;
                    } elseif (str_contains($enderecoMembro, 'huambo') && str_contains($localizacaoIgreja, 'huambo')) {
                        $mesmaCidade++;
                    } else {
                        $mesmaProvincia++;
                    }
                    $encontrouSimilaridade = true;
                    break;
                }
            }

            if (!$encontrouSimilaridade) {
                $outrasRegioes++;
            }
        }

        $this->usuariosProximos = [
            'mesma_cidade' => $mesmaCidade,
            'mesma_provincia' => $mesmaProvincia,
            'outras_regioes' => $outrasRegioes
        ];
    }

    protected function calcularEstatisticasGeograficas()
    {
        if (empty($this->graficoDistribuicaoGeografica)) {
            $this->estatisticasGeograficas = [
                'regiao_mais_populosa' => 'N/A',
                'cobertura_geografica' => 0,
                'media_por_regiao' => 0
            ];
            return;
        }

        $totalMembros = array_sum(array_column($this->graficoDistribuicaoGeografica, 'total'));
        $totalRegioes = count($this->graficoDistribuicaoGeografica);

        $regiaoMaisPopulosa = $this->graficoDistribuicaoGeografica[0]['localizacao'] ?? 'N/A';
        $mediaPorRegiao = $totalRegioes > 0 ? round($totalMembros / $totalRegioes, 1) : 0;

        $this->estatisticasGeograficas = [
            'regiao_mais_populosa' => $regiaoMaisPopulosa,
            'cobertura_geografica' => $totalRegioes,
            'media_por_regiao' => $mediaPorRegiao
        ];
    }

    public function setPeriodo($periodo)
    {
        $this->periodoSelecionado = $periodo;
        $this->carregarGraficos();
        $this->dispatch('update-charts', $this->getChartData());
    }

    public function render()
    {
        return view('admin.dashboard');
    }
}
