<?php

namespace App\Livewire\Church\Marketplace;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Marketplace\MarketplacePagamento;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

#[Title('Pagamentos | Marketplace')]
#[Layout('components.layouts.app')]
class Payments extends Component
{
    use WithPagination;

    // Propriedades para listagem
    public $payments = [];
    public $membroAtual;

    // Propriedades para filtros
    public $filtroStatus = '';
    public $filtroMetodo = '';
    public $filtroPedido = '';

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarPayments();
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

    protected function carregarPayments()
    {
        $query = MarketplacePagamento::with(['pedido.produto', 'pedido.comprador'])
            ->whereHas('pedido', function($q) {
                $q->where('igreja_id', $this->membroAtual->igreja_id);
            });

        // Aplicar filtros
        if ($this->filtroStatus) {
            $query->where('status', $this->filtroStatus);
        }

        if ($this->filtroMetodo) {
            $query->where('metodo', $this->filtroMetodo);
        }

        if ($this->filtroPedido) {
            $query->whereHas('pedido', function($q) {
                $q->where('id', 'ilike', '%' . $this->filtroPedido . '%');
            });
        }

        $this->payments = $query->orderBy('created_at', 'desc')->get();
    }

    public function atualizarStatusPagamento($paymentId, $novoStatus)
    {
        $payment = MarketplacePagamento::find($paymentId);

        if (!$payment || $payment->pedido->igreja_id !== $this->membroAtual->igreja_id) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Pagamento não encontrado.'
            ]);
            return;
        }

        $payment->update(['status' => $novoStatus]);
        $this->carregarPayments();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Status do pagamento atualizado com sucesso!'
        ]);
    }

    public function excluirPayment($paymentId)
    {
        $payment = MarketplacePagamento::find($paymentId);

        if (!$payment || $payment->pedido->igreja_id !== $this->membroAtual->igreja_id) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Pagamento não encontrado.'
            ]);
            return;
        }

        $payment->delete();
        $this->carregarPayments();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pagamento excluído com sucesso!'
        ]);
    }

    public function updatedFiltroStatus()
    {
        $this->carregarPayments();
    }

    public function updatedFiltroMetodo()
    {
        $this->carregarPayments();
    }

    public function updatedFiltroPedido()
    {
        $this->carregarPayments();
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'pendente' => 'Pendente',
            'confirmado' => 'Confirmado',
            'falhou' => 'Falhou',
            'estornado' => 'Estornado',
            default => ucfirst($status)
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pendente' => 'warning',
            'confirmado' => 'success',
            'falhou' => 'danger',
            'estornado' => 'secondary',
            default => 'secondary'
        };
    }

    public function getMetodoLabel($metodo)
    {
        return match($metodo) {
            'multicaixa_express' => 'Multicaixa Express',
            'bai_direto' => 'BAI Direto',
            'tpa' => 'TPA',
            'cash' => 'Dinheiro',
            'deposito' => 'Depósito',
            default => ucfirst(str_replace('_', ' ', $metodo))
        };
    }

    public function getStatsProperty()
    {
        $payments = collect($this->payments);
        $total = $payments->count();
        $confirmado = $payments->where('status', 'confirmado')->count();
        $pendente = $payments->where('status', 'pendente')->count();
        $totalValor = $payments->where('status', 'confirmado')->sum('valor');

        return [
            'total' => $total,
            'confirmado' => $confirmado,
            'pendente' => $pendente,
            'total_valor' => $totalValor,
        ];
    }

    public function render()
    {
        return view('church.marketplace.payments', [
            'stats' => $this->stats,
        ]);
    }
}
