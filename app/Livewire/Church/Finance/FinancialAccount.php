<?php

namespace App\Livewire\Church\Finance;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Financeiro\FinanceiroConta;
use App\Models\Financeiro\FinanceiroCanalDigital;


#[Title('Contas | Portal da Igreja')]
#[Layout('components.layouts.app')]
class FinancialAccount extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Abas
    public $activeTab = 'accounts';

    // Filtros e busca - Contas
    public $search = '';
    public $selectedStatus = '';
    public $perPage = 10;

    // Filtros e busca - Canais Digitais
    public $searchDigital = '';
    public $selectedType = '';
    public $selectedStatusDigital = '';

    // Modal properties - Contas
    public $showModal = false;
    public $editingAccount = null;
    public $account_banco = '';
    public $account_titular = '';
    public $account_iban = '';
    public $account_swift = '';
    public $account_numero_conta = '';
    public $account_moeda = 'AOA';
    public $account_ativa = true;
    public $account_observacoes = '';

    // Modal properties - Canais Digitais
    public $showDigitalModal = false;
    public $editingDigitalChannel = null;
    public $digital_tipo = '';
    public $digital_referencia = '';
    public $digital_titular = '';
    public $digital_moeda = 'AOA';
    public $digital_observacoes = '';
    public $digital_ativo = true;

    // Modal properties - Confirmação de Exclusão
    public $showDeleteModal = false;
    public $deleteType = ''; // 'account' ou 'channel'
    public $deleteItem = null;
    public $deletePassword = '';
    public $deleteError = '';

    protected function rules()
    {
        return [
            'account_banco' => 'required|string|max:255',
            'account_titular' => 'required|string|max:255',
            'account_iban' => 'nullable|string|max:50',
            'account_swift' => 'nullable|string|max:20',
            'account_numero_conta' => 'required|string|max:50',
            'account_moeda' => 'required|string|max:3',
            'account_ativa' => 'boolean',
            'account_observacoes' => 'nullable|string|max:500',
        ];
    }

    protected function digitalChannelRules()
    {
        return [
            'digital_tipo' => 'required|string|max:100',
            'digital_referencia' => 'required|string|max:255',
            'digital_titular' => 'required|string|max:255',
            'digital_moeda' => 'required|string|max:3',
            'digital_observacoes' => 'nullable|string|max:500',
            'digital_ativo' => 'boolean',
        ];
    }

    protected $listeners = ['refreshAccounts' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedStatus = '';
        $this->resetPage();
    }

    public function openModal($accountId = null)
    {
        if ($accountId) {
            $account = FinanceiroConta::find($accountId);
            if ($account) {
                $this->editingAccount = $account;
                $this->account_banco = $account->banco;
                $this->account_titular = $account->titular;
                $this->account_iban = $account->iban ?? '';
                $this->account_swift = $account->swift ?? '';
                $this->account_numero_conta = $account->numero_conta;
                $this->account_moeda = $account->moeda;
                $this->account_ativa = $account->ativa;
                $this->account_observacoes = $account->observacoes ?? '';
            }
        } else {
            $this->resetModal();
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->editingAccount = null;
        $this->account_banco = '';
        $this->account_titular = '';
        $this->account_iban = '';
        $this->account_swift = '';
        $this->account_numero_conta = '';
        $this->account_moeda = 'AOA';
        $this->account_ativa = true;
        $this->account_observacoes = '';
        $this->resetValidation();
    }

    public function saveAccount()
    {
        $this->validate();

        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return;
        }

        if ($this->editingAccount) {
            $this->editingAccount->update([
                'banco' => $this->account_banco,
                'titular' => $this->account_titular,
                'iban' => $this->account_iban,
                'swift' => $this->account_swift,
                'numero_conta' => $this->account_numero_conta,
                'moeda' => $this->account_moeda,
                'ativa' => $this->account_ativa,
                'observacoes' => $this->account_observacoes,
            ]);

            $this->dispatch('toast', ['message' => 'Conta atualizada com sucesso!', 'type' => 'success']);
        } else {
            FinanceiroConta::create([
                'id' => (string) Str::uuid(),
                'igreja_id' => $igrejaId,
                'banco' => $this->account_banco,
                'titular' => $this->account_titular,
                'iban' => $this->account_iban,
                'swift' => $this->account_swift,
                'numero_conta' => $this->account_numero_conta,
                'moeda' => $this->account_moeda,
                'ativa' => $this->account_ativa,
                'observacoes' => $this->account_observacoes,
            ]);

            $this->dispatch('toast', ['message' => 'Conta criada com sucesso!', 'type' => 'success']);
        }

        $this->closeModal();
        $this->dispatch('refreshAccounts');
    }

    public function deleteAccount($accountId)
    {
        $this->openDeleteModal('account', $accountId);
    }

    public function toggleAccountStatus($accountId)
    {
        $account = FinanceiroConta::find($accountId);
        if ($account) {
            $account->update(['ativa' => !$account->ativa]);
            $this->dispatch('toast', ['message' => 'Status da conta alterado com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshAccounts');
        }
    }

    public function getAccounts()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return FinanceiroConta::query()->whereRaw('1=0')->paginate($this->perPage);
        }

        $query = FinanceiroConta::query()
            ->where('igreja_id', $igrejaId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('banco', 'like', '%' . $this->search . '%')
                  ->orWhere('titular', 'like', '%' . $this->search . '%')
                  ->orWhere('numero_conta', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedStatus !== '') {
            $query->where('ativa', $this->selectedStatus === 'ativo');
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }



    public function getStatusLabel($ativa)
    {
        return $ativa ? 'Ativa' : 'Inativa';
    }

    public function getStatusBadgeClass($ativa)
    {
        return $ativa ? 'success' : 'secondary';
    }

    // === MÉTODOS PARA CANAIS DIGITAIS ===

    public function updatingSearchDigital()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatusDigital()
    {
        $this->resetPage();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openDigitalModal($channelId = null)
    {
        if ($channelId) {
            $channel = FinanceiroCanalDigital::find($channelId);
            if ($channel) {
                $this->editingDigitalChannel = $channel;
                $this->digital_tipo = $channel->tipo;
                $this->digital_referencia = $channel->referencia;
                $this->digital_titular = $channel->titular;
                $this->digital_moeda = $channel->moeda;
                $this->digital_observacoes = $channel->observacoes ?? '';
                $this->digital_ativo = $channel->ativo;
            }
        } else {
            $this->resetDigitalModal();
        }

        $this->showDigitalModal = true;
    }

    public function closeDigitalModal()
    {
        $this->showDigitalModal = false;
        $this->resetDigitalModal();
    }

    private function resetDigitalModal()
    {
        $this->editingDigitalChannel = null;
        $this->digital_tipo = '';
        $this->digital_referencia = '';
        $this->digital_titular = '';
        $this->digital_moeda = 'AOA';
        $this->digital_observacoes = '';
        $this->digital_ativo = true;
        $this->resetValidation();
    }

    public function saveDigitalChannel()
    {
        $this->validate($this->digitalChannelRules());

        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return;
        }

        if ($this->editingDigitalChannel) {
            $this->editingDigitalChannel->update([
                'tipo' => $this->digital_tipo,
                'referencia' => $this->digital_referencia,
                'titular' => $this->digital_titular,
                'moeda' => $this->digital_moeda,
                'observacoes' => $this->digital_observacoes,
                'ativo' => $this->digital_ativo,
            ]);

            $this->dispatch('toast', ['message' => 'Canal digital atualizado com sucesso!', 'type' => 'success']);
        } else {
            FinanceiroCanalDigital::create([
                'id' => (string) Str::uuid(),
                'igreja_id' => $igrejaId,
                'tipo' => $this->digital_tipo,
                'referencia' => $this->digital_referencia,
                'titular' => $this->digital_titular,
                'moeda' => $this->digital_moeda,
                'observacoes' => $this->digital_observacoes,
                'ativo' => $this->digital_ativo,
            ]);

            $this->dispatch('toast', ['message' => 'Canal digital criado com sucesso!', 'type' => 'success']);
        }

        $this->closeDigitalModal();
        $this->dispatch('refreshAccounts');
    }

    public function deleteDigitalChannel($channelId)
    {
        $this->openDeleteModal('channel', $channelId);
    }

    public function toggleDigitalChannelStatus($channelId)
    {
        $channel = FinanceiroCanalDigital::find($channelId);
        if ($channel) {
            $channel->update(['ativo' => !$channel->ativo]);
            $this->dispatch('toast', ['message' => 'Status do canal digital alterado com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshAccounts');
        }
    }

    // === MÉTODOS PARA MODAL DE EXCLUSÃO ===

    public function openDeleteModal($type, $itemId)
    {
        // Verificar permissões do usuário
        $user = Auth::user();
        if (!$this->canDeleteItems($user)) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Você não tem permissão para excluir itens financeiros.'
            ]);
            return;
        }

        $this->deleteType = $type;
        $this->deletePassword = '';
        $this->deleteError = '';

        if ($type === 'account') {
            $this->deleteItem = FinanceiroConta::find($itemId);
        } elseif ($type === 'channel') {
            $this->deleteItem = FinanceiroCanalDigital::find($itemId);
        }

        if ($this->deleteItem) {
            $this->showDeleteModal = true;
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteType = '';
        $this->deleteItem = null;
        $this->deletePassword = '';
        $this->deleteError = '';
    }

    public function confirmDelete()
    {
        // Validar senha
        if (empty($this->deletePassword)) {
            $this->deleteError = 'A senha é obrigatória.';
            return;
        }

        $user = Auth::user();
        if (!password_verify($this->deletePassword, $user->password)) {
            $this->deleteError = 'Senha incorreta.';
            return;
        }

        // Verificar permissões novamente
        if (!$this->canDeleteItems($user)) {
            $this->deleteError = 'Você não tem permissão para excluir itens financeiros.';
            return;
        }

        // Executar exclusão
        if ($this->deleteType === 'account') {
            $this->deleteAccountConfirmed();
        } elseif ($this->deleteType === 'channel') {
            $this->deleteChannelConfirmed();
        }

        $this->closeDeleteModal();
    }

    private function deleteAccountConfirmed()
    {
        if (!$this->deleteItem) return;

        // Verificar se há movimentos associados
        if ($this->deleteItem->movimentos()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Não é possível excluir uma conta com movimentos associados.'
            ]);
            return;
        }

        $this->deleteItem->delete();
        $this->dispatch('toast', ['message' => 'Conta excluída com sucesso!', 'type' => 'success']);
        $this->dispatch('refreshAccounts');
    }

    private function deleteChannelConfirmed()
    {
        if (!$this->deleteItem) return;

        $this->deleteItem->delete();
        $this->dispatch('toast', ['message' => 'Canal digital excluído com sucesso!', 'type' => 'success']);
        $this->dispatch('refreshAccounts');
    }

    private function canDeleteItems($user)
    {
        // Verificar se o usuário tem permissão para excluir
        $allowedRoles = [
            User::ROLE_ROOT,
            User::ROLE_SUPERADMIN,
            User::ROLE_ADMIN,
            User::ROLE_PASTOR,
            User::ROLE_MINISTRO
        ];

        return in_array($user->role, $allowedRoles);
    }

    public function getDigitalChannels()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return FinanceiroCanalDigital::query()->whereRaw('1=0')->paginate($this->perPage);
        }

        $query = FinanceiroCanalDigital::query()
            ->where('igreja_id', $igrejaId);

        if ($this->searchDigital) {
            $query->where(function ($q) {
                $q->where('tipo', 'like', '%' . $this->searchDigital . '%')
                  ->orWhere('referencia', 'like', '%' . $this->searchDigital . '%')
                  ->orWhere('titular', 'like', '%' . $this->searchDigital . '%');
            });
        }

        if ($this->selectedType) {
            $query->where('tipo', $this->selectedType);
        }

        if ($this->selectedStatusDigital !== '') {
            $query->where('ativo', $this->selectedStatusDigital === 'ativo');
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getDigitalChannelTypes()
    {
        return [
            // Canais Angolanos
            'bai_direto' => 'BAI Directo',
            'multicaixa_express' => 'Multicaixa Express',
            'bfa_net' => 'BFA Net',
            'unigtel_money' => 'Unitel Money',
            'kixi_credito' => 'Kixi Crédito',

            // Canais Internacionais Essenciais
            'paypal' => 'PayPal',
            'wise' => 'Wise',

            // Outros
            'outro' => 'Outro',
        ];
    }

    public function render()
    {
        return view('church.finance.financial-account', [
            'accounts' => $this->getAccounts(),
            'digitalChannels' => $this->getDigitalChannels(),
            'digitalChannelTypes' => $this->getDigitalChannelTypes(),
        ]);
    }
}
