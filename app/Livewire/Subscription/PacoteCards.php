<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Billings\Pacote;
use Illuminate\Support\Facades\Auth;

class PacoteCards extends Component
{   
    public $pacotes;
    public $pacoteAtual;
    public $assinaturaAtual;

    public function mount($pacotes, $pacoteAtual, $assinaturaAtual = null)
    {
        $this->pacotes = $pacotes;
        $this->pacoteAtual = $pacoteAtual;
        $this->assinaturaAtual = $assinaturaAtual;
    }


    public function abrirModalConfirmacao($pacoteId)
    {
        // Verificar se usuário está logado antes de abrir modal
        if (!Auth::check()) {
            // Abrir modal SweetAlert para login
            $this->dispatch('abrirModalLogin');
            return;
        }

        $pacote = Pacote::find($pacoteId);

        if (!$pacote) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Pacote não encontrado!'
            ]);
            return;
        }

        $this->dispatch('abrirModalPlano', [
            'id' => $pacote->id,
            'nome' => $pacote->nome,
            'preco' => $pacote->getPrecoFormatado(),
            'descricao' => $pacote->descricao ?? 'Plano completo para sua igreja',
            'preco_vitalicio' => $pacote->getPrecoVitalicioFormatado() ?? null,
            'acao' => 'nova_assinatura', // Sempre nova assinatura quando vem do PacoteCards
            'pacote_atual' => $this->assinaturaAtual?->pacote, // Passar pacote atual para comparação
            'assinatura_atual' => $this->assinaturaAtual // Passar toda a assinatura atual
        ]);
    }

    
    #[On('confirmarPacote')]
    public function confirmarPacote($id, $acao = 'nova_assinatura')
    {

        $pacote = Pacote::find($id);

        if (!$pacote) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Pacote não encontrado!'
            ]);
            return;
        }

        // ✅ Fechar modal e redirecionar para pagamento com a ação correta
        return redirect()->route('ecommerce.subscription.payment', [
            'pacote' => $pacote->id,
            'acao' => $acao
        ]);
    }

    public function render()
    {
        return view('subscription.pacote-cards');
    }
}
