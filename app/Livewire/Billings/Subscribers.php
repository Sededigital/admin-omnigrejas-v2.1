<?php

namespace App\Livewire\Billings;

use App\Mail\PagamentoAprovadoMail;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaLog;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\IgrejaAssinada;
use App\Models\Billings\PagamentoAssinaturaIgreja;
use App\Models\Billings\Trial\TrialUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Pedidos de assinaturas| Aprovação e Processamento')]
#[Layout('components.layouts.app')]
class Subscribers extends Component
{
    use WithSweetAlert;


    public $pagamentosPendentes;
    public $pagamentoSelecionado;
    public $observacoesConfirmacao = '';
    public $observacoesRejeicao = '';

    public function mount()
    {
        $this->carregarPagamentosPendentes();
    }

    public function carregarPagamentosPendentes()
    {
        $this->pagamentosPendentes = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
            ->pendentes()
            ->orderBy('data_pagamento', 'desc')
            ->get();
    }

    public function atualizarPagamentos()
    {
        $this->carregarPagamentosPendentes();

        $this->swalFire([
            'title' => '<span class="fw-bold text-dark">Atualizado</span>',
            'icon' => 'success',
            'html' => '
                <div class="text-center">

                    <p class="text-muted small mb-0">Os pagamentos pendentes foram recarregados com sucesso.</p>
                </div>
            ',
            'customClass' => [
                'popup' => 'swal2-responsive-modal shadow-lg',
                'confirmButton' => 'btn btn-success btn-lg px-4 fw-bold'
            ],
            'showConfirmButton' => true,
            'confirmButtonText' => '<i class="fas fa-check me-2"></i> Ok',
            'buttonsStyling' => false,
            'backdrop' => true,
            'timer' => 3000,
            'timerProgressBar' => true
        ]);
    }

    public function selecionarPagamento($pagamentoId)
    {
        $this->pagamentoSelecionado = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
            ->where('id', $pagamentoId)
            ->first();

        if (!$this->pagamentoSelecionado) {
            return;
        }

        // Buscar informações da assinatura atual/trial da igreja
        $assinaturaAtual = $this->pagamentoSelecionado->igreja->assinaturaAtual;

        // Verificar se algum usuário da igreja tem trial ativo
        $trial = null;
        $membros = $this->pagamentoSelecionado->igreja->membros()->with('user.trial')->get();

        foreach ($membros as $membro) {
            if ($membro->user && $membro->user->trial && $membro->user->trial->isAtivo()) {
                $trial = $membro->user->trial;
                break;
            }
        }

        $assinaturaInfo = null;
        if ($assinaturaAtual && $assinaturaAtual->estaAtiva()) {
            $assinaturaInfo = [
                'tipo' => 'assinatura',
                'pacote' => $assinaturaAtual->pacote->nome ?? 'N/A',
                'status' => $assinaturaAtual->status,
                'data_inicio' => $assinaturaAtual->data_inicio?->format('d/m/Y'),
                'data_fim' => $assinaturaAtual->data_fim?->format('d/m/Y') ?? 'Vitalício',
                'dias_restantes' => $assinaturaAtual->data_fim ? now()->diffInDays($assinaturaAtual->data_fim, false) : null,
            ];
        } elseif ($trial && $trial->isAtivo()) {
            $assinaturaInfo = [
                'tipo' => 'trial',
                'pacote' => 'Trial Gratuito',
                'status' => $trial->status,
                'data_inicio' => $trial->data_inicio?->format('d/m/Y'),
                'data_fim' => $trial->data_fim?->format('d/m/Y'),
                'dias_restantes' => $trial->diasRestantes(),
            ];
        }

        // Preparar HTML da assinatura
        $assinaturaHtml = '';
        if ($assinaturaInfo) {
            $tipo = $assinaturaInfo['tipo'] === 'trial' ? 'TRIAL ATIVO' : 'ASSINATURA ATIVA';
            $iconeCor = $assinaturaInfo['tipo'] === 'trial' ? 'text-warning' : 'text-info';
            $iconeInicioCor = $assinaturaInfo['tipo'] === 'trial' ? 'text-warning' : 'text-success';
            $iconeFimCor = $assinaturaInfo['tipo'] === 'trial' ? 'text-warning' : 'text-danger';
            $iconeDiasCor = ($assinaturaInfo['dias_restantes'] ?? 0) > 7 ? 'text-success' : (($assinaturaInfo['dias_restantes'] ?? 0) > 0 ? 'text-warning' : 'text-danger');

            $assinaturaHtml = '
                <div id="assinatura-info">
                    <hr class="my-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        <small class="text-muted fw-semibold">' . $tipo . '</small>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-box ' . $iconeCor . ' me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Pacote Atual</small>
                                    <strong class="text-dark">' . htmlspecialchars($assinaturaInfo['pacote']) . '</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-check ' . $iconeInicioCor . ' me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Data Início</small>
                                    <strong class="text-dark">' . htmlspecialchars($assinaturaInfo['data_inicio']) . '</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-times ' . $iconeFimCor . ' me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Data Fim</small>
                                    <strong class="text-dark">' . htmlspecialchars($assinaturaInfo['data_fim']) . '</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock ' . $iconeDiasCor . ' me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Dias Restantes</small>
                                    <strong class="text-dark">' . ($assinaturaInfo['dias_restantes'] !== null ? $assinaturaInfo['dias_restantes'] . ' dias' : 'Vitalício') . '</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';
        }

        $this->swalFire([
            'title' => 'Confirmar Aprovação de Pagamento',
            'html' => '
                <div class="text-start">
                    <div class="bg-light rounded p-2 mb-2 border">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-church text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Igreja</small>
                                        <strong class="text-dark">' . htmlspecialchars($this->pagamentoSelecionado->igreja->nome ?? 'N/A') . '</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Pacote</small>
                                        <strong class="text-dark">' . htmlspecialchars($this->pagamentoSelecionado->pacote_nome ?? 'N/A') . '</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Valor</small>
                                        <strong class="text-success">' . $this->pagamentoSelecionado->getValorFormatado() . '</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar text-warning me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Data do Pagamento</small>
                                        <strong class="text-dark">' . ($this->pagamentoSelecionado->data_pagamento ? $this->pagamentoSelecionado->data_pagamento->format('d/m/Y') : 'N/A') . '</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ' . $assinaturaHtml . '
                    </div>

                    <div class="mb-2">
                        <label for="observacoes" class="form-label fw-semibold">Observações (opcional)</label>
                        <textarea id="observacoes" class="form-control" rows="2"
                                wire:model.live="observacoesConfirmacao"
                                placeholder="Digite observações sobre a aprovação..."></textarea>
                    </div>
                </div>
            ',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonText' => 'Confirmar Aprovação',
            'cancelButtonText' => 'Cancelar',
            'reverseButtons' => true,
            'buttonsStyling' => false,
            'customClass' => [
                'popup' => 'swal-equal-buttons',
                'confirmButton' => 'btn btn-success fw-bold swal-btn m-2',
                'cancelButton' => 'btn btn-secondary fw-bold swal-btn'
            ],
            'width' => '900px',
            'heightAuto' => false,
            'backdrop' => true,
            'showClass' => [
                'popup' => 'animate__animated animate__fadeInDown animate__faster'
            ],
            'hideClass' => [
                'popup' => 'animate__animated animate__fadeOutUp animate__faster'
            ],
            'preConfirm' => '() => {
                Livewire.dispatch("iniciar-processamento-confirmado");
                return false;
            }',
            'allowOutsideClick' => false,
            'allowEscapeKey' => false
        ]);
    }

    public function mostrarComprovativoModal($pagamentoId)
    {
        try {
            $pagamento = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
                ->where('id', $pagamentoId)
                ->first();

            if (!$pagamento || !$pagamento->temComprovativo()) {
                $this->swalError([
                    'title' => 'Erro',
                    'text' => 'Comprovativo não encontrado.'
                ]);
                return;
            }

            // Obter URL do comprovativo
            if (filter_var($pagamento->comprovativo_url, FILTER_VALIDATE_URL)) {
                $urlComprovativo = $pagamento->comprovativo_url;
            } else {
                $urlComprovativo = \App\Helpers\SupabaseHelper::obterUrl($pagamento->comprovativo_url);
            }

            $tipoArquivo = strtolower($pagamento->comprovativo_tipo);
            $tiposImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $isImagem = collect($tiposImagem)->some(fn($ext) => str_contains($tipoArquivo, $ext));

            if ($isImagem) {
                // Mostrar imagem
                $this->swalFire([
                    'title' => 'Comprovativo de Pagamento',
                    'html' => '
                        <div class="text-center">
                            <div class="mb-3">
                                <img src="' . htmlspecialchars($urlComprovativo) . '" class="img-fluid rounded shadow"
                                    style="max-width: 100%; max-height: 400px; object-fit: contain;"
                                    alt="Comprovativo">
                            </div>
                            <div class="row g-2 text-start">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Tamanho</small>
                                    <strong class="text-dark">' . htmlspecialchars($pagamento->getComprovativoTamanhoFormatado()) . '</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Igreja</small>
                                    <strong class="text-dark">' . htmlspecialchars($pagamento->igreja->nome) . '</strong>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Valor</small>
                                    <strong class="text-success">' . $pagamento->getValorFormatado() . '</strong>
                                </div>
                            </div>
                        </div>
                    ',
                    'showCancelButton' => true,
                    'confirmButtonText' => '<i class="fas fa-download"></i> Baixar',
                    'cancelButtonText' => 'Fechar',
                    'customClass' => [
                        'popup' => 'swal-wide-modal',
                        'confirmButton' => 'btn bg-info text-light fw-bold m-2',
                        'cancelButton' => 'btn btn-secondary fw-bold m-2'
                    ],
                    'buttonsStyling' => false,
                    'width' => '800px',
                    'heightAuto' => false,
                    'didOpen' => '() => {
                        document.querySelector(".swal2-confirm").addEventListener("click", () => {
                            fetch("' . htmlspecialchars($urlComprovativo) . '")
                                .then(r => r.blob())
                                .then(blob => {
                                    const url = URL.createObjectURL(blob);
                                    const a = document.createElement("a");
                                    a.href = url;
                                    a.download = "' . htmlspecialchars($pagamento->comprovativo_nome) . '";
                                    a.click();
                                    URL.revokeObjectURL(url);
                                })
                                .catch(() => window.open("' . htmlspecialchars($urlComprovativo) . '", "_blank"));
                        });
                    }'
                ]);
            } else {
                // Download direto para PDF/outros
                $this->swalFire([
                    'title' => 'Preparando Download...',
                    'html' => '
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="fas fa-spinner fa-spin fa-4x text-info"></i>
                            </div>
                            <p class="h5 fw-semibold text-body-emphasis mb-3">Preparando comprovativo para download</p>
                            <p class="text-muted">Arquivo: ' . htmlspecialchars($pagamento->comprovativo_nome) . '</p>
                            <p class="text-muted">Tamanho: ' . htmlspecialchars($pagamento->getComprovativoTamanhoFormatado()) . '</p>
                            <div class="mt-3">
                                <small class="text-muted">O download começará automaticamente</small>
                            </div>
                        </div>
                    ',
                    'showConfirmButton' => false,
                    'showCancelButton' => true,
                    'cancelButtonText' => 'Cancelar',
                    'allowOutsideClick' => false,
                    'allowEscapeKey' => false,
                    'customClass' => [
                        'popup' => 'swal-wide-modal'
                    ],
                    'didOpen' => '() => {
                        setTimeout(() => {
                            fetch("' . htmlspecialchars($urlComprovativo) . '")
                                .then(r => r.blob())
                                .then(blob => {
                                    const url = URL.createObjectURL(blob);
                                    const a = document.createElement("a");
                                    a.href = url;
                                    a.download = "' . htmlspecialchars($pagamento->comprovativo_nome) . '";
                                    a.click();
                                    URL.revokeObjectURL(url);
                                    setTimeout(() => Swal.close(), 1000);
                                })
                                .catch(() => {
                                    window.open("' . htmlspecialchars($urlComprovativo) . '", "_blank");
                                    setTimeout(() => Swal.close(), 1000);
                                });
                        }, 2000);
                    }'
                ]);
            }

        } catch (\Exception $e) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Erro ao visualizar comprovativo: ' . $e->getMessage()
            ]);
        }
    }

    public function mostrarSucessoAprovacao($mensagem = '')
    {
        $this->swalFire([
            'title' => '<span class="fw-bold text-dark">Aprovação Concluída</span>',
            'icon' => 'success',
            'html' => '
                <div class="text-center">
                    <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-success-subtle mb-3 border border-success border-2">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <p class="h5 fw-semibold text-body-emphasis mb-2">Pagamento aprovado com sucesso!</p>
                    <p class="text-muted small mb-4">' . htmlspecialchars($mensagem) . '</p>
                </div>
            ',
            'customClass' => [
                'popup' => 'swal2-responsive-modal shadow-lg',
                'confirmButton' => 'btn btn-success btn-lg px-4 fw-bold'
            ],
            'showConfirmButton' => true,
            'confirmButtonText' => '<i class="fas fa-home me-2"></i> Continuar',
            'buttonsStyling' => false,
            'backdrop' => true,
            'allowOutsideClick' => false,
            'allowEscapeKey' => false
        ]);
    }

    public function mostrarModalRejeicao($pagamentoId)
    {

        $pagamento = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
            ->where('id', $pagamentoId)
            ->first();

        if (!$pagamento) {
            return;
        }

        $this->swalFire([
            'title' => 'Confirmar Rejeição de Pagamento',
            'html' => '
                <div class="text-start">
                    <div class="bg-light rounded p-2 mb-2 border">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-church text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Igreja</small>
                                        <strong class="text-dark">' . htmlspecialchars($pagamento->igreja->nome ?? 'N/A') . '</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-box text-info me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Pacote</small>
                                        <strong class="text-dark">' . htmlspecialchars($pagamento->pacote_nome ?? 'N/A') . '</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Valor</small>
                                        <strong class="text-success">' . $pagamento->getValorFormatado() . '</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar text-warning me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Data do Pagamento</small>
                                        <strong class="text-dark">' . ($pagamento->data_pagamento ? $pagamento->data_pagamento->format('d/m/Y') : 'N/A') . '</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-danger border border-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Esta ação rejeitará permanentemente o pagamento e a igreja precisará fazer um novo pedido.
                    </div>

                    <div class="mb-2">
                        <label for="observacoes_rejeicao" class="form-label fw-semibold text-danger">
                            <i class="fas fa-comment-alt me-1"></i>
                            Motivo da Rejeição <span class="text-danger">*</span>
                        </label>
                        <textarea id="observacoes_rejeicao" class="form-control border-danger"
                                wire:model.live="observacoesRejeicao"
                                placeholder="Explique o motivo da rejeição do pagamento..."
                                rows="3"></textarea>
                        <div class="form-text text-muted">
                            Este motivo será registrado no histórico e poderá ser visto pela igreja.
                        </div>
                    </div>
                </div>
            ',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => 'Confirmar Rejeição',
            'cancelButtonText' => 'Cancelar',
            'reverseButtons' => true,
            'buttonsStyling' => false,
            'customClass' => [
                'popup' => 'swal-equal-buttons',
                'confirmButton' => 'btn btn-danger fw-bold swal-btn m-2',
                'cancelButton' => 'btn btn-secondary fw-bold swal-btn'
            ],
            'width' => '900px',
            'heightAuto' => false,
            'backdrop' => true,
            'showClass' => [
                'popup' => 'animate__animated animate__fadeInDown animate__faster'
            ],
            'hideClass' => [
                'popup' => 'animate__animated animate__fadeOutUp animate__faster'
            ],
            'preConfirm' => '() => {
                Livewire.dispatch("rejeitar-pagamento-confirmado");
                return false;
            }',
            'allowOutsideClick' => false,
            'allowEscapeKey' => false
        ]);
    }

   #[On('rejeitar-pagamento-confirmado')]
    public function rejeitarPagamento()
    {
        
        $this->validate([
            'observacoesRejeicao' => 'required|string|max:500'
        ], [
            'observacoesRejeicao.required' => 'O motivo da rejeição é obrigatório.'
        ]);

        // Validar se o pagamento ainda pode ser rejeitado
        if (!$this->pagamentoSelecionado || !$this->pagamentoSelecionado->isPendente()) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Este pagamento não pode ser rejeitado.'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Rejeitar o pagamento
            $this->pagamentoSelecionado->rejeitar(Auth::user(), $this->observacoesRejeicao);

            // Criar log
            AssinaturaLog::create([
                'igreja_id' => $this->pagamentoSelecionado->igreja_id,
                'pacote_id' => $this->pagamentoSelecionado->pacote_id,
                'acao' => 'rejeitado',
                'descricao' => 'Pagamento rejeitado: ' . $this->observacoesRejeicao,
                'usuario_id' => Auth::id(),
                'data_acao' => now(),
                'detalhes' => [
                    'pagamento_id' => $this->pagamentoSelecionado->id,
                    'valor' => $this->pagamentoSelecionado->valor,
                    'motivo_rejeicao' => $this->observacoesRejeicao
                ]
            ]);

            DB::commit();

            $this->fecharModal();
            $this->carregarPagamentosPendentes();

            $this->swalSuccess([
                'title' => 'Sucesso',
                'text' => 'Pagamento rejeitado com sucesso.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Erro ao rejeitar pagamento: ' . $e->getMessage()
            ]);
        }
    }

    #[On('iniciar-processamento-confirmado')]
    public function iniciarProcessamento()
    {


        $this->validate([
            'observacoesConfirmacao' => 'nullable|string|max:500'
        ]);

        // Validar se o pagamento ainda pode ser aprovado
        if (!$this->pagamentoSelecionado || !$this->pagamentoSelecionado->isPendente()) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Este pagamento não pode ser aprovado.'
            ]);
            return;
        }



        // Mostrar modal de processamento
        $this->swalFire([
            'title' => 'Processando Aprovação...',
            'html' => '
                <div class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-spinner fa-spin fa-4x text-info"></i>
                    </div>
                    <p class="h5 fw-semibold text-body-emphasis mb-3">Processando aprovação do pagamento</p>
                    <p class="text-muted">Aguarde enquanto criamos a assinatura...</p>
                    <div class="mt-3">
                        <small class="text-muted">Processamento será concluído em alguns segundos</small>
                    </div>
                </div>
            ',
            'showConfirmButton' => false,
            'showCancelButton' => false,
            'allowOutsideClick' => false,
            'allowEscapeKey' => false,
            'backdrop' => true,
            'timer' => 10000,
            'timerProgressBar' => true,
            'didOpen' => '() => {
                setTimeout(() => {
                    Livewire.dispatch("confirmar-pagamento");
                }, 10000);
            }'
        ]);
    }

    #[On('confirmar-pagamento')]
    public function confirmarPagamento()
    {
        try {

            DB::beginTransaction();

            $this->processarAprovacao();

            DB::commit();


            $this->carregarPagamentosPendentes();

              $this->swalSuccess([
                'title' => 'Sucesso',
                'text' => 'Pagamento aprovado com sucesso! Assinatura ativada.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ]);
        }


    }

    private function processarAprovacao()
    {
        $pagamento = $this->pagamentoSelecionado;
        $igreja = $pagamento->igreja;

        // Determinar cenário
        $cenario = $this->determinarCenarioAssinatura($igreja);

        // Calcular datas
        $datas = $this->calcularDatasAssinatura($pagamento, $cenario);

        switch ($cenario) {
            case 'nova':
                return $this->processarNovaAssinatura($pagamento, $datas);
            case 'upgrade':
                return $this->processarUpgradeAssinatura($pagamento, $datas);
            case 'renovacao':
                return $this->processarRenovacaoAssinatura($pagamento, $datas);
            case 'trial':
                return $this->processarConversaoTrial($pagamento, $datas);
        }
    }

    private function calcularDatasAssinatura($pagamento, $cenario)
    {
        $assinaturaAtual = $pagamento->igreja->assinaturaAtual;

        // Data de início base
        $dataInicio = now()->addDays(2);

        // Para renovações/upgrade: usar data fim da assinatura atual + 1 dia
        if (in_array($cenario, ['upgrade', 'renovacao']) && $assinaturaAtual && $assinaturaAtual->estaAtiva()) {
            $dataInicio = $assinaturaAtual->data_fim->copy()->addDays(1);
        }



        if ($pagamento->is_vitalicio) {
            Log::info('DEBUG: Assinatura vitalícia detectada');
            return [
                'data_inicio' => $dataInicio,
                'data_fim' => null,
                'duracao_meses' => null,
                'vitalicio' => true
            ];
        }

        // Garantir que duracao_meses é um inteiro válido
        $duracaoMeses = (int) $pagamento->duracao_meses;
        if ($duracaoMeses <= 0) {
            $duracaoMeses = 1; // fallback para 1 mês se valor inválido
        }

        $dataFim = $dataInicio->copy()->addMonths($duracaoMeses);

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'duracao_meses' => $duracaoMeses,
            'vitalicio' => false
        ];
    }

    private function determinarCenarioAssinatura($igreja)
    {
        $assinaturaAtual = $igreja->assinaturaAtual;

        // Primeiro verificar se há trial ativo na igreja
        $trialAtivo = TrialUser::where('igreja_id', $igreja->id)
            ->where('status', 'ativo')
            ->where('data_fim', '>=', now())
            ->first();

        if ($trialAtivo) {
            return 'trial';
        }

        if (!$assinaturaAtual) {
            return 'nova';
        }

        if ($assinaturaAtual->estaAtiva()) {
            return 'upgrade'; // ou renovação dependendo da lógica
        }

        // Se assinatura existe mas não está ativa (expirada)
        return 'renovacao';
    }

    private function processarNovaAssinatura($pagamento, $datas)
    {
        $igreja = $pagamento->igreja;


        // 1. Criar AssinaturaAtual - Usar DB::insert para evitar triggers
        $dadosAssinaturaAtual = [
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'data_inicio' => $datas['data_inicio']->format('Y-m-d'),
            'data_fim' => $datas['data_fim'] ? $datas['data_fim']->format('Y-m-d') : null,
            'status' => 'Ativo',
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
            'created_at' => now(),
            'updated_at' => now(),
        ];


        // Usar DB::insert para evitar triggers automáticos
        DB::table('assinatura_atual')->insert($dadosAssinaturaAtual);

        // Buscar o registro criado
        AssinaturaAtual::where('igreja_id', $igreja->id)->first();


        // 2. Criar AssinaturaHistorico
        $dadosAssinaturaHistorico = [
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'data_inicio' => $datas['data_inicio']->format('Y-m-d'),
            'data_fim' => $datas['data_fim'] ? $datas['data_fim']->format('Y-m-d') : null,
            'valor' => $pagamento->valor,
            'status' => 'Ativo',
            'forma_pagamento' => $pagamento->metodo_pagamento,
            'transacao_id' => $pagamento->referencia,
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $assinaturaHistorico = AssinaturaHistorico::create($dadosAssinaturaHistorico);



        // 3. Criar IgrejaAssinada
         IgrejaAssinada::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'ativo' => true,
            'data_adesao' => $datas['data_inicio'],
        ]);

        // 4. Criar AssinaturaPagamento
        AssinaturaPagamento::create([
            'assinatura_id' => $assinaturaHistorico->id,
            'igreja_id' => $igreja->id,
            'valor' => $pagamento->valor,
            'metodo_pagamento' => $pagamento->metodo_pagamento,
            'referencia' => $pagamento->referencia,
            'status' => 'confirmado',
            'data_pagamento' => $pagamento->data_pagamento,
        ]);


        // 5. Atualizar status do pagamento original
        $pagamento->confirmar(Auth::user(), $this->observacoesConfirmacao);


        // 6. Criar logs
        AssinaturaLog::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'acao' => 'criado',
            'descricao' => 'Assinatura criada via aprovação de pagamento',
            'usuario_id' => Auth::id(),
            'data_acao' => now(),
            'detalhes' => [
                'pagamento_id' => $pagamento->id,
                'valor' => $pagamento->valor,
                'metodo' => $pagamento->metodo_pagamento
            ]
        ]);


        // 7. Enviar notificação
        $this->enviarNotificacaoAprovacao($igreja, $pagamento, $datas);

        return true;
    }

    private function processarUpgradeAssinatura($pagamento, $datas)
    {
        $igreja = $pagamento->igreja;
        $assinaturaAtual = $igreja->assinaturaAtual;

        // Atualizar assinatura atual
        $assinaturaAtual->update([
            'pacote_id' => $pagamento->pacote_id,
            'data_fim' => $datas['data_fim'],
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
        ]);

        // Criar histórico
        $assinaturaHistorico = AssinaturaHistorico::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'data_inicio' => $datas['data_inicio'],
            'data_fim' => $datas['data_fim'] ? $datas['data_fim'] : null,
            'valor' => $pagamento->valor,
            'status' => 'Ativo',
            'forma_pagamento' => $pagamento->metodo_pagamento,
            'transacao_id' => $pagamento->referencia,
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
        ]);

        // Atualizar IgrejaAssinada
        $igreja->igrejaAssinada->update([
            'pacote_id' => $pagamento->pacote_id,
        ]);

        // Criar pagamento
        AssinaturaPagamento::create([
            'assinatura_id' => $assinaturaHistorico->id,
            'igreja_id' => $igreja->id,
            'valor' => $pagamento->valor,
            'metodo_pagamento' => $pagamento->metodo_pagamento,
            'referencia' => $pagamento->referencia,
            'status' => 'confirmado',
            'data_pagamento' => $pagamento->data_pagamento,
        ]);

        // Atualizar pagamento original
        $pagamento->confirmar(Auth::user(), $this->observacoesConfirmacao);

        // Log
        AssinaturaLog::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'assinatura_id' => $assinaturaHistorico->id,
            'acao' => 'upgrade',
            'descricao' => 'Assinatura atualizada via aprovação de pagamento',
            'usuario_id' => Auth::id(),
            'data_acao' => now(),
        ]);

        // Notificação
        $this->enviarNotificacaoAprovacao($igreja, $pagamento, $datas);

        return true;
    }

    private function processarRenovacaoAssinatura($pagamento, $datas)
    {
        // Similar ao upgrade, mas para renovação
        return $this->processarUpgradeAssinatura($pagamento, $datas);
    }

    private function processarConversaoTrial($pagamento, $datas)
    {

        // Processar como nova assinatura
        $this->processarNovaAssinatura($pagamento, $datas);

         // Remover trial do usuário que criou o pagamento (usando created_by)
         $trial = TrialUser::where('user_id', $pagamento->created_by)
         ->where('igreja_id', $pagamento->igreja_id)
         ->where('status', 'ativo')
         ->first();

            if ($trial) {

            $trial->delete();

                // Log da remoção do trial
                AssinaturaLog::create([
                'igreja_id' => $pagamento->igreja_id,
                'pacote_id' => $pagamento->pacote_id,
                'acao' => 'upgrade', // Usando 'upgrade' pois é uma conversão de trial para assinatura paga
                'descricao' => 'Trial convertido para assinatura paga - registro removido',
                'usuario_id' => Auth::id(),
                'data_acao' => now(),
                'detalhes' => [
                    'pagamento_id' => $pagamento->id,
                    'trial_user_id' => $trial->id,
                    'user_id' => $pagamento->created_by,
                    'tipo_conversao' => 'trial_para_assinatura'
                ]
                ]);
            }

        return true;
    }

    private function enviarNotificacaoAprovacao($igreja, $pagamento, $datas)
    {
        // Buscar usuários admin/pastor da igreja
        $usuariosAdmin = $igreja->membros()
            ->whereIn('cargo', ['admin', 'pastor'])
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        foreach ($usuariosAdmin as $usuario) {
            Mail::to($usuario->email)->send(new PagamentoAprovadoMail($pagamento, $datas));
        }
    }

    public function visualizarComprovativo($pagamentoId)
    {
        try {
            $pagamento = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
                ->where('id', $pagamentoId)
                ->first();

            if (!$pagamento || !$pagamento->temComprovativo()) {
                $this->dispatch('show-error', 'Comprovativo não encontrado.');
                return;
            }

            // Obter URL do comprovativo - verificar se já é uma URL completa
            if (filter_var($pagamento->comprovativo_url, FILTER_VALIDATE_URL)) {
                // Já é uma URL completa
                $urlComprovativo = $pagamento->comprovativo_url;

            } else {
                // É apenas o caminho, obter URL via SupabaseHelper
                $urlComprovativo = \App\Helpers\SupabaseHelper::obterUrl($pagamento->comprovativo_url);
            }

            // Determinar tipo de arquivo
            $tipoArquivo = strtolower($pagamento->comprovativo_tipo);

            // Disparar evento para mostrar modal ou fazer download
            $this->dispatch('mostrarComprovativo', [
                'url' => $urlComprovativo,
                'tipo' => $tipoArquivo,
                'nome' => $pagamento->comprovativo_nome,
                'tamanho' => $pagamento->getComprovativoTamanhoFormatado(),
                'igreja' => $pagamento->igreja->nome,
                'pacote' => $pagamento->pacote_nome,
                'valor' => $pagamento->getValorFormatado(),
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Erro ao visualizar comprovativo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('billings.subscribers');
    }
}
