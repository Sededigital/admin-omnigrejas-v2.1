<?php

namespace App\Livewire\Billings;

use App\Models\Billings\Pacote;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\PacoteRecursos as PacoteRecursosModels;
use App\Models\RBAC\IgrejaPermissao;

#[Title('Recursos dos Pacotes')]
#[Layout('components.layouts.app')]
class PacoteRecursos extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $pacoteFilter = '';
    public $recursoFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingRecurso = null;
    public $pacote_id = '';
    public $recurso_tipo = '';
    public $limite_valor = '';
    public $unidade = 'quantidade';

    protected function rules()
    {
        return [
            'pacote_id' => 'required|exists:pacote,id',
            'recurso_tipo' => 'required|string|max:255',
            'limite_valor' => 'nullable|numeric',
            'unidade' => 'required|in:quantidade,mensal,diario,gb,outros',
        ];
    }

    protected $listeners = ['refreshPacoteRecursos' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPacoteFilter()
    {
        $this->resetPage();
    }

    public function updatedRecursoFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->pacoteFilter = '';
        $this->recursoFilter = '';
        $this->resetPage();
    }

    public function openModal($recursoId = null)
    {
        try {
            if ($recursoId) {
                $recurso = PacoteRecursosModels::find($recursoId);
                if ($recurso) {
                    $this->editingRecurso = $recurso;
                    $this->pacote_id = $recurso->pacote_id;
                    $this->recurso_tipo = $recurso->recurso_tipo;
                    $this->limite_valor = $recurso->limite_valor;
                    $this->unidade = $recurso->unidade;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Recurso não encontrado!'
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
        $this->editingRecurso = null;
        $this->pacote_id = '';
        $this->recurso_tipo = 'membros';
        $this->limite_valor = '';
        $this->unidade = 'quantidade';
        $this->resetValidation();
    }

    public function saveRecurso()
    {
        $this->validate();

        try {
            $data = [
                'pacote_id' => $this->pacote_id,
                'recurso_tipo' => $this->recurso_tipo,
                'limite_valor' => $this->limite_valor,
                'unidade' => $this->unidade,
            ];

            if ($this->editingRecurso) {
                $this->editingRecurso->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Limite de recurso atualizado com sucesso!'
                ]);
            } else {
                PacoteRecursosModels::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Limite de recurso criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshPacoteRecursos');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar limite de recurso: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteRecurso($recursoId)
    {
        try {
            $recurso = PacoteRecursosModels::find($recursoId);
            if ($recurso) {
                $recurso->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Limite de recurso removido com sucesso!'
                ]);
                $this->dispatch('refreshPacoteRecursos');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Limite de recurso não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao remover limite de recurso: ' . $e->getMessage()
            ]);
        }
    }

    public function getPacoteRecursos()
    {
        try {
            $query = PacoteRecursosModels::with(['pacote']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('recurso_tipo', 'like', '%' . $this->search . '%')
                      ->orWhere('unidade', 'like', '%' . $this->search . '%')
                      ->orWhereHas('pacote', function ($subQ) {
                          $subQ->where('nome', 'like', '%' . $this->search . '%');
                      });
                });
            }

            if ($this->pacoteFilter) {
                $query->where('pacote_id', $this->pacoteFilter);
            }

            if ($this->recursoFilter) {
                $query->where('recurso_tipo', $this->recursoFilter);
            }

            return $query->orderBy('pacote_id')
                        ->orderBy('recurso_tipo')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar limites de recursos: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getRecursoOptions()
    {
        try {
            $permissoes = IgrejaPermissao::select('codigo', 'nome')
                ->where('ativo', true)
                ->orderBy('nome')
                ->get()
                ->pluck('nome', 'codigo')
                ->toArray();

            return $permissoes;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getUnidadeOptions()
    {
        return [
            'quantidade' => 'Quantidade',
            'mensal' => 'Mensal',
            'diario' => 'Diário',
            'gb' => 'GB',
            'outros' => 'Outros',
        ];
    }

    public function getPacotes()
    {
        return Pacote::orderBy('nome')->get();
    }

    public function render()
    {
        return view('billings.pacote-recursos', [
            'pacoteRecursos' => $this->getPacoteRecursos(),
            'pacotes' => $this->getPacotes(),
            'recursoOptions' => $this->getRecursoOptions(),
            'unidadeOptions' => $this->getUnidadeOptions(),
        ]);
    }
}
