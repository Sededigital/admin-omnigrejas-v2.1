<?php

namespace App\Livewire\Ecommerce\Trial;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\Trial\TrialUser;

#[Title('Bem-vindo ao OmnIgrejas - Seu período de teste começou!')]
#[Layout('components.layouts.subscription')]
class TrialCriado extends Component
{
    public $trial;
    public $senhaTemporaria;

    public function mount()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Buscar trial recém-criado do usuário
        $this->trial = TrialUser::where('user_id', $user->id)
            ->where('status', 'ativo')
            ->with('igreja')
            ->first();

        if (!$this->trial) {
            return redirect()->route('ecommerce.home');
        }

        // Se o trial já tem mais de 1 dia, redirecionar
        if ($this->trial->diasDesdeCriacao() > 1) {
            return redirect('/');
        }
    }

    public function irParaDashboard()
    {
        return redirect('/');
    }


    public function render()
    {
        // Estatísticas de uso do trial
        $estatisticasUso = $this->trial->getEstatisticasUso();

        return view('ecommerce.trial.criado', [
            'trial' => $this->trial,
            'dataInicio' => $this->trial->data_inicio->format('d/m/Y'),
            'dataFim' => $this->trial->data_fim->format('d/m/Y'),
            'nomeUsuario' => $this->trial->user->name,
            'emailUsuario' => $this->trial->user->email,
            'igrejaNome' => $this->trial->igreja->nome,
            'periodoDias' => $this->trial->periodo_dias,
            'loginUrl' => url('/login'),
            'estatisticasUso' => $estatisticasUso,
            'diasRestantes' => $this->trial->diasRestantes(),
            'statusFormatado' => $this->trial->getStatusFormatado(),
            'ultimoAcesso' => $this->trial->getUltimoAcessoFormatado(),
            'totalDiasTrial' => $this->trial->diasDesdeCriacao(),
        ]);
    }
}