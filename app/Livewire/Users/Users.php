<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

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
        'role' => 'required|in:super_admin',
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
            session()->flash('success', 'Usuário atualizado com sucesso!');
        } else {
            $data['password'] = bcrypt('password123'); // Senha padrão
            $data['created_by'] = Auth::id();
            User::create($data);
            session()->flash('success', 'Usuário criado com sucesso!');
        }

        $this->closeModal();
        $this->dispatch('refreshUsers');
    }

    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== Auth::id()) {
            $user->delete();
            session()->flash('success', 'Usuário excluído com sucesso!');
            $this->dispatch('refreshUsers');
        }
    }

    public function toggleUserStatus($userId)
    {
        $user = User::find($userId);
        if ($user && $user->id !== Auth::id()) {
            $user->update(['is_active' => !$user->is_active]);
            session()->flash('success', 'Status do usuário alterado com sucesso!');
            $this->dispatch('refreshUsers');
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
            'membro' => 'primary',
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
}
