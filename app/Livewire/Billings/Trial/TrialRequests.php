<?php

namespace App\Livewire\Billings\Trial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithoutUrlPagination;
use App\Models\Billings\Trial\TrialRequest;
use App\Services\Trial\TrialService;
use App\Mail\TrialCriadoEmail;
use App\Mail\TrialRejeitadoEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

#[Title('Gerenciar Solicitações de Trial')]
#[Layout('components.layouts.app')]
class TrialRequests extends Component
{
    use WithPagination, WithoutUrlPagination;

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
        $this->dispatch('confirmarAprovacao', $requestId);
    }

    public function confirmarRejeicao($requestId)
    {
        $this->dispatch('confirmarRejeicao', $requestId);
    }


    #[On('aprovarRequest')]
    public function aprovarRequest($requestId, $observacoes = '')
    {   
        $request = TrialRequest::find($requestId);
        if (!$request) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Solicitação não encontrada!'
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

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Solicitação aprovada com sucesso! Trial criado e email enviado.'
            ]);

            // Formatar dados do trial para o modal
            $trialData = [
                'user' => [
                    'name' => $trial->user->name ?? 'Usuário'
                ],
                'dias_restantes' => $trial->diasRestantes()
            ];

            // Disparar evento de sucesso para o modal
            $this->dispatch('trial-aprovacao-sucesso', $trialData);

            $this->dispatch('refreshRequests');

        } catch (\Exception $e) {
            // Fechar modal de processamento em caso de erro
            $this->dispatch('close-processing-modal');

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao aprovar solicitação: ' . $e->getMessage()
            ]);
            Log::error('Erro ao aprovar solicitação de trial: ' . $e->getMessage());
        }
    }

    #[On('rejeitarRequest')]
    public function rejeitarRequest($requestId, $motivo)
    {
        $request = TrialRequest::find($requestId);
        if (!$request || $request instanceof \Illuminate\Database\Eloquent\Collection) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Solicitação não encontrada!'
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

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Solicitação rejeitada com sucesso! Email enviado ao solicitante.'
            ]);

            // Disparar evento de sucesso para o modal
            $this->dispatch('trial-rejeicao-sucesso', $request);

            $this->dispatch('refreshRequests');

        } catch (\Exception $e) {
            // Fechar modal de processamento em caso de erro
            $this->dispatch('close-processing-modal');

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao rejeitar solicitação: ' . $e->getMessage()
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

        $this->dispatch('showRequestDetails', $requestData);
    }

    public function render()
    {
        return view('billings.trial-requests', [
            'requests' => $this->getRequests(),
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}