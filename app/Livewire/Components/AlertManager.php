<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\Attributes\On;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

class AlertManager extends Component
{
    use WithSweetAlert;

    #[On('abrirModalPlano')]
    public function abrirModalPlano($data)
    {
        $plano = $data['plano'] ?? $data;
        $id = $plano['id'] ?? null;
        $acaoSugerida = $plano['acao'] ?? 'nova_assinatura';

        // Determinar opções disponíveis
        $opcoesDisponiveis = [];
        $isPacoteAtual = isset($plano['pacote_atual']) && $plano['pacote_atual']['id'] === $plano['id'];

        // Sempre permitir nova assinatura, exceto se for o pacote atual
        if (!$isPacoteAtual) {
            $opcoesDisponiveis[] = [
                'value' => 'nova_assinatura',
                'label' => 'Nova Assinatura',
                'description' => 'Assinar este plano pela primeira vez'
            ];
        }

        // Verificar se pode renovar
        if ($acaoSugerida === 'renovar' || $acaoSugerida === 'upgrade') {
            $opcoesDisponiveis[] = [
                'value' => 'renovar',
                'label' => 'Renovar Assinatura',
                'description' => $isPacoteAtual ? 'Renovar sua assinatura atual' : 'Renovar com este plano'
            ];
        }

        // Verificar se pode fazer upgrade
        if ($acaoSugerida === 'upgrade' && !$isPacoteAtual) {
            $opcoesDisponiveis[] = [
                'value' => 'upgrade',
                'label' => 'Fazer Upgrade',
                'description' => 'Atualizar para um plano superior'
            ];
        }

        // Se é o pacote atual, só permitir renovar
        if ($isPacoteAtual) {
            $opcoesDisponiveis = [[
                'value' => 'renovar',
                'label' => 'Renovar Assinatura',
                'description' => 'Renovar sua assinatura atual'
            ]];
        }

        // Se só há uma opção, usar modal simples
        if (count($opcoesDisponiveis) === 1) {
            $unicaOpcao = $opcoesDisponiveis[0];
            $this->modalSimples($plano, $unicaOpcao);
        } else {
            $this->modalComOpcoes($plano, $opcoesDisponiveis, $acaoSugerida);
        }
    }

    private function modalSimples($plano, $opcao)
    {
        $precoVitalicio = isset($plano['preco_vitalicio']) ? '<p class="text-success small"><strong>Ou ' . htmlspecialchars($plano['preco_vitalicio']) . ' vitalício</strong></p>' : '';

        $this->swalFire([
            'title' => 'Confirmar ' . $opcao['label'],
            'html' => '
                <div class="text-start">
                    <p><strong>Plano:</strong> ' . htmlspecialchars($plano['nome']) . '</p>
                    <p><strong>Valor:</strong> ' . htmlspecialchars($plano['preco']) . '/mês</p>
                    <p class="text-muted small">' . htmlspecialchars($plano['descricao']) . '</p>
                    ' . $precoVitalicio . '
                    <p class="text-info small mt-2"><em>' . htmlspecialchars($opcao['description']) . '</em></p>
                </div>
            ',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Sim, confirmar',
            'cancelButtonText' => 'Cancelar',
            'reverseButtons' => true,
            'customClass' => [
                'popup' => 'swal-equal-buttons',
                'confirmButton' => 'btn bg-info text-light fw-bold swal-btn',
                'cancelButton' => 'btn btn-secondary fw-bold swal-btn'
            ],
            'buttonsStyling' => false,
            'backdrop' => true,
            'showClass' => ['popup' => 'animate__animated animate__fadeInDown animate__faster'],
            'hideClass' => ['popup' => 'animate__animated animate__fadeOutUp animate__faster'],
            'didRender' => '() => {
                const confirmBtn = document.querySelector(".swal2-confirm");
                const cancelBtn = document.querySelector(".swal2-cancel");

                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                const newCancelBtn = cancelBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                newConfirmBtn.addEventListener("click", () => {
                    Swal.close();
                    Swal.fire({
                        title: "Processando...",
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-spinner fa-spin fa-4x text-info"></i>
                                </div>
                                <p class="h4 fw-semibold text-body-emphasis mb-3">Processando sua assinatura</p>
                                <p class="text-muted">Aguarde enquanto redirecionamos você para o pagamento...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: true,
                        customClass: { popup: "swal-wide-modal" }
                    });

                    Livewire.dispatch("confirmarPacote", { id: ' . $plano['id'] . ', acao: "' . $opcao['value'] . '" });
                });

                newCancelBtn.addEventListener("click", () => {
                    Swal.close();
                });
            }'
        ]);
    }

    private function modalComOpcoes($plano, $opcoes, $acaoSugerida)
    {
        $precoVitalicio = isset($plano['preco_vitalicio']) ? '<p class="text-success small"><strong>Ou ' . htmlspecialchars($plano['preco_vitalicio']) . ' vitalício</strong></p>' : '';

        $opcoesHtml = '';
        foreach ($opcoes as $opcao) {
            $checked = $opcao['value'] === $acaoSugerida ? 'checked' : '';
            $opcoesHtml .= '
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="tipoAssinatura" value="' . $opcao['value'] . '" id="opcao' . $opcao['value'] . '" ' . $checked . '>
                    <label class="form-check-label" for="opcao' . $opcao['value'] . '">
                        <strong>' . $opcao['label'] . '</strong>
                        <br><small class="text-muted">' . $opcao['description'] . '</small>
                    </label>
                </div>
            ';
        }

        $this->swalFire([
            'title' => 'Escolher Tipo de Assinatura',
            'html' => '
                <div class="text-start mb-4">
                    <p><strong>Plano:</strong> ' . htmlspecialchars($plano['nome']) . '</p>
                    <p><strong>Valor:</strong> ' . htmlspecialchars($plano['preco']) . '/mês</p>
                    <p class="text-muted small">' . htmlspecialchars($plano['descricao']) . '</p>
                    ' . $precoVitalicio . '
                </div>
                <div class="text-start">
                    <p class="mb-3"><strong>Escolha o tipo de assinatura:</strong></p>
                    ' . $opcoesHtml . '
                </div>
            ',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Continuar',
            'cancelButtonText' => 'Cancelar',
            'reverseButtons' => true,
            'customClass' => [
                'popup' => 'swal-equal-buttons',
                'confirmButton' => 'btn bg-info text-light fw-bold swal-btn',
                'cancelButton' => 'btn btn-secondary fw-bold swal-btn'
            ],
            'buttonsStyling' => false,
            'backdrop' => true,
            'showClass' => ['popup' => 'animate__animated animate__fadeInDown animate__faster'],
            'hideClass' => ['popup' => 'animate__animated animate__fadeOutUp animate__faster'],
            'didRender' => '() => {
                const confirmBtn = document.querySelector(".swal2-confirm");
                const cancelBtn = document.querySelector(".swal2-cancel");

                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                const newCancelBtn = cancelBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                newConfirmBtn.addEventListener("click", () => {
                    const selectedOption = document.querySelector("input[name=\"tipoAssinatura\"]:checked");
                    if (!selectedOption) {
                        Swal.showValidationMessage("Por favor, selecione um tipo de assinatura");
                        return;
                    }

                    Swal.close();
                    Swal.fire({
                        title: "Processando...",
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-spinner fa-spin fa-4x text-info"></i>
                                </div>
                                <p class="h4 fw-semibold text-body-emphasis mb-3">Processando sua assinatura</p>
                                <p class="text-muted">Aguarde enquanto redirecionamos você para o pagamento...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: true,
                        customClass: { popup: "swal-wide-modal" }
                    });

                    Livewire.dispatch("confirmarPacote", { id: ' . $plano['id'] . ', acao: selectedOption.value });
                });

                newCancelBtn.addEventListener("click", () => {
                    Swal.close();
                });
            }'
        ]);
    }



    #[On('pagamento-sucesso')]
    public function pagamentoSucesso($data)
    {
        $pagamento = $data['pagamento'] ?? $data;

        $this->swalFire([
            'title' => '<span class="fw-bold text-dark">Transação Concluída</span>',
            'icon' => 'success',
            'html' => '
                <div class="text-center">
                    <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-success-subtle mb-3 border border-success border-2">
                        <i class="fas fa-handshake fa-2x text-success"></i>
                    </div>

                    <p class="h5 fw-semibold text-body-emphasis mb-2">Seu comprovativo foi recebido!</p>
                    <p class="text-muted small mb-4">A sua solicitação de pagamento está em análise e será processada em breve.</p>

                    <div class="row g-2 justify-content-center text-center">
                        <div class="col-6">
                            <div class="border rounded-3 p-3 bg-light shadow-sm detail-card">
                                <h6 class="text-info mb-1 small fw-bold text-uppercase">Referência</h6>
                                <strong class="fs-6 text-dark">' . htmlspecialchars($pagamento['referencia'] ?? '') . '</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-3 p-3 bg-light shadow-sm detail-card">
                                <h6 class="text-warning mb-1 small fw-bold text-uppercase">Status Atual</h6>
                                <span class="badge bg-warning fs-6 py-1 px-3 fw-bold">' . htmlspecialchars($pagamento['status'] ?? '') . '</span>
                            </div>
                        </div>
                    </div>

                    <div class="alert mt-4 p-3 rounded-3 text-start border-info-subtle bg-info text-light-subtle">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-hourglass-half me-3 text-info fs-4"></i>
                            <div>
                                <strong class="text-info">Próximo Passo:</strong>
                                <div class="small text-dark">
                                    Sua análise é feita em até <strong class="fw-bold">24 horas úteis</strong>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ',
            'customClass' => [
                'popup' => 'swal2-responsive-modal shadow-lg',
                'confirmButton' => 'btn btn-success btn-lg px-4 fw-bold'
            ],
            'showConfirmButton' => true,
            'confirmButtonText' => '<i class="fas fa-home me-2"></i> Ir para o Dashboard',
            'buttonsStyling' => false,
            'backdrop' => true,
            'allowOutsideClick' => false,
            'allowEscapeKey' => false,
            'didOpen' => '() => {
                const confirmBtn = document.querySelector(".swal2-confirm");
                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                newConfirmBtn.addEventListener("click", () => {
                    window.location.href = "/e-commerce/payments-assignatures";
                });
            }'
        ]);
    }

    #[On('abrirModalLogin')]
    public function abrirModalLogin()
    {
        $this->swalFire([
            'title' => '<i class="fas fa-exclamation-triangle text-warning me-2"></i>Login Necessário',
            'html' => '
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-user-lock fa-4x text-info"></i>
                    </div>
                    <p class="h5 fw-semibold text-body-emphasis mb-3">Você precisa estar logado para continuar</p>
                    <p class="text-muted mb-4">Para assinar um plano e acessar todas as funcionalidades, faça login em sua conta. Ou peça um teste.</p>
                </div>
            ',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => '<i class="fas fa-sign-in-alt me-2"></i>Fazer Login',
            'cancelButtonText' => 'Cancelar',
            'reverseButtons' => true,
            'customClass' => [
                'popup' => 'swal-equal-buttons',
                'confirmButton' => 'btn bg-info text-light fw-bold swal-btn',
                'cancelButton' => 'btn btn-secondary fw-bold swal-btn'
            ],
            'buttonsStyling' => false,
            'backdrop' => true,
            'showClass' => ['popup' => 'animate__animated animate__fadeInDown animate__faster'],
            'hideClass' => ['popup' => 'animate__animated animate__fadeOutUp animate__faster'],
            'didOpen' => '() => {
                const confirmBtn = document.querySelector(".swal2-confirm");
                const cancelBtn = document.querySelector(".swal2-cancel");

                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                const newCancelBtn = cancelBtn.cloneNode(true);
                cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

                newConfirmBtn.addEventListener("click", () => {
                    window.location.href = "/login";
                });

                newCancelBtn.addEventListener("click", () => {
                    Swal.close();
                });
            }'
        ]);
    }

    #[On('solicitacao-pendente')]
    public function solicitacaoPendente()
    {
        $this->swalSuccess([
            'title' => 'Pedido Pendente',
            'text' => 'Você já possui uma solicitação de período de teste pendente de aprovação. Nossa equipe irá analisar e você receberá um email com a decisão.',
            'icon' => 'warning'
        ]);
    }

    #[On('limite-atingido')]
    public function limiteAtingido()
    {
        $this->swalError([
            'title' => 'Limite Atingido',
            'text' => 'Você já utilizou o limite máximo de 2 períodos de teste. Cada usuário tem direito a apenas 2 solicitações aprovadas. Entre em contacto com o suporte técnico.'
        ]);
    }

    public function render()
    {
        return view('components.alert-manager');
    }
}
