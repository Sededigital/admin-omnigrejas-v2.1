<?php

namespace App\Livewire\Church;

use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\Igrejas\AliancaIgreja;
use App\Helpers\RBAC\PermissionHelper;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Title('Vitrine de Igrejas | Portal da Igreja')]
#[Layout('components.layouts.app')]
class ChurchShowcase extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // ========================================
    // PROPRIEDADES PARA FILTROS E BUSCA
    // ========================================
    public $search = '';
    public $statusFilter = ''; // Mostrar todas as igrejas por padrão
    public $categoriaFilter = '';
    public $tipoFilter = '';
    public $orderBy = 'nome';
    public $orderDirection = 'asc';

    // ========================================
    // PROPRIEDADES PARA DETALHES DA IGREJA
    // ========================================
    public $igrejaSelecionada = null;
    public $showDetalhesModal = false;
    public $loadingDetalhes = false;

    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================

    public function mount()
    {
        // Verificar permissões usando PermissionHelper
        $permissionHelper = new PermissionHelper(Auth::user());
        if (!$permissionHelper->hasPermission('ver_vitrine_igrejas')) {
            abort(403, 'Você não tem permissão para acessar a vitrine de igrejas.');
        }
    }

    public function render()
    {
        $igrejas = $this->getIgrejas();
        $categorias = CategoriaIgreja::where('ativa', true)->get();

        return view('church.church-showcase', [
            'igrejas' => $igrejas,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Busca igrejas para exibição na vitrine
     * Apenas igrejas aprovadas e com informações públicas
     */
    public function getIgrejas()
    {
        $userId = Auth::id();

        $query = Igreja::with([
            'categoria',
            'aliancas' => function($q) {
                $q->where('igreja_aliancas.status', 'ativo');
            },
            'lideranca' => function($q) {
                $q->where('status', 'ativo')
                  ->whereIn('cargo', ['admin', 'pastor', 'ministro' ])
                  ->with('user:id,name,email');
            }
        ])
        ->withCount([
            'membros' => function($q) {
                $q->where('status', 'ativo');
            }
        ]); // Removido filtro de aprovação para mostrar todas as igrejas

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nome', 'ilike', '%' . $this->search . '%')
                  ->orWhere('localizacao', 'ilike', '%' . $this->search . '%')
                  ->orWhere('sobre', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->categoriaFilter) {
            $query->where('categoria_id', $this->categoriaFilter);
        }

        if ($this->tipoFilter) {
            $query->where('tipo', $this->tipoFilter);
        }

        if ($this->statusFilter) {
            $query->where('status_aprovacao', $this->statusFilter);
        }

        // Aplicar ordenação
        switch ($this->orderBy) {
            case 'nome':
                $query->orderBy('nome', $this->orderDirection);
                break;
            case 'membros':
                $query->orderBy('membros_count', $this->orderDirection);
                break;
            case 'data':
                $query->orderBy('created_at', $this->orderDirection);
                break;
        }

        return $query->paginate(12);
    }

    public function sortBy($column)
    {
        if ($this->orderBy === $column) {
            $this->orderDirection = $this->orderDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderBy = $column;
            $this->orderDirection = 'asc';
        }
    }

    /**
     * Exibe detalhes básicos de uma igreja
     * Informações limitadas para não expor dados sensíveis
     */
    public function verDetalhes($igrejaId)
    {
        $this->loadingDetalhes = true;

        $igreja = Igreja::with([
            'categoria',
            'aliancas' => function($q) {
                $q->where('igreja_aliancas.status', 'ativo');
            },
            'lideranca' => function($q) {
                $q->with('user:id,name,email');
            },
            'criador'
        ])
        ->withCount([
            'membros' => function($q) {
                $q->where('status', 'ativo');
            },
            'eventos' => function($q) {
                $q->where('data_evento', '>=', now());
            }
        ])
        ->find($igrejaId);

        if (!$igreja) {
            $this->loadingDetalhes = false;
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Igreja não encontrada.'
            ]);
            return;
        }

        $this->igrejaSelecionada = $igreja;
        $this->showDetalhesModal = true;
        $this->loadingDetalhes = false;

        $this->dispatch('open-church-details-modal');
    }

    public function fecharDetalhes()
    {
        $this->showDetalhesModal = false;
        $this->igrejaSelecionada = null;
        $this->loadingDetalhes = false;
    }

    // ========================================
    // MÉTODOS AUXILIARES PARA A VIEW
    // ========================================

    public function getCorAvatar($index)
    {
        $cores = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
        return $cores[$index % count($cores)];
    }

    public function getIniciais($nome)
    {
        $palavras = explode(' ', trim($nome));
        $iniciais = '';

        foreach ($palavras as $palavra) {
            $iniciais .= strtoupper(substr($palavra, 0, 1));
            if (strlen($iniciais) >= 2) break;
        }

        return str_pad($iniciais, 2, 'I');
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'aprovado' => 'bg-success',
            'pendente' => 'bg-warning',
            'rejeitado' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusText($status)
    {
        return match($status) {
            'aprovado' => 'Ativa',
            'pendente' => 'Pendente',
            'rejeitado' => 'Rejeitada',
            default => 'Desconhecido'
        };
    }

    public function getTipoText($tipo)
    {
        return match($tipo) {
            'sede' => 'Sede',
            'filial' => 'Filial',
            'independente' => 'Independente',
            default => 'Independente'
        };
    }

    // ========================================
    // LISTENERS PARA FILTROS
    // ========================================

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoriaFilter()
    {
        $this->resetPage();
    }

    public function updatedTipoFilter()
    {
        $this->resetPage();
    }

    #[On('refreshComponent')]
    public function refreshComponent()
    {
        $this->resetPage();
    }
}
