<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Mail\PasswordReset;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;


#[Title('Recuperar senha')]
#[Layout('components.layouts.auth.guest')]
class ForgotPassword extends Component
{
    #[Rule('required|email')]
    public $email = '';

    public $emailSent = false;

    public function sendResetLink()
    {
        
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Não encontramos um usuário com este endereço de email.');
            return;
        }

        // Gerar token de reset
        $token = Password::createToken($user);

        // Enviar email
        Mail::to($user->email)->send(new PasswordReset($user, $token));

        $this->emailSent = true;
        session()->flash('status', 'Link de redefinição de senha enviado para seu email!');
    }

    public function render()
    {
        return view('auth.forgot-password');
    }
}
