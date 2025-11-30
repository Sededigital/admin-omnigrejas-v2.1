<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Log;

class FinancialReportExport implements WithMultipleSheets
{
    protected $summary;
    protected $categorySummary;
    protected $accountSummary;
    protected $recentMovements;
    protected $igreja;
    protected $dateFrom;
    protected $dateTo;
    protected $reportType;

    public function __construct($summary, $categorySummary, $accountSummary, $recentMovements, $igreja, $dateFrom, $dateTo, $reportType = 'summary')
    {
        $this->summary = $summary;
        $this->categorySummary = $categorySummary;
        $this->accountSummary = $accountSummary;
        $this->recentMovements = $recentMovements;
        $this->igreja = $igreja;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportType = $reportType;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Debug: verificar o tipo de relatório
        Log::info('FinancialReportExport - ReportType: ' . $this->reportType);

        // Sempre incluir o resumo
        $sheets[] = new SummarySheet($this->summary, $this->igreja, $this->dateFrom, $this->dateTo, $this->reportType);

        // Incluir planilhas baseado no tipo de relatório
        if ($this->reportType === 'detailed') {
            Log::info('Adding detailed sheets');
            $sheets[] = new CategorySheet($this->categorySummary, $this->igreja);
            $sheets[] = new AccountSheet($this->accountSummary, $this->igreja);
            $sheets[] = new MovementsSheet($this->recentMovements, $this->igreja);
        } elseif ($this->reportType === 'category') {
            Log::info('Adding category sheet');
            $sheets[] = new CategorySheet($this->categorySummary, $this->igreja);
        } elseif ($this->reportType === 'account') {
            Log::info('Adding account sheet');
            $sheets[] = new AccountSheet($this->accountSummary, $this->igreja);
        } elseif ($this->reportType === 'rendimento') {
            Log::info('Adding rendimento sheet');
            $sheets[] = new RendimentoSheet($this->summary, $this->igreja, $this->dateFrom, $this->dateTo);
        } else {
            Log::info('ReportType not recognized: ' . $this->reportType);
        }

        Log::info('Total sheets: ' . count($sheets));

        return $sheets;
    }
}

// Planilha de Resumo
class SummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $summary;
    protected $igreja;
    protected $dateFrom;
    protected $dateTo;
    protected $reportType;

    public function __construct($summary, $igreja, $dateFrom, $dateTo, $reportType)
    {
        $this->summary = $summary;
        $this->igreja = $igreja;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportType = $reportType;
    }

    public function title(): string
    {
        return 'Resumo';
    }

    public function headings(): array
    {
        $reportTypeText = match($this->reportType) {
            'summary' => 'Resumo',
            'detailed' => 'Detalhado',
            'category' => 'Por Categoria',
            'account' => 'Por Conta',
            default => 'Geral'
        };

        return [
            ['OmnIgrejas - Sistema de Gestão Eclesiástica'],
            [$this->igreja->nome ?? 'Sistema OmnIgrejas'],
            ['Relatório Financeiro ' . $reportTypeText . ' - Período: ' . \Carbon\Carbon::parse($this->dateFrom)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($this->dateTo)->format('d/m/Y')],
            [''],
            ['Indicador', 'Valor', 'Percentual'],
        ];
    }

    public function collection()
    {
        return collect([
            ['Total Entradas', number_format($this->summary['total_entradas'] ?? 0, 2, ',', '.'), ''],
            ['Total Saídas', number_format($this->summary['total_saidas'] ?? 0, 2, ',', '.'), ''],
            ['Saldo Líquido', number_format($this->summary['saldo_liquido'] ?? 0, 2, ',', '.'), ''],
            ['Percentual de Economia', number_format($this->summary['percentual_economia'] ?? 0, 1) . '%', ''],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo do cabeçalho principal - MAIOR e mais destacado
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo do nome da igreja - destacado
        $sheet->getStyle('A2:C2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '495057'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Estilo do período - destacado
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '28A745'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D4EDDA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'C3E6CB'],
                ],
            ],
        ]);

        // Estilo dos cabeçalhos da tabela
        $sheet->getStyle('A5:C5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo das células de dados
        $sheet->getStyle('A6:C10')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Mesclar células do cabeçalho
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');

        // Ajustar altura das linhas
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(18);

        return $sheet;
    }
}

// Planilha de Categorias
class CategorySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $categorySummary;
    protected $igreja;

    public function __construct($categorySummary, $igreja)
    {
        $this->categorySummary = $categorySummary;
        $this->igreja = $igreja;
    }

    public function title(): string
    {
        return 'Por Categoria';
    }

    public function headings(): array
    {
        return [
            ['OmnIgrejas - ' . ($this->igreja->nome ?? 'Sistema OmnIgrejas')],
            ['Movimentos por Categoria'],
            [''],
            ['Categoria', 'Entradas (AOA)', 'Saídas (AOA)', 'Saldo (AOA)', 'Percentual (%)'],
        ];
    }

    public function collection()
    {
        return $this->categorySummary->map(function ($category) {
            return [
                $category['nome'],
                number_format($category['entradas'], 2, ',', '.'),
                number_format($category['saidas'], 2, ',', '.'),
                number_format($category['saldo'], 2, ',', '.'),
                number_format($category['percentual'], 1),
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo do cabeçalho principal
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo do subtítulo
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '495057'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Estilo dos cabeçalhos da tabela
        $sheet->getStyle('A4:E4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo das células de dados
        $sheet->getStyle('A5:E100')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Mesclar células do cabeçalho
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');

        // Ajustar altura das linhas
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(18);

        return $sheet;
    }
}

// Planilha de Rendimento
class RendimentoSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $summary;
    protected $igreja;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($summary, $igreja, $dateFrom, $dateTo)
    {
        $this->summary = $summary;
        $this->igreja = $igreja;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function title(): string
    {
        return 'Rendimento';
    }

    public function headings(): array
    {
        return [
            ['OmnIgrejas - ' . ($this->igreja->nome ?? 'Sistema OmnIgrejas')],
            ['Relatório de Rendimento'],
            ['Período: ' . \Carbon\Carbon::parse($this->dateFrom)->format('d/m/Y') . ' até ' . \Carbon\Carbon::parse($this->dateTo)->format('d/m/Y')],
            [''],
            ['Componente', 'Valor (AOA)', 'Descrição'],
        ];
    }

    public function collection()
    {
        $data = [
            ['Entradas (Movimentos)', number_format($this->summary['total_entradas'] ?? 0, 2, ',', '.'), 'Movimentos de entrada registrados'],
            ['Doações Online', number_format($this->summary['doacoes_online'] ?? 0, 2, ',', '.'), 'Doações recebidas online'],
            ['Total Entradas', number_format(($this->summary['total_entradas'] ?? 0) + ($this->summary['doacoes_online'] ?? 0), 2, ',', '.'), 'Entradas + Doações Online'],
            ['Saídas', number_format($this->summary['total_saidas'] ?? 0, 2, ',', '.'), 'Movimentos de saída registrados'],
            ['', '', ''],
            ['RENDIMENTO TOTAL', number_format($this->summary['total_rendimento'] ?? 0, 2, ',', '.'), '(Entradas + Doações) - Saídas'],
        ];

        return collect($data);
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo do cabeçalho principal
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo do subtítulo
        $sheet->getStyle('A2:C2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '495057'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Estilo do período
        $sheet->getStyle('A3:C3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '28A745'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D4EDDA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'C3E6CB'],
                ],
            ],
        ]);

        // Estilo dos cabeçalhos da tabela
        $sheet->getStyle('A5:C5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo das células de dados
        $sheet->getStyle('A6:C10')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Destaque especial para o rendimento total
        $sheet->getStyle('A11:C11')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Mesclar células do cabeçalho
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');
        $sheet->mergeCells('A11:C11');

        // Ajustar altura das linhas
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(18);
        $sheet->getRowDimension(11)->setRowHeight(22);

        return $sheet;
    }
}

// Planilha de Contas
class AccountSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $accountSummary;
    protected $igreja;

    public function __construct($accountSummary, $igreja)
    {
        $this->accountSummary = $accountSummary;
        $this->igreja = $igreja;
    }

    public function title(): string
    {
        return 'Por Conta';
    }

    public function headings(): array
    {
        return [
            ['OmnIgrejas - ' . ($this->igreja->nome ?? 'Sistema OmnIgrejas')],
            ['Análise por Conta'],
            [''],
            ['Banco', 'Número da Conta', 'Entradas (AOA)', 'Saídas (AOA)', 'Saldo (AOA)', 'Status'],
        ];
    }

    public function collection()
    {
        return collect($this->accountSummary)->map(function ($account) {
            return [
                $account['banco'],
                $account['numero_conta'],
                number_format($account['entradas'], 2, ',', '.'),
                number_format($account['saidas'], 2, ',', '.'),
                number_format($account['saldo'], 2, ',', '.'),
                $account['ativa'] ? 'Ativa' : 'Inativa',
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo do cabeçalho principal
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo do subtítulo
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '495057'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Estilo dos cabeçalhos da tabela
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo das células de dados
        $sheet->getStyle('A5:F100')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Mesclar células do cabeçalho
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        // Ajustar altura das linhas
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(18);

        return $sheet;
    }
}

// Planilha de Movimentos
class MovementsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $recentMovements;
    protected $igreja;

    public function __construct($recentMovements, $igreja)
    {
        $this->recentMovements = $recentMovements;
        $this->igreja = $igreja;
    }

    public function title(): string
    {
        return 'Movimentos';
    }

    public function headings(): array
    {
        return [
            ['OmnIgrejas - ' . ($this->igreja->nome ?? 'Sistema OmnIgrejas')],
            ['Movimentos Recentes'],
            [''],
            ['Data', 'Tipo', 'Descrição', 'Categoria', 'Valor (AOA)', 'Responsável'],
        ];
    }

    public function collection()
    {
        return collect($this->recentMovements)->map(function ($movement) {
            return [
                \Carbon\Carbon::parse($movement->data_transacao)->format('d/m/Y'),
                ucfirst($movement->tipo),
                $movement->descricao,
                $movement->categoria->nome ?? 'N/A',
                number_format($movement->valor, 2, ',', '.'),
                $movement->responsavel->name ?? 'N/A',
            ];
        });
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo do cabeçalho principal
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo do subtítulo
        $sheet->getStyle('A2:F2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '495057'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Estilo dos cabeçalhos da tabela
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Estilo das células de dados
        $sheet->getStyle('A5:F100')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DEE2E6'],
                ],
            ],
        ]);

        // Mesclar células do cabeçalho
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');

        // Ajustar altura das linhas
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(4)->setRowHeight(18);

        return $sheet;
    }
}