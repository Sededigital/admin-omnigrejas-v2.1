<?php

namespace App\Livewire\Billings\Trial;

use App\Mail\TrialCriadoEmail;
use App\Mail\TrialRejeitadoEmail;
use App\Models\Billings\Trial\TrialRequest;
use App\Services\Trial\TrialService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Gerenciar Solicitações de Trial')]
#[Layout('components.layouts.app')]
class TrialRequests extends Component
{
    use WithPagination, WithoutUrlPagination;
    use WithSweetAlert;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function confirmarAprovacao($requestId)
    {
        $this->swalFire([
            'title' => 'Confirmar Aprovação',
            'text' => 'Tem certeza que deseja aprovar esta solicitação de trial? O usuário receberá acesso imediato ao sistema.',
            'icon' => 'question',
            'showCancelButton' => true,
            'confirmButtonColor' => '#28a745',
            'cancelButtonColor' => '#6c757d',
            'confirmButtonText' => 'Sim, Aprovar',
            'cancelButtonText' => 'Cancelar',
            'input' => 'textarea',
            'inputPlaceholder' => 'Observações (opcional)...',
            'inputAttributes' => [
                'aria-label' => 'Observações sobre a aprovação'
            ],
            'didRender' => '() => {
                const confirmBtn = document.querySelector(".swal2-confirm");
                const textarea = document.querySelector("textarea");

                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                newConfirmBtn.addEventListener("click", (e) => {
                    e.preventDefault();

                    const observacoes = textarea ? textarea.value : "";

                    Swal.close();

                    Swal.fire({
                        title: "Processando Aprovação...",
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-spinner fa-spin fa-4x text-success"></i>
                                </div>
                                <p class="h4 fw-semibold text-body-emphasis mb-3">Aprovando solicitação de trial</p>
                                <p class="text-muted">Aguarde enquanto criamos o trial e enviamos o email de acesso...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: "swal-wide-modal"
                        }
                    });

                    setTimeout(() => {
                        Livewire.dispatch("aprovarRequest", {
                            requestId: ' . $requestId . ',
                            observacoes: observacoes
                        });
                    }, 100);
                });
            }'
        ]);
    }

    public function confirmarRejeicao($requestId)
    {
        $this->swalFire([
            'title' => 'Confirmar Rejeição',
            'text' => 'Tem certeza que deseja rejeitar esta solicitação de trial?',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonColor' => '#dc3545',
            'cancelButtonColor' => '#6c757d',
            'confirmButtonText' => 'Sim, Rejeitar',
            'cancelButtonText' => 'Cancelar',
            'input' => 'textarea',
            'inputPlaceholder' => 'Motivo da rejeição (obrigatório)...',
            'inputValidator' => '(value) => {
                if (!value) {
                    return "O motivo da rejeição é obrigatório!";
                }
            }',
            'didRender' => '() => {
                const confirmBtn = document.querySelector(".swal2-confirm");
                const textarea = document.querySelector("textarea");

                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

                newConfirmBtn.addEventListener("click", (e) => {
                    e.preventDefault();

                    const motivo = textarea ? textarea.value : "";

                    if (!motivo) {
                        Swal.showValidationMessage("O motivo da rejeição é obrigatório!");
                        return;
                    }

                    Swal.close();

                    Swal.fire({
                        title: "Processando Rejeição...",
                        html: `
                            <div class="text-center">
                                <div class="mb-4">
                                    <i class="fas fa-spinner fa-spin fa-4x text-danger"></i>
                                </div>
                                <p class="h4 fw-semibold text-body-emphasis mb-3">Rejeitando solicitação de trial</p>
                                <p class="text-muted">Aguarde enquanto notificamos o solicitante...</p>
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        customClass: {
                            popup: "swal-wide-modal"
                        }
                    });

                    setTimeout(() => {
                        Livewire.dispatch("rejeitarRequest", {
                            requestId: ' . $requestId . ',
                            motivo: motivo
                        });
                    }, 100);
                });
            }'
        ]);
    }


    #[On('aprovarRequest')]
    public function aprovarRequest($requestId, $observacoes = '')
    {
        $request = TrialRequest::find($requestId);
        if (!$request) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Solicitação não encontrada!'
            ]);
            return;
        }

        try {
            // Aprovar a solicitação
            $request->aprovar(Auth::user(), $observacoes);

            // Criar o trial efetivamente
            $dadosTrial = [
                'name' => $request->nome,
                'email' => $request->email,
                'password' => $request->password,
                'igreja_nome' => $request->igreja_nome,
                'denominacao' => $request->denominacao,
                'phone' => $request->telefone,
                'cidade' => $request->cidade,
                'provincia' => $request->provincia,
                'periodo_dias' => $request->periodo_dias,
                'criado_por' => Auth::id(),
            ];

            $trial = TrialService::criarTrial($dadosTrial);

            // Enviar email de aprovação
            try {
                Mail::to($request->email)->send(new TrialCriadoEmail($trial, $request->password));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar email de aprovação de trial: ' . $e->getMessage());
            }

            // Disparar alert de sucesso
            $this->swalFire([
                'title' => '<span class="fw-bold text-dark">Trial Aprovado com Sucesso</span>',
                'icon' => 'success',
                'html' => '
                    <div class="text-center">
                        <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-success-subtle mb-3 border border-success border-2">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>

                        <p class="h5 fw-semibold text-body-emphasis mb-2">Solicitação aprovada!</p>
                        <p class="text-muted small mb-4">O trial foi criado e o email de acesso foi enviado ao usuário.</p>

                        <div class="row g-2 justify-content-center text-center">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 bg-light shadow-sm">
                                    <h6 class="text-info mb-1 small fw-bold text-uppercase">Usuário</h6>
                                    <strong class="fs-6 text-dark">' . htmlspecialchars($trial->user->name ?? 'Usuário') . '</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 bg-light shadow-sm">
                                    <h6 class="text-success mb-1 small fw-bold text-uppercase">Expira em</h6>
                                    <span class="badge bg-success fs-6 py-1 px-3 fw-bold">' . $trial->diasRestantes() . ' dias</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert mt-4 p-3 rounded-3 text-start border-success-subtle bg-success-subtle">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope me-3 text-success fs-4"></i>
                                <div>
                                    <strong class="text-success">Email enviado:</strong>
                                    <div class="small text-dark">
                                        O usuário recebeu as credenciais de acesso por email.
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
                'buttonsStyling' => true,
                'confirmButtonText' => '<i class="fas fa-check me-2"></i> Entendido',
                'backdrop' => true,
                'allowOutsideClick' => false,
                'allowEscapeKey' => false
            ]);

            $this->dispatch('refreshRequests');

        } catch (\Exception $e) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Erro ao aprovar solicitação: ' . $e->getMessage()
            ]);
            Log::error('Erro ao aprovar solicitação de trial: ' . $e->getMessage());
        }
    }

    #[On('rejeitarRequest')]
    public function rejeitarRequest($requestId, $motivo)
    {
        $request = TrialRequest::find($requestId);
        if (!$request || $request instanceof \Illuminate\Database\Eloquent\Collection) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Solicitação não encontrada!'
            ]);
            return;
        }

        try {
            // Rejeitar a solicitação
            $request->rejeitar(Auth::user(), $motivo, '');

            // Enviar email de rejeição
            try {
                Mail::to($request->email)->send(new TrialRejeitadoEmail($request));
            } catch (\Exception $e) {
                Log::error('Erro ao enviar email de rejeição de trial: ' . $e->getMessage());
            }

            // Disparar alert de sucesso
            $this->swalFire([
                'title' => '<span class="fw-bold text-dark">Solicitação Rejeitada</span>',
                'icon' => 'info',
                'html' => '
                    <div class="text-center">
                        <div class="d-inline-flex justify-content-center align-items-center p-3 rounded-circle bg-danger-subtle mb-3 border border-danger border-2">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>

                        <p class="h5 fw-semibold text-body-emphasis mb-2">Solicitação rejeitada!</p>
                        <p class="text-muted small mb-4">O solicitante foi notificado sobre a decisão.</p>

                        <div class="row g-2 justify-content-center text-center">
                            <div class="col-6">
                                <div class="border rounded-3 p-3 bg-light shadow-sm">
                                    <h6 class="text-info mb-1 small fw-bold text-uppercase">Solicitante</h6>
                                    <strong class="fs-6 text-dark">' . htmlspecialchars($request->nome) . '</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded-3 p-3 bg-light shadow-sm">
                                    <h6 class="text-danger mb-1 small fw-bold text-uppercase">Status</h6>
                                    <span class="badge bg-danger fs-6 py-1 px-3 fw-bold">Rejeitado</span>
                                </div>
                            </div>
                        </div>

                        <div class="alert mt-4 p-3 rounded-3 text-start border-danger-subtle bg-danger-subtle">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-envelope me-3 text-danger fs-4"></i>
                                <div>
                                    <strong class="text-danger">Email enviado:</strong>
                                    <div class="small text-dark">
                                        O solicitante recebeu a notificação de rejeição.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ',
                'customClass' => [
                    'popup' => 'swal2-responsive-modal shadow-lg',
                    'confirmButton' => 'btn bg-info text-light btn-lg px-4 fw-bold'
                ],
                'showConfirmButton' => true,
                'buttonsStyling' => true,
                'confirmButtonText' => '<i class="fas fa-check me-2"></i> Entendido',
                'backdrop' => true,
                'allowOutsideClick' => false,
                'allowEscapeKey' => false
            ]);

            $this->dispatch('refreshRequests');

        } catch (\Exception $e) {
            $this->swalError([
                'title' => 'Erro',
                'text' => 'Erro ao rejeitar solicitação: ' . $e->getMessage()
            ]);
            Log::error('Erro ao rejeitar solicitação de trial: ' . $e->getMessage());
        }
    }

    public function getRequests()
    {
        try {
            $query = TrialRequest::with(['aprovadoPor', 'rejeitadoPor']);

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('nome', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('igreja_nome', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            return $query->orderBy('created_at', 'desc')
                        ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao carregar solicitações: ' . $e->getMessage()
            ]);
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function getStatusOptions()
    {
        return TrialRequest::getStatusOptions();
    }

    public function showRequestDetails($requestId)
    {
        $request = TrialRequest::find($requestId);

        if (!$request) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Solicitação não encontrada!'
            ]);
            return;
        }

        // Formatar dados para o modal
        $requestData = [
            'id' => $request->id,
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'cidade' => $request->cidade,
            'provincia' => $request->provincia,
            'igreja_nome' => $request->igreja_nome,
            'denominacao' => $request->denominacao,
            'periodo_dias' => $request->periodo_dias,
            'status' => $request->status,
            'created_at' => $request->created_at->toISOString(),
            'aprovado_em' => $request->aprovado_em?->toISOString(),
            'rejeitado_em' => $request->rejeitado_em?->toISOString(),
            'motivo_rejeicao' => $request->motivo_rejeicao,
            'observacoes' => $request->observacoes,
        ];

        $this->swalFire([
            'title' => '<span class="fw-bold text-dark">Detalhes da Solicitação</span>',
            'icon' => 'info',
            'html' => $this->buildRequestDetailsHtml($request),
            'customClass' => [
                'popup' => 'swal2-wide-modal shadow-lg',
                'confirmButton' => 'btn bg-info text-light btn-lg px-4 fw-bold'
            ],
            'showConfirmButton' => true,
            'confirmButtonText' => '<i class="fas fa-check me-2"></i> Fechar',
            'buttonsStyling' => false,
            'backdrop' => true,
            'allowOutsideClick' => true,
            'allowEscapeKey' => true,
            'width' => '800px'
        ]);
    }

    private function buildRequestDetailsHtml($request)
    {
        $statusBadgeColor = match($request->status) {
            'pendente' => 'warning',
            'aprovado' => 'success',
            'rejeitado' => 'danger',
            default => 'secondary'
        };

        $html = '<div class="text-start">
                    <div class="row g-3">
                        <!-- Informações Pessoais -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <h6 class="text-info mb-3 fw-bold">
                                    <i class="fas fa-user me-2"></i>Informações Pessoais
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nome:</strong> ' . htmlspecialchars($request->nome) . '<br>
                                        <strong>Email:</strong> ' . htmlspecialchars($request->email) . '<br>
                                        <strong>Telefone:</strong> ' . (htmlspecialchars($request->telefone) ?? 'Não informado') . '
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Cidade:</strong> ' . (htmlspecialchars($request->cidade) ?? 'Não informado') . '<br>
                                        <strong>Província:</strong> ' . (htmlspecialchars($request->provincia) ?? 'Não informado') . '
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações da Igreja -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <h6 class="text-success mb-3 fw-bold">
                                    <i class="fas fa-church me-2"></i>Informações da Igreja
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nome da Igreja:</strong> ' . htmlspecialchars($request->igreja_nome) . '<br>
                                        <strong>Denominação:</strong> ' . htmlspecialchars($request->denominacao) . '
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Período Solicitado:</strong> ' . $request->periodo_dias . ' dias
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status e Datas -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <h6 class="text-warning mb-3 fw-bold">
                                    <i class="fas fa-clock me-2"></i>Status e Datas
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Status:</strong>
                                        <span class="badge bg-' . $statusBadgeColor . ' ms-2">
                                            ' . ucfirst($request->status) . '
                                        </span><br>
                                        <strong>Data da Solicitação:</strong> ' . $request->created_at->format('d/m/Y H:i') . '
                                    </div>
                                    <div class="col-md-6">';

        if ($request->aprovado_em) {
            $html .= '<strong>Aprovado em:</strong> ' . $request->aprovado_em->format('d/m/Y H:i') . '<br>';
        }

        if ($request->rejeitado_em) {
            $html .= '<strong>Rejeitado em:</strong> ' . $request->rejeitado_em->format('d/m/Y H:i') . '<br>';
        }

        if ($request->motivo_rejeicao) {
            $html .= '<strong>Motivo da Rejeição:</strong> ' . htmlspecialchars($request->motivo_rejeicao);
        }

        $html .= '    </div>
                            </div>
                        </div>';

        if ($request->observacoes) {
            $html .= '
                        <!-- Observações -->
                        <div class="col-12">
                            <div class="border rounded-3 p-3 bg-info text-light-subtle">
                                <h6 class="text-info mb-2 fw-bold">
                                    <i class="fas fa-sticky-note me-2"></i>Observações
                                </h6>
                                <p class="mb-0 text-dark">' . htmlspecialchars($request->observacoes) . '</p>
                            </div>
                        </div>';
        }

        $html .= '
                    </div>
                </div>';

        return $html;
    }


    public function render()
    {
        return view('billings.trial-requests', [
            'requests' => $this->getRequests(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
