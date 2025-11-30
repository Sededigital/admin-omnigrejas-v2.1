<?php

namespace App\Livewire\Church\LeadershipBody;

use App\Models\Igrejas\IgrejaMembro;
use App\Models\Igrejas\Igreja;
use App\Helpers\RBAC\PermissionHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Corpo de Liderança')]
#[Layout('components.layouts.app')]
class LeadershipBody extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ========================================
    // PROPRIEDADES PARA FILTROS
    // ========================================
    public $search = '';
    public $cargoFilter = '';
    public $igrejaFilter = '';
    public $statusFilter = 'ativo';

    // ========================================
    // PROPRIEDADES PARA MODAL
    // ========================================
    public $showLeaderModal = false;
    public $selectedLeader = null;
    public $leaderDetails = null;

    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================
    protected $permissionHelper;

    public function boot()
    {
        $this->permissionHelper = new PermissionHelper(Auth::user());
    }

    public function mount()
    {
        // Verificar permissões básicas
        if (!$this->permissionHelper->hasPermission('gerenciar_corpo_lideranca')) {
            abort(403, 'Você não tem permissão para acessar o Corpo de Liderança.');
        }
    }

    // ========================================
    // MÉTODOS PARA BUSCAR DADOS
    // ========================================

    /**
     * Retorna IDs das igrejas que o usuário pode acessar
     */
    private function getIgrejasPermitidasIds()
    {
        $userId = Auth::id();

        // Igrejas onde o usuário é membro
        $igrejasComoMembro = DB::table('igreja_membros')
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->pluck('igreja_id')
            ->toArray();

        // Igrejas que o usuário criou
        $igrejasCriadas = Igreja::where('created_by', $userId)
            ->pluck('id')
            ->toArray();

        // Combinar e remover duplicatas
        return array_unique(array_merge($igrejasComoMembro, $igrejasCriadas));
    }

    /**
     * Busca todos os líderes das igrejas permitidas (sem repetições)
     */
    public function getLideresProperty()
    {
        $igrejasPermitidas = $this->getIgrejasPermitidasIds();

        // Primeiro, buscar todos os registros de membros líderes
        $query = IgrejaMembro::with(['user', 'igreja'])
            ->whereIn('igreja_id', $igrejasPermitidas)
            ->whereIn('cargo', ['admin', 'pastor', 'ministro', 'obreiro', 'diacono' ])
            ->where('status', 'ativo');

        // Aplicar filtros
        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'ILIKE', '%' . $this->search . '%')
                  ->orWhere('email', 'ILIKE', '%' . $this->search . '%');
            });
        }

        if ($this->cargoFilter) {
            $query->where('cargo', $this->cargoFilter);
        }

        if ($this->igrejaFilter) {
            $query->where('igreja_id', $this->igrejaFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Buscar todos os registros
        $lideresRecords = $query->get();

        // Agrupar por user_id para evitar repetições
        $lideresAgrupados = [];
        foreach ($lideresRecords as $record) {
            $userId = $record->user_id;

            if (!isset($lideresAgrupados[$userId])) {
                // Primeiro registro deste usuário
                $lideresAgrupados[$userId] = [
                    'user' => $record->user,
                    'cargo' => $record->cargo,
                    'status' => $record->status,
                    'data_entrada' => $record->data_entrada, // Usar a primeira data encontrada
                    'numero_membro' => $record->numero_membro,
                    'principal' => $record->principal,
                    'igrejas' => [],
                    'created_at' => $record->created_at,
                ];
            }

            // Adicionar igreja à lista (evitar duplicatas)
            $igrejaJaAdicionada = false;
            foreach ($lideresAgrupados[$userId]['igrejas'] as $igreja) {
                if ($igreja->id === $record->igreja->id) {
                    $igrejaJaAdicionada = true;
                    break;
                }
            }

            if (!$igrejaJaAdicionada) {
                $lideresAgrupados[$userId]['igrejas'][] = $record->igreja;
            }

            // Atualizar data de entrada para a mais antiga se houver múltiplas
            if ($record->data_entrada < $lideresAgrupados[$userId]['data_entrada']) {
                $lideresAgrupados[$userId]['data_entrada'] = $record->data_entrada;
            }
        }

        // Converter para coleção e ordenar
        $lideresCollection = collect($lideresAgrupados)->map(function($lider) {
            return (object) $lider;
        })->sortBy(function($lider) {
            // Ordenar por cargo (admin primeiro, depois pastor, etc.)
            $ordemCargos = [ 'admin' => 1,  'pastor' => 2, 'ministro' => 3, 'obreiro' => 4, 'diacono' => 5];
            return $ordemCargos[$lider->cargo] ?? 6;
        })->sortByDesc('created_at');

        // Implementar paginação manual
        $perPage = 12;
        $currentPage = $this->getPage();
        $total = $lideresCollection->count();
        $offset = ($currentPage - 1) * $perPage;

        $lideresPaginados = $lideresCollection->slice($offset, $perPage);

        // Criar objeto de paginação manual
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $lideresPaginados,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return $paginator;
    }

    /**
     * Busca igrejas para filtro
     */
    public function getIgrejasProperty()
    {
        $igrejasPermitidas = $this->getIgrejasPermitidasIds();

        return Igreja::whereIn('id', $igrejasPermitidas)
                    ->where('status_aprovacao', 'aprovado')
                    ->orderBy('nome')
                    ->get();
    }

    /**
     * Estatísticas dos líderes
     */
    public function getEstatisticasProperty()
    {
        $igrejasPermitidas = $this->getIgrejasPermitidasIds();

        $stats = IgrejaMembro::whereIn('igreja_id', $igrejasPermitidas)
            ->whereIn('cargo', ['admin', 'pastor', 'ministro', 'obreiro', 'diacono' ])
            ->where('status', 'ativo')
            ->selectRaw('cargo, COUNT(*) as total')
            ->groupBy('cargo')
            ->pluck('total', 'cargo')
            ->toArray();

        return [
            'total_lideres' => array_sum($stats),
            'admins' => $stats['admin'] ?? 0,
            'pastores' => $stats['pastor'] ?? 0,
            'ministros' => $stats['ministro'] ?? 0,
            'obreiros' => $stats['obreiro'] ?? 0,
            'diaconos' => $stats['diacono'] ?? 0,
        ];
    }

    // ========================================
    // MÉTODOS DE UTILITÁRIO
    // ========================================

    /**
     * Retorna a cor do badge baseada no cargo
     */
    public function getCargoBadgeClass($cargo)
    {
        return match($cargo) {
            'admin' => 'bg-danger',
            'pastor' => 'bg-primary',
            'ministro' => 'bg-info',
            'obreiro' => 'bg-secondary',
            'diacono' => 'bg-success',
            default => 'bg-light text-dark'
        };
    }

    /**
     * Retorna o nome formatado do cargo
     */
    public function getCargoNome($cargo)
    {
        return match($cargo) {
            'admin' => 'Administrador',
            'pastor' => 'Pastor',
            'ministro' => 'Ministro',
            'obreiro' => 'Obreiro',
            'diacono' => 'Diácono',
            default => ucfirst($cargo)
        };
    }

    /**
     * Retorna ícone baseado no cargo
     */
    public function getCargoIcon($cargo)
    {
        return match($cargo) {
            'admin' => 'fas fa-crown',
            'pastor' => 'fas fa-church',
            'ministro' => 'fas fa-praying-hands',
            'obreiro' => 'fas fa-hands-helping',
            'diacono' => 'fas fa-hand-holding-heart',
            default => 'fas fa-user'
        };
    }

    /**
     * Gera iniciais do nome
     */
    public function getIniciais($nome)
    {
        $palavras = explode(' ', trim($nome));
        $iniciais = '';

        foreach ($palavras as $palavra) {
            $iniciais .= strtoupper(substr($palavra, 0, 1));
            if (strlen($iniciais) >= 2) break;
        }

        return str_pad($iniciais, 2, 'U');
    }

    // ========================================
    // MÉTODOS DE AÇÃO
    // ========================================

    public function limparFiltros()
    {
        $this->search = '';
        $this->cargoFilter = '';
        $this->igrejaFilter = '';
        $this->statusFilter = 'ativo';
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCargoFilter()
    {
        $this->resetPage();
    }

    public function updatedIgrejaFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    // ========================================
    // MÉTODOS DO MODAL
    // ========================================

    public function openLeaderModal($userId)
    {
        // Limpar dados anteriores primeiro
        $this->leaderDetails = null;
        $this->selectedLeader = $userId;

        // Pequeno delay para garantir que o modal abra primeiro
        $this->dispatch('open-leader-modal');

        // Buscar detalhes do líder
        $this->loadLeaderDetails();
    }

    public function closeLeaderModal()
    {
        $this->showLeaderModal = false;
        $this->selectedLeader = null;
        $this->leaderDetails = null;
    }

    private function loadLeaderDetails()
    {
        if (!$this->selectedLeader) {
            $this->leaderDetails = null;
            return;
        }

        $igrejasPermitidas = $this->getIgrejasPermitidasIds();

        // Buscar todas as posições de liderança do usuário
        $liderancas = IgrejaMembro::with(['igreja', 'user'])
            ->where('user_id', $this->selectedLeader)
            ->whereIn('igreja_id', $igrejasPermitidas)
            ->whereIn('cargo', ['admin', 'pastor', 'ministro', 'obreiro', 'diacono'])
            ->where('status', 'ativo')
            ->get();

        // Mesmo que não encontre lideranças, buscar o usuário para mostrar informações básicas
        $user = \App\Models\User::find($this->selectedLeader);

        if (!$user) {
            $this->leaderDetails = null;
            return;
        }

        // Se não encontrou lideranças, criar dados básicos
        if ($liderancas->isEmpty()) {
            $this->leaderDetails = [
                'user' => $user,
                'liderancas' => collect(),
                'estatisticas' => [
                    'total_igrejas' => 0,
                    'cargos' => [],
                    'tempo_lideranca' => null,
                ],
            ];
            return;
        }

        // Estatísticas do líder
        $estatisticasLider = [
            'total_igrejas' => $liderancas->unique('igreja_id')->count(),
            'cargos' => $liderancas->groupBy('cargo')->map->count(),
            'tempo_lideranca' => $liderancas->min('data_entrada'),
        ];

        $this->leaderDetails = [
            'user' => $user,
            'liderancas' => $liderancas,
            'estatisticas' => $estatisticasLider,
        ];
    }

    // ========================================
    // RENDERIZAÇÃO
    // ========================================

    public function render()
    {
        return view('church.leadership-body.leadership-body', [
            'lideres' => $this->lideres,
            'igrejas' => $this->igrejas,
            'estatisticas' => $this->estatisticas,
        ]);
    }
}
