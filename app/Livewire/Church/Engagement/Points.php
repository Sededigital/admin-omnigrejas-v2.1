<?php

namespace App\Livewire\Church\Engagement;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Outros\EngajamentoPonto;
use App\Models\Outros\EngajamentoBadge;
use App\Services\EngajamentoService;

class Points extends Component
{
    use WithPagination;

    // Propriedades para filtros
    public $filtroTipo = ''; // 'positivo', 'negativo', 'neutro'
    public $filtroPeriodo = 'todos'; // 'hoje', 'semana', 'mes', 'todos'
    public $filtroUsuario = '';
    public $search = '';

    // Propriedades para paginação
    public $perPage = 15;

    // Propriedades para modais
    public $showModal = false;
    public $showViewModal = false;
    public $editingPoint = null;
    public $viewingUser = null;
    public $pointData = [
        'user_id' => '',
        'pontos' => '',
        'motivo' => '',
        'motivo_custom' => '',
    ];
    public $userDetails = [
        'user' => null,
        'total_pontos' => 0,
        'total_registros' => 0,
        'badges' => [],
        'ultima_atividade' => null,
    ];

    protected $rules = [
        'pointData.user_id' => 'required|exists:users,id',
        'pointData.pontos' => 'required|integer|min:-1000|max:1000',
        'pointData.motivo' => 'required|string|max:255',
    ];

    public function mount()
    {

    }

    public function render()
    {
        $igrejaId = Auth::user()->getIgrejaId();
        

        // Query agrupada por usuário para mostrar pontuação total
        $baseQuery = EngajamentoPonto::where('igreja_id', $igrejaId);

        // Aplicar filtros na query base
        if ($this->filtroTipo) {
            switch ($this->filtroTipo) {
                case 'positivo':
                    $baseQuery->where('pontos', '>', 0);
                    break;
                case 'negativo':
                    $baseQuery->where('pontos', '<', 0);
                    break;
                case 'neutro':
                    $baseQuery->where('pontos', '=', 0);
                    break;
            }
        }

        if ($this->filtroPeriodo !== 'todos') {
            switch ($this->filtroPeriodo) {
                case 'hoje':
                    $baseQuery->whereDate('data', today());
                    break;
                case 'semana':
                    $baseQuery->whereBetween('data', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'mes':
                    $baseQuery->whereYear('data', now()->year)
                              ->whereMonth('data', now()->month);
                    break;
            }
        }

        if ($this->filtroUsuario) {
            $baseQuery->where('user_id', $this->filtroUsuario);
        }

        if ($this->search) {
            $baseQuery->where(function($q) {
                $q->whereHas('usuario', function($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('motivo', 'like', '%' . $this->search . '%');
            });
        }

        // Query agrupada por usuário com pontuação total
        $groupedPoints = $baseQuery
            ->selectRaw('
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
            ->paginate($this->perPage);

        Log::info('Engagement Points - Total users found: ' . $groupedPoints->total());

        // Estatísticas (mantém as mesmas)
        $stats = $this->getStats();

        // Usuários disponíveis para filtro (apenas membros da igreja do usuário logado)
        $usuarios = User::whereHas('membros', function($q) use ($igrejaId) {
            $q->where('igreja_id', $igrejaId)
              ->where('status', 'ativo');
        })->select('id', 'name')->get();

        return view('church.engagement.points', compact('groupedPoints', 'stats', 'usuarios'));
    }

    protected function getStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        return [
            'total_pontos' => EngajamentoPonto::where('igreja_id', $igrejaId)->sum('pontos'),
            'pontos_positivos' => EngajamentoPonto::where('igreja_id', $igrejaId)->where('pontos', '>', 0)->sum('pontos'),
            'pontos_negativos' => EngajamentoPonto::where('igreja_id', $igrejaId)->where('pontos', '<', 0)->sum('pontos'),
            'total_registros' => EngajamentoPonto::where('igreja_id', $igrejaId)->count(),
            'usuarios_ativos' => EngajamentoPonto::where('igreja_id', $igrejaId)->distinct('user_id')->count('user_id'),
        ];
    }

    public function abrirModal()
    {
        $this->resetModal();
        $this->showModal = true;
    }

    public function verDetalhes($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $igrejaId = Auth::user()->getIgrejaId();

            // Buscar estatísticas detalhadas do usuário
            $this->userDetails = [
                'user' => $user,
                'total_pontos' => EngajamentoPonto::where('user_id', $userId)
                    ->where('igreja_id', $igrejaId)
                    ->sum('pontos'),
                'total_registros' => EngajamentoPonto::where('user_id', $userId)
                    ->where('igreja_id', $igrejaId)
                    ->count(),
                'badges' => EngajamentoBadge::where('user_id', $userId)
                    ->where('igreja_id', $igrejaId)
                    ->with('usuario')
                    ->orderBy('data', 'desc')
                    ->get(),
                'ultima_atividade' => EngajamentoPonto::where('user_id', $userId)
                    ->where('igreja_id', $igrejaId)
                    ->max('data'),
            ];

            $this->showViewModal = true;
        }
    }

    public function editar($pointId)
    {
        $point = EngajamentoPonto::find($pointId);
        if ($point) {
            $this->editingPoint = $point;
            $this->pointData = [
                'user_id' => $point->user_id,
                'pontos' => $point->pontos,
                'motivo' => $point->motivo,
            ];
            $this->showModal = true;
            $this->dispatch('open-point-modal');
        }
    }

    public function salvar()
    {
        $this->validate();

        $user = User::find($this->pointData['user_id']);
        if (!$user) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Usuário não encontrado!'
            ]);
            return;
        }

        $engajamentoService = app(EngajamentoService::class);

        // Determinar o motivo final (usar custom se selecionado)
        $motivoFinal = $this->pointData['motivo'];
        if ($motivoFinal === 'manual' && !empty($this->pointData['motivo_custom'])) {
            $motivoFinal = $this->pointData['motivo_custom'];
        }

        if ($this->editingPoint) {
            // Para edição, atualiza diretamente (não usa o service para evitar duplicação)
            $this->editingPoint->update([
                'user_id' => $this->pointData['user_id'],
                'pontos' => $this->pointData['pontos'],
                'motivo' => $motivoFinal,
            ]);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pontuação atualizada com sucesso!'
            ]);
        } else {
            // Para criação, usa o service para manter consistência e verificar badges
            $success = $engajamentoService->registrarPontos(
                $user,
                $motivoFinal,
                $this->pointData['pontos'],
                'Pontuação manual registrada pelo administrador'
            );

            if ($success) {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pontuação registrada com sucesso!'
                ]);
            } else {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Erro ao registrar pontuação!'
                ]);
            }
        }

        $this->resetModal();
        $this->showModal = false;
    }

    public function excluir($pointId)
    {
        $point = EngajamentoPonto::find($pointId);
        if ($point) {
            $point->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Pontuação excluída com sucesso!'
            ]);
        }
    }

    protected function resetModal()
    {
        $this->editingPoint = null;
        $this->pointData = [
            'user_id' => '',
            'pontos' => '',
            'motivo' => '',
            'motivo_custom' => '',
        ];
    }

    public function limparFiltros()
    {
        $this->filtroTipo = '';
        $this->filtroPeriodo = 'todos';
        $this->filtroUsuario = '';
        $this->search = '';
        $this->resetPage();
    }
}
