<?php
namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use App\Services\EngajamentoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Helpers\RBAC\PermissionHelper;
use App\Models\RBAC\IgrejaMembroFuncao;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;



#[Title('Iniciar sessão')]
#[Layout('components.layouts.auth.guest')]
class Login extends Component
{


    public $email;
    public $password;
    public $remember = false;
    public $loading = false;
    public $loginSuccessful = false;

     protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];


    // Renomeado de session() para login()
    public function login()
    {
        $this->loading = true;
        $this->validate();

        // Verificar se o usuário existe
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            // Email não encontrado
            $this->loading = false;
            throw ValidationException::withMessages([
                'email' => ['Este email não está registrado em nosso sistema.'],
            ]);
        }

        // Verificar se a senha está correta
        if (!Hash::check($this->password, $user->password)) {
            // Senha incorreta
            $this->loading = false;
            throw ValidationException::withMessages([
                'password' => ['A senha fornecida está incorreta.'],
            ]);
        }

        // Verificar se o usuário está ativo
        if (!$user->is_active) {
            // Usuário inativo
            $this->loading = false;
            throw ValidationException::withMessages([
                'email' => ['Sua conta está desativada. Entre em contato com o administrador.'],
            ]);
        }

        // ✅ VERIFICAÇÃO DE STATUS DO USUÁRIO: Impedir login se usuário não estiver ativo
        if (!$this->verificarStatusUsuarioAtivo($user)) {
            $this->loading = false;
            throw ValidationException::withMessages([
                'email' => ['Sua conta está suspensa ou bloqueada. Entre em contato com o administrador.'],
            ]);
        }

        // ✅ VERIFICAÇÃO DE STATUS DO MEMBRO: Impedir login se membro não estiver ativo
        if (!$this->verificarStatusMembroAtivo($user)) {
            $this->loading = false;
            throw ValidationException::withMessages([
                'email' => ['Sua conta de membro está inativa. Entre em contato com o administrador da igreja.'],
            ]);
        }
         // 🔍 LOG DETALHADO: Verificar funções do membro no login
         //**/ $this->logFuncoesMembro($user);
        // Credenciais válidas - enviar evento para bloquear botão
        $this->dispatch('login-credentials-valid');

        // Tentativa de login bem-sucedida
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {

            session()->regenerate();

            // ✅ VERIFICAÇÃO DE FUNÇÕES: Impedir login se usuário não tem funções atribuídas
            if (!$this->verificarFuncoesAtribuidas($user)) {
                Auth::logout();
                $this->loading = false;
                $this->dispatch('login-failed');
                throw ValidationException::withMessages([
                    'email' => ['Sua conta não possui funções atribuídas. Entre em contato com o administrador da igreja.'],
                ]);
            }



            // 🎮 REGISTRAR PONTOS POR LOGIN DIÁRIO
            try {
                $engajamentoService = app(EngajamentoService::class);
                $engajamentoService->registrarLoginDiario($user);
            } catch (\Exception $e) {
                // Não impedir login por erro no sistema de pontos
                Log::error('Erro ao registrar pontos de login diário: ' . $e->getMessage());
            }

            // Marcar login como bem-sucedido para manter botão desabilitado
            $this->loginSuccessful = true;

            $role = Auth::user()->role;

            switch ($role) {
                case 'super_admin':
                    return redirect()->intended(route('dashboard.administrative'));
                  break;
                case 'admin':
                    return redirect()->intended(route('dashboard-admin.church'));
                    break;
                case 'pastor':
                    return redirect()->intended(route('dashboard-admin.church'));
                    break;
                case 'ministro':
                    return redirect()->intended(route('dashboard-admin.church'));
                    break;
                case 'membro':
                    return redirect()->intended(route('dashboard.member'));
                    break;
                case 'diacono':
                    return redirect()->intended(route('dashboard.member'));
                    break;
                case 'obreiro':
                    return redirect()->intended(route('dashboard.member'));
                    break;
                case 'root':
                    return redirect()->intended(route('dashboard.root'));
                    break;
                default:
                    // Caso o role não seja reconhecido, redirecionar para login
                    Auth::logout();
                    $this->loading = false;
                    $this->dispatch('login-failed');
                    throw ValidationException::withMessages([
                        'email' => ['Tipo de usuário não autorizado.'],
                    ]);
            }
        } else {
            // Erro genérico (fallback)
            $this->loading = false;
            throw ValidationException::withMessages([
                'email' => ['Erro ao fazer login. Tente novamente.'],
            ]);
        }
    }

    /**
     * Verifica se o usuário tem funções atribuídas ativas
     */
    private function verificarFuncoesAtribuidas(User $user): bool
    {
        // Admin/Pastor têm acesso total independente de funções
        if (PermissionHelper::hasFullAccess($user)) {
            return true;
        }

        // Buscar primeiro membro ativo do usuário (removendo condição problemática de principal)
        $membro = $user->membros()->where('status', 'ativo')->first();
        if (!$membro) {
            return false;
        }

        // Verificar se tem pelo menos uma função ativa atribuída
        return IgrejaMembroFuncao::where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->exists();
    }

    /**
     * Verifica se o usuário tem pelo menos um membro ativo em alguma igreja
     */
    private function verificarStatusMembroAtivo(User $user): bool
    {
        // Super Admin e Root têm acesso total independente de status de membro
        if (in_array($user->role, ['super_admin', 'root'])) {
            return true;
        }

        // Verificar se o usuário tem pelo menos um membro ativo
        return $user->membros()->where('status', 'ativo')->exists();
    }

    /**
     * Verifica se o status do usuário permite login
     */
    private function verificarStatusUsuarioAtivo(User $user): bool
    {
        // Super Admin e Root têm acesso total independente de status
        if (in_array($user->role, ['super_admin', 'root'])) {
            return true;
        }

        // Verificar se o status do usuário permite login
        return $user->status === 'ativo';
    }

    /**
     * Log detalhado das funções do membro durante o login
     */
    /*
    private function logFuncoesMembro(User $user): void
    {
        //** Buscar primeiro membro ativo do usuário
        $membro = $user->membros()->where('status', 'ativo')->first();

        if (!$membro) {
            \Illuminate\Support\Facades\Log::info("🔍 LOGIN FUNCTIONS CHECK - NO MEMBER FOUND\n" .
                "User ID: {$user->id}\n" .
                "User Email: {$user->email}\n" .
                "User Role: {$user->role}\n" .
                "Status: NO MEMBER FOUND\n" .
                "Functions: NONE\n" .
                str_repeat("=", 50));
            return;
        }

        //** Buscar funções atribuídas ao membro
        $funcoesAtribuidas = \App\Models\RBAC\IgrejaMembroFuncao::with(['funcao.permissoes'])
            ->where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->get();

        $logMessage = "🔍 LOGIN FUNCTIONS CHECK - MEMBER DETAILS\n" .
            "User ID: {$user->id}\n" .
            "User Email: {$user->email}\n" .
            "User Role: {$user->role}\n" .
            "Member ID: {$membro->id}\n" .
            "Member Status: {$membro->status}\n" .
            "Member Cargo: {$membro->cargo}\n" .
            "Member Igreja ID: {$membro->igreja_id}\n" .
            "Functions Count: {$funcoesAtribuidas->count()}\n";

        if ($funcoesAtribuidas->isNotEmpty()) {
            $logMessage .= "Functions List:\n";
            foreach ($funcoesAtribuidas as $funcao) {
                $logMessage .= "  - Function: {$funcao->funcao->nome} (ID: {$funcao->funcao->id})\n";
                $logMessage .= "    Permissions: " . $funcao->funcao->permissoes->pluck('nome')->join(', ') . "\n";
                $logMessage .= "    Status: {$funcao->status}\n";
                $logMessage .= "    Atribuido em: {$funcao->atribuido_em}\n";
                if ($funcao->valido_ate) {
                    $logMessage .= "    Valido até: {$funcao->valido_ate}\n";
                }
                $logMessage .= "\n";
            }
        } else {
            $logMessage .= "Functions List: NO FUNCTIONS ASSIGNED\n";
        }

        $logMessage .= str_repeat("=", 50);

        \Illuminate\Support\Facades\Log::info($logMessage);
    }
    */

    public function render()
    {
        return view('auth.login');
    }
}
