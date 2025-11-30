<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

#[Title('Confirmar Sua Senha')]
#[Layout('components.layouts.auth.guest')]
class ConfirmPassword extends Component
{
    public $password = '';

    protected $rules = [
        'password' => 'required',
    ];

    public function confirmPassword()
    {
        $this->validate();

        if (!Hash::check($this->password, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'password' => ['A senha fornecida não está correta.'],
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        return redirect()->intended(url('/'));
    }

    public function render()
    {
        return view('auth.confirm-password');
    }
}
