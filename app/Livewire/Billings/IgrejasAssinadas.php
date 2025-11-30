<?php

namespace App\Livewire\Billings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use App\Models\Billings\Pacote;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\IgrejaAssinada;

#[Title('Igrejas Assinadas')]
#[Layout('components.layouts.app')]
class IgrejasAssinadas extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $pacoteFilter = '';

    // Confirmação properties
    public $confirmacaoNome = '';

    // Modal properties
    public $showModal = false;
    public $editingIgrejaAssinada = null;


    // Modal de confirmação properties
    public $showStatusModal = false;
    public $showDeleteModal = false;
    public $statusAction = '';
    public $selectedIgrejaAssinadaId = '';
    public $selectedIgrejaAssinada = null;



    protected $listeners = [
        'refreshIgrejasAssinadas' => '$refresh'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPacoteFilter()
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

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
        $this->dispatch('modalClosed');
    }



    public function deleteIgrejaAssinada($igrejaAssinadaId)
    {
        try {
            $igrejaAssinada = IgrejaAssinada::find($igrejaAssinadaId);
            if ($igrejaAssinada) {
                $igrejaAssinada->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Registro excluído com sucesso!'
                ]);
                $this->dispatch('refreshIgrejasAssinadas');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Registro não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir registro: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus($igrejaAssinadaId)
    {
        try {
            $igrejaAssinada = IgrejaAssinada::find($igrejaAssinadaId);
            if ($igrejaAssinada) {
                $igrejaAssinada->update([
                    'ativo' => !$igrejaAssinada->ativo,
                    'data_cancelamento' => !$igrejaAssinada->ativo ? now() : null
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Status alterado com sucesso!'
                ]);
                $this->dispatch('refreshIgrejasAssinadas');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ]);
        }
    }

    public function getIgrejasAssinadas()
    {
        try {
            $query = IgrejaAssinada::with(['igreja', 'pacote']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->whereHas('igreja', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->search . '%')
                             ->orWhere('nif', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('pacote', function ($subQ) {
                        $subQ->where('nome', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('observacoes', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter !== '') {
                $query->where('ativo', $this->statusFilter === 'ativo');
            }

            if ($this->pacoteFilter) {
                $query->where('pacote_id', $this->pacoteFilter);
            }

            return $query->orderBy('data_adesao', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar registros: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function closeModalFromJS()
    {
        $this->closeModal();
    }

    // Métodos para modais de confirmação
    public function openStatusModal($igrejaAssinadaId, $action)
    {
        $this->selectedIgrejaAssinadaId = $igrejaAssinadaId;
        $this->statusAction = $action;
        $this->selectedIgrejaAssinada = IgrejaAssinada::with(['igreja', 'pacote'])->find($igrejaAssinadaId);
        $this->showStatusModal = true;
        $this->dispatch('showStatusModal');
    }

    public function openDeleteModal($igrejaAssinadaId)
    {
        $this->selectedIgrejaAssinadaId = $igrejaAssinadaId;
        $this->selectedIgrejaAssinada = IgrejaAssinada::with(['igreja', 'pacote'])->find($igrejaAssinadaId);
        $this->showDeleteModal = true;
        $this->dispatch('showDeleteModal');
    }

    public function confirmStatusChange()
    {
        // Validar confirmação
        if (!$this->selectedIgrejaAssinada || trim($this->confirmacaoNome) !== trim($this->selectedIgrejaAssinada->igreja->nome)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Nome da igreja incorreto. Digite exatamente o nome da igreja.'
            ]);
            return;
        }

        try {
            
            $igrejaAssinada = IgrejaAssinada::find($this->selectedIgrejaAssinadaId);
            if ($igrejaAssinada) {
                $novoStatus = $this->statusAction === 'desativar' ? false : true;
                $igrejaAssinada->update([
                    'ativo' => $novoStatus,
                    'data_cancelamento' => $novoStatus ? null : now()
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Status alterado com sucesso!'
                ]);
                $this->dispatch('refreshIgrejasAssinadas');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ]);
        }

        $this->closeStatusModal();
    }

    public function confirmDelete()
    {
        // Validar confirmação
        if (!$this->selectedIgrejaAssinada || trim($this->confirmacaoNome) !== trim($this->selectedIgrejaAssinada->igreja->nome)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Nome da igreja incorreto. Digite exatamente o nome da igreja.'
            ]);
            return;
        }

        try {
            $igrejaAssinada = IgrejaAssinada::find($this->selectedIgrejaAssinadaId);
            if ($igrejaAssinada) {
                $igrejaAssinada->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Registro excluído com sucesso!'
                ]);
                $this->dispatch('refreshIgrejasAssinadas');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir registro: ' . $e->getMessage()
            ]);
        }

        $this->closeDeleteModal();
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->selectedIgrejaAssinadaId = '';
        $this->statusAction = '';
        $this->selectedIgrejaAssinada = null;
        $this->confirmacaoNome = '';
        $this->dispatch('closeStatusModal');
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedIgrejaAssinadaId = '';
        $this->selectedIgrejaAssinada = null;
        $this->confirmacaoNome = '';
        $this->dispatch('closeDeleteModal');
    }

    public function render()
    {
        return view('billings.igrejas-assinadas', [
            'igrejasAssinadas' => $this->getIgrejasAssinadas(),
            'igrejas' => Igreja::orderBy('nome')->get(),
            'pacotes' => Pacote::orderBy('nome')->get(),
        ]);
    }
}
