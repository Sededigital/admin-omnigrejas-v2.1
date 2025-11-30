<?php

namespace App\Livewire\Church\Members;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Igrejas\Voluntario;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;

#[Title('Voluntários | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Volunteers extends Component
{
    // Propriedades para listagem
    public $voluntarios = [];
    public $membroAtual;

    // Propriedades para modal
    public $isEditing = false;
    public $voluntarioSelecionado = null;

    // Propriedades do formulário
    #[Rule('required|exists:igreja_membros,id')]
    public $membro_id = '';

    #[Rule('required|string|max:255')]
    public $area_interesse = '';

    #[Rule('required|string|max:255')]
    public $disponibilidade = '';

    public $ativo = true;

    // Propriedades para filtros
    public $filtroAtivo = 'todos'; // 'todos', 'ativos', 'inativos'
    public $filtroArea = '';

    public function mount()
    {
        $this->carregarMembroAtual();
        $this->carregarVoluntarios();
    }

    protected function carregarMembroAtual()
    {
        $this->membroAtual = IgrejaMembro::where('user_id', Auth::id())
            ->where('status', 'ativo')
            ->where('cargo', '!=', 'membro')
            ->first();

        if (!$this->membroAtual) {
            abort(403, 'Acesso negado. Apenas líderes ativos podem acessar voluntários.');
        }
    }

    protected function carregarVoluntarios()
    {
        $query = Voluntario::with(['membro.user', 'membro.igreja'])
            ->whereHas('membro', function($query) {
                $query->where('igreja_id', Auth::user()->getIgrejaId());
            });

        // Aplicar filtros
        if ($this->filtroAtivo === 'ativos') {
            $query->where('ativo', true);
        } elseif ($this->filtroAtivo === 'inativos') {
            $query->where('ativo', false);
        }

        if ($this->filtroArea) {
            $query->where('area_interesse', 'ILIKE', '%' . $this->filtroArea . '%');
        }

        $this->voluntarios = $query->orderBy('created_at', 'desc')->get();
    }

    public function openModal($voluntarioId = null)
    {
        $this->resetModal();

        if ($voluntarioId) {
            $voluntario = Voluntario::find($voluntarioId);

            if (!$voluntario || $voluntario->membro->igreja_id !== Auth::user()->getIgrejaId()) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'message' => 'Voluntário não encontrado.'
                ]);
                return;
            }

            $this->voluntarioSelecionado = $voluntario;
            $this->membro_id = $voluntario->membro_id;
            $this->area_interesse = $voluntario->area_interesse;
            $this->disponibilidade = $voluntario->disponibilidade;
            $this->ativo = $voluntario->ativo;
            $this->isEditing = true;
        } else {
            $this->isEditing = false;
        }

        $this->dispatch('open-volunteer-modal');
    }

    public function salvarVoluntario()
    {
        $this->validate();

        // Verificar se o membro pertence à igreja do usuário
        $membro = IgrejaMembro::find($this->membro_id);
        if (!$membro || $membro->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->addError('membro_id', 'Membro não encontrado ou não pertence à sua igreja.');
            return;
        }

        // Verificar se já existe um voluntário para este membro
        $voluntarioExistente = Voluntario::where('membro_id', $this->membro_id)->first();
        if ($voluntarioExistente && (!$this->isEditing || $voluntarioExistente->id !== $this->voluntarioSelecionado->id)) {
            $this->addError('membro_id', 'Este membro já está cadastrado como voluntário.');
            return;
        }

        if ($this->isEditing) {
            $this->voluntarioSelecionado->update([
                'area_interesse' => $this->area_interesse,
                'disponibilidade' => $this->disponibilidade,
                'ativo' => $this->ativo,
            ]);

            $mensagem = 'Voluntário atualizado com sucesso!';
        } else {
            Voluntario::create([
                'membro_id' => $this->membro_id,
                'area_interesse' => $this->area_interesse,
                'disponibilidade' => $this->disponibilidade,
                'ativo' => $this->ativo,
            ]);

            $mensagem = 'Voluntário cadastrado com sucesso!';
        }

        $this->carregarVoluntarios();
        $this->dispatch('close-volunteer-modal');

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function toggleStatus($voluntarioId)
    {
        $voluntario = Voluntario::find($voluntarioId);

        if (!$voluntario || $voluntario->membro->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Voluntário não encontrado.'
            ]);
            return;
        }

        $voluntario->update(['ativo' => !$voluntario->ativo]);

        $status = $voluntario->ativo ? 'ativado' : 'desativado';

        $this->carregarVoluntarios();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Voluntário {$status} com sucesso!"
        ]);
    }

    public function excluirVoluntario($voluntarioId)
    {
        $voluntario = Voluntario::find($voluntarioId);

        if (!$voluntario || $voluntario->membro->igreja_id !== Auth::user()->getIgrejaId()) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Voluntário não encontrado.'
            ]);
            return;
        }

        $voluntario->delete();
        $this->carregarVoluntarios();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Voluntário excluído com sucesso!'
        ]);
    }


    protected function resetModal()
    {
        $this->voluntarioSelecionado = null;
        $this->membro_id = '';
        $this->area_interesse = '';
        $this->disponibilidade = '';
        $this->ativo = true;
        $this->resetValidation();
    }

    public function updatedFiltroAtivo()
    {
        $this->carregarVoluntarios();
    }

    public function updatedFiltroArea()
    {
        $this->carregarVoluntarios();
    }

    public function getMembrosDisponiveisProperty()
    {
        return IgrejaMembro::where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'ativo')
            ->whereDoesntHave('voluntario') // Apenas membros que não são voluntários
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

    public function render()
    {
        return view('church.members.volunteers', [
            'membrosDisponiveis' => $this->membrosDisponiveis,
        ]);
    }
}
