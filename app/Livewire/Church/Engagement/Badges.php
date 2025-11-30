<?php

namespace App\Livewire\Church\Engagement;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Outros\EngajamentoBadge;
use App\Models\Outros\EngajamentoPonto;
use App\Services\EngajamentoService;

class Badges extends Component
{
    use WithPagination;

    // Propriedades para filtros
    public $filtroTipo = ''; // filtro por tipo de badge
    public $filtroPeriodo = 'todos'; // 'hoje', 'semana', 'mes', 'todos'
    public $filtroUsuario = '';
    public $search = '';

    // Propriedades para paginação
    public $perPage = 15;

    // Propriedades para modais
    public $showModal = false;
    public $showViewModal = false;
    public $editingBadge = null;
    public $viewingUser = null;
    public $badgeData = [
        'user_id' => '',
        'badge' => '',
        'badge_custom' => '',
        'descricao' => '',
    ];
    public $userDetails = [
        'user' => null,
        'total_pontos' => 0,
        'total_registros' => 0,
        'badges' => [],
        'ultima_atividade' => null,
    ];

    protected $rules = [
        'badgeData.user_id' => 'required|exists:users,id',
        'badgeData.badge' => 'required|string|max:100',
        'badgeData.descricao' => 'nullable|string|max:500',
    ];

    public function mount()
    {

    }

    public function render()
    {
        $igrejaId = Auth::user()->getIgrejaId();
        Log::info('Engagement Badges - Igreja ID do usuário logado: ' . $igrejaId);

        // Query base para filtros
        $baseQuery = EngajamentoBadge::where('igreja_id', $igrejaId);

        // Aplicar filtros na query base
        if ($this->filtroTipo) {
            $baseQuery->where('badge', 'like', '%' . $this->filtroTipo . '%');
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
                })->orWhere('badge', 'like', '%' . $this->search . '%')
                  ->orWhere('descricao', 'like', '%' . $this->search . '%');
            });
        }

        // Query agrupada por usuário com badges conquistados
        $groupedBadges = $baseQuery
            ->selectRaw('
                user_id,
                COUNT(*) as total_badges,
                MAX(data) as ultima_conquista,
                STRING_AGG(badge, \'|\') as badges_lista,
                STRING_AGG(descricao, \'|\') as descricoes_lista,
                STRING_AGG(data::text, \'|\') as datas_lista
            ')
            ->groupBy('user_id')
            ->with(['usuario'])
            ->orderBy('total_badges', 'desc')
            ->orderBy('ultima_conquista', 'desc')
            ->paginate($this->perPage);

        Log::info('Engagement Badges - Total users found: ' . $groupedBadges->total());

        // Estatísticas
        $stats = $this->getStats();

        // Usuários disponíveis para filtro (apenas membros da igreja do usuário logado)
        $usuarios = User::whereHas('membros', function($q) use ($igrejaId) {
            $q->where('igreja_id', $igrejaId)
              ->where('status', 'ativo');
        })->select('id', 'name')->get();

        // Tipos de badges disponíveis
        $tiposBadges = EngajamentoBadge::where('igreja_id', Auth::user()->getIgrejaId())
            ->distinct('badge')
            ->pluck('badge')
            ->toArray();

        return view('church.engagement.badges', compact('groupedBadges', 'stats', 'usuarios', 'tiposBadges'));
    }

    protected function getStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        return [
            'total_badges' => EngajamentoBadge::where('igreja_id', $igrejaId)->count(),
            'badges_hoje' => EngajamentoBadge::where('igreja_id', $igrejaId)
                ->whereDate('data', today())
                ->count(),
            'badges_semana' => EngajamentoBadge::where('igreja_id', $igrejaId)
                ->whereBetween('data', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'usuarios_com_badges' => EngajamentoBadge::where('igreja_id', $igrejaId)
                ->distinct('user_id')
                ->count('user_id'),
            'tipos_badges' => EngajamentoBadge::where('igreja_id', $igrejaId)
                ->distinct('badge')
                ->count('badge'),
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

    public function editar($badgeId)
    {
        $badge = EngajamentoBadge::find($badgeId);
        if ($badge) {
            $this->editingBadge = $badge;
            $this->badgeData = [
                'user_id' => $badge->user_id,
                'badge' => $badge->badge,
                'descricao' => $badge->descricao,
            ];
            $this->showModal = true;
            $this->dispatch('open-badge-modal');
        }
    }

    public function salvar()
    {
        $this->validate();

        $user = User::find($this->badgeData['user_id']);
        if (!$user) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Usuário não encontrado!'
            ]);
            return;
        }

        $engajamentoService = app(EngajamentoService::class);

        // Determinar o nome final do badge (usar custom se selecionado)
        $badgeFinal = $this->badgeData['badge'];
        if ($badgeFinal === 'manual' && !empty($this->badgeData['badge_custom'])) {
            $badgeFinal = $this->badgeData['badge_custom'];
        }

        if ($this->editingBadge) {
            // Para edição, atualiza diretamente (não usa o service para evitar duplicação)
            $this->editingBadge->update([
                'user_id' => $this->badgeData['user_id'],
                'badge' => $badgeFinal,
                'descricao' => $this->badgeData['descricao'],
            ]);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Badge atualizado com sucesso!'
            ]);
        } else {
            // Para criação, concede badge manualmente
            $success = $this->concederBadgeManual($user, $badgeFinal, $this->badgeData['descricao']);

            if ($success) {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Badge concedido com sucesso!'
                ]);
            } else {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Erro ao conceder badge!'
                ]);
            }
        }

        $this->resetModal();
        $this->showModal = false;
    }

    /**
     * Concede um badge manualmente ao usuário
     */
    private function concederBadgeManual(User $user, string $badgeNome, ?string $descricao = null): bool
    {
        try {
            // Verificar se usuário tem igreja vinculada
            $igrejaMembro = $user->membros()->where('status', 'ativo')->first();
            if (!$igrejaMembro) {
                return false;
            }

            // Verificar se já tem este badge
            $badgeExistente = EngajamentoBadge::where('user_id', $user->id)
                                             ->where('igreja_id', Auth::user()->getIgrejaId())
                                             ->where('badge', $badgeNome)
                                             ->exists();

            if ($badgeExistente) {
                return false; // Já tem o badge
            }

            // Conceder badge
            EngajamentoBadge::create([
                'user_id' => $user->id,
                'igreja_id' => Auth::user()->getIgrejaId(),
                'badge' => $badgeNome,
                'descricao' => $descricao ?? "Badge concedido manualmente pelo administrador",
                'data' => now(),
            ]);

            // Registrar pontos extras por badge concedido
            $engajamentoService = app(EngajamentoService::class);
            $engajamentoService->registrarPontos($user, 'badge_conquistado', null, "Badge concedido: {$badgeNome}");

            return true;

        } catch (\Exception $e) {
            Log::error("Erro ao conceder badge manual: " . $e->getMessage());
            return false;
        }
    }

    public function excluir($badgeId)
    {
        $badge = EngajamentoBadge::find($badgeId);
        if ($badge) {
            $badge->delete();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Badge removido com sucesso!'
            ]);
        }
    }

    protected function resetModal()
    {
        $this->editingBadge = null;
        $this->badgeData = [
            'user_id' => '',
            'badge' => '',
            'badge_custom' => '',
            'descricao' => '',
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
