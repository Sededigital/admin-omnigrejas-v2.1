<?php

namespace App\Livewire\Billings;

use App\Models\Igrejas\Igreja;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\IgrejaRecursosBloqueados;
use App\Helpers\Billings\SubscriptionHelper;
use Illuminate\Support\Facades\Auth;

#[Title('Recursos Bloqueados')]
#[Layout('components.layouts.app')]
class RecursosBloqueados extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $recursoFilter = '';
    public $statusFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingBloqueio = null;
    public $igreja_id = '';
    public $recurso_tipo = 'membros';
    public $motivo_bloqueio = '';
    public $observacoes = '';

    protected function rules()
    {
        return [
            'igreja_id' => 'required|exists:igrejas,id',
            'recurso_tipo' => 'required|string|max:255',
            'motivo_bloqueio' => 'required|string|max:500',
            'observacoes' => 'nullable|string|max:1000',
        ];
    }

    protected $listeners = ['refreshRecursosBloqueados' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedRecursoFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->recursoFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function openModal($bloqueioId = null)
    {
        try {
            if ($bloqueioId) {
                $bloqueio = IgrejaRecursosBloqueados::find($bloqueioId);
                if ($bloqueio) {
                    $this->editingBloqueio = $bloqueio;
                    $this->igreja_id = $bloqueio->igreja_id;
                    $this->recurso_tipo = $bloqueio->recurso_tipo;
                    $this->motivo_bloqueio = $bloqueio->motivo_bloqueio;
                    $this->observacoes = $bloqueio->observacoes;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Bloqueio não encontrado!'
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
        $this->editingBloqueio = null;
        $this->igreja_id = '';
        $this->recurso_tipo = 'membros';
        $this->motivo_bloqueio = '';
        $this->observacoes = '';
        $this->resetValidation();
    }

    public function saveBloqueio()
    {
        $this->validate();

        try {
            $data = [
                'igreja_id' => $this->igreja_id,
                'recurso_tipo' => $this->recurso_tipo,
                'motivo_bloqueio' => $this->motivo_bloqueio,
                'observacoes' => $this->observacoes,
                'bloqueado_por' => Auth::id(),
            ];

            if ($this->editingBloqueio) {
                $this->editingBloqueio->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Bloqueio atualizado com sucesso!'
                ]);
            } else {
                // Usar Helper para bloquear recurso
                SubscriptionHelper::bloquearRecurso(
                    $this->igreja_id,
                    $this->recurso_tipo,
                    $this->motivo_bloqueio,
                    $this->observacoes
                );

                // Criar registro na tabela igreja_recursos_bloqueados
                \App\Models\Billings\IgrejaRecursosBloqueados::create([
                    'igreja_id' => $this->igreja_id,
                    'recurso_tipo' => $this->recurso_tipo,
                    'motivo_bloqueio' => $this->motivo_bloqueio,
                    'observacoes' => $this->observacoes,
                    'bloqueado_por' => Auth::id(),
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Recurso bloqueado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshRecursosBloqueados');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar bloqueio: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteBloqueio($bloqueioId)
    {
        try {
            $bloqueio = IgrejaRecursosBloqueados::find($bloqueioId);
            if ($bloqueio) {
                $bloqueio->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Bloqueio removido com sucesso!'
                ]);
                $this->dispatch('refreshRecursosBloqueados');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Bloqueio não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao remover bloqueio: ' . $e->getMessage()
            ]);
        }
    }

    public function desbloquearRecurso($bloqueioId)
    {
        try {
            $bloqueio = IgrejaRecursosBloqueados::find($bloqueioId);
            if ($bloqueio) {
                // Usar Helper para desbloquear
                SubscriptionHelper::desbloquearRecurso($bloqueio->igreja_id, $bloqueio->recurso_tipo);

                // Atualizar registro na tabela igreja_recursos_bloqueados
                $bloqueio->update([
                    'desbloqueado_em' => now(),
                    'desbloqueado_por' => Auth::id(),
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Recurso desbloqueado com sucesso!'
                ]);
                $this->dispatch('refreshRecursosBloqueados');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao desbloquear recurso: ' . $e->getMessage()
            ]);
        }
    }

    public function getRecursosBloqueados()
    {
        try {
            $query = IgrejaRecursosBloqueados::with(['igreja', 'bloqueadoPor']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('motivo_bloqueio', 'like', '%' . $this->search . '%')
                      ->orWhere('observacoes', 'like', '%' . $this->search . '%')
                      ->orWhereHas('igreja', function ($subQ) {
                          $subQ->where('nome', 'like', '%' . $this->search . '%');
                      });
                });
            }

            if ($this->recursoFilter) {
                $query->where('recurso_tipo', $this->recursoFilter);
            }

            if ($this->statusFilter) {
                if ($this->statusFilter === 'ativo') {
                    $query->whereNull('desbloqueado_em');
                } elseif ($this->statusFilter === 'removido') {
                    $query->whereNotNull('desbloqueado_em');
                }
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar recursos bloqueados: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getRecursoOptions()
    {
        try {
            $permissoes = \App\Models\RBAC\IgrejaPermissao::select('codigo', 'nome')
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

    public function getStatusOptions()
    {
        return [
            'ativo' => 'Ativo',
            'removido' => 'Removido',
        ];
    }

    public function getIgrejas()
    {
        return Igreja::orderBy('nome')->get();
    }

    public function render()
    {
        return view('billings.recursos-bloqueados', [
            'recursosBloqueados' => $this->getRecursosBloqueados(),
            'igrejas' => $this->getIgrejas(),
            'recursoOptions' => $this->getRecursoOptions(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
