<?php

namespace App\Livewire\Billings;

use App\Models\Igreja;
use App\Models\Billings\Pacote;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\AssinaturaHistorico;

#[Title('Histórico de Assinaturas')]
#[Layout('components.layouts.app')]
class AssinaturasHistorico extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $pacoteFilter = '';

    protected $listeners = ['refreshHistorico' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPacoteFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->pacoteFilter = '';
        $this->resetPage();
    }

    public function getHistorico()
    {
        try {
            
            $query = AssinaturaHistorico::with(['igreja', 'pacote']);

            if ($this->search) {
                $query->whereHas('igreja', function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('nif', 'like', '%' . $this->search . '%');
                })->orWhereHas('pacote', function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->pacoteFilter) {
                $query->where('pacote_id', $this->pacoteFilter);
            }

            return $query->orderBy('data_inicio', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar histórico: ' . $e->getMessage()
            ]);

            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getPacotes()
    {
        return Pacote::orderBy('nome')->get();
    }

    public function getStatusOptions()
    {
        return [
            'Ativo' => 'Ativo',
            'Cancelado' => 'Cancelado',
            'Expirado' => 'Expirado'
        ];
    }

    public function render()
    {
        return view('billings.assinaturas-historico', [
            'historico' => $this->getHistorico(),
            'pacotes' => $this->getPacotes(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
