<?php

namespace App\Livewire\Billings;

use App\Models\Billings\Modulo;
use App\Models\Billings\Pacote;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Billings\PacotePermissao;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;

#[Title('Permissões de Pacotes')]
#[Layout('components.layouts.app')]
class PacotePermissoes extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $pacoteFilter = '';
    public $moduloFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingPermissao = null;
    public $pacote_id = '';
    public $modulo_id = '';
    public $permissao = 'leitura';

    protected $rules = [
        'pacote_id' => 'required|exists:pacote,id',
        'modulo_id' => 'required|exists:modulos,id',
        'permissao' => 'required|in:leitura,escrita,nenhuma',
    ];

    protected $listeners = ['refreshPermissoes' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPacoteFilter()
    {
        $this->resetPage();
    }

    public function updatingModuloFilter()
    {
        $this->resetPage();
    }

    public function updatedPacoteId()
    {
        // Quando pacote é selecionado, limpar módulo para forçar re-seleção
        $this->modulo_id = '';
        $this->resetValidation('modulo_id');
    }

    public function updatedModuloId()
    {
        // Se módulo for selecionado primeiro, limpar pacote
        if ($this->modulo_id && !$this->pacote_id) {
            $this->pacote_id = '';
            $this->resetValidation('pacote_id');
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Selecione primeiro o pacote antes de escolher o módulo.'
            ]);
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->pacoteFilter = '';
        $this->moduloFilter = '';
        $this->resetPage();
    }

    public function openModal($permissaoId = null)
    {
        try {
            if ($permissaoId) {
                $permissao = PacotePermissao::find($permissaoId);
                if ($permissao) {
                    $this->editingPermissao = $permissao;
                    $this->pacote_id = $permissao->pacote_id;
                    $this->modulo_id = $permissao->modulo_id;
                    $this->permissao = $permissao->permissao;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Permissão não encontrada!'
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
        $this->editingPermissao = null;
        $this->pacote_id = '';
        $this->modulo_id = '';
        $this->permissao = 'leitura';
        $this->resetValidation();
    }

    public function savePermissao()
    {
        $this->validate();

        try {
            // Verificar se já existe uma permissão para este pacote e módulo
            $existing = PacotePermissao::where('pacote_id', $this->pacote_id)
                                      ->where('modulo_id', $this->modulo_id)
                                      ->when($this->editingPermissao, function($query) {
                                          return $query->where('id', '!=', $this->editingPermissao->id);
                                      })
                                      ->first();

            if ($existing) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Já existe uma permissão para este pacote e módulo!'
                ]);
                return;
            }

            $data = [
                'pacote_id' => $this->pacote_id,
                'modulo_id' => $this->modulo_id,
                'permissao' => $this->permissao,
            ];

            if ($this->editingPermissao) {
                $this->editingPermissao->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Permissão atualizada com sucesso!'
                ]);
            } else {
                PacotePermissao::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Permissão criada com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshPermissoes');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar permissão: ' . $e->getMessage()
            ]);
        }
    }

    public function deletePermissao($permissaoId)
    {
        try {
            $permissao = PacotePermissao::find($permissaoId);
            if ($permissao) {
                $permissao->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Permissão excluída com sucesso!'
                ]);
                $this->dispatch('refreshPermissoes');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Permissão não encontrada!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir permissão: ' . $e->getMessage()
            ]);
        }
    }

    public function getPermissoes()
    {
        try {

            $query = PacotePermissao::with(['pacote', 'modulo']);

            if ($this->search) {
                $query->whereHas('pacote', function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%');
                })->orWhereHas('modulo', function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->pacoteFilter) {
                $query->where('pacote_id', $this->pacoteFilter);
            }

            if ($this->moduloFilter) {
                $query->where('modulo_id', $this->moduloFilter);
            }

            return $query->orderBy('pacote_id')
                        ->orderBy('modulo_id')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao carregar permissões: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getPacotes()
    {
        return Pacote::orderBy('nome')->get();
    }

    public function getModulos()
    {
        $query = Modulo::orderBy('nome');

        // Se um pacote estiver selecionado, filtrar apenas módulos não registrados para este pacote
        if ($this->pacote_id && !$this->editingPermissao) {
            $modulosRegistrados = PacotePermissao::where('pacote_id', $this->pacote_id)
                ->pluck('modulo_id')
                ->toArray();

            if (!empty($modulosRegistrados)) {
                $query->whereNotIn('id', $modulosRegistrados);
            }
        }

        return $query->get();
    }

    public function getPermissaoOptions()
    {
        $options = [
            'leitura' => 'Leitura',
            'escrita' => 'Escrita',
            'nenhuma' => 'Nenhuma'
        ];

        // Se pacote e módulo estiverem selecionados, filtrar apenas permissões não registradas
        if ($this->pacote_id && $this->modulo_id && !$this->editingPermissao) {
            $permissoesRegistradas = PacotePermissao::where('pacote_id', $this->pacote_id)
                ->where('modulo_id', $this->modulo_id)
                ->pluck('permissao')
                ->toArray();

            // Remover permissões já registradas das opções disponíveis
            foreach ($permissoesRegistradas as $permissao) {
                unset($options[$permissao]);
            }
        }

        return $options;
    }

    public function render()
    {
        return view('billings.pacote-permissoes', [
            'permissoes' => $this->getPermissoes(),
            'pacotes' => $this->getPacotes(),
            'modulos' => $this->getModulos(),
            'permissaoOptions' => $this->getPermissaoOptions(),
        ]);
    }
}
