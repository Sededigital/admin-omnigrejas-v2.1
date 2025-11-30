<?php

namespace App\Livewire\Church\Finance;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Financeiro\FinanceiroConta;
use App\Models\Financeiro\FinanceiroCategoria;
use App\Models\Financeiro\FinanceiroMovimento;

#[Title('Movimentos Financeiros | Portal da Igreja')]
#[Layout('components.layouts.app')]
class FinancialMoviment extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtros e busca
    public $search = '';
    public $selectedType = '';
    public $selectedAccount = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    // Modal properties - Movimento
    public $showModal = false;
    public $editingMovement = null;
    public $tipo = 'entrada';
    public $data_transacao = '';
    public $valor = '';
    public $conta_id = '';
    public $categoria_id = '';
    public $metodo_pagamento = '';
    public $descricao = '';
    public $observacoes = '';
    public $responsavel_id = '';
    public $comprovante_url = '';

    // Modal properties - Confirmação de Exclusão
    public $showDeleteModal = false;
    public $deleteType = ''; // 'movement'
    public $deleteItem = null;
    public $deletePassword = '';
    public $deleteError = '';

    protected function rules()
    {
        return [
            'tipo' => 'required|in:entrada,saida',
            'data_transacao' => 'required|date',
            'valor' => 'required|numeric|min:0.01',
            'conta_id' => 'required|exists:financeiro_contas,id',
            'categoria_id' => 'required|exists:financeiro_categorias,id',
            'metodo_pagamento' => 'required|string|max:100',
            'descricao' => 'required|string|max:255',
            'observacoes' => 'nullable|string|max:500',
            'responsavel_id' => 'nullable|exists:users,id',
            'comprovante_url' => 'nullable|url',
        ];
    }

    protected $listeners = ['refreshMovements' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function updatingSelectedAccount()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function setTypeFilter($type)
    {
        $this->selectedType = $type;
        $this->resetPage();
    }

    public function setAccountFilter($accountId)
    {
        $this->selectedAccount = $accountId;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedType = '';
        $this->selectedAccount = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function openModal($movementId = null)
    {
        if ($movementId) {
            $movement = FinanceiroMovimento::find($movementId);
            if ($movement) {
                $this->editingMovement = $movement;
                $this->tipo = $movement->tipo;
                $this->data_transacao = $movement->data_transacao ? $movement->data_transacao->format('Y-m-d') : '';
                $this->valor = $movement->valor;
                $this->conta_id = $movement->conta_id;
                $this->categoria_id = $movement->categoria_id;
                $this->metodo_pagamento = $movement->metodo_pagamento;
                $this->descricao = $movement->descricao;
                $this->observacoes = $movement->observacoes ?? '';
                $this->responsavel_id = $movement->responsavel_id;
                $this->comprovante_url = $movement->comprovante_url ?? '';
            }
        } else {
            $this->resetModal();
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->editingMovement = null;
        $this->tipo = 'entrada';
        $this->data_transacao = date('Y-m-d');
        $this->valor = '';
        $this->conta_id = '';
        $this->categoria_id = '';
        $this->metodo_pagamento = '';
        $this->descricao = '';
        $this->observacoes = '';
        $this->responsavel_id = '';
        $this->comprovante_url = '';
        $this->resetValidation();
    }

    public function saveMovement()
    {
        $this->validate();

        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return;
        }

        if ($this->editingMovement) {
            $this->editingMovement->update([
                'tipo' => $this->tipo,
                'data_transacao' => $this->data_transacao,
                'valor' => $this->valor,
                'conta_id' => $this->conta_id,
                'categoria_id' => $this->categoria_id,
                'metodo_pagamento' => $this->metodo_pagamento,
                'descricao' => $this->descricao,
                'observacoes' => $this->observacoes,
                'responsavel_id' => $this->responsavel_id,
                'comprovante_url' => $this->comprovante_url,
            ]);

            $this->dispatch('toast', ['message' => 'Movimento atualizado com sucesso!', 'type' => 'success']);
        } else {
            FinanceiroMovimento::create([
                'id' => (string) Str::uuid(),
                'igreja_id' => $igrejaId,
                'tipo' => $this->tipo,
                'data_transacao' => $this->data_transacao,
                'valor' => $this->valor,
                'conta_id' => $this->conta_id,
                'categoria_id' => $this->categoria_id,
                'metodo_pagamento' => $this->metodo_pagamento,
                'descricao' => $this->descricao,
                'observacoes' => $this->observacoes,
                'responsavel_id' => $this->responsavel_id,
                'comprovante_url' => $this->comprovante_url,
                'created_by' => Auth::id(),
            ]);

            $this->dispatch('toast', ['message' => 'Movimento criado com sucesso!', 'type' => 'success']);
        }

        $this->closeModal();
        $this->dispatch('refreshMovements');
    }

    public function openDeleteModal($type, $itemId)
    {
        $user = Auth::user();

        if (!$this->canDeleteItems($user)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Você não tem permissão para excluir itens financeiros.'
            ]);
            return;
        }

        if ($type === 'movement') {
            $this->deleteItem = FinanceiroMovimento::with('auditorias')->find($itemId);
            if ($this->deleteItem) {
                // Verificar se pode excluir
                if (!$this->canDeleteMovement($this->deleteItem)) {
                    $dependencies = $this->getMovementDependencies($this->deleteItem);
                    $this->dispatch('toast', [
                        'type' => 'warning',
                        'message' => 'Não é possível excluir este movimento porque possui vínculos com: ' . implode(', ', $dependencies)
                    ]);
                    return;
                }

                $this->deleteType = 'movement';
                $this->showDeleteModal = true;
                $this->deletePassword = '';
                $this->deleteError = '';
            }
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteType = '';
        $this->deleteItem = null;
        $this->deletePassword = '';
        $this->deleteError = '';
    }

    public function confirmDelete()
    {
        $user = Auth::user();

        if (!$this->canDeleteItems($user)) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Permissão negada para exclusão.'
            ]);
            return;
        }

        if (!$this->deleteItem) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Item não encontrado.'
            ]);
            return;
        }

        // Validar senha
        if (!password_verify($this->deletePassword, $user->password)) {
            $this->deleteError = 'Senha incorreta. Tente novamente.';
            return;
        }

        try {
            if ($this->deleteType === 'movement') {
                $this->deleteItem->delete();
                $this->dispatch('toast', [
                    'message' => 'Movimento excluído com sucesso!',
                    'type' => 'success'
                ]);
            }

            $this->closeDeleteModal();
            $this->dispatch('refreshMovements');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir item: ' . $e->getMessage()
            ]);
        }
    }

    private function canDeleteItems($user)
    {
        return in_array($user->role, [
            User::ROLE_ROOT,
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_PASTOR,
            User::ROLE_MINISTRO
        ]);
    }

    public function viewDetails($movementId)
    {
        $movement = FinanceiroMovimento::with(['conta', 'categoria', 'responsavel'])->find($movementId);
        if ($movement) {
            $this->dispatch('toast', [
                'message' => 'Detalhes do movimento: ' . $movement->descricao . ' - ' . number_format($movement->valor, 2, ',', '.') . ' AOA',
                'type' => 'info'
            ]);
        }
    }

    public function deleteMovement($movementId)
    {
        $this->openDeleteModal('movement', $movementId);
    }

    public function canDeleteMovement($movement)
    {
        $user = Auth::user();

        // Verificar se é admin, pastor ou ministro
        if (!$this->canDeleteItems($user)) {
            return false;
        }

        // Verificar se há vínculos com auditoria
        if ($movement->auditorias()->count() > 0) {
            return false;
        }

        return true;
    }

    public function getMovementDependencies($movement)
    {
        $dependencies = [];

        if ($movement->auditorias()->count() > 0) {
            $dependencies[] = 'registros de auditoria';
        }

        return $dependencies;
    }

    public function getMovements()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return FinanceiroMovimento::query()->whereRaw('1=0')->paginate($this->perPage);
        }

        $query = FinanceiroMovimento::query()
            ->with(['categoria', 'responsavel'])
            ->where('igreja_id', $igrejaId);

        if ($this->search) {
            $query->where('descricao', 'like', '%' . $this->search . '%');
        }

        if ($this->selectedType) {
            $query->where('tipo', $this->selectedType);
        }

        if ($this->selectedAccount) {
            $query->where('conta_id', $this->selectedAccount);
        }

        if ($this->dateFrom) {
            $query->where('data_transacao', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('data_transacao', '<=', $this->dateTo);
        }

        return $query->orderBy('data_transacao', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getMovementStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'total_entradas' => 0,
                'total_saidas' => 0,
                'saldo_liquido' => 0,
                'total_movements' => 0,
            ];
        }

        $entradas = FinanceiroMovimento::where('igreja_id', $igrejaId)
                                      ->where('tipo', 'entrada')
                                      ->sum('valor');

        $saidas = FinanceiroMovimento::where('igreja_id', $igrejaId)
                                    ->where('tipo', 'saida')
                                    ->sum('valor');

        $totalMovements = FinanceiroMovimento::where('igreja_id', $igrejaId)->count();

        return [
            'total_entradas' => $entradas,
            'total_saidas' => $saidas,
            'saldo_liquido' => $entradas - $saidas,
            'total_movements' => $totalMovements,
        ];
    }

    public function getAccounts()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        return FinanceiroConta::where('igreja_id', $igrejaId)
                             ->where('ativa', true)
                             ->orderBy('banco')
                             ->get();
    }

    public function getCategories()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return collect();
        }

        return FinanceiroCategoria::where('igreja_id', $igrejaId)
                                 ->orderBy('nome')
                                 ->get();
    }

    public function getTypeLabel($type)
    {
        return match($type) {
            'entrada' => 'Entrada',
            'saida' => 'Saída',
            default => 'Desconhecido'
        };
    }

    public function getTypeBadgeClass($type)
    {
        return match($type) {
            'entrada' => 'success',
            'saida' => 'danger',
            default => 'secondary'
        };
    }

    public function render()
    {
        return view('church.finance.financial-moviment', [
            'movements' => $this->getMovements(),
            'stats' => $this->getMovementStats(),
            'accounts' => $this->getAccounts(),
            'categories' => $this->getCategories(),
        ]);
    }
}
