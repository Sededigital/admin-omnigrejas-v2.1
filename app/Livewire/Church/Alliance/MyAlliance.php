<?php

namespace App\Livewire\Church\Alliance;

use Livewire\Component;
use App\Models\Igrejas\AliancaIgreja;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\Igrejas\IgrejaAlianca;
use App\Models\Igrejas\AliancaLider;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Minhas Alianças | Portal da Igreja')]
#[Layout('components.layouts.app')]
class MyAlliance extends Component
{
    // Propriedades para filtros
    public $statusFilter = '';
    public $categoriaFilter = '';
    public $orderBy = 'created_at';
    public $orderDirection = 'desc';
    public $perPage = 12;

    // Propriedades da igreja atual
    public $igreja;
    public $stats = [];

    // Propriedades para o modal de criação/edição
    public $showModal = false;
    public $editingAliancaId = null;
    public $isEditing = false;

    // Campos do formulário
    public $nome = '';
    public $sigla = '';
    public $descricao = '';
    public $categoria_id = null;
    public $limite_membros = null;

    protected $rules = [
        'nome' => 'required|string|max:150',
        'sigla' => 'nullable|string|max:20',
        'descricao' => 'required|string|max:1000',
        'categoria_id' => 'nullable|exists:categorias_igrejas,id',
        'limite_membros' => 'nullable|integer|min:1|max:10000000',
    ];

    public function mount()
    {
        $this->carregarIgreja();
        $this->carregarStats();
    }

    protected function carregarIgreja()
    {
        $this->igreja = Auth::user()->getIgreja();
    }

    protected function carregarStats()
    {
        if (!$this->igreja) return;

        $aliancasCriadas = AliancaIgreja::where('created_by', Auth::id())->get();

        $this->stats = [
            'total' => $aliancasCriadas->count(),
            'aprovadas' => $aliancasCriadas->where('status', 'aprovada')->count(),
            'pendentes' => $aliancasCriadas->where('status', 'pendente_validacao')->count(),
            'prontas' => $aliancasCriadas->where('status', 'pronta_aprovacao')->count(),
            'aderentes_total' => $aliancasCriadas->sum('aderentes_count'),
        ];
    }

    public function criarNovaAlianca()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    public function editarAlianca($aliancaId)
    {
        $alianca = AliancaIgreja::where('id', $aliancaId)
            ->where('created_by', Auth::id())
            ->first();

        if ($alianca) {
            $this->editingAliancaId = $aliancaId;
            $this->isEditing = true;

            // Preencher os campos
            $this->nome = $alianca->nome;
            $this->sigla = $alianca->sigla;
            $this->descricao = $alianca->descricao;
            $this->categoria_id = $alianca->categoria_id;
            $this->limite_membros = $alianca->limite_membros;

            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingAliancaId = null;
        $this->isEditing = false;
        $this->nome = '';
        $this->sigla = '';
        $this->descricao = '';
        $this->categoria_id = null;
        $this->limite_membros = null;
        $this->resetErrorBag();
    }


    public function salvarAlianca()
    {
        $this->validate();

        try {
    
            $data = [
                'nome' => $this->nome,
                'sigla' => $this->sigla,
                'descricao' => $this->descricao,
                'status'=>'aprovada',
                'categoria_id' => Auth::user()->getIgreja()->categoria_id,
                'limite_membros' => $this->limite_membros,
                'created_by' => Auth::id(),
            ];

            if ($this->isEditing) {
                // Atualizar aliança existente
                $alianca = AliancaIgreja::where('id', $this->editingAliancaId)
                    ->where('created_by', Auth::id())
                    ->first();

                if ($alianca) {
                    $alianca->update($data);

                    $this->dispatch('toast', [
                        'type' => 'success',
                        'message' => 'Aliança atualizada com sucesso!'
                    ]);
                }
            } else {
                // Criar nova aliança
                DB::beginTransaction();
                try {
                    $alianca = AliancaIgreja::create($data);

                    // Adicionar a igreja do criador como participante
                    $participacao = IgrejaAlianca::create([
                        'igreja_id' => $this->igreja->id,
                        'alianca_id' => $alianca->id,
                        'status' => 'ativo',
                        'data_adesao' => now(),
                        'created_by' => Auth::id(),
                    ]);

                    // Adicionar o criador como líder da aliança
                    $membroCriador = IgrejaMembro::where('igreja_id', $this->igreja->id)
                        ->where('user_id', Auth::id())
                        ->where('status', 'ativo')
                        ->first();

                    if ($membroCriador) {
                        AliancaLider::create([
                            'igreja_alianca_id' => $participacao->id,
                            'membro_id' => $membroCriador->id,
                            'cargo_na_alianca' => 'admin', // Criador é admin da aliança
                            'ativo' => true,
                            'data_adesao' => now(),
                        ]);
                    }

                    // Atualizar contador de aderentes da aliança recém-criada
                    $alianca->fresh()->atualizarContadorAderentes();

                    DB::commit();

                    $this->dispatch('toast', [
                        'type' => 'success',
                        'message' => 'Aliança criada com sucesso! Sua igreja foi adicionada automaticamente.'
                    ]);

                } catch (\Exception $e) {
                    DB::rollback();
                    $this->dispatch('toast', [
                        'type' => 'error',
                        'message' => 'Erro ao criar aliança: ' . $e->getMessage()
                    ]);
                    return;
                }
            }

            $this->carregarStats();
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Erro ao salvar aliança: ' . $e->getMessage()
            ]);
        }
    }

    public function excluirAlianca($aliancaId)
    {
        $alianca = AliancaIgreja::where('id', $aliancaId)
            ->where('created_by', Auth::id())
            ->first();

        if ($alianca && $alianca->aderentes_count === 0) {
            $alianca->delete();
            $this->carregarStats();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Aliança excluída com sucesso!'
            ]);
        } else {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Não é possível excluir uma aliança com membros.'
            ]);
        }
    }

    public function getMinhasAliancasProperty()
    {
        $query = AliancaIgreja::with(['categoria', 'aprovador'])
            ->where('created_by', Auth::id());

        // Aplicar filtros
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoriaFilter) {
            $query->where('categoria_id', $this->categoriaFilter);
        }

        // Ordenação
        $query->orderBy($this->orderBy, $this->orderDirection);

        return $query->paginate($this->perPage);
    }

    public function getCategoriasProperty()
    {
        return CategoriaIgreja::where('ativa', true)->orderBy('nome')->get();
    }

    public function render()
    {
        return view('church.alliance.my-alliance', [
            'minhasAliancas' => $this->minhasAliancas,
            'categorias' => $this->categorias,
        ]);
    }
}
