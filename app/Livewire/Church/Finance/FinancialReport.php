<?php

namespace App\Livewire\Church\Finance;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Financeiro\FinanceiroMovimento;
use App\Models\Financeiro\FinanceiroConta;
use App\Models\Financeiro\FinanceiroCategoria;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class FinancialReport extends Component
{
    // Filtros de período
    public $selectedPeriod = 'month';
    public $dateFrom = '';
    public $dateTo = '';

    // Filtros adicionais
    public $reportType = 'summary';
    public $selectedAccount = '';
    public $selectedCategory = '';

    // Estado dos filtros
    public $isApplyingFilters = false;

    public function mount()
    {
        $this->setDefaultDates();
    }

    public function setDefaultDates()
    {
        $now = Carbon::now();

        switch ($this->selectedPeriod) {
            case 'month':
                $this->dateFrom = $now->startOfMonth()->format('Y-m-d');
                $this->dateTo = $now->endOfMonth()->format('Y-m-d');
                break;
            case 'quarter':
                $this->dateFrom = $now->startOfQuarter()->format('Y-m-d');
                $this->dateTo = $now->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->dateFrom = $now->startOfYear()->format('Y-m-d');
                $this->dateTo = $now->endOfYear()->format('Y-m-d');
                break;
            case 'custom':
                // Mantém as datas atuais se já estiverem definidas
                if (!$this->dateFrom) {
                    $this->dateFrom = $now->startOfMonth()->format('Y-m-d');
                }
                if (!$this->dateTo) {
                    $this->dateTo = $now->endOfMonth()->format('Y-m-d');
                }
                break;
        }
    }

    public function updatedSelectedPeriod()
    {
        if ($this->selectedPeriod !== 'custom') {
            $this->setDefaultDates();
        }
    }

    public function updatedDateFrom()
    {
        $this->validateDateRange();
    }

    public function updatedDateTo()
    {
        $this->validateDateRange();
    }

    private function validateDateRange()
    {
        if ($this->dateFrom && $this->dateTo) {
            $from = Carbon::createFromFormat('Y-m-d', $this->dateFrom);
            $to = Carbon::createFromFormat('Y-m-d', $this->dateTo);

            if ($from->gt($to)) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Data inicial não pode ser maior que a data final.'
                ]);
            }
        }
    }

    public function applyFilters()
    {
        $this->isApplyingFilters = true;

        try {
            // Validar filtros
            $this->validateFilters();

            // Pequena pausa para mostrar o loading
            sleep(0.5);

            // Dar feedback ao usuário
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Filtros aplicados com sucesso!'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao aplicar filtros: ' . $e->getMessage()
            ]);
        } finally {
            $this->isApplyingFilters = false;
        }
    }

    private function validateFilters()
    {
        if ($this->dateFrom && $this->dateTo) {
            if (Carbon::parse($this->dateFrom)->gt(Carbon::parse($this->dateTo))) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Data inicial não pode ser maior que a data final.'
                ]);
            }
        }
    }

    public function getSummary()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'total_entradas' => 0,
                'total_saidas' => 0,
                'saldo_liquido' => 0,
                'percentual_economia' => 0,
                'total_rendimento' => 0,
            ];
        }

        $query = FinanceiroMovimento::where('igreja_id', $igrejaId)
                                    ->whereBetween('data_transacao', [$this->dateFrom, $this->dateTo]);

        if ($this->selectedAccount) {
            $query->where('conta_id', $this->selectedAccount);
        }

        if ($this->selectedCategory) {
            $query->where('categoria_id', $this->selectedCategory);
        }

        $entradas = (clone $query)->where('tipo', 'entrada')->sum('valor');
        $saidas = (clone $query)->where('tipo', 'saida')->sum('valor');

        // Calcular doações online
        $doacoesOnline = \App\Models\Outros\DoacaoOnline::where('igreja_id', $igrejaId)
            ->where('status', 'completed') // Apenas doações concluídas
            ->whereBetween('data', [$this->dateFrom, $this->dateTo])
            ->sum('valor');

        // Rendimento = (Entradas + Doações Online) - Saídas
        $totalRendimento = ($entradas + $doacoesOnline) - $saidas;

        $percentualEconomia = $saidas > 0 ? (($entradas - $saidas) / $saidas) * 100 : 0;

        return [
            'total_entradas' => $entradas,
            'total_saidas' => $saidas,
            'saldo_liquido' => $entradas - $saidas,
            'percentual_economia' => $percentualEconomia,
            'total_rendimento' => $totalRendimento,
            'doacoes_online' => $doacoesOnline,
        ];
    }

    public function getCategorySummary()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        $query = FinanceiroMovimento::query()
            ->selectRaw('
                financeiro_categorias.nome,
                SUM(CASE WHEN financeiro_movimentos.tipo = \'entrada\' THEN financeiro_movimentos.valor ELSE 0 END) as entradas,
                SUM(CASE WHEN financeiro_movimentos.tipo = \'saida\' THEN financeiro_movimentos.valor ELSE 0 END) as saidas,
                SUM(CASE WHEN financeiro_movimentos.tipo = \'entrada\' THEN financeiro_movimentos.valor ELSE -financeiro_movimentos.valor END) as saldo
            ')
            ->join('financeiro_categorias', 'financeiro_movimentos.categoria_id', '=', 'financeiro_categorias.id')
            ->where('financeiro_movimentos.igreja_id', $igrejaId)
            ->whereBetween('financeiro_movimentos.data_transacao', [$this->dateFrom, $this->dateTo])
            ->groupBy('financeiro_categorias.id', 'financeiro_categorias.nome')
            ->orderBy('saldo', 'desc')
            ->get();

        $totalSaldo = $query->sum('saldo');

        return $query->map(function ($item) use ($totalSaldo) {
            $percentual = $totalSaldo > 0 ? ($item->saldo / $totalSaldo) * 100 : 0;
            return [
                'nome' => $item->nome,
                'entradas' => $item->entradas,
                'saidas' => $item->saidas,
                'saldo' => $item->saldo,
                'percentual' => $percentual,
            ];
        });
    }

    public function getAccountSummary()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        return FinanceiroConta::query()
            ->selectRaw('
                financeiro_contas.*,
                COALESCE(SUM(CASE WHEN financeiro_movimentos.tipo = \'entrada\' THEN financeiro_movimentos.valor ELSE 0 END), 0) as entradas,
                COALESCE(SUM(CASE WHEN financeiro_movimentos.tipo = \'saida\' THEN financeiro_movimentos.valor ELSE 0 END), 0) as saidas
            ')
            ->leftJoin('financeiro_movimentos', function ($join) {
                $join->on('financeiro_contas.id', '=', 'financeiro_movimentos.conta_id')
                     ->whereBetween('financeiro_movimentos.data_transacao', [$this->dateFrom, $this->dateTo]);
            })
            ->where('financeiro_contas.igreja_id', $igrejaId)
            ->groupBy('financeiro_contas.id')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'banco' => $account->banco,
                    'numero_conta' => $account->numero_conta,
                    'ativa' => $account->ativa,
                    'entradas' => $account->entradas,
                    'saidas' => $account->saidas,
                    'saldo' => ($account->saldo_atual ?? 0) + $account->entradas - $account->saidas,
                ];
            });
    }

    public function getRecentMovements()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        return FinanceiroMovimento::query()
            ->with(['categoria', 'responsavel'])
            ->where('igreja_id', $igrejaId)
            ->whereBetween('data_transacao', [$this->dateFrom, $this->dateTo])
            ->orderBy('data_transacao', 'desc')
            ->limit(10)
            ->get();
    }

    public function getChartData()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'labels' => [],
                'values' => [],
            ];
        }

        $data = FinanceiroMovimento::query()
            ->selectRaw('
                TO_CHAR(data_transacao, \'YYYY-MM\') as mes,
                SUM(CASE WHEN tipo = \'entrada\' THEN valor ELSE 0 END) as entradas,
                SUM(CASE WHEN tipo = \'saida\' THEN valor ELSE 0 END) as saidas
            ')
            ->where('igreja_id', $igrejaId)
            ->whereBetween('data_transacao', [$this->dateFrom, $this->dateTo])
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return [
            'labels' => $data->pluck('mes')->toArray(),
            'entradas' => $data->pluck('entradas')->toArray(),
            'saidas' => $data->pluck('saidas')->toArray(),
        ];
    }

    public function getCategoryChartData()
    {
        $categorySummary = $this->getCategorySummary();

        return [
            'labels' => $categorySummary->pluck('nome')->toArray(),
            'values' => $categorySummary->pluck('saldo')->toArray(),
        ];
    }

    public function printReport()
    {
        // Para impressão, simplesmente chamamos o exportPDF que pode ser impresso
        return $this->exportPDF();
    }

    public function exportPDF()
    {
        try {
            $user = Auth::user();
            $igreja = $user->getIgreja();

            if (!$igreja) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Não foi possível identificar a igreja do usuário.'
                ]);
                return;
            }

            $data = [
                'summary' => $this->getSummary(),
                'categorySummary' => $this->getCategorySummary(),
                'accountSummary' => $this->getAccountSummary(),
                'recentMovements' => $this->getRecentMovements(),
                'igreja' => $igreja,
                'dateFrom' => $this->dateFrom,
                'dateTo' => $this->dateTo,
                'reportType' => $this->reportType,
                'selectedAccount' => $this->selectedAccount,
                'selectedCategory' => $this->selectedCategory,
            ];

            $pdf = Pdf::loadView('pdf.finance.financial-report', $data);
            $pdf->setPaper('a4', 'portrait');

            $filename = 'relatorio-financeiro-' . $igreja->nome . '-' . now()->format('Y-m-d-H-i-s') . '.pdf';

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $filename);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao gerar PDF: ' . $e->getMessage()
            ]);
        }
    }

    public function exportExcel()
    {
        try {
            $user = Auth::user();
            $igreja = $user->getIgreja();

            if (!$igreja) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Não foi possível identificar a igreja do usuário.'
                ]);
                return;
            }

            return Excel::download(
                new \App\Exports\FinancialReportExport(
                    $this->getSummary(),
                    $this->getCategorySummary(),
                    $this->getAccountSummary(),
                    $this->getRecentMovements(),
                    $igreja,
                    $this->dateFrom,
                    $this->dateTo,
                    $this->reportType
                ),
                'relatorio-financeiro-' . $igreja->nome . '-' . now()->format('Y-m-d-H-i-s') . '.xlsx'
            );

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao gerar Excel: ' . $e->getMessage()
            ]);
        }
    }

    public function exportCSV()
    {
        try {
            $user = Auth::user();
            $igreja = $user->getIgreja();

            if (!$igreja) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Não foi possível identificar a igreja do usuário.'
                ]);
                return;
            }

            // Preparar dados para CSV
            $csvData = [];

            // Cabeçalho
            $csvData[] = ['OmnIgrejas - Sistema de Gestão Eclesiástica'];
            $csvData[] = [$igreja->nome ?? 'Sistema OmnIgrejas'];

            $reportTypeText = match($this->reportType) {
                'summary' => 'Resumo',
                'detailed' => 'Detalhado',
                'category' => 'Por Categoria',
                'account' => 'Por Conta',
                default => 'Geral'
            };

            $csvData[] = ['Relatório Financeiro - ' . $reportTypeText];
            $csvData[] = ['Período: ' . \Carbon\Carbon::parse($this->dateFrom)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($this->dateTo)->format('d/m/Y')];
            $csvData[] = [];

            // Resumo - Sempre incluir
            $csvData[] = ['RESUMO FINANCEIRO'];
            $csvData[] = ['Indicador', 'Valor'];
            $summary = $this->getSummary();
            $csvData[] = ['Total Entradas', number_format($summary['total_entradas'] ?? 0, 2, ',', '.') . ' AOA'];
            $csvData[] = ['Total Saídas', number_format($summary['total_saidas'] ?? 0, 2, ',', '.') . ' AOA'];
            $csvData[] = ['Saldo Líquido', number_format($summary['saldo_liquido'] ?? 0, 2, ',', '.') . ' AOA'];
            $csvData[] = ['Percentual de Economia', number_format($summary['percentual_economia'] ?? 0, 1) . '%'];
            $csvData[] = [];

            // Conteúdo baseado no tipo de relatório
            if ($this->reportType === 'detailed') {
                // Categorias
                $csvData[] = ['MOVIMENTOS POR CATEGORIA'];
                $csvData[] = ['Categoria', 'Entradas (AOA)', 'Saídas (AOA)', 'Saldo (AOA)', 'Percentual (%)'];
                $categorySummary = $this->getCategorySummary();
                foreach ($categorySummary as $category) {
                    $csvData[] = [
                        $category['nome'],
                        number_format($category['entradas'], 2, ',', '.'),
                        number_format($category['saidas'], 2, ',', '.'),
                        number_format($category['saldo'], 2, ',', '.'),
                        number_format($category['percentual'], 1)
                    ];
                }
                $csvData[] = [];

                // Contas
                $csvData[] = ['ANÁLISE POR CONTA'];
                $csvData[] = ['Banco', 'Número da Conta', 'Entradas (AOA)', 'Saídas (AOA)', 'Saldo (AOA)', 'Status'];
                $accountSummary = $this->getAccountSummary();
                foreach ($accountSummary as $account) {
                    $csvData[] = [
                        $account['banco'],
                        $account['numero_conta'],
                        number_format($account['entradas'], 2, ',', '.'),
                        number_format($account['saidas'], 2, ',', '.'),
                        number_format($account['saldo'], 2, ',', '.'),
                        $account['ativa'] ? 'Ativa' : 'Inativa'
                    ];
                }
                $csvData[] = [];

                // Movimentos recentes
                $csvData[] = ['MOVIMENTOS RECENTES'];
                $csvData[] = ['Data', 'Tipo', 'Descrição', 'Categoria', 'Valor (AOA)', 'Responsável'];
                $recentMovements = $this->getRecentMovements();
                foreach ($recentMovements as $movement) {
                    $csvData[] = [
                        \Carbon\Carbon::parse($movement->data_transacao)->format('d/m/Y'),
                        ucfirst($movement->tipo),
                        $movement->descricao,
                        $movement->categoria->nome ?? 'N/A',
                        number_format($movement->valor, 2, ',', '.'),
                        $movement->responsavel->name ?? 'N/A'
                    ];
                }
            } elseif ($this->reportType === 'category') {
                // Apenas categorias
                $csvData[] = ['MOVIMENTOS POR CATEGORIA'];
                $csvData[] = ['Categoria', 'Entradas (AOA)', 'Saídas (AOA)', 'Saldo (AOA)', 'Percentual (%)'];
                $categorySummary = $this->getCategorySummary();
                foreach ($categorySummary as $category) {
                    $csvData[] = [
                        $category['nome'],
                        number_format($category['entradas'], 2, ',', '.'),
                        number_format($category['saidas'], 2, ',', '.'),
                        number_format($category['saldo'], 2, ',', '.'),
                        number_format($category['percentual'], 1)
                    ];
                }
            } elseif ($this->reportType === 'account') {
                // Apenas contas
                $csvData[] = ['ANÁLISE POR CONTA'];
                $csvData[] = ['Banco', 'Número da Conta', 'Entradas (AOA)', 'Saídas (AOA)', 'Saldo (AOA)', 'Status'];
                $accountSummary = $this->getAccountSummary();
                foreach ($accountSummary as $account) {
                    $csvData[] = [
                        $account['banco'],
                        $account['numero_conta'],
                        number_format($account['entradas'], 2, ',', '.'),
                        number_format($account['saidas'], 2, ',', '.'),
                        number_format($account['saldo'], 2, ',', '.'),
                        $account['ativa'] ? 'Ativa' : 'Inativa'
                    ];
                }
            } elseif ($this->reportType === 'rendimento') {
                // Relatório de rendimento
                $csvData[] = ['ANÁLISE DE RENDIMENTO'];
                $csvData[] = ['Componente', 'Valor (AOA)', 'Descrição'];
                $csvData[] = ['Entradas (Movimentos)', number_format($summary['total_entradas'] ?? 0, 2, ',', '.'), 'Movimentos de entrada registrados'];
                $csvData[] = ['Doações Online', number_format($summary['doacoes_online'] ?? 0, 2, ',', '.'), 'Doações recebidas online'];
                $csvData[] = ['Total Entradas', number_format(($summary['total_entradas'] ?? 0) + ($summary['doacoes_online'] ?? 0), 2, ',', '.'), 'Entradas + Doações Online'];
                $csvData[] = ['Saídas', number_format($summary['total_saidas'] ?? 0, 2, ',', '.'), 'Movimentos de saída registrados'];
                $csvData[] = ['', '', ''];
                $csvData[] = ['RENDIMENTO TOTAL', number_format($summary['total_rendimento'] ?? 0, 2, ',', '.'), '(Entradas + Doações) - Saídas'];
            }

            // Gerar conteúdo CSV
            $csvContent = '';
            foreach ($csvData as $row) {
                $csvContent .= implode(';', $row) . "\n";
            }

            $filename = 'relatorio-financeiro-' . $igreja->nome . '-' . now()->format('Y-m-d-H-i-s') . '.csv';

            return response($csvContent)
                ->header('Content-Type', 'text/csv; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao gerar CSV: ' . $e->getMessage()
            ]);
        }
    }

    public function getAccounts()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        return FinanceiroConta::where('igreja_id', $igrejaId)
                             ->where('ativa', true)
                             ->orderBy('banco')
                             ->get();
    }

    public function getCategories()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        return FinanceiroCategoria::where('igreja_id', $igrejaId)
                                 ->orderBy('nome')
                                 ->get();
    }

    public function render()
    {
        return view('church.finance.financial-report', [
            'summary' => $this->getSummary(),
            'categorySummary' => $this->getCategorySummary(),
            'accountSummary' => $this->getAccountSummary(),
            'recentMovements' => $this->getRecentMovements(),
            'chartData' => $this->getChartData(),
            'categoryChartData' => $this->getCategoryChartData(),
            'accounts' => $this->getAccounts(),
            'categories' => $this->getCategories(),
        ]);
    }
}
