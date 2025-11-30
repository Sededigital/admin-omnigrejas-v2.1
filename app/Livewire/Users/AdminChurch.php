<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use App\Services\MemberDeletionService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log as Logger;
use App\Mail\MemberCredentials;
use Illuminate\Support\Str;

#[Title('Administradores de Igrejas | Portal do Sistema')]
#[Layout('components.layouts.app')]
class AdminChurch extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Propriedades para filtros
    public $search = '';
    public $selectedChurch = '';
    public $perPage = 10;

    // Propriedade para igreja pré-selecionada (via parâmetro de rota)
    public $preSelectedChurch = null;

    // Modal de adicionar admin
    public $showAddAdminModal = false;
    public $selectedChurchForAdmin = null;
    public $adminName = '';
    public $adminEmail = '';
    public $adminPhone = '';

    // Modal de editar admin
    public $showEditAdminModal = false;
    public $editingAdmin = null;
    public $editName = '';
    public $editEmail = '';
    public $editPhone = '';
    public $editIsActive = true;

    // Modal de confirmar exclusão
    public $deleteAdminId;
    public $deleteType;

    protected $rules = [
        'adminName' => 'required|string|max:255',
        'adminEmail' => 'required|email|unique:users,email',
        'adminPhone' => 'nullable|string|phone:AO',
        'selectedChurchForAdmin' => 'required|exists:igrejas,id',
    ];

    protected $editRules = [
        'editName' => 'required|string|max:255',
        'editPhone' => 'nullable|string|phone:AO',
        'editIsActive' => 'boolean',
    ];

    public function mount($churchId = null)
    {
        if ($churchId) {
            $this->preSelectedChurch = $churchId;
            $this->selectedChurch = $churchId;
            $this->selectedChurchForAdmin = $churchId;
            $this->dispatch('open-add-admin-modal');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedChurch()
    {
        $this->resetPage();
    }

    public function setChurchFilter($churchId)
    {
        $this->selectedChurch = $churchId;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedChurch = '';
        $this->resetPage();
    }

    public function openAddAdminModal($churchId = null)
    {
        $this->selectedChurchForAdmin = $churchId ?: $this->selectedChurch;
        $this->adminName = '';
        $this->adminEmail = '';
        $this->adminPhone = '';
        $this->showAddAdminModal = true;
        $this->resetValidation();
    }

    public function closeAddAdminModal()
    {
        $this->showAddAdminModal = false;
        $this->selectedChurchForAdmin = null;
        $this->adminName = '';
        $this->adminEmail = '';
        $this->adminPhone = '';
        $this->resetValidation();

        // Se veio com igreja pré-selecionada, redirecionar para admin/church usando SPA
        if ($this->preSelectedChurch) {
            return $this->redirect(route('admin.church'), navigate: true);
        }
    }

    public function openEditAdminModal($adminId)
    {
        $admin = User::find($adminId);
        if ($admin && $admin->role === 'admin') {
            $this->editingAdmin = $admin;
            $this->editName = $admin->name;
            $this->editEmail = $admin->email;
            $this->editPhone = $admin->phone;
            $this->editIsActive = $admin->is_active;
            $this->showEditAdminModal = true;
            $this->resetValidation();
        }
    }

    public function closeEditAdminModal()
    {
        $this->showEditAdminModal = false;
        $this->editingAdmin = null;
        $this->editName = '';
        $this->editEmail = '';
        $this->editPhone = '';
        $this->editIsActive = true;
        $this->resetValidation();
    }

    public function addAdmin()
    {
        $this->validate();

        try {
            // Gerar senha segura
            $senhaGerada = $this->gerarSenhaAdmin();

            // Criar usuário admin
            $user = User::create([
                'id' => (string) Str::uuid(),
                'name' => $this->adminName,
                'email' => $this->adminEmail,
                'phone' => $this->adminPhone,
                'password' => Hash::make($senhaGerada),
                'role' => 'admin',
                'is_active' => true,
                'created_by' => Auth::id(),
            ]);

            // Criar membro da igreja
            $igreja = Igreja::find($this->selectedChurchForAdmin);
            $membro = IgrejaMembro::create([
                'id' => (string) Str::uuid(),
                'igreja_id' => $this->selectedChurchForAdmin,
                'user_id' => $user->id,
                'cargo' => 'admin',
                'status' => 'ativo',
                'data_entrada' => now(),
                'created_by' => Auth::id(),
            ]);

            // Criar perfil do membro
            \App\Models\Igrejas\MembroPerfil::create([
                'id' => (string) Str::uuid(),
                'igreja_membro_id' => $membro->id,
                'genero' => 'nao_informado',
                'created_by' => Auth::id(),
            ]);

            // Criar histórico do membro
            \App\Models\Igrejas\IgrejaMembrosHistorico::create([
                'id' => (string) Str::uuid(),
                'igreja_membro_id' => $membro->id,
                'cargo' => 'admin',
                'inicio' => now(),
            ]);

            // Enviar email com credenciais
            try {
                Mail::to($user->email)->send(
                    new MemberCredentials($user, $senhaGerada, $igreja->nome)
                );

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Administrador adicionado com sucesso! Credenciais enviadas por email.'
                ]);
            } catch (\Exception $e) {
                Log::error('Erro ao enviar email para admin', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);

                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Administrador adicionado, mas houve erro no envio do email. Senha: ' . $senhaGerada
                ]);
            }

            // Se veio com igreja pré-selecionada, redirecionar após sucesso
            if ($this->preSelectedChurch) {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Administrador adicionado com sucesso! Redirecionando...'
                ]);

                return redirect()->route('admin.church');
            }

            $this->closeAddAdminModal();

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar admin', [
                'error' => $e->getMessage(),
                'church_id' => $this->selectedChurchForAdmin,
                'email' => $this->adminEmail
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao adicionar administrador: ' . $e->getMessage()
            ]);
        }
    }

    public function updateAdmin()
    {
        $this->editRules['editEmail'] = 'required|email|unique:users,email,' . $this->editingAdmin->id;
        $this->validate($this->editRules);

        try {
            $this->editingAdmin->update([
                'name' => $this->editName,
                'email' => $this->editEmail,
                'phone' => $this->editPhone,
                'is_active' => $this->editIsActive,
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Administrador atualizado com sucesso!'
            ]);

            $this->closeEditAdminModal();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao atualizar administrador: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleAdminStatus($adminId)
    {
        $admin = User::find($adminId);
        if ($admin && $admin->role === 'admin') {
            $admin->update(['is_active' => !$admin->is_active]);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Status do administrador alterado com sucesso!'
            ]);
        }
    }

    public function openDeleteModal($adminId, $type)
    {
        $this->deleteAdminId = $adminId;
        $this->deleteType = $type;
    }

    public function confirmDelete()
    {
        if (!$this->deleteAdminId || !$this->deleteType) {
            return;
        }

        $this->deleteAdmin($this->deleteAdminId, $this->deleteType);

        // Fechar modal
        $this->deleteAdminId = null;
        $this->deleteType = null;
        $this->dispatch('close-delete-modal');
    }

    public function deleteAdmin($adminId, $deletionType = 'soft')
    {
        $admin = User::find($adminId);
        if (!$admin || $admin->role !== 'admin' || $admin->id === Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Administrador não encontrado ou não pode ser removido!'
            ]);
            return;
        }

        try {
            $deletionService = new MemberDeletionService();
            $currentUser = Auth::user();

            // Encontrar o membro relacionado ao admin
            $member = IgrejaMembro::where('user_id', $adminId)
                                 ->where('cargo', 'admin')
                                 ->first();

            if (!$member) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Membro administrador não encontrado!'
                ]);
                return;
            }

            if ($deletionType === 'hard') {
                // Exclusão completa do sistema
                $deletionService->deleteMemberCompletely($admin, $member, $currentUser);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Administrador removido permanentemente do sistema!'
                ]);
            } else {
                // Apenas desativar (soft delete)
                $deletionService->removeMemberFromChurch($member, $currentUser);

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Administrador desativado com sucesso!'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao remover administrador', [
                'admin_id' => $adminId,
                'deletion_type' => $deletionType,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao remover administrador: ' . $e->getMessage()
            ]);
        }
    }

    private function gerarSenhaAdmin()
    {
        // Formato: admin + 4 números (ex: admin1234)
        return 'admin' . rand(1000, 9999);
    }

    public function getAdmins()
    {
        $query = User::query()
            ->where('role', 'admin')
            ->with(['membros.igreja']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedChurch) {
            $query->whereHas('membros', function ($q) {
                $q->where('igreja_id', $this->selectedChurch);
            });
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getChurchOptions()
    {
        return Igreja::orderBy('nome')
                    ->orderByRaw("CASE WHEN status_aprovacao = 'aprovado' THEN 1 ELSE 2 END")
                    ->get();
    }

    public function getAdminStats()
    {
        $totalAdmins = User::where('role', 'admin')->count();
        $activeAdmins = User::where('role', 'admin')->where('is_active', true)->count();
        $inactiveAdmins = $totalAdmins - $activeAdmins;

        return [
            'total' => $totalAdmins,
            'active' => $activeAdmins,
            'inactive' => $inactiveAdmins,
        ];
    }

    public function render()
    {
        $preSelectedChurch = null;
        if ($this->preSelectedChurch) {
            $preSelectedChurch = Igreja::find($this->preSelectedChurch);
            // Debug: verificar se a igreja foi encontrada
            if (!$preSelectedChurch) {
                Logger::warning('AdminChurch: Igreja não encontrada para ID: ' . $this->preSelectedChurch);
            } else {
                Logger::info('AdminChurch: Igreja encontrada: ' . $preSelectedChurch->nome . ' (ID: ' . $preSelectedChurch->id . ')');
                // Garantir que selectedChurchForAdmin está definido
                $this->selectedChurchForAdmin = $preSelectedChurch->id;
                // Auto-abrir modal se veio com igreja pré-selecionada
                $this->dispatch('open-add-admin-modal');
            }
        }

        // Debug removido

        return view('users.admin-church', [
            'admins' => $this->getAdmins(),
            'churches' => $this->getChurchOptions(),
            'stats' => $this->getAdminStats(),
            'preSelectedChurchObj' => $preSelectedChurch,
        ]);
    }
}