<?php

namespace App\Livewire\Church\Members;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Cursos\Curso;
use App\Models\Eventos\Evento;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Igrejas\Ministerio;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use App\Models\Outros\EngajamentoPonto;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\Financeiro\FinanceiroMovimento;
use App\Models\Financeiro\FinanceiroCategoria;


#[Title('Estatística | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Statistics extends Component
{

    public $selectedPeriod = '30';
    public $startDate;
    public $endDate;
    public $paperSize = 'a4'; // a4 ou a3

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function applyFilters()
    {
        // Aplicar filtros personalizados
        $this->dispatch('filters-applied');
        $this->dispatch('refreshCharts'); // Notificar JavaScript para atualizar gráficos
    }

    public function refreshData()
    {
        // Atualizar dados
        session()->flash('message', 'Dados atualizados com sucesso!');
        $this->dispatch('refreshCharts'); // Notificar JavaScript para recriar gráficos
    }

    public function dehydrate()
    {
        // Quando o componente for "hidratado" (atualizado), passar os dados atualizados para o JavaScript
        $this->dispatch('updateChartData', $this->getChartData());
    }

    private function getChartData($startDate = null, $endDate = null)
    {
        $igrejaId = $this->getIgrejaId();

        // Se não foram passadas datas, usar as do componente
        if (!$startDate || !$endDate) {
            $startDate = Carbon::parse($this->startDate);
            $endDate = Carbon::parse($this->endDate);
        }

        // Crescimento de membros no período filtrado
        $membersGrowthData = [];
        $membersGrowthLabels = [];

        // Calcular diferença em dias entre as datas
        $daysDiff = $startDate->diffInDays($endDate);
        $monthsDiff = $startDate->diffInMonths($endDate);

        if ($daysDiff <= 31) {
            // Para períodos curtos (até 1 mês): mostrar por dia
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $membersGrowthLabels[] = $currentDate->format('d/m');
                $count = IgrejaMembro::where('igreja_id', $igrejaId)
                    ->whereDate('created_at', $currentDate->format('Y-m-d'))
                    ->count();
                $membersGrowthData[] = $count;
                $currentDate->addDay();
            }
        } elseif ($monthsDiff <= 3) {
            // Para períodos de 1-3 meses: mostrar por mês
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                // Incluir ano se for período customizado ou multi-ano
                if ($this->selectedPeriod === 'custom' || $startDate->year !== $endDate->year) {
                    $membersGrowthLabels[] = $currentDate->format('M/Y');
                } else {
                    $membersGrowthLabels[] = $currentDate->format('M');
                }

                $count = IgrejaMembro::where('igreja_id', $igrejaId)
                    ->whereYear('created_at', $currentDate->year)
                    ->whereMonth('created_at', $currentDate->month)
                    ->count();
                $membersGrowthData[] = $count;
                $currentDate->addMonth();
            }
        } elseif ($monthsDiff <= 12) {
            // Para períodos de 3-12 meses: mostrar por mês
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                // Incluir ano se for período customizado ou multi-ano
                if ($this->selectedPeriod === 'custom' || $startDate->year !== $endDate->year) {
                    $membersGrowthLabels[] = $currentDate->format('M/Y');
                } else {
                    $membersGrowthLabels[] = $currentDate->format('M');
                }

                $count = IgrejaMembro::where('igreja_id', $igrejaId)
                    ->whereYear('created_at', $currentDate->year)
                    ->whereMonth('created_at', $currentDate->month)
                    ->count();
                $membersGrowthData[] = $count;
                $currentDate->addMonth();
            }
        } else {
            // Para períodos longos (> 1 ano): mostrar por mês com ano
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $membersGrowthLabels[] = $currentDate->format('M/Y');
                $count = IgrejaMembro::where('igreja_id', $igrejaId)
                    ->whereYear('created_at', $currentDate->year)
                    ->whereMonth('created_at', $currentDate->month)
                    ->count();
                $membersGrowthData[] = $count;
                $currentDate->addMonth();
            }
        }

      

        return [
            'members' => [
                'labels' => $membersGrowthLabels,
                'data' => $membersGrowthData
            ],
            'roles' => [
                'labels' => ['Membros', 'Obreiros', 'Diáconos', 'Ministros', 'Pastores'],
                'data' => [
                    IgrejaMembro::where('igreja_id', $igrejaId)->where('cargo', 'membro')->count(),
                    IgrejaMembro::where('igreja_id', $igrejaId)->where('cargo', 'obreiro')->count(),
                    IgrejaMembro::where('igreja_id', $igrejaId)->where('cargo', 'diacono')->count(),
                    IgrejaMembro::where('igreja_id', $igrejaId)->where('cargo', 'ministro')->count(),
                    IgrejaMembro::where('igreja_id', $igrejaId)->where('cargo', 'pastor')->count(),
                ]
            ],
            'events' => [
                'labels' => ['Cultos', 'Estudos', 'Eventos', 'Reuniões'],
                'data' => [
                    Evento::where('igreja_id', $igrejaId)->where('tipo', 'culto')->whereBetween('data_evento', [$startDate, $endDate])->count(),
                    Evento::where('igreja_id', $igrejaId)->where('tipo', 'estudo')->whereBetween('data_evento', [$startDate, $endDate])->count(),
                    Evento::where('igreja_id', $igrejaId)->where('tipo', 'evento')->whereBetween('data_evento', [$startDate, $endDate])->count(),
                    Evento::where('igreja_id', $igrejaId)->where('tipo', 'reuniao')->whereBetween('data_evento', [$startDate, $endDate])->count(),
                ]
            ],
            'ministries' => [
                'labels' => Ministerio::where('igreja_id', $igrejaId)->pluck('nome')->toArray(),
                'data' => Ministerio::where('igreja_id', $igrejaId)
                    ->withCount(['membros' => function($query) use ($startDate, $endDate) {
                        $query->whereBetween('igreja_membros_ministerios.created_at', [$startDate, $endDate]);
                    }])
                    ->pluck('membros_count')
                    ->toArray()
            ],
            'gender' => $this->getGenderDistribution($igrejaId),
            'age_groups' => $this->getAgeGroups($igrejaId),
            'chat_activity' => $this->getChatActivity($igrejaId, $startDate, $endDate),
            'financial_categories' => $this->getFinancialCategoriesData($igrejaId, $startDate, $endDate)
        ];
    }

    public function exportReport()
    {
        try {
            $igrejaId = $this->getIgrejaId();
            $startDate = Carbon::parse($this->startDate);
            $endDate = Carbon::parse($this->endDate);

            // Garantir que paperSize tenha um valor padrão
            $paperSize = $this->paperSize ?? 'a4';


            // Buscar dados da igreja
            $igreja = Igreja::find($igrejaId);

            // Preparar dados para o PDF com filtros aplicados
            $stats = $this->getStatsData($igrejaId, $startDate, $endDate);
            $chartData = $this->getChartData($startDate, $endDate);
            $topMembers = $this->getTopMembers($igrejaId, $startDate, $endDate);
            $popularEvents = $this->getPopularEvents($igrejaId, $startDate, $endDate);
            $financialSummary = $this->getFinancialSummary();

            // Gerar PDF
            $pdf = Pdf::loadView('church.members.pdf.statistics-report', compact(
                'igreja',
                'stats',
                'chartData',
                'topMembers',
                'popularEvents',
                'financialSummary',
                'startDate',
                'endDate',
                'paperSize'
            ));

            // Configurar PDF com tamanho selecionado
            $pdf->setPaper($paperSize, 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96,
                'defaultPaperSize' => $paperSize
            ]);

            // Nome do arquivo
            $fileName = 'relatorio-estatisticas-' . $igreja->nome . '-' . now()->format('Y-m-d-H-i-s') . '.pdf';


            // Retornar download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (\Exception $e) {


            // Redirecionar de volta
            return redirect()->back();
        }
    }

    public function updatedStartDate()
    {
        // Quando a data inicial muda, aplicar filtros se for período customizado
        if ($this->selectedPeriod === 'custom') {
            $this->applyFilters();
        }
    }

    public function updatedEndDate()
    {
        // Quando a data final muda, aplicar filtros se for período customizado
        if ($this->selectedPeriod === 'custom') {
            $this->applyFilters();
        }
    }

    public function updatedSelectedPeriod()
    {
        // Quando o período muda, atualizar automaticamente
        if ($this->selectedPeriod !== 'custom') {
            $days = (int) $this->selectedPeriod;
            $this->startDate = now()->subDays($days)->format('Y-m-d');
            $this->endDate = now()->format('Y-m-d');
            $this->applyFilters(); // Aplicar filtros automaticamente
        }
    }

    private function getIgrejaId()
    {
        return Auth::user()->getIgrejaId();
    }

    private function getStatsData($igrejaId, $startDate, $endDate)
    {
        // Estatísticas principais (sempre totais, não filtradas por data)
        $totalMembers = IgrejaMembro::where('igreja_id', $igrejaId)->count();
        $activeMembers = IgrejaMembro::where('igreja_id', $igrejaId)->where('status', 'ativo')->count();

        // Eventos no período filtrado
        $eventsInPeriod = Evento::where('igreja_id', $igrejaId)
            ->whereBetween('data_evento', [$startDate, $endDate])
            ->count();

        // Cursos ativos (sempre total, não filtrado por data)
        $activeCourses = Curso::where('igreja_id', $igrejaId)->where('status', 'ativo')->count();

        // Crescimento de membros no período filtrado
        $membersInPeriod = IgrejaMembro::where('igreja_id', $igrejaId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Membros antes do período para calcular crescimento
        $membersBeforePeriod = IgrejaMembro::where('igreja_id', $igrejaId)
            ->where('created_at', '<', $startDate)
            ->count();

        $membersGrowth = $membersBeforePeriod > 0 ?
            round((($membersBeforePeriod + $membersInPeriod - $membersBeforePeriod) / $membersBeforePeriod) * 100, 1) : 0;

        // Calcular média de participantes em eventos no período filtrado
        $avgAttendance = $this->calculateAverageAttendance($igrejaId, $startDate, $endDate);

        // Calcular total de alunos matriculados
        $totalStudents = $this->calculateTotalStudents($igrejaId);

        // Novas métricas
        $retentionRate = $this->calculateRetentionRate($igrejaId);
        $averageAge = $this->calculateAverageAge($igrejaId);
        $conversionRate = $this->getConversionRate($igrejaId, $startDate, $endDate);
        $birthdaysThisMonth = $this->getBirthdaysThisMonth($igrejaId);

        return [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'events_this_month' => $eventsInPeriod, // Agora usa o período filtrado
            'active_courses' => $activeCourses,
            'members_growth' => $membersGrowth,
            'active_percentage' => $totalMembers > 0 ? round(($activeMembers / $totalMembers) * 100, 1) : 0,
            'avg_attendance' => $avgAttendance,
            'total_students' => $totalStudents,
            'retention_rate' => $retentionRate,
            'average_age' => $averageAge,
            'conversion_rate' => $conversionRate,
            'birthdays_this_month' => $birthdaysThisMonth,
        ];
    }

    private function getTopMembers($igrejaId, $startDate, $endDate)
    {

        // Query agrupada por usuário para mostrar pontuação total (igual ao Points.php)
        // Primeiro, buscar todos os membros ativos da igreja
        $activeMembers = IgrejaMembro::where('igreja_id', $igrejaId)
            ->where('status', 'ativo')
            ->pluck('user_id')
            ->toArray();

        if (empty($activeMembers)) {

            return collect();
        }

        // Query agrupada por usuário para mostrar pontuação total (igual ao Points.php)
        // Usar whereDate para filtrar apenas pela data (ignorando hora)
        $groupedPointsQuery = EngajamentoPonto::where('igreja_id', $igrejaId)
            ->whereDate('data', '>=', $startDate->format('Y-m-d'))
            ->whereDate('data', '<=', $endDate->format('Y-m-d'))
            ->whereIn('user_id', $activeMembers); // Apenas membros ativos da igreja


        $groupedPoints = $groupedPointsQuery->selectRaw('
                user_id,
                SUM(pontos) as pontos_totais,
                COUNT(*) as total_registros,
                MAX(data) as ultima_atividade,
                STRING_AGG(DISTINCT motivo, \', \') as motivos
            ')
            ->groupBy('user_id')
            ->with(['usuario'])
            ->orderBy('pontos_totais', 'desc')
            ->orderBy('ultima_atividade', 'desc')
            ->limit(10)
            ->get();



        // Se não há pontos, criar registros vazios para os membros ativos (top 10)
        if ($groupedPoints->isEmpty()) {

            // Pegar os primeiros 10 membros ativos (ordenados por data de entrada)
            $topActiveMembers = IgrejaMembro::where('igreja_id', $igrejaId)
                ->where('status', 'ativo')
                ->with('user')
                ->orderBy('data_entrada', 'desc')
                ->limit(10)
                ->get();

            $members = $topActiveMembers->map(function($member) {
                // Criar objeto similar ao groupedPoints
                $member->total_points = 0;
                $member->activities_count = 0;
                $member->ultima_atividade = $member->created_at;
                $member->motivos = '';



                return $member;
            });

            return $members;
        }

        // Mapear para o formato esperado pela view
        $members = $groupedPoints->map(function($point) use ($igrejaId) {
            // Buscar dados do membro
            $member = IgrejaMembro::where('user_id', $point->user_id)
                ->where('igreja_id', $igrejaId)
                ->where('status', 'ativo')
                ->with('user')
                ->first();

            if ($member) {
                $member->total_points = $point->pontos_totais;
                $member->activities_count = $point->total_registros;
                $member->ultima_atividade = $point->ultima_atividade;
                $member->motivos = $point->motivos;



                return $member;
            }


            return null;
        })->filter()->values(); // Remover nulls e reindexar


        return $members;
    }

    private function getPopularEvents($igrejaId, $startDate, $endDate)
    {
        return Evento::where('igreja_id', $igrejaId)
            ->whereBetween('data_evento', [$startDate, $endDate])
            ->limit(10)
            ->get()
            ->map(function($event) {
                // Simplesmente contar o número de escalas (cada escala = 1 participante)
                // Se um membro tem múltiplas funções, conta múltiplas vezes
                $participants_count = $event->escalas()->count();

                // Se não há escalas, assumir pelo menos 1 participante
                if ($participants_count === 0) {
                    $participants_count = 1;
                }

                $event->participants_count = $participants_count;
                return $event;
            })
            ->sortByDesc('participants_count');
    }

    private function getFinancialSummary()
    {
        $igrejaId = $this->getIgrejaId();
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Receitas (entradas) no período
        $income = FinanceiroMovimento::where('igreja_id', $igrejaId)
            ->where('tipo', 'entrada')
            ->whereBetween('data_transacao', [$startDate, $endDate])
            ->sum('valor');

        // Despesas (saídas) no período
        $expenses = FinanceiroMovimento::where('igreja_id', $igrejaId)
            ->where('tipo', 'saida')
            ->whereBetween('data_transacao', [$startDate, $endDate])
            ->sum('valor');

        // Calcular saldo
        $balance = $income - $expenses;

        // Doações - procurar por categoria de doações ou usar todas as entradas
        $donationsCategory = FinanceiroCategoria::where('igreja_id', $igrejaId)
            ->where('nome', 'like', '%doa%')
            ->orWhere('nome', 'like', '%ofer%')
            ->first();

        if ($donationsCategory) {
            // Se encontrou categoria de doações, usar apenas essa
            $donations = FinanceiroMovimento::where('igreja_id', $igrejaId)
                ->where('categoria_id', $donationsCategory->id)
                ->where('tipo', 'entrada')
                ->whereBetween('data_transacao', [$startDate, $endDate])
                ->sum('valor');
        } else {
            // Caso contrário, usar todas as entradas como doações
            $donations = $income;
        }

        // Formatar valores em AOA (Kwanza)
        return [
            'income' => $this->formatCurrency($income),
            'expenses' => $this->formatCurrency($expenses),
            'balance' => $this->formatCurrency($balance),
            'donations' => $this->formatCurrency($donations),
            'raw_income' => $income,
            'raw_expenses' => $expenses,
            'raw_balance' => $balance,
            'raw_donations' => $donations
        ];
    }

    private function formatCurrency($value)
    {
        return number_format($value, 2, ',', '.') . ' AOA';
    }

    private function calculateAverageAttendance($igrejaId, $startDate, $endDate)
    {
        // Buscar eventos no período
        $events = Evento::where('igreja_id', $igrejaId)
            ->whereBetween('data_evento', [$startDate, $endDate])
            ->get();

        if ($events->isEmpty()) {
            return 0;
        }

        $totalParticipants = 0;
        $eventCount = 0;

        foreach ($events as $event) {
            // Simplesmente contar o número de escalas
            $participants = $event->escalas()->count();

            // Se não há escalas, assumir pelo menos 1 participante
            if ($participants === 0) {
                $participants = 1;
            }

            $totalParticipants += $participants;
            $eventCount++;
        }

        return $eventCount > 0 ? round($totalParticipants / $eventCount, 1) : 0;
    }

    private function calculateTotalStudents($igrejaId)
    {
        // Buscar matrículas ativas em cursos da igreja
        $totalStudents = \App\Models\Cursos\CursoMatricula::whereHas('turma.curso', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId);
        })
        ->where('status', 'ativo')
        ->count();

        return $totalStudents;
    }

    private function getGenderDistribution($igrejaId)
    {
        $genders = \App\Models\Igrejas\MembroPerfil::whereHas('membro', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId)->where('status', 'ativo');
        })
        ->selectRaw('genero, COUNT(*) as count')
        ->groupBy('genero')
        ->pluck('count', 'genero')
        ->toArray();

        $labels = [];
        $data = [];

        foreach (['masculino', 'feminino', 'nao_informado'] as $gender) {
            $labels[] = match($gender) {
                'masculino' => 'Masculino',
                'feminino' => 'Feminino',
                'nao_informado' => 'Não Informado'
            };
            $data[] = $genders[$gender] ?? 0;
        }

        // Garantir que sempre tenha dados, mesmo que zeros
        if (empty($data) || array_sum($data) === 0) {
            $data = [0, 0, 0]; // Valores padrão para garantir que o gráfico apareça
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getAgeGroups($igrejaId)
    {
        $ageGroups = [
            '18-25' => 0,
            '26-35' => 0,
            '36-50' => 0,
            '51+' => 0
        ];

        $birthdates = \App\Models\Igrejas\MembroPerfil::whereHas('membro', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId)->where('status', 'ativo');
        })
        ->whereNotNull('data_nascimento')
        ->pluck('data_nascimento')
        ->toArray();

        foreach ($birthdates as $birthdate) {
            $age = Carbon::parse($birthdate)->age;

            if ($age >= 18 && $age <= 25) {
                $ageGroups['18-25']++;
            } elseif ($age >= 26 && $age <= 35) {
                $ageGroups['26-35']++;
            } elseif ($age >= 36 && $age <= 50) {
                $ageGroups['36-50']++;
            } elseif ($age > 50) {
                $ageGroups['51+']++;
            }
        }

        // Garantir que sempre tenha dados, mesmo que zeros
        $data = array_values($ageGroups);
        if (empty($data) || array_sum($data) === 0) {
            $data = [0, 0, 0, 0]; // Valores padrão para garantir que o gráfico apareça
        }

        return [
            'labels' => array_keys($ageGroups),
            'data' => $data
        ];
    }

    private function getChatActivity($igrejaId, $startDate, $endDate)
    {
        // Usuários mais ativos no chat da igreja
        $chatActivity = \App\Models\Chats\IgrejaChatMensagem::whereHas('chat', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->selectRaw('autor_id, COUNT(*) as message_count')
        ->groupBy('autor_id')
        ->orderBy('message_count', 'desc')
        ->limit(10)
        ->with('autor')
        ->get();

        $labels = [];
        $data = [];

        foreach ($chatActivity as $activity) {
            $labels[] = $activity->autor->name ?? 'Usuário ' . $activity->autor_id;
            $data[] = $activity->message_count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getFinancialCategoriesData($igrejaId, $startDate, $endDate)
    {
        $categories = FinanceiroCategoria::where('igreja_id', $igrejaId)->get();

        $incomeData = [];
        $expenseData = [];
        $labels = [];

        foreach ($categories as $category) {
            $labels[] = $category->nome;

            $income = FinanceiroMovimento::where('igreja_id', $igrejaId)
                ->where('categoria_id', $category->id)
                ->where('tipo', 'entrada')
                ->whereBetween('data_transacao', [$startDate, $endDate])
                ->sum('valor');

            $expense = FinanceiroMovimento::where('igreja_id', $igrejaId)
                ->where('categoria_id', $category->id)
                ->where('tipo', 'saida')
                ->whereBetween('data_transacao', [$startDate, $endDate])
                ->sum('valor');

            $incomeData[] = $income;
            $expenseData[] = $expense;
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expenses' => $expenseData
        ];
    }

    private function getBirthdaysThisMonth($igrejaId)
    {
        $currentMonth = now()->month;

        return \App\Models\Igrejas\MembroPerfil::whereHas('membro', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId)->where('status', 'ativo');
        })
        ->whereNotNull('data_nascimento')
        ->whereRaw('EXTRACT(MONTH FROM data_nascimento) = ?', [$currentMonth])
        ->with('membro.user')
        ->orderByRaw('EXTRACT(DAY FROM data_nascimento)')
        ->get()
        ->map(function($perfil) {
            return [
                'nome' => $perfil->membro->user->name ?? 'Nome não disponível',
                'dia' => Carbon::parse($perfil->data_nascimento)->day,
                'idade' => Carbon::parse($perfil->data_nascimento)->age
            ];
        });
    }

    private function calculateRetentionRate($igrejaId)
    {
        // Membros ativos atuais
        $activeMembers = IgrejaMembro::where('igreja_id', $igrejaId)
            ->where('status', 'ativo')
            ->count();

        // Membros que se tornaram inativos nos últimos 12 meses
        $inactiveLastYear = IgrejaMembro::where('igreja_id', $igrejaId)
            ->where('status', 'inativo')
            ->where('updated_at', '>=', now()->subYear())
            ->count();

        // Membros totais (ativos + inativos dos últimos 12 meses)
        $totalMembers = $activeMembers + $inactiveLastYear;

        return $totalMembers > 0 ? round(($activeMembers / $totalMembers) * 100, 1) : 0;
    }

    private function calculateAverageAge($igrejaId)
    {
        $ages = \App\Models\Igrejas\MembroPerfil::whereHas('membro', function($query) use ($igrejaId) {
            $query->where('igreja_id', $igrejaId)->where('status', 'ativo');
        })
        ->whereNotNull('data_nascimento')
        ->pluck('data_nascimento')
        ->map(function($birthdate) {
            return Carbon::parse($birthdate)->age;
        })
        ->toArray();

        $averageAge = !empty($ages) ? round(array_sum($ages) / count($ages), 1) : 0;

        return $averageAge;
    }

    private function getConversionRate($igrejaId, $startDate, $endDate)
    {
        // Visitantes (pode ser estimado pelos relatórios de culto)
        $visitantes = \App\Models\Igrejas\RelatorioCulto::where('igreja_id', $igrejaId)
            ->whereBetween('data_relatorio', [$startDate, $endDate])
            ->sum('numero_visitantes');

        // Batismos/decisões
        $batismos = \App\Models\Igrejas\RelatorioCulto::where('igreja_id', $igrejaId)
            ->whereBetween('data_relatorio', [$startDate, $endDate])
            ->sum('numero_batismos');

        $decisoes = \App\Models\Igrejas\RelatorioCulto::where('igreja_id', $igrejaId)
            ->whereBetween('data_relatorio', [$startDate, $endDate])
            ->sum('numero_decisoes');

        $conversoes = $batismos + $decisoes;

        return $visitantes > 0 ? round(($conversoes / $visitantes) * 100, 1) : 0;
    }

    public function render()
    {
        $igrejaId = $this->getIgrejaId();
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        // Usar o método getStatsData() que já aplica os filtros corretamente
        $stats = $this->getStatsData($igrejaId, $startDate, $endDate);

        // O crescimento de membros agora é calculado dentro do getChartData()
        // que já respeita os filtros aplicados

        // Dados para gráficos usando o método centralizado
        $chartData = $this->getChartData($startDate, $endDate);

        // Top membros mais ativos (baseado em pontos de engajamento no período filtrado)
        $topMembers = $this->getTopMembers($igrejaId, $startDate, $endDate);

        // Eventos mais populares
        $popularEvents = $this->getPopularEvents($igrejaId, $startDate, $endDate)->take(5);

        // Resumo financeiro
        $financialSummary = $this->getFinancialSummary();

        return view('church.members.statistics', compact(
            'stats',
            'chartData',
            'topMembers',
            'popularEvents',
            'financialSummary'
        ));
    }
}
