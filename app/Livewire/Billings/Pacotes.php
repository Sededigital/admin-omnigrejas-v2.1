<?php

namespace App\Livewire\Billings;

use App\Models\Billings\Pacote;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;

#[Title('Gestão de Pacotes')]
#[Layout('components.layouts.app')]
class Pacotes extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingPacote = null;
    public $nome = '';
    public $descricao = '';
    public $preco = '';
    public $duracao_meses = '';
    public $trial_dias = '';

    protected function rules()
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'preco' => 'required|numeric|min:0',
            'duracao_meses' => 'required|integer|min:1',
            'trial_dias' => 'nullable|integer|min:0',
        ];

        if ($this->editingPacote) {
            $rules['nome'] .= '|unique:pacote,nome,' . $this->editingPacote->id;
        } else {
            $rules['nome'] .= '|unique:pacote,nome';
        }

        return $rules;
    }

    protected $listeners = ['refreshPacotes' => '$refresh', 'closeModalFromJS'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function openModal($pacoteId = null)
    {
        try {

            if ($pacoteId) {
                $pacote = Pacote::find($pacoteId);
                if ($pacote) {
                    $this->editingPacote = $pacote;
                    $this->nome = $pacote->nome;
                    $this->descricao = $pacote->descricao;
                    $this->preco = $pacote->preco;
                    $this->duracao_meses = $pacote->duracao_meses;
                    $this->trial_dias = $pacote->trial_dias;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Pacote não encontrado!'
                    ]);
                    return;
                }
            } else {
                $this->resetModal();
            }

            $this->showModal = true;
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao abrir modal: ' . $e->getMessage()
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
        $this->dispatch('modalClosed');
    }

    private function resetModal()
    {
        $this->editingPacote = null;
        $this->nome = '';
        $this->descricao = '';
        $this->preco = '';
        $this->duracao_meses = '';
        $this->trial_dias = '';
        $this->resetValidation();
    }

    public function savePacote()
    {
        $this->validate();

        try {
            $data = [
                'nome' => $this->nome,
                'descricao' => $this->descricao,
                'preco' => $this->preco,
                'duracao_meses' => $this->duracao_meses,
                'trial_dias' => $this->trial_dias,
            ];

            if ($this->editingPacote) {
                $this->editingPacote->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pacote atualizado com sucesso!'
                ]);
            } else {
                Pacote::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pacote criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshPacotes');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar pacote: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePacote($pacoteId)
    {
        try {
            $pacote = Pacote::find($pacoteId);
            if ($pacote) {
                $pacote->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Pacote excluído com sucesso!'
                ]);
                $this->dispatch('refreshPacotes');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Pacote não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir pacote: ' . $e->getMessage()
            ]);
        }
    }

    public function getPacotes()
    {
        try {
            $query = Pacote::query();

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('descricao', 'like', '%' . $this->search . '%');
                });
            }

            return $query->orderBy('nome')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar pacotes: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function closeModalFromJS()
    {
        $this->closeModal();
    }

    public function render()
    {
        return view('billings.pacotes', [
            'pacotes' => $this->getPacotes(),
        ]);
    }
}
