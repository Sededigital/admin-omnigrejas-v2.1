<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Title('Dashboard | Logs')]
#[Layout('components.layouts.app')]
class Logs extends Component
{
    public $authenticated = false;
    public $password = '';
    public $logContent = '';

    protected $rules = [
        'password' => 'required',
    ];

    public function checkPassword()
    {
        $this->validate();

        if ($this->password === 'kediambiko') {
            $this->authenticated = true;
            $this->password = '';
            $this->loadLog();
        } else {
            $this->addError('password', 'Senha incorreta.');
        }
    }

    public function loadLog()
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $this->logContent = file_get_contents($logPath);
        } else {
            $this->logContent = 'Arquivo de log não encontrado.';
        }
    }

    public function clearLog()
    {
        if (!$this->authenticated) return;

        $logPath = storage_path('logs/laravel.log');
        file_put_contents($logPath, '');
        $this->logContent = '';
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Log limpo com sucesso!'
        ]);
    }

    public function render()
    {
        return view('users.logs');
    }
}