<?php

namespace App\Livewire\Billings;

use App\Models\Igrejas\Igreja;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\AssinaturaAlertas;
use App\Helpers\Billings\SubscriptionHelper;

#[Title('Gestão de Alertas SaaS')]
#[Layout('components.layouts.app')]
class Alertas extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $tipoFilter = '';
    public $statusFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingAlerta = null;
    public $igreja_id = '';
    public $tipo_alerta = 'limite_excedido';
    public $titulo = '';
    public $mensagem = '';
    public $expires_at = '';

    protected function rules()
    {
        return [
            'igreja_id' => 'required|exists:igrejas,id',
            'tipo_alerta' => 'required|in:limite_excedido,expiracao_proxima,recursos_bloqueados,manutencao',
            'titulo' => 'required|string|max:255',
            'mensagem' => 'required|string|max:1000',
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    protected $listeners = ['refreshAlertas' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTipoFilter()
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
        $this->tipoFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function openModal($alertaId = null)
    {
        try {
            if ($alertaId) {
                $alerta = AssinaturaAlertas::find($alertaId);
                if ($alerta) {
                    $this->editingAlerta = $alerta;
                    $this->igreja_id = $alerta->igreja_id;
                    $this->tipo_alerta = $alerta->tipo_alerta;
                    $this->titulo = $alerta->titulo;
                    $this->mensagem = $alerta->mensagem;
                    $this->expires_at = $alerta->expires_at ? $alerta->expires_at->format('Y-m-d') : '';
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Alerta não encontrado!'
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
        $this->editingAlerta = null;
        $this->igreja_id = '';
        $this->tipo_alerta = 'limite_excedido';
        $this->titulo = '';
        $this->mensagem = '';
        $this->expires_at = '';
        $this->resetValidation();
    }

    public function saveAlerta()
    {
        $this->validate();

        try {
            $data = [
                'igreja_id' => $this->igreja_id,
                'tipo_alerta' => $this->tipo_alerta,
                'titulo' => $this->titulo,
                'mensagem' => $this->mensagem,
                'expires_at' => $this->expires_at ?: null,
            ];

            if ($this->editingAlerta) {
                $this->editingAlerta->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Alerta atualizado com sucesso!'
                ]);
            } else {
                // Usar Helper para criar alerta
                SubscriptionHelper::criarAlerta(
                    $this->igreja_id,
                    $this->tipo_alerta,
                    $this->titulo,
                    $this->mensagem,
                    $this->expires_at
                );

                // Criar registro na tabela assinatura_alertas
                \App\Models\Billings\AssinaturaAlertas::create([
                    'igreja_id' => $this->igreja_id,
                    'tipo_alerta' => $this->tipo_alerta,
                    'titulo' => $this->titulo,
                    'mensagem' => $this->mensagem,
                    'expires_at' => $this->expires_at,
                ]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Alerta criado com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshAlertas');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar alerta: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteAlerta($alertaId)
    {
        try {
            $alerta = AssinaturaAlertas::find($alertaId);
            if ($alerta) {
                $alerta->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Alerta excluído com sucesso!'
                ]);
                $this->dispatch('refreshAlertas');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Alerta não encontrado!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir alerta: ' . $e->getMessage()
            ]);
        }
    }

    public function enviarAlertaAgora($alertaId)
    {
        try {
            $alerta = AssinaturaAlertas::find($alertaId);
            if ($alerta) {
                // Usar Helper para enviar alerta imediatamente
                SubscriptionHelper::enviarAlertaImediatamente($alerta);

                // Atualizar registro na tabela assinatura_alertas
                $alerta->update(['enviado_em' => now()]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Alerta enviado com sucesso!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao enviar alerta: ' . $e->getMessage()
            ]);
        }
    }

    public function getAlertas()
    {
        try {
            $query = AssinaturaAlertas::with(['igreja']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->search . '%')
                      ->orWhere('mensagem', 'like', '%' . $this->search . '%')
                      ->orWhereHas('igreja', function ($subQ) {
                          $subQ->where('nome', 'like', '%' . $this->search . '%');
                      });
                });
            }

            if ($this->tipoFilter) {
                $query->where('tipo_alerta', $this->tipoFilter);
            }

            if ($this->statusFilter) {
                if ($this->statusFilter === 'ativo') {
                    $query->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
                } elseif ($this->statusFilter === 'expirado') {
                    $query->where('expires_at', '<=', now());
                }
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar alertas: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getTipoOptions()
    {
        return [
            'limite_excedido' => 'Limite Excedido',
            'expiracao_proxima' => 'Expiração Próxima',
            'recursos_bloqueados' => 'Recursos Bloqueados',
            'manutencao' => 'Manutenção',
        ];
    }

    public function getStatusOptions()
    {
        return [
            'ativo' => 'Ativo',
            'expirado' => 'Expirado',
        ];
    }

    public function getIgrejas()
    {
        return Igreja::orderBy('nome')->get();
    }

    public function render()
    {
        return view('billings.alertas', [
            'alertas' => $this->getAlertas(),
            'igrejas' => $this->getIgrejas(),
            'tipoOptions' => $this->getTipoOptions(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
