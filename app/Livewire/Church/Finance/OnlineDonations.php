<?php

namespace App\Livewire\Church\Finance;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use App\Models\Outros\DoacaoOnline;
use Illuminate\Support\Facades\Auth;

class OnlineDonations extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtros e busca
    public $search = '';
    public $selectedStatus = '';
    public $selectedGateway = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $selectedDonation = null;

    protected $listeners = ['refreshDonations' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingSelectedGateway()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    public function setGatewayFilter($gateway)
    {
        $this->selectedGateway = $gateway;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->selectedGateway = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function openModal($donationId = null)
    {
        if ($donationId) {
            $this->selectedDonation = DoacaoOnline::find($donationId);
        } else {
            $this->selectedDonation = null;
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedDonation = null;
    }

    public function viewDetails($donationId)
    {
        $this->openModal($donationId);
    }

    public function approveDonation($donationId)
    {
        $donation = DoacaoOnline::find($donationId);
        if ($donation && $donation->status === 'pendente') {
            $donation->update(['status' => 'confirmado']);
            $this->dispatch('toast', ['message' => 'Doação aprovada com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshDonations');
        }
    }

    public function cancelDonation($donationId)
    {
        $donation = DoacaoOnline::find($donationId);
        if ($donation && $donation->status === 'pendente') {
            $donation->update(['status' => 'cancelado']);
            $this->dispatch('toast', ['message' => 'Doação cancelada com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshDonations');
        }
    }

    public function refundDonation($donationId)
    {
        $donation = DoacaoOnline::find($donationId);
        if ($donation && $donation->status === 'confirmado') {
            $donation->update(['status' => 'reembolsado']);
            $this->dispatch('toast', ['message' => 'Reembolso processado com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshDonations');
        }
    }

    public function deleteDonation($donationId)
    {
        $donation = DoacaoOnline::find($donationId);
        if ($donation) {
            $donation->delete();
            $this->dispatch('toast', ['message' => 'Doação excluída com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshDonations');
        }
    }

    public function toggleDonationStatus($donationId)
    {
        $donation = DoacaoOnline::find($donationId);
        if ($donation) {
            $newStatus = $donation->status === 'confirmado' ? 'pendente' : 'confirmado';
            $donation->update(['status' => $newStatus]);
            $this->dispatch('toast', ['message' => 'Status da doação alterado com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshDonations');
        }
    }

    public function getDonations()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return DoacaoOnline::query()->whereRaw('1=0')->paginate($this->perPage);
        }

        $query = DoacaoOnline::query()
            ->with(['usuario', 'igreja']) // Carregar também o relacionamento com igreja
            ->where('igreja_id', $igrejaId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('referencia', 'like', '%' . $this->search . '%')
                  ->orWhereHas('usuario', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        if ($this->selectedGateway) {
            $query->where('metodo', $this->selectedGateway);
        }

        if ($this->dateFrom) {
            $query->where('data', '>=', $this->dateFrom . ' 00:00:00');
        }

        if ($this->dateTo) {
            $query->where('data', '<=', $this->dateTo . ' 23:59:59');
        }

        return $query->orderBy('data', 'desc')
                    ->paginate($this->perPage);
    }

    public function getStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'total_doacoes' => 0,
                'total_doadores' => 0,
                'doacoes_pendentes' => 0,
                'media_mensal' => 0,
            ];
        }

        $donations = DoacaoOnline::where('igreja_id', $igrejaId)->get();

        $totalDoacoes = $donations->where('status', 'confirmado')->sum('valor');
        $totalDoadores = $donations->where('status', 'confirmado')->unique('user_id')->count();
        $doacoesPendentes = $donations->where('status', 'pendente')->count();

        // Calcular média mensal baseada nos últimos 12 meses
        $doacoesUltimoAno = $donations->where('status', 'confirmado')
                                     ->where('data', '>=', now()->subYear());
        $mediaMensal = $doacoesUltimoAno->count() > 0
                      ? $doacoesUltimoAno->sum('valor') / 12
                      : 0;

        return [
            'total_doacoes' => $totalDoacoes,
            'total_doadores' => $totalDoadores,
            'doacoes_pendentes' => $doacoesPendentes,
            'media_mensal' => $mediaMensal,
        ];
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'confirmado' => 'Confirmado',
            'pendente' => 'Pendente',
            'cancelado' => 'Cancelado',
            'reembolsado' => 'Reembolsado',
            default => 'Desconhecido'
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'confirmado' => 'success',
            'pendente' => 'warning',
            'cancelado' => 'danger',
            'reembolsado' => 'info',
            default => 'secondary'
        };
    }

    public function configurePaymentGateway()
    {
        // Por enquanto, mostrar mensagem informativa
        // Em produção, isso redirecionaria para uma página de configuração
        $this->dispatch('toast', [
            'type' => 'info',
            'message' => 'Configuração de gateways de pagamento será implementada em breve. Entre em contato com o administrador do sistema.'
        ]);
    }

    public function shareDonationPage()
    {
        // Compartilhar link da página de doações
        $donationUrl = url('doacoes');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Link copiado para a área de transferência!'
        ]);

        // Em produção, isso poderia abrir um modal de compartilhamento
        // ou integrar com APIs de redes sociais
    }

    public function getGatewayStatus()
    {
        // Em produção, isso viria de uma tabela de configurações de gateway
        // Por enquanto, simulamos com dados estáticos baseados no modelo DoacaoOnline
        return [
            'paypal' => [
                'ativo' => true,
                'configurado' => true,
                'nome' => 'PayPal',
                'icone' => 'fab fa-paypal'
            ],
            'stripe' => [
                'ativo' => true,
                'configurado' => true,
                'nome' => 'Stripe',
                'icone' => 'fab fa-stripe'
            ],
            'pagseguro' => [
                'ativo' => false,
                'configurado' => false,
                'nome' => 'PagSeguro',
                'icone' => 'fas fa-credit-card'
            ],
            'mercadopago' => [
                'ativo' => true,
                'configurado' => true,
                'nome' => 'Mercado Pago',
                'icone' => 'fas fa-shopping-cart'
            ],
        ];
    }

    public function render()
    {
        return view('church.finance.online-donations', [
            'donations' => $this->getDonations(),
            'stats' => $this->getStats(),
            'gatewayStatus' => $this->getGatewayStatus(),
        ]);
    }
}
