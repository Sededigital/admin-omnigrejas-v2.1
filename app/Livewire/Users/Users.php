<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserCredentials;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\MemberDeletionService;

#[Title('Dashboard | Usuários')]
#[Layout('components.layouts.app')]
class Users extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRole = '';
    public $perPage = 10;

    // Modal properties
    public $showModal = false;
    public $editingUser = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $role = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'role' => 'required|in:super_admin,root',
        'is_active' => 'boolean',
    ];

    protected $listeners = ['refreshUsers' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedRole()
    {
        $this->resetPage();
    }

    public function setRoleFilter($role)
    {
        $this->selectedRole = $role;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedRole = '';
        $this->resetPage();
    }

    public function openModal($userId = null)
    {
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $this->editingUser = $user;
                $this->name = $user->name;
                $this->email = $user->email;
                $this->phone = $user->phone;
                $this->role = $user->role;
                $this->is_active = $user->is_active;
            }
        } else {
            $this->resetModal();
        }

        $this->showModal = true;
    }

    #[On('closeModal')]
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->editingUser = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function saveUser()
    {
        if ($this->editingUser) {
            $this->rules['email'] = 'required|email|unique:users,email,' . $this->editingUser->id;
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if ($this->editingUser) {
            $this->editingUser->update($data);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Usuário atualizado com sucesso!'
            ]);
        } else {
            // Gerar senha segura para o novo usuário
            $senhaGerada = $this->gerarSenhaUsuario();

            $data['password'] = bcrypt($senhaGerada);
            $data['created_by'] = Auth::id();
            $user = User::create($data);

            // Enviar email com credenciais automaticamente
            try {

                Mail::to($user->email)->send(new UserCredentials($user, $senhaGerada));

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Usuário criado com sucesso! Credenciais enviadas por email.'
                ]);

            } catch (\Exception $e) {
                
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Usuário criado, mas houve erro no envio do email. Senha gerada: ' . $senhaGerada
                ]);
            }
        }

        $this->closeModal();
        $this->dispatch('refreshUsers');
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== Auth::id()) {
            try {
                if ($user->membros()->exists()) {
                    // Usuário tem vínculo com igreja, usar service para exclusão completa
                    $service = new MemberDeletionService();
                    $member = $user->membros()->first(); // Assume um membro principal, ou pode loop se múltiplos
                    $service->deleteMemberCompletely($user, $member, Auth::user());
                } else {
                    // Usuário sem vínculo, apenas deletar
                    $user->delete();
                }

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Usuário excluído com sucesso!'
                ]);
                $this->dispatch('refreshUsers');
            } catch (\Exception $e) {
                Log::error('Erro ao excluir usuário', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);

                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
                ]);
            }
        }
    }

    public function toggleUserStatus($userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== Auth::id()) {
            $user->update(['is_active' => !$user->is_active]);
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Status do usuário alterado com sucesso!'
            ]);
            $this->dispatch('refreshUsers');
        }
    }

    /**
     * Envia email com novas credenciais para o usuário
     */
    public function enviarCredenciais($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não encontrado.'
            ]);
            return;
        }

        try {
            // Gerar nova senha
            $novaSenha = $this->gerarSenhaUsuario();

            // Atualizar senha do usuário
            $user->update([
                'password' => Hash::make($novaSenha)
            ]);

            // Enviar email
            Mail::to($user->email)->send(new UserCredentials($user, $novaSenha));

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Novas credenciais enviadas com sucesso para ' . $user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar credenciais', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao enviar credenciais: ' . $e->getMessage()
            ]);
        }
    }

    public function getUsers()
    {
        $query = User::query()
            ->where('id', '!=', Auth::id()) // Excluir usuário logado
            ->where('role', '!=', 'root') // Excluir root
            ->with(['membros.igreja']); // Carregar relação com igrejas

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedRole) {
            if ($this->selectedRole === 'pastor') {
                // Quando filtrar por pastor, incluir também admins (mesmas permissões)
                $query->whereIn('role', ['pastor', 'admin' ]);
            } else {
                $query->where('role', $this->selectedRole);
            }
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getUserStats()
    {
        $baseQuery = User::where('id', '!=', Auth::id())
                        ->where('role', '!=', 'root');

        $totalUsers = $baseQuery->count();

        $activeUsers = (clone $baseQuery)->where('is_active', true)->count();

        $inactiveUsers = $totalUsers - $activeUsers;

        $newUsersThisMonth = (clone $baseQuery)
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
            'new_this_month' => $newUsersThisMonth,
        ];
    }

    public function getRoleLabel($role)
    {
        return match($role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Administrador',
            'pastor' => 'Pastor',
            'ministro' => 'Ministro',
            'obreiro' => 'Obreiro',
            'diacono' => 'Diácono',
            'membro' => 'Membro',
            'anonymous' => 'Anônimo',
            default => 'Desconhecido'
        };
    }

    public function getRoleBadgeClass($role)
    {
        return match($role) {
            'super_admin' => 'danger',
            'admin' => 'warning',
            'pastor' => 'info',
            'ministro' => 'success',
            'obreiro' => 'secondary',
            'diacono' => 'dark',
            'membro' => 'dark',
            'anonymous' => 'light',
            default => 'secondary'
        };
    }

    public function render()
    {
        return view('users.geral-users', [
            'users' => $this->getUsers(),
            'stats' => $this->getUserStats(),
        ]);
    }

    /**
     * Gera uma senha segura para o usuário
     * Requisitos: mínimo 6 dígitos, sem caracteres especiais
     * Opções: apenas números ou "admin" + números
     */
    private function gerarSenhaUsuario()
    {
        // 70% chance de gerar senha com "admin" + números
        // 30% chance de gerar apenas números
        if (rand(1, 10) <= 7) {
            // Formato: admin + 3-4 números (total mínimo 9 caracteres)
            $numeros = str_pad(rand(0, 9999), rand(3, 4), '0', STR_PAD_LEFT);
            return 'admin' . $numeros;
        } else {
            // Apenas números (6-8 dígitos)
            return str_pad(rand(100000, 99999999), rand(6, 8), '0', STR_PAD_LEFT);
        }
    }
}
