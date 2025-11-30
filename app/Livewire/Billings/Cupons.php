<?php

namespace App\Livewire\Billings;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\Billings\AssinaturaCupom;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;

#[Title('Cupons de Desconto')]
#[Layout('components.layouts.app')]
class Cupons extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingCupom = null;
    public $codigo = '';
    public $descricao = '';
    public $desconto_percentual = '';
    public $desconto_valor = '';
    public $valido_de = '';
    public $valido_ate = '';
    public $uso_max = 1;
    public $ativo = true;

    protected $rules = [
        'codigo' => 'required|string|max:50|unique:assinatura_cupons,codigo',
        'descricao' => 'nullable|string|max:255',
        'desconto_percentual' => 'nullable|integer|min:0|max:100',
        'desconto_valor' => 'nullable|numeric|min:0',
        'valido_de' => 'nullable|date',
        'valido_ate' => 'nullable|date|after:valido_de',
        'uso_max' => 'required|integer|min:1',
        'ativo' => 'boolean',
    ];

    protected $listeners = ['refreshCupons' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function openModal($cupomId = null)
    {
        try {
            if ($cupomId) {
                $cupom = AssinaturaCupom::find($cupomId);
                if ($cupom) {
                    $this->editingCupom = $cupom;
                    $this->codigo = $cupom->codigo;
                    $this->descricao = $cupom->descricao;
                    $this->desconto_percentual = $cupom->desconto_percentual;
                    $this->desconto_valor = $cupom->desconto_valor;
                    $this->valido_de = $cupom->valido_de ? $cupom->valido_de->format('Y-m-d') : '';
                    $this->valido_ate = $cupom->valido_ate ? $cupom->valido_ate->format('Y-m-d') : '';
                    $this->uso_max = $cupom->uso_max;
                    $this->ativo = $cupom->ativo;

                    // Atualizar regras para edição (ignorar unique no próprio registro)
                    $this->rules['codigo'] = 'required|string|max:50|unique:assinatura_cupons,codigo,' . $cupomId;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Cupom não encontrado!'
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
        $this->editingCupom = null;
        $this->codigo = '';
        $this->descricao = '';
        $this->desconto_percentual = '';
        $this->desconto_valor = '';
        $this->valido_de = '';
        $this->valido_ate = '';
        $this->uso_max = 1;
        $this->ativo = true;
        $this->resetValidation();
        $this->rules['codigo'] = 'required|string|max:50|unique:assinatura_cupons,codigo';
    }

    public function saveCupom()
    {
        $this->validate();

        try {
            $data = [
                'codigo' => strtoupper($this->codigo),
                'descricao' => $this->descricao,
                'desconto_percentual' => $this->desconto_percentual ?: null,
                'desconto_valor' => $this->desconto_valor ?: null,
                'valido_de' => $this->valido_de ?: null,
                'valido_ate' => $this->valido_ate ?: null,
                'uso_max' => $this->uso_max,
                'ativo' => $this->ativo,
            ];

            if ($this->editingCupom) {
                $this->editingCupom->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Cupom atualizado com sucesso!'
                ]);
            } else {
                AssinaturaCupom::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Cupom criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshCupons');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar cupom: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteCupom($cupomId)
    {
        try {
            $cupom = AssinaturaCupom::find($cupomId);
            if ($cupom) {
                $cupom->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Cupom excluído com sucesso!'
                ]);
                $this->dispatch('refreshCupons');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Cupom não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir cupom: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus($cupomId)
    {
        try {
            $cupom = AssinaturaCupom::find($cupomId);
            if ($cupom) {
                $cupom->update(['ativo' => !$cupom->ativo]);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Status alterado com sucesso!'
                ]);
                $this->dispatch('refreshCupons');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ]);
        }
    }

    public function getCupons()
    {
        try {

            $query = AssinaturaCupom::query();

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', '%' . $this->search . '%')
                      ->orWhere('descricao', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter !== '') {
                $query->where('ativo', $this->statusFilter === 'ativo');
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar cupons: ' . $e->getMessage()
            ]);

             return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function generateCodigo()
    {
        $this->codigo = 'CUPOM' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public function closeModalFromJS()
    {
        $this->closeModal();
    }

    public function render()
    {
        return view('billings.cupons', [
            'cupons' => $this->getCupons(),
        ]);
    }
}
