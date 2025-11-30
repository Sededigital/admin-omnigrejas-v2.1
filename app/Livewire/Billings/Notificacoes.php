<?php

namespace App\Livewire\Billings;

use App\Models\Igreja;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaNotificacao;

#[Title('Notificações de Assinaturas')]
#[Layout('components.layouts.app')]
class Notificacoes extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $tipoFilter = '';
    public $statusFilter = '';
    public $assinaturaFilter = '';

    // Modal properties
    public $showModal = false;
    public $editingNotificacao = null;
    public $assinatura_id = '';
    public $tipo = 'lembrete';
    public $titulo = '';
    public $mensagem = '';
    public $status = 'enviada';

    protected $rules = [
        'assinatura_id' => 'required|exists:assinatura_historico,id',
        'tipo' => 'required|in:lembrete,atraso,cancelamento',
        'titulo' => 'required|string|max:255',
        'mensagem' => 'nullable|string',
        'status' => 'required|in:enviada,lida,ignorada',
    ];

    protected $listeners = ['refreshNotificacoes' => '$refresh'];

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

    public function updatedAssinaturaFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->tipoFilter = '';
        $this->statusFilter = '';
        $this->assinaturaFilter = '';
        $this->resetPage();
    }

    public function openModal($notificacaoId = null)
    {
        try {
            if ($notificacaoId) {
                $notificacao = AssinaturaNotificacao::find($notificacaoId);
                if ($notificacao) {
                    $this->editingNotificacao = $notificacao;
                    $this->assinatura_id = $notificacao->assinatura_id;
                    $this->tipo = $notificacao->tipo;
                    $this->titulo = $notificacao->titulo;
                    $this->mensagem = $notificacao->mensagem;
                    $this->status = $notificacao->status;
                } else {
                    $this->dispatch('toast', [
                        'type' => 'danger',
                        'message' => 'Notificação não encontrada!'
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
        $this->editingNotificacao = null;
        $this->assinatura_id = '';
        $this->tipo = 'lembrete';
        $this->titulo = '';
        $this->mensagem = '';
        $this->status = 'enviada';
        $this->resetValidation();
    }

    public function saveNotificacao()
    {
        $this->validate();

        try {
            $data = [
                'assinatura_id' => $this->assinatura_id,
                'tipo' => $this->tipo,
                'titulo' => $this->titulo,
                'mensagem' => $this->mensagem,
                'status' => $this->status,
            ];

            if ($this->editingNotificacao) {
                $this->editingNotificacao->update($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Notificação atualizada com sucesso!'
                ]);
            } else {
                AssinaturaNotificacao::create($data);
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Notificação criada com sucesso!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('refreshNotificacoes');

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar notificação: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteNotificacao($notificacaoId)
    {
        try {
            $notificacao = AssinaturaNotificacao::find($notificacaoId);
            if ($notificacao) {
                $notificacao->delete();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Notificação excluída com sucesso!'
                ]);
                $this->dispatch('refreshNotificacoes');
            } else {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Notificação não encontrada!'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir notificação: ' . $e->getMessage()
            ]);
        }
    }

    public function marcarComoLida($notificacaoId)
    {
        try {
            $notificacao = AssinaturaNotificacao::find($notificacaoId);
            if ($notificacao) {
                $notificacao->marcarComoLida();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Notificação marcada como lida!'
                ]);
                $this->dispatch('refreshNotificacoes');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao marcar notificação: ' . $e->getMessage()
            ]);
        }
    }

    public function marcarComoIgnorada($notificacaoId)
    {
        try {
            $notificacao = AssinaturaNotificacao::find($notificacaoId);
            if ($notificacao) {
                $notificacao->marcarComoIgnorada();
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Notificação marcada como ignorada!'
                ]);
                $this->dispatch('refreshNotificacoes');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao marcar notificação: ' . $e->getMessage()
            ]);
        }
    }

    public function getNotificacoes()
    {
        try {
            $query = AssinaturaNotificacao::with(['assinatura.igreja', 'assinatura.pacote']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->search . '%')
                      ->orWhere('mensagem', 'like', '%' . $this->search . '%')
                      ->orWhereHas('assinatura.igreja', function ($subQ) {
                          $subQ->where('nome', 'like', '%' . $this->search . '%');
                      });
                });
            }

            if ($this->tipoFilter) {
                $query->where('tipo', $this->tipoFilter);
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->assinaturaFilter) {
                $query->where('assinatura_id', $this->assinaturaFilter);
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao carregar notificações: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getTipoOptions()
    {
        return [
            'lembrete' => 'Lembrete',
            'atraso' => 'Atraso',
            'cancelamento' => 'Cancelamento'
        ];
    }

    public function getStatusOptions()
    {
        return [
            'enviada' => 'Enviada',
            'lida' => 'Lida',
            'ignorada' => 'Ignorada'
        ];
    }

    public function getAssinaturas()
    {
        return AssinaturaHistorico::with(['igreja', 'pacote'])->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('billings.notificacoes', [
            'notificacoes' => $this->getNotificacoes(),
            'assinaturas' => $this->getAssinaturas(),
            'tipoOptions' => $this->getTipoOptions(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
