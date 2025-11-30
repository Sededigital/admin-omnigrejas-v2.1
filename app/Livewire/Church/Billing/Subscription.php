<?php

namespace App\Livewire\Church\Billing;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Billings\Modulo;
use App\Models\Billings\Pacote;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\PacotePermissao;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;

#[Title('Assinaturas | Omnigrejas Admin')]
#[Layout('components.layouts.app')]
class Subscription extends Component
{
    public $igreja;
    public $assinaturaAtual;
    public $assinaturasHistorico;
    public $pagamentosRecentes;
    public $pacoteAtual;
    public $permissoesPacote;
    public $modulos;
    public $estatisticas;

    public function mount()
    {
        $this->igreja = Auth::user()->getIgreja();

        if (!$this->igreja) {
            return;
        }

        $this->carregarDados();
    }

    public function carregarDados()
    {
        // Assinatura atual
        $this->assinaturaAtual = AssinaturaAtual::where('igreja_id', $this->igreja->id)->first();

        // Histórico de assinaturas
        $this->assinaturasHistorico = AssinaturaHistorico::where('igreja_id', $this->igreja->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pagamentos recentes
        $this->pagamentosRecentes = AssinaturaPagamento::where('igreja_id', $this->igreja->id)
            ->orderBy('data_pagamento', 'desc')
            ->take(5)
            ->get();

        // Pacote atual
        if ($this->assinaturaAtual) {
            
            $this->pacoteAtual = Pacote::find($this->assinaturaAtual->pacote_id);

            // Permissões do pacote
            if ($this->pacoteAtual) {
                $this->permissoesPacote = PacotePermissao::where('pacote_id', $this->pacoteAtual->id)
                    ->with('modulo')
                    ->get();
            }
        }

        // Módulos disponíveis
        $this->modulos = Modulo::all();

        // Estatísticas
        $this->carregarEstatisticas();
    }

    private function carregarEstatisticas()
    {
        $this->estatisticas = [
            'total_pagamentos' => AssinaturaPagamento::where('igreja_id', $this->igreja->id)->count(),
            'valor_total_pago' => AssinaturaPagamento::where('igreja_id', $this->igreja->id)
                ->where('status', 'confirmado')
                ->sum('valor'),
            'assinaturas_ativas' => AssinaturaAtual::where('igreja_id', $this->igreja->id)
                ->where('status', 'Ativo')
                ->count(),
            'dias_restantes' => $this->assinaturaAtual ? $this->assinaturaAtual->getDaysUntilExpiration() : 0,
        ];
    }

    public function renovarAssinatura()
    {
        // Lógica para renovar assinatura
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de renovação em desenvolvimento. Entre em contato com o suporte ou administração'
        ]);
    }

    public function cancelarAssinatura()
    {
        // Lógica para cancelar assinatura
        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => 'Entre em contato com o suporte para cancelar sua assinatura. Entre em contato com o suporte ou administração'
        ]);
    }

    public function imprimirAssinatura()
    {
        // Lógica para imprimir assinatura
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de impressão em desenvolvimento. Entre em contato com o suporte ou administração'
        ]);
    }

    public function atualizarMetodoPagamento()
    {
        // Lógica para atualizar método de pagamento
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Funcionalidade de atualização de pagamento em desenvolvimento. Entre em contato com o suporte ou administração'
        ]);
    }

    public function marcarAlertaComoLido($alertaId)
    {
        try {
            $alerta = \App\Models\Billings\AssinaturaAlertas::find($alertaId);

            if ($alerta && $alerta->igreja_id === $this->igreja->id) {
                $alerta->update(['lido' => true]);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Alerta marcado como lido!'
                ]);

                // Recarregar dados para atualizar a interface
                $this->carregarDados();
            } else {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Alerta não encontrado ou sem permissão.'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao marcar alerta como lido.'
            ]);
        }
    }

    public function verTodosAlertas()
    {
        // Redirecionar para uma página dedicada de alertas
      //  return redirect()->route('');
    }

    public function render()
    {
        return view('church.billing.subscription', [
            'igreja' => $this->igreja,
            'assinaturaAtual' => $this->assinaturaAtual,
            'assinaturasHistorico' => $this->assinaturasHistorico,
            'pagamentosRecentes' => $this->pagamentosRecentes,
            'pacoteAtual' => $this->pacoteAtual,
            'permissoesPacote' => $this->permissoesPacote,
            'modulos' => $this->modulos,
            'estatisticas' => $this->estatisticas,
        ]);
    }
}
