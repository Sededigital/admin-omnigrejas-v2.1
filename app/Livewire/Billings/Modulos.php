<?php

namespace App\Livewire\Billings;

use App\Models\Billings\Modulo;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;

#[Title('Gestão de Módulos')]
#[Layout('components.layouts.app')]
class Modulos extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingModulo = null;
    public $nome = '';
    public $descricao = '';

    protected $rules = [
        'nome' => 'required|string|max:255|unique:modulos,nome',
        'descricao' => 'nullable|string|max:1000',
    ];

    protected $listeners = ['refreshModulos' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function openModal($moduloId = null)
    {
        try {
            if ($moduloId) {
                $modulo = Modulo::find($moduloId);
                if ($modulo) {
                    $this->editingModulo = $modulo;
                    $this->nome = $modulo->nome;
                    $this->descricao = $modulo->descricao;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Módulo não encontrado!'
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
    }

    private function resetModal()
    {
        $this->editingModulo = null;
        $this->nome = '';
        $this->descricao = '';
        $this->resetValidation();
    }

    public function saveModulo()
    {
        $this->validate();

        try {
            $data = [
                'nome' => $this->nome,
                'descricao' => $this->descricao,
            ];

            if ($this->editingModulo) {
                $this->editingModulo->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Módulo atualizado com sucesso!'
                ]);
            } else {
                Modulo::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Módulo criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshModulos');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar módulo: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteModulo($moduloId)
    {
        try {
            $modulo = Modulo::find($moduloId);
            if ($modulo) {
                $modulo->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Módulo excluído com sucesso!'
                ]);
                $this->dispatch('refreshModulos');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Módulo não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir módulo: ' . $e->getMessage()
            ]);
        }
    }

    public function getModulos()
    {
        try {
            $query = Modulo::query();

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
                'message' => 'Erro ao carregar módulos: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function render()
    {
        return view('billings.modulos', [
            'modulos' => $this->getModulos(),
        ]);
    }
}
