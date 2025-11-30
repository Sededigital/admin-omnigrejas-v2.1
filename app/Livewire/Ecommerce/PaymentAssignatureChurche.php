<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\PagamentoAssinaturaIgreja;


#[Title('Status dos Pagamentos - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class PaymentAssignatureChurche extends Component
{
    use WithPagination;

    // Filtros e busca
    public $search = '';
    public $statusFilter = '';
    public $metodoFilter = '';
    public $perPage = 10;

    // Controle de igrejas (se múltiplas)
    public $igrejaSelecionada;
    public $igrejasDisponiveis;

    // Modal de detalhes
    public $showModal = false;
    public $pagamentoSelecionado;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        // Carregar igrejas disponíveis do usuário
        $this->carregarIgrejasDisponiveis();

        // Definir igreja padrão (igreja com pagamento mais recente)
        if ($this->igrejasDisponiveis && $this->igrejasDisponiveis->isNotEmpty()) {
            $this->selecionarIgrejaPadrao();
        }
    }

    private function carregarIgrejasDisponiveis()
    {
        $user = Auth::user();

        // Buscar todas as igrejas onde o usuário é membro ativo
        $this->igrejasDisponiveis = collect($user->membros()
            ->where('status', 'ativo')
            ->with('igreja')
            ->get()
            ->map(function($membro) {
                return [
                    'id' => $membro->igreja->id,
                    'nome' => $membro->igreja->nome,
                    'sigla' => $membro->igreja->sigla,
                    'categoria' => $membro->igreja->categoria->nome ?? 'Geral',
                    'cargo' => $membro->cargo,
                    'principal' => $membro->principal
                ];
            })
            ->sortByDesc('principal') // Igrejas principais primeiro
            ->values());
    }

    private function selecionarIgrejaPadrao()
    {
        // Primeiro, tentar encontrar a igreja com o pagamento mais recente
        $user = Auth::user();
        $igrejaIds = $user->membros()->where('status', 'ativo')->pluck('igreja_id');

        $pagamentoMaisRecente = PagamentoAssinaturaIgreja::whereIn('igreja_id', $igrejaIds)
            ->orderBy('data_pagamento', 'desc')
            ->first();

        if ($pagamentoMaisRecente) {
            $this->igrejaSelecionada = $pagamentoMaisRecente->igreja_id;
        } else {
            // Se não há pagamentos, selecionar a primeira igreja
            $this->igrejaSelecionada = $this->igrejasDisponiveis->first()['id'];
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedMetodoFilter()
    {
        $this->resetPage();
    }

    public function updatedIgrejaSelecionada()
    {
        $this->resetPage();
    }

    public function verDetalhes($pagamentoId)
    {
        $this->pagamentoSelecionado = PagamentoAssinaturaIgreja::with(['igreja', 'pacote', 'confirmadoPor', 'criadoPor'])
            ->find($pagamentoId);

        if ($this->pagamentoSelecionado) {
            $this->showModal = true;
        }
    }

    public function fecharModal()
    {
        $this->showModal = false;
        $this->pagamentoSelecionado = null;
    }

    public function getPagamentos()
    {
        $query = PagamentoAssinaturaIgreja::with(['igreja', 'pacote', 'confirmadoPor'])
            ->where('igreja_id', $this->igrejaSelecionada)
            ->orderBy('data_pagamento', 'desc'); // Ordenar por data de pagamento mais recente

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('referencia', 'like', '%' . $this->search . '%')
                  ->orWhere('observacoes', 'like', '%' . $this->search . '%')
                  ->orWhereHas('pacote', function($pq) {
                      $pq->where('nome', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->metodoFilter) {
            $query->where('metodo_pagamento', $this->metodoFilter);
        }

        return $query->paginate($this->perPage);
    }

    public function getEstatisticas()
    {
        $pagamentos = PagamentoAssinaturaIgreja::where('igreja_id', $this->igrejaSelecionada);

        return [
            'total' => (clone $pagamentos)->count(),
            'pendentes' => (clone $pagamentos)->where('status', 'pendente')->count(),
            'confirmados' => (clone $pagamentos)->where('status', 'confirmado')->count(),
            'rejeitados' => (clone $pagamentos)->where('status', 'rejeitado')->count(),
            'expirados' => (clone $pagamentos)->where('status', 'expirado')->count(),
            'valor_total' => (clone $pagamentos)->sum('valor'),
            'valor_confirmado' => (clone $pagamentos)->where('status', 'confirmado')->sum('valor'),
        ];
    }

    public function getEstatisticasGerais()
    {
        // Estatísticas gerais de todas as igrejas do usuário
        $user = Auth::user();
        $igrejaIds = $user->membros()->where('status', 'ativo')->pluck('igreja_id');

        $pagamentos = PagamentoAssinaturaIgreja::whereIn('igreja_id', $igrejaIds);

        return [
            'total_geral' => $pagamentos->count(),
            'confirmados_geral' => $pagamentos->where('status', 'confirmado')->count(),
            'pendentes_geral' => $pagamentos->where('status', 'pendente')->count(),
            'valor_total_geral' => $pagamentos->sum('valor'),
        ];
    }

    public function render()
    {
        return view('ecommerce.payment-assignature-churche', [
            'pagamentos' => $this->getPagamentos(),
            'estatisticas' => $this->getEstatisticas(),
            'estatisticasGerais' => $this->getEstatisticasGerais(),
            'igrejasDisponiveis' => $this->igrejasDisponiveis,
        ]);
    }
}
