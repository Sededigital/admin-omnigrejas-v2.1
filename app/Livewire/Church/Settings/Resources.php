<?php

namespace App\Livewire\Church\Settings;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Outros\Recurso;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Recursos | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Resources extends Component
{
    // Propriedades para listagem
    public $recursos = [];
    public $membroAtual;

    // Propriedades para modal
    public $isEditing = false;
    public $recursoSelecionado = null;

    // Propriedades do formulário
    #[Rule('required|string|max:255')]
    public $nome = '';

    #[Rule('required|in:sala,equipamento,material,outro')]
    public $tipo = '';

    #[Rule('nullable|string|max:1000')]
    public $descricao = '';

    public $disponivel = true;

    // Propriedades para filtros
    public $filtroTipo = '';
    public $filtroDisponivel = 'todos'; // 'todos', 'disponiveis', 'indisponiveis'

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarRecursos();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar recursos.');
        }
    }

    protected function carregarRecursos()
    {
        $query = Recurso::where('igreja_id', $this->membroAtual->igreja_id);

        // Aplicar filtros
        if ($this->filtroTipo) {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->filtroDisponivel === 'disponiveis') {
            $query->where('disponivel', true);
        } elseif ($this->filtroDisponivel === 'indisponiveis') {
            $query->where('disponivel', false);
        }

        $this->recursos = $query->orderBy('nome')->get();
    }

    public function openModal($recursoId = null)
    {
        $this->resetModal();

        if ($recursoId) {
            $recurso = Recurso::find($recursoId);

            if (!$recurso || $recurso->igreja_id !== $this->membroAtual->igreja_id) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Recurso não encontrado.'
                ]);
                return;
            }

            $this->recursoSelecionado = $recurso;
            $this->nome = $recurso->nome;
            $this->tipo = $recurso->tipo;
            $this->descricao = $recurso->descricao;
            $this->disponivel = $recurso->disponivel;
            $this->isEditing = true;
        } else {
            $this->isEditing = false;
        }

        $this->dispatch('open-resource-modal');
    }

    public function salvarRecurso()
    {
        $this->validate();

        if ($this->isEditing) {
            $this->recursoSelecionado->update([
                'nome' => $this->nome,
                'tipo' => $this->tipo,
                'descricao' => $this->descricao,
                'disponivel' => $this->disponivel,
            ]);

            $mensagem = 'Recurso atualizado com sucesso!';
        } else {
            Recurso::create([
                'igreja_id' => $this->membroAtual->igreja_id,
                'nome' => $this->nome,
                'tipo' => $this->tipo,
                'descricao' => $this->descricao,
                'disponivel' => $this->disponivel,
            ]);

            $mensagem = 'Recurso cadastrado com sucesso!';
        }

        $this->carregarRecursos();
        $this->dispatch('close-resource-modal');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function toggleDisponibilidade($recursoId)
    {
        $recurso = Recurso::find($recursoId);

        if (!$recurso || $recurso->igreja_id !== $this->membroAtual->igreja_id) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Recurso não encontrado.'
            ]);
            return;
        }

        $recurso->update(['disponivel' => !$recurso->disponivel]);

        $status = $recurso->disponivel ? 'disponibilizado' : 'indisponibilizado';

        $this->carregarRecursos();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Recurso {$status} com sucesso!"
        ]);
    }

    public function excluirRecurso($recursoId)
    {
        $recurso = Recurso::find($recursoId);

        if (!$recurso || $recurso->igreja_id !== $this->membroAtual->igreja_id) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Recurso não encontrado.'
            ]);
            return;
        }

        $recurso->delete();
        $this->carregarRecursos();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Recurso excluído com sucesso!'
        ]);
    }


    protected function resetModal()
    {
        $this->recursoSelecionado = null;
        $this->nome = '';
        $this->tipo = '';
        $this->descricao = '';
        $this->disponivel = true;
        $this->resetValidation();
    }

    public function updatedFiltroTipo()
    {
        $this->carregarRecursos();
    }

    public function updatedFiltroDisponivel()
    {
        $this->carregarRecursos();
    }

    public function getTiposRecursosProperty()
    {
        return [
            'sala' => 'Sala',
            'equipamento' => 'Equipamento',
            'material' => 'Material',
            'outro' => 'Outro',
        ];
    }

    public function render()
    {
        return view('church.settings.resources', [
            'tiposRecursos' => $this->tiposRecursos,
        ]);
    }
}
