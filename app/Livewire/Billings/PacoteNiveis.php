<?php

namespace App\Livewire\Billings;

use App\Models\Billings\Pacote;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\PacoteNiveis as PacoteNiveisModels;

#[Title('Níveis dos Pacotes')]
#[Layout('components.layouts.app')]
class PacoteNiveis extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $pacoteFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingNivel = null;
    public $pacote_id = '';
    public $nivel = '';
    public $prioridade = '';
    public $recursos_extras = '';

    protected function rules()
    {
        return [
            'pacote_id' => 'required|exists:pacote,id',
            'nivel' => 'required|string|max:50',
            'prioridade' => 'required|integer|min:1|max:10',
            'recursos_extras' => 'nullable|json',
        ];
    }

    protected $listeners = ['refreshPacoteNiveis' => '$refresh'];

    public function updatingSearch()
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
        $this->pacoteFilter = '';
        $this->resetPage();
    }

    public function openModal($nivelId = null)
    {
        try {
            if ($nivelId) {
                $nivel = PacoteNiveisModels::find($nivelId);
                if ($nivel) {
                    $this->editingNivel = $nivel;
                    $this->pacote_id = $nivel->pacote_id;
                    $this->nivel = $nivel->nivel;
                    $this->prioridade = $nivel->prioridade;
                    $this->recursos_extras = $nivel->recursos_extras ? json_encode($nivel->recursos_extras, JSON_PRETTY_PRINT) : '';
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Nível não encontrado!'
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
        $this->editingNivel = null;
        $this->pacote_id = '';
        $this->nivel = '';
        $this->prioridade = '';
        $this->recursos_extras = '';
        $this->resetValidation();
    }

    public function saveNivel()
    {
        $this->validate();

        try {
            $recursosExtras = null;
            if ($this->recursos_extras) {
                $recursosExtras = json_decode($this->recursos_extras, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('JSON inválido para recursos extras');
                }
            }

            $data = [
                'pacote_id' => $this->pacote_id,
                'nivel' => $this->nivel,
                'prioridade' => $this->prioridade,
                'recursos_extras' => $recursosExtras,
            ];

            if ($this->editingNivel) {
                $this->editingNivel->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Nível atualizado com sucesso!'
                ]);
            } else {
                PacoteNiveisModels::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Nível criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshPacoteNiveis');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar nível: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteNivel($nivelId)
    {
        try {
            $nivel = PacoteNiveisModels::find($nivelId);
            if ($nivel) {
                $nivel->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Nível removido com sucesso!'
                ]);
                $this->dispatch('refreshPacoteNiveis');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Nível não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao remover nível: ' . $e->getMessage()
            ]);
        }
    }

    public function getPacoteNiveis()
    {
        try {
            $query = PacoteNiveisModels::with(['pacote']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('nivel', 'like', '%' . $this->search . '%')
                      ->orWhere('prioridade', 'like', '%' . $this->search . '%')
                      ->orWhereHas('pacote', function ($subQ) {
                          $subQ->where('nome', 'like', '%' . $this->search . '%');
                      });
                });
            }

            if ($this->pacoteFilter) {
                $query->where('pacote_id', $this->pacoteFilter);
            }

            return $query->orderBy('pacote_id')
                        ->orderBy('prioridade')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar níveis: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getPacotes()
    {
        return Pacote::orderBy('nome')->get();
    }

    public function render()
    {
        return view('billings.pacote-niveis', [
            'pacoteNiveis' => $this->getPacoteNiveis(),
            'pacotes' => $this->getPacotes(),
        ]);
    }
}
