<?php

namespace App\Livewire\Profile;


use BaconQrCode\Writer;
use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Mail\TwoFactorEnabled;
use Livewire\Attributes\Title;
use App\Mail\TwoFactorDisabled;
use Livewire\Attributes\Layout;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;


#[Title('Sistema 2FA | Segurança')]
#[Layout('components.layouts.app')]
class TwoFactorPage extends Component
{   

    #[Rule('required|string|size:6')]
    public $code = '';

    public $secretKeyInput = '';
    public $qrCode = '';
    public $secretKey = '';
    public $showQrCode = false;
    public $showRecoveryCodes = false;
    public $recoveryCodes = [];
    public $activationMethod = 'code'; // 'code' ou 'secret'
    public $confirmDisable = false;
    public $loadingDownload = false;
    public $loadingGenerate = false;

    public function mount()
    {
        $user = Auth::user();

        if (!$user->two_factor_secret) {
            $this->generateNewSecret();
        }
    }



    public function generateNewSecret()
    {
        $google2fa = new Google2FA();
        $this->secretKey = $google2fa->generateSecretKey();
        $this->generateQrCode();
        $this->showQrCode = true;
    }

    public function generateQrCode()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $this->secretKey
        );

        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $this->qrCode = $writer->writeString($qrCodeUrl);
    }

    public function useCodeMethod()
    {
        $this->activationMethod = 'code';
        $this->resetValidation();
        $this->code = '';
        $this->secretKeyInput = '';
    }

    public function useSecretMethod()
    {
        $this->activationMethod = 'secret';
        $this->resetValidation();
        $this->code = '';
        $this->secretKeyInput = '';
    }

    public function enableTwoFactor()
    {
        $user = Auth::user();
        $google2fa = new Google2FA();

        if ($this->activationMethod === 'code') {
            // Validar código de verificação
            $this->validate([
                'code' => 'required|string|size:6'
            ]);

            // Verificar se o código está correto
            if (!$google2fa->verifyKey($this->secretKey, $this->code)) {
                $this->addError('code', 'Código inválido. Tente novamente.');
                return;
            }
        } else {
            // Validar chave secreta
            $this->validate([
                'secretKeyInput' => 'required|string|min:16'
            ]);

            // Verificar se a chave secreta está correta
            if ($this->secretKeyInput !== $this->secretKey) {
                $this->addError('secretKeyInput', 'Chave secreta inválida. Verifique se digitou corretamente.');
                return;
            }
        }

        // Ativar 2FA
        $user->forceFill([
            'two_factor_secret' => encrypt($this->secretKey),
            'two_factor_confirmed_at' => now(),
        ])->save();

        // Gerar códigos de recuperação
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        $this->recoveryCodes = $recoveryCodes;
        $this->showRecoveryCodes = true;
        $this->showQrCode = false;

        // Enviar email de notificação
        Mail::to($user->email)->send(new TwoFactorEnabled($user, $recoveryCodes));


        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Autenticação de dois fatores ativada com sucesso!'
        ]);
       
    }

    public function disableTwoFactor()
    {
        $user = Auth::user();

        // Desativar 2FA
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        // Enviar email de notificação
        Mail::to($user->email)->send(new TwoFactorDisabled($user));


        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Autenticação de dois fatores desativada!'
        ]);

    }

    public function generateNewRecoveryCodes()
    {
        $this->loadingGenerate = true;

        $user = Auth::user();
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        $this->recoveryCodes = $recoveryCodes;
        $this->showRecoveryCodes = true;

        $this->loadingGenerate = false;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Novos códigos de recuperação gerados com sucesso!'
        ]);

    }

    public function downloadRecoveryCodesPDF()
    {
        $this->loadingDownload = true;

        // Verificar se o usuário tem códigos de recuperação
        if (empty($this->recoveryCodes) && !$this->showRecoveryCodes) {
            $this->loadingDownload = false;
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Você precisa visualizar os códigos de recuperação primeiro.'
            ]);
            return;
        }

        // Simular processamento mínimo
        usleep(500000); // 0.5 segundos

        $this->loadingDownload = false;

        // Dispatch para chamar a função JavaScript
        $this->dispatch('generate-pdf');
    }

    private function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        return $codes;
    }


    public function render()
    {   
        $user = Auth::user();
        $hasTwoFactor = $user->two_factor_secret && !empty($user->two_factor_secret);

        if ($hasTwoFactor) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
        } else {
            $recoveryCodes = [];
        }

        return view('profile.two-factor-page', [
            'hasTwoFactor' => $hasTwoFactor,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}
