<?php

namespace App\Livewire\Church\Marketplace;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Marketplace\MarketplaceProduto;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

#[Title('Produtos | Marketplace')]
#[Layout('components.layouts.app')]
class Products extends Component
{
    use WithPagination;

    // Propriedades para listagem
    public $products = [];
    public $membroAtual;

    // Propriedades para modal
    public $isEditing = false;
    public $productSelecionado = null;

    // Propriedades do formulário
    #[Rule('required|string|max:255')]
    public $nome = '';

    #[Rule('nullable|string|max:1000')]
    public $descricao = '';

    #[Rule('required|numeric|min:0')]
    public $preco = '';

    #[Rule('required|integer|min:0')]
    public $estoque = 0;

    #[Rule('boolean')]
    public $ativo = true;

    // Propriedades para filtros
    public $filtroNome = '';
    public $filtroAtivo = '';
    public $filtroIgreja = '';

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarProducts();
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

    protected function carregarProducts()
    {
        $query = MarketplaceProduto::with(['igreja', 'pedidos'])
            ->where('igreja_id', $this->membroAtual->igreja_id);

        // Aplicar filtros
        if ($this->filtroNome) {
            $query->where('nome', 'ilike', '%' . $this->filtroNome . '%');
        }

        if ($this->filtroAtivo !== '') {
            $query->where('ativo', $this->filtroAtivo === '1');
        }

        $this->products = $query->orderBy('created_at', 'desc')->get();
    }

    public function openModal($productId = null)
    {
        $this->resetModal();

        if ($productId) {
            $product = MarketplaceProduto::find($productId);

            if (!$product || $product->igreja_id !== $this->membroAtual->igreja_id) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Produto não encontrado.'
                ]);
                return;
            }

            $this->productSelecionado = $product;
            $this->nome = $product->nome;
            $this->descricao = $product->descricao;
            $this->preco = $product->preco;
            $this->estoque = $product->estoque;
            $this->ativo = $product->ativo;
            $this->isEditing = true;
        } else {
            $this->ativo = true;
            $this->isEditing = false;
        }

        $this->dispatch('open-product-modal');
    }

    public function salvarProduct()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->productSelecionado->update([
                'nome' => $this->nome,
                'descricao' => $this->descricao,
                'preco' => $this->preco,
                'estoque' => $this->estoque,
                'ativo' => $this->ativo,
            ]);

            $mensagem = 'Produto atualizado com sucesso!';
        } else {
            MarketplaceProduto::create([
                'igreja_id' => $this->membroAtual->igreja_id,
                'nome' => $this->nome,
                'descricao' => $this->descricao,
                'preco' => $this->preco,
                'estoque' => $this->estoque,
                'ativo' => $this->ativo,
            ]);

            $mensagem = 'Produto cadastrado com sucesso!';
        }

        $this->carregarProducts();
        $this->dispatch('close-product-modal');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function excluirProduct($productId)
    {
        $product = MarketplaceProduto::find($productId);

        if (!$product || $product->igreja_id !== $this->membroAtual->igreja_id) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto não encontrado.'
            ]);
            return;
        }

        // Verificar se tem pedidos
        if ($product->pedidos()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível excluir um produto que possui pedidos.'
            ]);
            return;
        }

        $product->delete();
        $this->carregarProducts();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Produto excluído com sucesso!'
        ]);
    }

    public function toggleProductStatus($productId)
    {
        $product = MarketplaceProduto::find($productId);

        if (!$product || $product->igreja_id !== $this->membroAtual->igreja_id) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto não encontrado.'
            ]);
            return;
        }

        $product->update(['ativo' => !$product->ativo]);
        $this->carregarProducts();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Status do produto alterado com sucesso!'
        ]);
    }

    protected function resetModal()
    {
        $this->productSelecionado = null;
        $this->nome = '';
        $this->descricao = '';
        $this->preco = '';
        $this->estoque = 0;
        $this->ativo = true;
        $this->resetValidation();
    }

    public function updatedFiltroNome()
    {
        $this->carregarProducts();
    }

    public function updatedFiltroAtivo()
    {
        $this->carregarProducts();
    }

    public function getStatsProperty()
    {
        $products = collect($this->products);
        $total = $products->count();
        $active = $products->where('ativo', true)->count();
        $inactive = $total - $active;
        $totalOrders = $products->sum(function($product) {
            return $product->pedidos->count();
        });

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'total_orders' => $totalOrders,
        ];
    }

    public function render()
    {
        return view('church.marketplace.products', [
            'stats' => $this->stats,
        ]);
    }
}
