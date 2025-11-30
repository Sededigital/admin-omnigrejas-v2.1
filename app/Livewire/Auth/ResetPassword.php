<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;


#[Title('Atualize sua senha')]
#[Layout('components.layouts.auth.guest')]
class ResetPassword extends Component
{
    #[Rule('required|email')]
    public $email = '';

    #[Rule('required|min:8')]
    public $password = '';

    #[Rule('required|same:password')]
    public $password_confirmacao = '';

    public $token = '';

    public function mount($token)
    {
        $this->token = $token;
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmacao,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {

            session()->flash('status', 'Sua senha foi redefinida com sucesso!');

            return $this->redirect(route('login'));
        } else {
             session()->flash('status', __($status));
             session()->flash('status_type', 'danger'); // erro
        }
    }

    public function render()
    {
        return view('auth.reset-password');
    }
}
