<?php

namespace App\Livewire\Super;

trait DashboardConfirmation
{
    // Propriedades para controle de modais de confirmação
    public $showConfirmModal = false;
    public $confirmAction = '';
    public $confirmTitle = '';
    public $confirmMessage = '';
    public $confirmButtonText = '';
    public $confirmButtonClass = '';
    public $actionParams = [];

    // Métodos de confirmação para cada ação
    public function confirmarRenovarAssinatura($assinaturaId, $nomeIgreja = 'Igreja')
    {
        $this->showConfirmModal = true;
        $this->confirmAction = 'renovarAssinatura';
        $this->actionParams = ['id' => $assinaturaId];
        $this->confirmTitle = 'Renovar Assinatura';
        $this->confirmMessage = "Tem certeza que deseja renovar a assinatura da {$nomeIgreja} por mais 30 dias?";
        $this->confirmButtonText = 'Sim, Renovar';
        $this->confirmButtonClass = 'btn-success';
    }

    public function confirmarMarcarFalhaResolvida($falhaId, $nomeIgreja = 'Igreja')
    {
        $this->showConfirmModal = true;
        $this->confirmAction = 'marcarFalhaComoResolvida';
        $this->actionParams = ['id' => $falhaId];
        $this->confirmTitle = 'Resolver Falha de Pagamento';
        $this->confirmMessage = "Confirma que a falha de pagamento da {$nomeIgreja} foi resolvida?";
        $this->confirmButtonText = 'Sim, Resolver';
        $this->confirmButtonClass = 'btn-success';
    }

    public function confirmarEnviarLembrete($assinaturaId, $nomeIgreja = 'Igreja')
    {
        $this->showConfirmModal = true;
        $this->confirmAction = 'enviarLembreteVencimento';
        $this->actionParams = ['id' => $assinaturaId];
        $this->confirmTitle = 'Enviar Lembrete';
        $this->confirmMessage = "Deseja enviar um lembrete de vencimento para {$nomeIgreja}?";
        $this->confirmButtonText = 'Sim, Enviar';
        $this->confirmButtonClass = 'btn-info';
    }

    public function confirmarSuspenderAssinatura($assinaturaId, $nomeIgreja = 'Igreja')
    {
        $this->showConfirmModal = true;
        $this->confirmAction = 'suspenderAssinatura';
        $this->actionParams = ['id' => $assinaturaId];
        $this->confirmTitle = 'Suspender Assinatura';
        $this->confirmMessage = "⚠️ ATENÇÃO: Esta ação irá suspender imediatamente a assinatura da {$nomeIgreja}. Deseja continuar?";
        $this->confirmButtonText = 'Sim, Suspender';
        $this->confirmButtonClass = 'btn-danger';
    }

    public function confirmarGerarRelatorio()
    {
        $this->showConfirmModal = true;
        $this->confirmAction = 'gerarRelatorioAssinaturas';
        $this->actionParams = [];
        $this->confirmTitle = 'Gerar Relatório de Assinaturas';
        $this->confirmMessage = 'Deseja gerar um relatório completo de todas as assinaturas? O arquivo será enviado por email.';
        $this->confirmButtonText = 'Sim, Gerar';
        $this->confirmButtonClass = 'btn-info';
    }

    public function confirmarSincronizarPagamentos()
    {
        $this->showConfirmModal = true;
        $this->confirmAction = 'sincronizarPagamentos';
        $this->actionParams = [];
        $this->confirmTitle = 'Sincronizar Pagamentos';
        $this->confirmMessage = 'Esta ação irá sincronizar todos os pagamentos com o gateway. Pode levar alguns minutos. Continuar?';
        $this->confirmButtonText = 'Sim, Sincronizar';
        $this->confirmButtonClass = 'btn-primary';
    }

    public function executarAcaoConfirmada()
    {
        $this->showConfirmModal = false;

        switch ($this->confirmAction) {
            case 'renovarAssinatura':
                $this->renovarAssinatura($this->actionParams['id']);
                break;
            case 'marcarFalhaComoResolvida':
                $this->marcarFalhaComoResolvida($this->actionParams['id']);
                break;
            case 'enviarLembreteVencimento':
                $this->enviarLembreteVencimento($this->actionParams['id']);
                break;
            case 'suspenderAssinatura':
                $this->suspenderAssinatura($this->actionParams['id']);
                break;
            case 'gerarRelatorioAssinaturas':
                $this->gerarRelatorioAssinaturas();
                break;
            case 'sincronizarPagamentos':
                $this->sincronizarPagamentos();
                break;
        }

        $this->resetConfirmModal();
    }

    public function cancelarAcao()
    {
        $this->resetConfirmModal();
    }

    private function resetConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->confirmAction = '';
        $this->confirmTitle = '';
        $this->confirmMessage = '';
        $this->confirmButtonText = '';
        $this->confirmButtonClass = '';
        $this->actionParams = [];
    }
}
