<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Mail\RecoveryCodeSent;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

#[Title('Definicões de AUTH | 2FA ')]
#[Layout('components.layouts.auth.guest')]
class TwoFactorChallenge extends Component
{
    public $code = '';
    public $recovery_code = '';
    public $showRecoveryCode = false;
    public $showEmailOption = false;
    public $emailSent = false;

    public function mount()
    {
        // Componente inicializado
    }

    public function toggleRecoveryCode()
    {
        $this->showRecoveryCode = !$this->showRecoveryCode;
        $this->showEmailOption = false;
        $this->emailSent = false;
        $this->resetValidation();
    }

    public function enableEmailOption()
    {
        $this->showEmailOption = true;
        $this->showRecoveryCode = false;
        $this->emailSent = false;
        $this->resetValidation();
    }

    public function backToMain()
    {
        $this->showEmailOption = false;
        $this->showRecoveryCode = false;
        $this->emailSent = false;
        $this->resetValidation();
    }

    public function sendRecoveryCodeByEmail()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                $this->addError('email', 'Usuário não autenticado.');
                return;
            }

            if (!$user->two_factor_secret) {
                $this->addError('email', '2FA não está ativo para este usuário.');
                return;
            }

            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];

            if (empty($recoveryCodes)) {
                $this->addError('email', 'Nenhum código de recuperação disponível.');
                return;
            }

            $recoveryCode = $recoveryCodes[0];

            Mail::to($user->email)->send(new RecoveryCodeSent($user, $recoveryCode));

            $this->emailSent = true;

        } catch (\Exception $e) {
            $this->addError('email', 'Erro ao enviar email. Tente novamente.');
        }
    }

    public function verifyCode()
    {
        $user = Auth::user();

        if (!$user) {
            $this->addError('general', 'Usuário não autenticado.');
            return;
        }

        try {
            if ($this->showRecoveryCode) {
                // Validar apenas código de recuperação
                $this->validate([
                    'recovery_code' => 'required|string'
                ]);
            } else {
                // Validar apenas código 2FA
                $this->validate([
                    'code' => 'required|string|size:6'
                ]);
            }
        } catch (\Exception $e) {
            return;
        }

        $google2fa = new Google2FA();

        if ($this->showRecoveryCode) {
            // Verificar código de recuperação
            try {
                $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
            } catch (\Exception $e) {
                $this->addError('recovery_code', 'Erro ao processar códigos de recuperação.');
                return;
            }

            if (in_array($this->recovery_code, $recoveryCodes)) {
                // Remover o código usado
                $recoveryCodes = array_diff($recoveryCodes, [$this->recovery_code]);
                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(array_values($recoveryCodes))),
                ])->save();

                // Marcar sessão como 2FA verificado
                session(['two-factor.login' => true]);

                // Redirecionar baseado no role do usuário
                $route = match($user->role) {
                    'super_admin' => 'dashboard.administrative',
                    'admin', 'pastor', 'ministro' => 'dashboard-admin.church',
                    'membro', 'diacono', 'obreiro' => 'dashboard.member',
                    'root' => 'dashboard.root',
                    default => 'dashboard',
                };
                return redirect()->route($route);

            } else {
                $this->addError('recovery_code', 'Código de recuperação inválido.');
                return;
            }
        } else {
            // Verificar código 2FA
            try {
                $secret = decrypt($user->two_factor_secret);
            } catch (\Exception $e) {
                $this->addError('code', 'Erro ao processar chave secreta.');
                return;
            }

            if ($google2fa->verifyKey($secret, $this->code)) {
                // Marcar sessão como 2FA verificado
                session(['two-factor.login' => true]);

                // Redirecionar baseado no role do usuário
                $route = match($user->role) {
                    'super_admin' => 'dashboard.administrative',
                    'admin', 'pastor', 'ministro' => 'dashboard-admin.church',
                    'membro', 'diacono', 'obreiro' => 'dashboard.member',
                    'root' => 'dashboard.root',
                    default => 'dashboard',
                };
                return redirect()->route($route);

            } else {
                $this->addError('code', 'Código inválido. Tente novamente.');
                return;
            }
        }
    }

    public function render()
    {
        return view('auth.two-factor-challenge');
    }
}

