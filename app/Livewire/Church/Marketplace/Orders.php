<?php

namespace App\Livewire\Church\Marketplace;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Marketplace\MarketplacePedido;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Title('Pedidos | Marketplace')]
#[Layout('components.layouts.app')]
class Orders extends Component
{
    use WithPagination;

    // Propriedades para listagem
    public $orders = [];
    public $membroAtual;

    // Propriedades para filtros
    public $filtroStatus = '';
    public $filtroProduto = '';
    public $filtroComprador = '';

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarOrders();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = \App\Models\Igrejas\IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar o marketplace.');
        }
    }

    protected function carregarOrders()
    {
        $query = MarketplacePedido::with(['produto', 'comprador', 'pagamentos', 'igreja'])
            ->where('igreja_id', Auth::user()->getIgrejaId());

        // Aplicar filtros
        if ($this->filtroStatus) {
            $query->where('status', $this->filtroStatus);
        }

        if ($this->filtroProduto) {
            $query->whereHas('produto', function($q) {
                $q->where('nome', 'ilike', '%' . $this->filtroProduto . '%');
            });
        }

        if ($this->filtroComprador) {
            $query->whereHas('comprador', function($q) {
                $q->where('name', 'ilike', '%' . $this->filtroComprador . '%');
            });
        }

        $this->orders = $query->orderBy('created_at', 'desc')->get();
    }

    public function atualizarStatus($orderId, $novoStatus)
    {
        $order = MarketplacePedido::find($orderId);

        if (!$order || $order->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Pedido não encontrado.'
            ]);
            return;
        }

        $order->update(['status' => $novoStatus]);
        $this->carregarOrders();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Status do pedido atualizado com sucesso!'
        ]);
    }

    public function excluirOrder($orderId)
    {
        $order = MarketplacePedido::find($orderId);

        if (!$order || $order->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Pedido não encontrado.'
            ]);
            return;
        }

        // Verificar se tem pagamentos
        if ($order->pagamentos()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível excluir um pedido que possui pagamentos.'
            ]);
            return;
        }

        $order->delete();
        $this->carregarOrders();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pedido excluído com sucesso!'
        ]);
    }

    public function updatedFiltroStatus()
    {
        $this->carregarOrders();
    }

    public function updatedFiltroProduto()
    {
        $this->carregarOrders();
    }

    public function updatedFiltroComprador()
    {
        $this->carregarOrders();
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            'enviado' => 'Enviado',
            'concluido' => 'Concluído',
            'cancelado' => 'Cancelado',
            default => ucfirst($status)
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pendente' => 'warning',
            'pago' => 'info',
            'enviado' => 'primary',
            'concluido' => 'success',
            'cancelado' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatsProperty()
    {
        $orders = collect($this->orders);
        $total = $orders->count();
        $pendente = $orders->where('status', 'pendente')->count();
        $concluido = $orders->where('status', 'concluido')->count();
        $totalValor = $orders->sum(function($order) {
            return $order->produto->preco * $order->quantidade;
        });

        return [
            'total' => $total,
            'pendente' => $pendente,
            'concluido' => $concluido,
            'total_valor' => $totalValor,
        ];
    }

    public function render()
    {
        return view('church.marketplace.orders', [
            'stats' => $this->stats,
        ]);
    }
}
