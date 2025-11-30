<?php

namespace App\Livewire\Church\Only;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Igrejas\AtendimentoPastoral;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Cuidado Pastoral | Portal da Igreja')]
#[Layout('components.layouts.app')]
class PastoralCare extends Component
{
    // Propriedades para listagem
    public $atendimentos = [];
    public $membroAtual;

    // Propriedades para modal
    public $isEditing = false;
    public $atendimentoSelecionado = null;

    // Propriedades do formulário
    #[Rule('required|exists:igreja_membros,id')]
    public $membro_id = '';

    #[Rule('required|exists:users,id')]
    public $pastor_id = '';

    #[Rule('required|in:aconselhamento,visita,oracao,encorajamento,outro')]
    public $tipo = '';

    #[Rule('nullable|string|max:1000')]
    public $descricao = '';

    #[Rule('nullable|date')]
    public $data_atendimento = '';

    // Propriedades para filtros
    public $filtroTipo = '';
    public $filtroPastor = '';
    public $filtroMembro = '';

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarAtendimentos();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar cuidados pastorais.');
        }
    }

    protected function carregarAtendimentos()
    {
        $query = AtendimentoPastoral::with(['membro.user', 'pastor', 'igreja'])
            ->where('igreja_id', Auth::user()->getIgrejaId());

        // Aplicar filtros
        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->filtroPastor) {
            $query->where('pastor_id', $this->filtroPastor);
        }

        if ($this->filtroMembro) {
            $query->where('membro_id', $this->filtroMembro);
        }

        $this->atendimentos = $query->orderBy('data_atendimento', 'desc')->get();
    }

    public function openModal($atendimentoId = null)
    {
        $this->resetModal();

        if ($atendimentoId) {
            $atendimento = AtendimentoPastoral::find($atendimentoId);

            if (!$atendimento || $atendimento->igreja_id !== Auth::user()->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Atendimento pastoral não encontrado.'
                ]);
                return;
            }

            $this->atendimentoSelecionado = $atendimento;
            $this->membro_id = $atendimento->membro_id;
            $this->pastor_id = $atendimento->pastor_id;
            $this->tipo = $atendimento->tipo;
            $this->descricao = $atendimento->descricao;
            $this->data_atendimento = $atendimento->data_atendimento?->format('Y-m-d');
            $this->isEditing = true;
        } else {
            $this->data_atendimento = now()->format('Y-m-d');
            $this->isEditing = false;
        }

        $this->dispatch('open-pastoral-care-modal');
    }

    public function salvarAtendimento()
    {
        $this->validate();

        // Verificar se o membro pertence à igreja do usuário
        $membro = IgrejaMembro::find($this->membro_id);
        if (!$membro || $membro->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->adddanger('membro_id', 'Membro não encontrado ou não pertence à sua igreja.');
            return;
        }

        // Verificar se o pastor é válido (pode ser de outra igreja ou externo)
        $pastor = \App\Models\User::find($this->pastor_id);
        if (!$pastor) {
            $this->adddanger('pastor_id', 'Pastor não encontrado.');
            return;
        }

        if ($this->isEditing) {
            $this->atendimentoSelecionado->update([
                'membro_id' => $this->membro_id,
                'pastor_id' => $this->pastor_id,
                'tipo' => $this->tipo,
                'descricao' => $this->descricao,
                'data_atendimento' => $this->data_atendimento,
            ]);

            $mensagem = 'Atendimento pastoral atualizado com sucesso!';
        } else {
            AtendimentoPastoral::create([
                'igreja_id' => Auth::user()->getIgrejaId(),
                'membro_id' => $this->membro_id,
                'pastor_id' => $this->pastor_id,
                'tipo' => $this->tipo,
                'descricao' => $this->descricao,
                'data_atendimento' => $this->data_atendimento,
            ]);

            $mensagem = 'Atendimento pastoral registrado com sucesso!';
        }

        $this->carregarAtendimentos();
        $this->dispatch('close-pastoral-care-modal');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function excluirAtendimento($atendimentoId)
    {
        $atendimento = AtendimentoPastoral::find($atendimentoId);

        if (!$atendimento || $atendimento->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Atendimento pastoral não encontrado.'
            ]);
            return;
        }

        $atendimento->delete();
        $this->carregarAtendimentos();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Atendimento pastoral excluído com sucesso!'
        ]);
    }


    protected function resetModal()
    {
        $this->atendimentoSelecionado = null;
        $this->membro_id = '';
        $this->pastor_id = '';
        $this->tipo = '';
        $this->descricao = '';
        $this->data_atendimento = '';
        $this->resetValidation();
    }

    public function updatedFiltroTipo()
    {
        $this->carregarAtendimentos();
    }

    public function updatedFiltroPastor()
    {
        $this->carregarAtendimentos();
    }

    public function updatedFiltroMembro()
    {
        $this->carregarAtendimentos();
    }

    public function getTiposAtendimentoProperty()
    {
        return [
            'aconselhamento' => 'Aconselhamento',
            'visita' => 'Visita Domiciliar',
            'oracao' => 'Oração',
            'encorajamento' => 'Encorajamento',
            'outro' => 'Outro',
        ];
    }

    public function getMembrosDisponiveisProperty()
    {
        return IgrejaMembro::where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'ativo')
            ->whereNotIn('cargo', ['admin', 'pastor' ])
            ->with('user')
            ->get()
            ->map(function($membro) {
                return [
                    'id' => $membro->id,
                    'nome' => $membro->user->name,
                    'cargo' => $membro->cargo,
                ];
            });
    }

    public function getPastoresDisponiveisProperty()
    {
        // Buscar apenas membros da mesma igreja com cargo pastor, admin ou ministro
        return IgrejaMembro::where('igreja_id', Auth::user()->getIgrejaId())
            ->whereIn('cargo', ['pastor', 'admin', 'ministro' ])
            ->where('status', 'ativo')
            ->with('user')
            ->get()
            ->map(function($membro) {
                return [
                    'id' => $membro->user_id,
                    'nome' => $membro->user->name,
                    'cargo' => $membro->cargo,
                ];
            });
    }

    public function render()
    {
        return view('church.only.pastoral-care', [
            'tiposAtendimento' => $this->tiposAtendimento,
            'membrosDisponiveis' => $this->membrosDisponiveis,
            'pastoresDisponiveis' => $this->pastoresDisponiveis,
        ]);
    }
}
