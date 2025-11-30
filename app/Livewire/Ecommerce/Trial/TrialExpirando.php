<?php

namespace App\Livewire\Ecommerce\Trial;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\Trial\TrialUser;

#[Title('Período de Teste Expirando - OmnIgrejas')]
#[Layout('components.layouts.subscription')]
class TrialExpirando extends Component
{
    public $trial;
    public $diasRestantes;
    public $mostrarUpgrade = false;

    public function mount()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Buscar trial ativo do usuário
        $this->trial = TrialUser::where('user_id', $user->id)
            ->where('status', 'ativo')
            ->with('igreja')
            ->first();

        if (!$this->trial) {
            return redirect()->route('ecommerce.home');
        }

        // Calcular dias restantes
        $this->diasRestantes = $this->trial->diasRestantes();


    }

    public function mostrarUpgrade()
    {
        $this->mostrarUpgrade = true;
    }

    public function fazerUpgrade()
    {
        // Redirecionar para página de upgrade
        return redirect()->route('ecommerce.subscription.upgrade');
    }


    public function render()
    {
        return view('ecommerce.trial.expirando', [
            'trial' => $this->trial,
            'diasRestantes' => $this->diasRestantes,
            'dataExpiracao' => $this->trial->data_fim->format('d/m/Y'),
            'nomeUsuario' => $this->trial->user->name,
            'igrejaNome' => $this->trial->igreja->nome,
        ]);
    }
}