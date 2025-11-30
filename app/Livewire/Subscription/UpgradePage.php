<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Igrejas\Igreja;
use Livewire\Attributes\Title;
use App\Models\Billings\Pacote;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaAlertas;
use App\Models\Billings\AssinaturaHistorico;

#[Title('Upgrade de Assinatura - OmnIgrejas E-commerce')]
#[Layout('components.layouts.subscription')]
class UpgradePage extends Component
{
    public $igreja;
    public $pacotes;
    public $assinaturaAtual;
    public $assinaturasHistorico;
    public $alertasAtivos;

    #[Locked]
    public $selectedPacote = null;

    public $showComparison = false;
    public $modo = 'upgrade'; // 'upgrade', 'renovar', 'nova_assinatura'

    public function mount($igrejaId = null)
    {
        // Para usuários não logados, não tentar buscar igreja
        if (!Auth::check()) {
            // Carregar apenas pacotes disponíveis para visualização
            $this->pacotes = Pacote::orderBy('preco')->get();
            $this->modo = 'nova_assinatura'; // Sempre mostrar como nova assinatura
            return;
        }

        // Buscar igreja (por parâmetro ou sessão) apenas para usuários logados
        $this->igreja = $igrejaId ? Igreja::find($igrejaId) : session('igreja_atual');

        if (!$this->igreja) {
            return redirect()->route('selecionar.igreja');
        }

        // Carregar dados da igreja apenas para usuários logados
        $this->carregarDadosIgreja();

        // Determinar modo baseado no status da assinatura
        $this->determinarModo();
    }



    private function carregarDadosIgreja()
    {
        // Carregar pacotes disponíveis ordenados por preço (para determinar o popular)
        $this->pacotes = Pacote::orderBy('preco')->get();

        // Carregar assinatura atual
        $this->assinaturaAtual = AssinaturaAtual::where('igreja_id', $this->igreja->id)->first();

        // Carregar histórico de assinaturas
        $this->assinaturasHistorico = AssinaturaHistorico::where('igreja_id', $this->igreja->id)
            ->orderBy('data_inicio', 'desc')
            ->take(5)
            ->get();

        // Carregar alertas ativos
        $this->alertasAtivos = AssinaturaAlertas::where('igreja_id', $this->igreja->id)
            ->ativos()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function determinarModo()
    {
        if (!$this->assinaturaAtual) {
            // Nunca teve assinatura
            $this->modo = 'nova_assinatura';
        } elseif ($this->assinaturaAtual->isExpired()) {
            // Assinatura expirada - precisa renovar
            $this->modo = 'renovar';
        } else {
            // Assinatura ativa - pode fazer upgrade
            $this->modo = 'upgrade';
        }
    }


    public function toggleComparison()
    {
        $this->showComparison = !$this->showComparison;

        // Reset selected package when switching to comparison view
        if ($this->showComparison) {
            $this->selectedPacote = null;
        }
    }

    
    public function marcarAlertaLido($alertaId)
    {
        $alerta = $this->alertasAtivos->find($alertaId);
        if ($alerta) {
            $alerta->marcarComoLido();
            $this->alertasAtivos = $this->alertasAtivos->fresh(); // Recarregar
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Alerta marcado como lido!'
            ]);
        }
    }



    private function podeAssinarPacote(Pacote $pacote): bool
    {
        // Se não tem assinatura atual, pode assinar qualquer pacote
        if (!$this->assinaturaAtual) {
            return true;
        }

        // Se assinatura expirada, pode renovar ou fazer upgrade
        if ($this->assinaturaAtual->isExpired()) {
            return true;
        }

        // Se assinatura ativa, só pode fazer upgrade para pacotes superiores
        if ($this->modo === 'upgrade') {
            return $pacote->preco > $this->assinaturaAtual->pacote->preco;
        }

        return false;
    }

    // ========================================
    // HELPERS PARA A VIEW
    // ========================================

    public function getStatusAssinatura()
    {
        if (!$this->assinaturaAtual) {
            return [
                'status' => 'sem_assinatura',
                'titulo' => 'Sem Assinatura',
                'mensagem' => 'Sua igreja ainda não possui uma assinatura ativa.',
                'tipo_alerta' => 'info',
                'acao_sugerida' => 'Assinar Agora'
            ];
        }

        // Debug: verificar valores
        $isExpired = $this->assinaturaAtual->isExpired();
        $isExpiringSoon = $this->assinaturaAtual->isExpiringSoon(30);
        $diasParaExpirar = $this->assinaturaAtual->diasParaExpirar();

        // Se já expirou, mostrar como expirada
        if ($isExpired) {
            return [
                'status' => 'expirada',
                'titulo' => 'Assinatura Expirada',
                'mensagem' => "Sua assinatura do pacote {$this->assinaturaAtual->pacote->nome} expirou em {$this->assinaturaAtual->data_fim->format('d/m/Y')}.",
                'tipo_alerta' => 'warning',
                'acao_sugerida' => 'Renovar Agora'
            ];
        }

        // Só mostrar "expirando em breve" se faltam 30 dias ou menos
        if ($diasParaExpirar !== null && $diasParaExpirar > 0 && $diasParaExpirar <= 30) {
            return [
                'status' => 'expirando_em_breve',
                'titulo' => 'Assinatura Expirando em Breve',
                'mensagem' => "Sua assinatura expira em {$diasParaExpirar} dias.",
                'tipo_alerta' => 'warning',
                'acao_sugerida' => 'Renovar Agora'
            ];
        }

        // Caso contrário, está ativa
        return [
            'status' => 'ativa',
            'titulo' => 'Assinatura Ativa',
            'mensagem' => "Pacote {$this->assinaturaAtual->pacote->nome} - Ativa até {$this->assinaturaAtual->data_fim->format('d/m/Y')}.",
            'tipo_alerta' => 'success',
            'acao_sugerida' => 'Fazer Upgrade'
        ];
    }

    public function getPacotesDisponiveis()
    {
        if ($this->modo === 'nova_assinatura') {
            // Todos os pacotes disponíveis para nova assinatura
            return $this->pacotes;
        }

        if ($this->modo === 'renovar') {
            // Mesmo pacote ou superiores para renovação
            return $this->pacotes->filter(function($pacote) {
                return $pacote->preco >= $this->assinaturaAtual->pacote->preco;
            });
        }

        if ($this->modo === 'upgrade') {
            // Todos os pacotes para upgrade - o modal decidirá se permite ou não
            return $this->pacotes;
        }

        return $this->pacotes;
    }

    public function getPacoteAtual()
    {
        return $this->assinaturaAtual?->pacote;
    }

    public function getHistoricoAssinaturas()
    {
        return $this->assinaturasHistorico;
    }

    public function getAlertasAtivos()
    {
        return $this->alertasAtivos;
    }

    public function getModoFormatado()
    {
        return match($this->modo) {
            'nova_assinatura' => 'Nova Assinatura',
            'renovar' => 'Renovar Assinatura',
            'upgrade' => 'Fazer Upgrade',
            default => 'Assinar'
        };
    }

    public function render()
    {
        // Para usuários não logados, passar dados limitados
        if (!Auth::check()) {
            return view('subscription.upgrade-page', [
                'statusAssinatura' => [
                    'status' => 'sem_assinatura',
                    'titulo' => 'Visualização de Planos',
                    'mensagem' => 'Faça login para ver detalhes da sua assinatura atual.',
                    'tipo_alerta' => 'info',
                    'acao_sugerida' => 'Fazer Login'
                ],
                'pacotesDisponiveis' => $this->pacotes,
                'pacoteAtual' => null,
                'historicoAssinaturas' => collect(),
                'alertasAtivos' => collect(),
                'modoFormatado' => 'Nova Assinatura',
            ]);
        }

        return view('subscription.upgrade-page', [
            'statusAssinatura' => $this->getStatusAssinatura(),
            'pacotesDisponiveis' => $this->getPacotesDisponiveis(),
            'pacoteAtual' => $this->getPacoteAtual(),
            'historicoAssinaturas' => $this->getHistoricoAssinaturas(),
            'alertasAtivos' => $this->getAlertasAtivos(),
            'modoFormatado' => $this->getModoFormatado(),
        ]);
    }
}