<?php

namespace App\Livewire\Church\Ministries;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Igrejas\Ministerio;
use App\Models\Igrejas\Igreja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\RBAC\PermissionHelper;

#[Title('Ministérios | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Ministries extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ========================================
    // PROPRIEDADES PARA MINISTÉRIOS
    // ========================================
    public $ministerioSelecionado = null;
    public $isEditingMinisterio = false;

    // Filtros para ministérios
    public $filtroMinisterioStatus = '';
    public $filtroMinisterioBusca = '';

    // Formulário de ministérios
    #[Rule('required|string|max:100')]
    public $ministerioNome = '';

    #[Rule('nullable|string|max:500')]
    public $ministerioDescricao = '';

    #[Rule('boolean')]
    public $ministerioAtivo = true;

    // ========================================
    // PROPRIEDADES GERAIS
    // ========================================
    public $igrejaAtual;
    public $confirmacaoExclusao = false;
    public $itemParaExcluir = null;

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'cancelarExclusao' => 'cancelarExclusao'
    ];

    public function mount()
    {
        // Verificar permissões de acesso
        if (!PermissionHelper::hasFullAccess(Auth::user())) {
            abort(403, 'Acesso negado. Você não tem permissão para gerenciar ministérios.');
        }

        $this->carregarIgrejaAtual();
    }

    protected function carregarIgrejaAtual()
    {
        $user = Auth::user();
        $this->igrejaAtual = $user->getIgreja();

        if (!$this->igrejaAtual) {
            abort(403, 'Usuário não está associado a nenhuma igreja.');
        }
    }

    protected function getMinisteriosQuery()
    {
        $query = Ministerio::where('igreja_id', $this->igrejaAtual->id)
            ->with(['membros'])
            ->withCount('membros');

        if ($this->filtroMinisterioStatus !== '') {
            $query->where('ativo', $this->filtroMinisterioStatus === 'ativo');
        }

        if ($this->filtroMinisterioBusca) {
            $query->where(function($q) {
                $q->where('nome', 'ILIKE', '%' . $this->filtroMinisterioBusca . '%')
                  ->orWhere('descricao', 'ILIKE', '%' . $this->filtroMinisterioBusca . '%');
            });
        }

        return $query->orderBy('nome');
    }

    public function abrirModalMinisterio($ministerioId = null)
    {
        $this->resetModalMinisterio();

        if ($ministerioId) {
            $ministerio = Ministerio::find($ministerioId);

            if (!$ministerio || $ministerio->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Ministério não encontrado.'
                ]);
                return;
            }

            $this->ministerioSelecionado = $ministerio;
            $this->ministerioNome = $ministerio->nome;
            $this->ministerioDescricao = $ministerio->descricao;
            $this->ministerioAtivo = $ministerio->ativo;
            $this->isEditingMinisterio = true;
        } else {
            $this->isEditingMinisterio = false;
        }

        $this->dispatch('open-ministerio-modal');
    }

    public function salvarMinisterio()
    {
        $this->validate();

        try {
            // Verificar unicidade do nome na igreja
            if (!$this->isEditingMinisterio) {
                $exists = Ministerio::where('igreja_id', $this->igrejaAtual->id)
                    ->where('nome', $this->ministerioNome)
                    ->exists();

                if ($exists) {
                    $this->addError('ministerioNome', 'Já existe um ministério com este nome nesta igreja.');
                    return;
                }
            }

            if ($this->isEditingMinisterio) {
                $this->ministerioSelecionado->update([
                    'nome' => $this->ministerioNome,
                    'descricao' => $this->ministerioDescricao,
                    'ativo' => $this->ministerioAtivo,
                ]);

                $mensagem = 'Ministério atualizado com sucesso!';
            } else {
                Ministerio::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'nome' => $this->ministerioNome,
                    'descricao' => $this->ministerioDescricao,
                    'ativo' => $this->ministerioAtivo,
                ]);

                $mensagem = 'Ministério criado com sucesso!';
            }

            $this->dispatch('close-ministerio-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar ministério: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'dados' => [
                    'nome' => $this->ministerioNome,
                    'is_editing' => $this->isEditingMinisterio
                ]
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar ministério. Verifique os campos.'
            ]);
        }
    }

    public function excluirMinisterio($ministerioId)
    {
        try {
            $ministerio = Ministerio::find($ministerioId);

            if (!$ministerio || $ministerio->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Ministério não encontrado.'
                ]);
                return;
            }

            // Verificar se o ministério tem membros
            if ($ministerio->membros()->exists()) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Este ministério não pode ser excluído pois possui membros associados.'
                ]);
                return;
            }

            $ministerio->delete();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Ministério excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir ministério: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'ministerio_id' => $ministerioId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir ministério. Tente novamente.'
            ]);
        }
    }

    protected function resetModalMinisterio()
    {
        $this->ministerioSelecionado = null;
        $this->ministerioNome = '';
        $this->ministerioDescricao = '';
        $this->ministerioAtivo = true;
        $this->resetValidation();
    }

    // ========================================
    // LISTENERS PARA FILTROS
    // ========================================

    public function updatedFiltroMinisterioStatus()
    {
        $this->resetPage();
    }

    public function updatedFiltroMinisterioBusca()
    {
        $this->resetPage();
    }

    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================

    public function getMinisteriosProperty()
    {
        return $this->getMinisteriosQuery()->paginate(15);
    }

    public function confirmarExclusao($ministerioId)
    {

        $this->itemParaExcluir = $ministerioId;
        $this->confirmacaoExclusao = true;
        $this->dispatch('confirmarExclusao');
    }

    public function cancelarExclusao()
    {
        $this->confirmacaoExclusao = false;
        $this->itemParaExcluir = null;
    }

    public function executarExclusao()
    {
        if ($this->itemParaExcluir) {
            $this->excluirMinisterio($this->itemParaExcluir);
            $this->cancelarExclusao();
        }
    }

    public function render()
    {
        return view('church.ministries.ministries', [
            'ministerios' => $this->ministerios,
        ]);
    }
}
