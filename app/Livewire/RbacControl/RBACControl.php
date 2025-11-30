<?php

namespace App\Livewire\RbacControl;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\RBAC\IgrejaFuncao;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\RBAC\IgrejaPermissao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Helpers\RBAC\PermissionHelper;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\RBAC\IgrejaMembroFuncao;
use App\Models\RBAC\IgrejaPermissaoLog;

#[Title('Controle de sistema | Portal da Igreja')]
#[Layout('components.layouts.app')]
class RBACControl extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    // ========================================
    // PROPRIEDADES PARA NAVEGAÇÃO ENTRE ABAS
    // ========================================
    public $abaAtiva = 'permissoes'; // permissoes, funcoes, atribuicoes, logs
    public $abasDisponiveis = []; // Array com abas que o usuário pode acessar

    // ========================================
    // PROPRIEDADES PARA PERMISSÕES
    // ========================================
    public $permissaoSelecionada = null;
    public $isEditingPermissao = false;
    private $permissoesCache = null;

    // Filtros para permissões
    public $filtroPermissaoIgreja = '';
    public $filtroPermissaoCategoria = '';
    public $filtroPermissaoStatus = '';
    public $filtroPermissaoBusca = '';

    // Formulário de permissões
    public $permissaoNome = '';

    public $permissaoDescricao = '';

    public $permissaoCodigo = '';

    public $permissaoCategoria = '';

    public $permissaoNivel = 'medio'; // Será convertido para numero na hora de salvar

    public $permissaoAtiva = true;

    // ========================================
    // PROPRIEDADES PARA FUNÇÕES
    // ========================================
    public $funcaoSelecionada = null;
    public $isEditingFuncao = false;

    // Filtros para funções
    public $filtroFuncaoIgreja = '';
    public $filtroFuncaoStatus = '';
    public $filtroFuncaoBusca = '';

    // Formulário de funções
    public $funcaoNome = '';

    public $funcaoDescricao = '';

    public $funcaoNivel = 'medio';

    public $funcaoAtiva = true;

    public $funcaoPermissoesSelecionadas = [];

    // ========================================
    // PROPRIEDADES PARA ATRIBUIÇÕES
    // ========================================

    public $membroSelecionado = null;
    public $funcoesDisponiveis = [];

    // Filtros para atribuições
    public $filtroAtribuicaoMembro = '';
    public $filtroAtribuicaoFuncao = '';
    public $filtroAtribuicaoStatus = '';

    // Formulário de atribuição
    #[Rule('required|exists:igreja_membros,id')]
    public $atribuicaoMembroId = '';

    #[Rule('required|exists:igreja_funcoes,id')]
    public $atribuicaoFuncaoId = '';

    #[Rule('nullable|date')]
    public $atribuicaoValidoAte = '';

    #[Rule('nullable|string|max:500')]
    public $atribuicaoObservacoes = '';

    // ========================================
    // PROPRIEDADES PARA LOGS
    // ========================================


    // Filtros para logs
    public $filtroLogAcao = '';
    public $filtroLogUsuario = '';
    public $filtroLogDataInicio = '';
    public $filtroLogDataFim = '';

    // ========================================
    // PROPRIEDADES GERAIS
    // ========================================
    public $igrejaAtual;
    public $categorias = [];
    public $confirmacaoExclusao = false;
    public $itemParaExcluir = null;
    public $tipoExclusao = ''; // permissao, funcao, atribuicao

    // Propriedades para modal de confirmação genérico
    public $confirmacaoAcao = false;
    public $acaoParaConfirmar = ''; // revogar_funcao, reativar_funcao, excluir_permissao, etc.
    public $itemParaConfirmar = null;
    public $mensagemConfirmacao = '';
    public $confirmacaoLoading = false; // Controla o spinner no botão

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'confirmarExclusao' => 'confirmarExclusao',
        'cancelarExclusao' => 'cancelarExclusao',
        'confirmarAcao' => 'confirmarAcao',
        'cancelarAcao' => 'cancelarAcao'
    ];

    public function mount()
    {
        // Verificar permissões de acesso - Admins têm acesso total
        if (!PermissionHelper::hasFullAccess(Auth::user())) {
            abort(403, 'Acesso negado. Você não tem permissão para gerenciar permissões.');
        }

        $this->carregarIgrejaAtual();
        $this->carregarDadosIniciais();

        // Determinar abas disponíveis baseado no role do usuário
        $this->determinarAbasDisponiveis();

        // Definir aba inicial baseada no role
        $this->definirAbaInicial();

        // Carregar dados da aba ativa
        $this->carregarDadosAbaAtiva();
    }

    protected function carregarIgrejaAtual()
    {
        $user = Auth::user();
        $this->igrejaAtual = $user->getIgreja();

        if (!$this->igrejaAtual) {
            abort(403, 'Usuário não está associado a nenhuma igreja.');
        }
    }

    protected function carregarDadosIniciais()
    {
        // Carregar categorias disponíveis
        $this->categorias = Cache::remember('rbac_categorias', 3600, function () {
            return CategoriaIgreja::ativas()->get();
        });

        // Verificar e criar permissões padrão se necessário
        $this->verificarPermissoesPadrao();
    }

    protected function verificarPermissoesPadrao()
    {
        // Verificar se já existem permissões para esta igreja
        $permissoesCount = IgrejaPermissao::where('igreja_id', $this->igrejaAtual->id)->count();

        if ($permissoesCount === 0) {
            // Criar permissões padrão usando a função do banco
            try {
                DB::statement("SELECT criar_permissoes_padrao(?)", [$this->igrejaAtual->id]);

                // Limpar cache
                PermissionHelper::invalidatePermissionCache();

                // Log da criação
                IgrejaPermissaoLog::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'acao' => 'criacao_permissoes_padrao',
                    'detalhes' => [
                        'motivo' => 'Permissões padrão criadas automaticamente no primeiro acesso ao RBAC',
                        'quantidade_permissoes' => 25, // Baseado no SQL
                    ],
                    'realizado_por' => Auth::id(),
                    'realizado_em' => now(),
                ]);

            } catch (\Exception $e) {
                // Log do erro mas não interrompe o fluxo
                Log::error('Erro ao criar permissões padrão: ' . $e->getMessage());
            }
        }
    }

    protected function determinarAbasDisponiveis()
    {
        $user = Auth::user();

        // ROOT tem acesso a todas as abas
        if ($user->isRoot()) {
            $this->abasDisponiveis = ['permissoes', 'funcoes', 'atribuicoes', 'logs'];
            return;
        }

        // Super Admin, Admin e Pastor têm acesso apenas a Funções e Atribuições
        if ($user->hasAnyRole(['super_admin', 'admin', 'pastor' ])) {
            $this->abasDisponiveis = ['funcoes', 'atribuicoes'];
            return;
        }

        // Outros roles não têm acesso ao RBAC (fallback)
        $this->abasDisponiveis = [];
    }

    protected function definirAbaInicial()
    {
        $user = Auth::user();

        // Se for ROOT, começa com permissões
        if ($user->isRoot()) {
            $this->abaAtiva = 'permissoes';
            return;
        }

        // Para outros roles, começa com funções (se disponível)
        if (in_array('funcoes', $this->abasDisponiveis)) {
            $this->abaAtiva = 'funcoes';
        } elseif (count($this->abasDisponiveis) > 0) {
            $this->abaAtiva = $this->abasDisponiveis[0]; // Primeira aba disponível
        } else {
            $this->abaAtiva = 'funcoes'; // Fallback
        }
    }

    public function carregarDadosAbaAtiva()
    {
        // Método mantido para compatibilidade, mas dados são carregados no render()
        $this->resetPage();
    }

    // ========================================
    // MÉTODOS PARA PERMISSÕES
    // ========================================


    protected function getFuncoesQuery()
    {
        $query = IgrejaFuncao::with(['permissoes', 'membroFuncoes'])
            ->withCount('membrosAtivos')
            ->where('igreja_id', $this->igrejaAtual->id);

        if ($this->filtroFuncaoStatus !== '') {
            $query->where('ativo', $this->filtroFuncaoStatus === 'ativo');
        }

        if ($this->filtroFuncaoBusca) {
            $query->where(function($q) {
                $q->where('nome', 'ILIKE', '%' . $this->filtroFuncaoBusca . '%')
                  ->orWhere('descricao', 'ILIKE', '%' . $this->filtroFuncaoBusca . '%');
            });
        }

        return $query->orderBy('nome');
    }

    protected function getAtribuicoesQuery()
    {
        $query = IgrejaMembroFuncao::with(['membro.user', 'funcao'])
            ->whereHas('membro', function($q) {
                $q->where('igreja_id', $this->igrejaAtual->id);
            });

        if ($this->filtroAtribuicaoMembro) {
            $query->where('membro_id', $this->filtroAtribuicaoMembro);
        }

        if ($this->filtroAtribuicaoFuncao) {
            $query->where('funcao_id', $this->filtroAtribuicaoFuncao);
        }

        if ($this->filtroAtribuicaoStatus !== '') {
            $query->where('status', $this->filtroAtribuicaoStatus);
        }

        return $query->orderBy('created_at', 'desc');
    }

    protected function getLogsQuery()
    {
        $query = IgrejaPermissaoLog::with(['realizadoPor', 'permissao', 'funcao', 'membro.user'])
            ->where('igreja_id', $this->igrejaAtual->id);

        if ($this->filtroLogAcao) {
            $query->where('acao', $this->filtroLogAcao);
        }

        if ($this->filtroLogUsuario) {
            $query->where('realizado_por', $this->filtroLogUsuario);
        }

        if ($this->filtroLogDataInicio) {
            $query->whereDate('realizado_em', '>=', $this->filtroLogDataInicio);
        }

        if ($this->filtroLogDataFim) {
            $query->whereDate('realizado_em', '<=', $this->filtroLogDataFim);
        }

        return $query->orderBy('realizado_em', 'desc');
    }

    protected function getPermissoesQuery()
    {
        $query = IgrejaPermissao::where('igreja_id', $this->igrejaAtual->id);

        // Aplicar filtros
        if ($this->filtroPermissaoCategoria) {
            $query->where('categoria', $this->filtroPermissaoCategoria);
        }

        if ($this->filtroPermissaoStatus !== '') {
            $query->where('ativo', $this->filtroPermissaoStatus === 'ativo');
        }

        if ($this->filtroPermissaoBusca) {
            $query->where(function($q) {
                $q->where('nome', 'ILIKE', '%' . $this->filtroPermissaoBusca . '%')
                  ->orWhere('codigo', 'ILIKE', '%' . $this->filtroPermissaoBusca . '%')
                  ->orWhere('descricao', 'ILIKE', '%' . $this->filtroPermissaoBusca . '%');
            });
        }

        return $query->orderBy('nome');
    }

    public function abrirModalPermissao($permissaoId = null)
    {
        $this->resetModalPermissao();

        if ($permissaoId) {
            $permissao = IgrejaPermissao::find($permissaoId);

            if (!$permissao || $permissao->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Permissão não encontrada.'
                ]);
                return;
            }

            $this->permissaoSelecionada = $permissao;
            $this->permissaoNome = $permissao->nome;
            $this->permissaoDescricao = $permissao->descricao;
            $this->permissaoCodigo = $permissao->codigo;
            $this->permissaoCategoria = $permissao->categoria;
            $this->permissaoNivel = $permissao->getNivelString();
            $this->permissaoAtiva = $permissao->ativo;
            $this->isEditingPermissao = true;
        } else {
            $this->isEditingPermissao = false;
        }

        $this->dispatch('open-permissao-modal');
    }

    public function salvarPermissao()
    {
        // Validações manuais
        $this->validate([
            'permissaoNome' => 'required|string|max:100',
            'permissaoDescricao' => 'required|string|max:255',
            'permissaoCodigo' => 'required|string|max:50',
            'permissaoCategoria' => 'required|in:admin,visualizacao,edicao',
            'permissaoNivel' => 'required|in:baixo,medio,alto,critico',
            'permissaoAtiva' => 'boolean',
        ]);

        // Converter nível string para número
        $nivelHierarquia = IgrejaPermissao::convertStringToNumber($this->permissaoNivel);

        try {
            // Verificar unicidade do código (exceto para edição)
            if (!$this->isEditingPermissao) {
                $exists = IgrejaPermissao::where('igreja_id', $this->igrejaAtual->id)
                    ->where('codigo', $this->permissaoCodigo)
                    ->exists();

                if ($exists) {
                    $this->addError('permissaoCodigo', 'Este código já está em uso nesta igreja.');
                    return;
                }
            }

            if ($this->isEditingPermissao) {
                $this->permissaoSelecionada->update([
                    'nome' => $this->permissaoNome,
                    'descricao' => $this->permissaoDescricao,
                    'codigo' => $this->permissaoCodigo,
                    'categoria' => $this->permissaoCategoria,
                    'nivel_hierarquia' => $nivelHierarquia,
                    'ativo' => $this->permissaoAtiva,
                ]);

                // Log de atualização
                IgrejaPermissaoLog::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'permissao_id' => $this->permissaoSelecionada->id,
                    'acao' => 'atualizar_permissao',
                    'detalhes' => [
                        'campos_alterados' => ['nome', 'descricao', 'codigo', 'categoria', 'nivel', 'ativo'],
                        'valores_anteriores' => [
                            'nome' => $this->permissaoSelecionada->getOriginal('nome'),
                            'codigo' => $this->permissaoSelecionada->getOriginal('codigo'),
                        ],
                        'valores_novos' => [
                            'nome' => $this->permissaoNome,
                            'codigo' => $this->permissaoCodigo,
                        ]
                    ],
                    'realizado_por' => Auth::id(),
                    'realizado_em' => now(),
                ]);

                $mensagem = 'Permissão atualizada com sucesso!';
            } else {
                $permissao = IgrejaPermissao::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'nome' => $this->permissaoNome,
                    'descricao' => $this->permissaoDescricao,
                    'codigo' => $this->permissaoCodigo,
                    'categoria' => $this->permissaoCategoria,
                    'nivel_hierarquia' => $nivelHierarquia,
                    'ativo' => $this->permissaoAtiva,
                    'created_by' => Auth::id(),
                ]);

                // Log de criação
                IgrejaPermissaoLog::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'permissao_id' => $permissao->id,
                    'acao' => 'criar_permissao',
                    'detalhes' => [
                        'nome' => $this->permissaoNome,
                        'codigo' => $this->permissaoCodigo,
                        'categoria' => $this->permissaoCategoria,
                    ],
                    'realizado_por' => Auth::id(),
                    'realizado_em' => now(),
                ]);

                $mensagem = 'Permissão criada com sucesso!';
            }

            // Limpar cache
            PermissionHelper::invalidatePermissionCache();

            $this->dispatch('close-permissao-modal');
            $this->dispatch('close-confirmacao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar permissão: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'dados' => [
                    'nome' => $this->permissaoNome,
                    'codigo' => $this->permissaoCodigo,
                    'is_editing' => $this->isEditingPermissao
                ]
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar permissão. Verifique os campos.'
            ]);
        }
    }

    public function excluirPermissao($permissaoId)
    {
        try {
            $permissao = IgrejaPermissao::find($permissaoId);

            if (!$permissao || $permissao->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Permissão não encontrada.'
                ]);
                return;
            }

            // Verificar se a permissão está sendo usada por alguma função
            $estaSendoUsada = $permissao->funcoes()->exists();

            if ($estaSendoUsada) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Esta permissão não pode ser excluída pois está sendo usada por uma ou mais funções.'
                ]);
                return;
            }

            $permissao->delete();

            // Log de exclusão
            IgrejaPermissaoLog::create([
                'igreja_id' => $this->igrejaAtual->id,
                'acao' => 'excluir_permissao',
                'detalhes' => [
                    'permissao_nome' => $permissao->nome,
                    'permissao_codigo' => $permissao->codigo,
                    'motivo' => 'Permissão não estava sendo usada por nenhuma função'
                ],
                'realizado_por' => Auth::id(),
                'realizado_em' => now(),
            ]);

            // Limpar cache
            PermissionHelper::invalidatePermissionCache();

            $this->dispatch('close-confirmacao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Permissão excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir permissão: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'permissao_id' => $permissaoId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir permissão. Tente novamente.'
            ]);
        }
    }

    public function excluirFuncao($funcaoId)
    {
        try {
            $funcao = IgrejaFuncao::find($funcaoId);

            if (!$funcao || $funcao->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Função não encontrada.'
                ]);
                return;
            }

            // Verificar se a função está sendo usada por algum membro
            $estaSendoUsada = $funcao->membroFuncoes()->exists();

            if ($estaSendoUsada) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Esta função não pode ser excluída pois está sendo usada por um ou mais membros.'
                ]);
                return;
            }

            $funcao->delete();

            // Log de exclusão
            IgrejaPermissaoLog::create([
                'igreja_id' => $this->igrejaAtual->id,
                'acao' => 'excluir_funcao',
                'detalhes' => [
                    'funcao_nome' => $funcao->nome,
                    'motivo' => 'Função não estava sendo usada por nenhum membro'
                ],
                'realizado_por' => Auth::id(),
                'realizado_em' => now(),
            ]);

            // Limpar cache
            PermissionHelper::invalidatePermissionCache();

            $this->dispatch('close-confirmacao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Função excluída com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao excluir função: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'funcao_id' => $funcaoId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir função. Tente novamente.'
            ]);
        }
    }

    // ========================================
    // MÉTODOS PARA FUNÇÕES
    // ========================================


    public function abrirModalFuncao($funcaoId = null)
    {
        $this->resetModalFuncao();

        if ($funcaoId) {
            $funcao = IgrejaFuncao::with('permissoes')->find($funcaoId);

            if (!$funcao || $funcao->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Função não encontrada.'
                ]);
                return;
            }

            $this->funcaoSelecionada = $funcao;
            $this->funcaoNome = $funcao->nome;
            $this->funcaoDescricao = $funcao->descricao;
            $this->funcaoNivel = $funcao->nivel;
            $this->funcaoAtiva = $funcao->ativo;
            $this->funcaoPermissoesSelecionadas = $funcao->permissoes->pluck('id')->toArray();
            $this->isEditingFuncao = true;
        } else {
            $this->isEditingFuncao = false;
        }

        $this->dispatch('open-funcao-modal');
    }

    public function salvarFuncao()
    {
        // Validação deixa as bordas vermelhas aparecerem
        $this->validate([
            'funcaoNome' => 'required|string|max:100',
            'funcaoDescricao' => 'nullable|string|max:500',
            'funcaoNivel' => 'required|in:baixo,medio,alto,critico',
            'funcaoAtiva' => 'boolean',
            'funcaoPermissoesSelecionadas' => 'array',
            'funcaoPermissoesSelecionadas.*' => 'exists:igreja_permissoes,id'
        ]);

        try {
            // Verificar unicidade do nome da função na igreja
            if (!$this->isEditingFuncao) {
                $exists = IgrejaFuncao::where('igreja_id', $this->igrejaAtual->id)
                    ->where('nome', $this->funcaoNome)
                    ->exists();

                if ($exists) {
                    $this->addError('funcaoNome', 'Já existe uma função com este nome nesta igreja.');
                    return;
                }
            }

            if ($this->isEditingFuncao) {
                $this->funcaoSelecionada->update([
                    'nome' => $this->funcaoNome,
                    'descricao' => $this->funcaoDescricao,
                    'nivel' => $this->funcaoNivel,
                    'ativo' => $this->funcaoAtiva,
                ]);

                // Atualizar permissões da função
                $this->funcaoSelecionada->permissoes()->sync($this->funcaoPermissoesSelecionadas);

                $mensagem = 'Função atualizada com sucesso!';
            } else {
                $funcao = IgrejaFuncao::create([
                    'igreja_id' => $this->igrejaAtual->id,
                    'nome' => $this->funcaoNome,
                    'descricao' => $this->funcaoDescricao,
                    'nivel' => $this->funcaoNivel,
                    'ativo' => $this->funcaoAtiva,
                    'created_by' => Auth::id(),
                ]);

                // Associar permissões à função
                $funcao->permissoes()->attach($this->funcaoPermissoesSelecionadas);

                $mensagem = 'Função criada com sucesso!';
            }

            // Limpar cache
            PermissionHelper::invalidatePermissionCache();

            $this->dispatch('close-funcao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar função: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'dados' => [
                    'nome' => $this->funcaoNome,
                    'is_editing' => $this->isEditingFuncao
                ]
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar função. Verifique os campos.'
            ]);
        }
    }

    // ========================================
    // MÉTODOS PARA ATRIBUIÇÕES
    // ========================================


    public function abrirModalAtribuicao($membroId = null)
    {
        $this->resetModalAtribuicao();

        if ($membroId) {
            $membro = IgrejaMembro::with(['user', 'funcoesAtivas'])->find($membroId);

            if (!$membro || $membro->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Membro não encontrado.'
                ]);
                return;
            }

            $this->membroSelecionado = $membro;
            $this->atribuicaoMembroId = $membro->id;
        }

        $this->carregarFuncoesDisponiveis();
        $this->dispatch('open-atribuicao-modal');
    }

    protected function carregarFuncoesDisponiveis()
    {
        $this->funcoesDisponiveis = IgrejaFuncao::where('igreja_id', $this->igrejaAtual->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
    }

    public function atribuirFuncao()
    {
        try {
            $this->validate([
                'atribuicaoMembroId' => 'required|exists:igreja_membros,id',
                'atribuicaoFuncaoId' => 'required|exists:igreja_funcoes,id',
                'atribuicaoValidoAte' => 'nullable|date|after:today',
                'atribuicaoObservacoes' => 'nullable|string|max:500'
            ]);

            // Verificar se o membro pertence à igreja
            $membro = IgrejaMembro::find($this->atribuicaoMembroId);
            if (!$membro || $membro->igreja_id !== $this->igrejaAtual->id) {
                $this->addError('atribuicaoMembroId', 'Membro não encontrado ou não pertence à sua igreja.');
                return;
            }

            // Verificar se a função pertence à igreja
            $funcao = IgrejaFuncao::find($this->atribuicaoFuncaoId);
            if (!$funcao || $funcao->igreja_id !== $this->igrejaAtual->id) {
                $this->addError('atribuicaoFuncaoId', 'Função não encontrada.');
                return;
            }

            // Verificar se o membro já tem esta função ativa
            $jaTemFuncao = IgrejaMembroFuncao::where('membro_id', $this->atribuicaoMembroId)
                ->where('funcao_id', $this->atribuicaoFuncaoId)
                ->where('status', 'ativo')
                ->exists();

            if ($jaTemFuncao) {
                $this->addError('atribuicaoFuncaoId', 'Este membro já possui esta função ativa.');
                return;
            }

            $atribuicao = IgrejaMembroFuncao::create([
                'membro_id' => $this->atribuicaoMembroId,
                'funcao_id' => $this->atribuicaoFuncaoId,
                'igreja_id' => $this->igrejaAtual->id,
                'status' => 'ativo',
                'valido_ate' => $this->atribuicaoValidoAte ?: null, // Converte string vazia para null
                'observacoes' => $this->atribuicaoObservacoes,
                'atribuido_por' => Auth::id(),
            ]);

            // Limpar cache do usuário
            PermissionHelper::clearUserCache($membro->user_id);

            $this->dispatch('close-atribuicao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Função atribuída com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atribuir função: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'membro_id' => $this->atribuicaoMembroId,
                'funcao_id' => $this->atribuicaoFuncaoId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao atribuir função. Tente novamente.'
            ]);
        }
    }

    public function revogarFuncao($atribuicaoId)
    {
        try {
            $atribuicao = IgrejaMembroFuncao::find($atribuicaoId);

            if (!$atribuicao) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Atribuição não encontrada.'
                ]);
                return;
            }

            // Verificar se pertence à igreja
            if ($atribuicao->membro->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Acesso negado.'
                ]);
                return;
            }

            $atribuicao->update([
                'status' => 'revogado',
                'revogado_por' => Auth::id(),
                'revogado_em' => now(),
            ]);

            // Limpar cache do usuário
            PermissionHelper::clearUserCache($atribuicao->membro->user_id);

            $this->dispatch('close-confirmacao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Função revogada com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao revogar função: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'atribuicao_id' => $atribuicaoId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao revogar função. Tente novamente.'
            ]);
        }
    }

    public function reativarFuncao($atribuicaoId)
    {
        try {
            $atribuicao = IgrejaMembroFuncao::find($atribuicaoId);

            if (!$atribuicao) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Atribuição não encontrada.'
                ]);
                return;
            }

            // Verificar se pertence à igreja
            if ($atribuicao->membro->igreja_id !== $this->igrejaAtual->id) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Acesso negado.'
                ]);
                return;
            }

            // Verificar se a função ainda está ativa
            if (!$atribuicao->funcao->ativo) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Não é possível reativar esta atribuição pois a função foi desativada.'
                ]);
                return;
            }

            $atribuicao->update([
                'status' => 'ativo',
                'revogado_por' => null,
                'revogado_em' => null,
            ]);

            // Limpar cache do usuário
            PermissionHelper::clearUserCache($atribuicao->membro->user_id);

            $this->dispatch('close-confirmacao-modal');

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Função reativada com sucesso!'
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao reativar função: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'igreja_id' => $this->igrejaAtual->id ?? null,
                'atribuicao_id' => $atribuicaoId
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao reativar função. Tente novamente.'
            ]);
        }
    }

    // ========================================
    // MÉTODOS PARA LOGS
    // ========================================


    // ========================================
    // MÉTODOS DE RESET E UTILITÁRIOS
    // ========================================

    protected function resetModalPermissao()
    {
        $this->permissaoSelecionada = null;
        $this->permissaoNome = '';
        $this->permissaoDescricao = '';
        $this->permissaoCodigo = '';
        $this->permissaoCategoria = '';
        $this->permissaoNivel = 'medio';
        $this->permissaoAtiva = true;
        $this->resetValidation();
    }

    protected function resetModalFuncao()
    {
        $this->funcaoSelecionada = null;
        $this->funcaoNome = '';
        $this->funcaoDescricao = '';
        $this->funcaoNivel = 'medio';
        $this->funcaoAtiva = true;
        $this->funcaoPermissoesSelecionadas = [];
        $this->resetValidation();
    }

    protected function resetModalAtribuicao()
    {
        $this->membroSelecionado = null;
        $this->atribuicaoMembroId = '';
        $this->atribuicaoFuncaoId = '';
        $this->atribuicaoValidoAte = '';
        $this->atribuicaoObservacoes = '';
        $this->resetValidation();
    }

    // ========================================
    // LISTENERS PARA FILTROS
    // ========================================

    public function updatedAbaAtiva()
    {
        // Validar se a aba está disponível para o usuário
        if (!in_array($this->abaAtiva, $this->abasDisponiveis)) {
            // Se não estiver disponível, voltar para a primeira aba disponível
            $this->abaAtiva = $this->abasDisponiveis[0] ?? 'funcoes';
        }

        $this->resetPage();
        $this->carregarDadosAbaAtiva();
    }

    public function updatedFiltroPermissaoCategoria()
    {
        $this->resetPage();

    }

    public function updatedFiltroPermissaoStatus()
    {
        $this->resetPage();

    }

    public function updatedFiltroPermissaoBusca()
    {
        $this->resetPage();

    }

    public function updatedFiltroFuncaoStatus()
    {
        $this->resetPage();
    }

    public function updatedFiltroFuncaoBusca()
    {
        $this->resetPage();
    }

    public function updatedFiltroAtribuicaoMembro()
    {
        $this->resetPage();
    }

    public function updatedFiltroAtribuicaoFuncao()
    {
        $this->resetPage();
    }

    public function updatedFiltroAtribuicaoStatus()
    {
        $this->resetPage();
    }

    public function updatedFiltroLogAcao()
    {
        $this->resetPage();
    }

    public function updatedFiltroLogUsuario()
    {
        $this->resetPage();
    }



    // ========================================
    // PROPRIEDADES COMPUTADAS
    // ========================================

    public function getPermissoesProperty()
    {
        return $this->getPermissoesQuery()->paginate(15);
    }

    public function getPermissoesDisponiveisProperty()
    {
        return IgrejaPermissao::where('igreja_id', $this->igrejaAtual->id)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
    }

    public function getMembrosDisponiveisProperty()
    {
        return IgrejaMembro::where('igreja_id', $this->igrejaAtual->id)
            ->where('status', 'ativo')
            ->where('user_id', '!=', Auth::id()) // Excluir o próprio usuário
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

    public function getUsuariosDisponiveisProperty()
    {
        return IgrejaMembro::where('igreja_id', $this->igrejaAtual->id)
            ->with('user')
            ->get()
            ->map(function($membro) {
                return [
                    'id' => $membro->user_id,
                    'nome' => $membro->user->name,
                ];
            })
            ->unique('id')
            ->sortBy('nome');
    }

    public function getAcoesLogProperty()
    {
        return [
            'criar_permissao' => 'Criar Permissão',
            'atualizar_permissao' => 'Atualizar Permissão',
            'excluir_permissao' => 'Excluir Permissão',
            'criar_funcao' => 'Criar Função',
            'atualizar_funcao' => 'Atualizar Função',
            'excluir_funcao' => 'Excluir Função',
            'atribuir_funcao' => 'Atribuir Função',
            'revogar_funcao' => 'Revogar Função',
            'reativar_funcao' => 'Reativar Função',
        ];
    }

    public function getAbasDisponiveisInfoProperty()
    {
        $abasInfo = [
            'permissoes' => [
                'titulo' => 'Permissões',
                'icone' => 'fas fa-key',
                'cor' => 'primary',
                'disponivel' => in_array('permissoes', $this->abasDisponiveis)
            ],
            'funcoes' => [
                'titulo' => 'Funções',
                'icone' => 'fas fa-users-cog',
                'cor' => 'success',
                'disponivel' => in_array('funcoes', $this->abasDisponiveis)
            ],
            'atribuicoes' => [
                'titulo' => 'Atribuições',
                'icone' => 'fas fa-user-tag',
                'cor' => 'warning',
                'disponivel' => in_array('atribuicoes', $this->abasDisponiveis)
            ],
            'logs' => [
                'titulo' => 'Logs',
                'icone' => 'fas fa-history',
                'cor' => 'info',
                'disponivel' => in_array('logs', $this->abasDisponiveis)
            ],
        ];

        return $abasInfo;
    }

    // ========================================
    // MÉTODOS PARA MODAL DE CONFIRMAÇÃO GENÉRICO
    // ========================================

    public function abrirModalConfirmacao($acao, $itemId, $mensagem = null)
    {
        $this->acaoParaConfirmar = $acao;
        $this->itemParaConfirmar = $itemId;

        // Definir mensagem padrão baseada na ação
        if (!$mensagem) {
            switch ($acao) {
                case 'revogar_funcao':
                    $mensagem = 'Tem certeza que deseja revogar esta função do membro?';
                    break;
                case 'reativar_funcao':
                    $mensagem = 'Tem certeza que deseja reativar esta função para o membro?';
                    break;
                case 'excluir_permissao':
                    $mensagem = 'Tem certeza que deseja excluir esta permissão? Esta ação não pode ser desfeita.';
                    break;
                case 'excluir_funcao':
                    $mensagem = 'Tem certeza que deseja excluir esta função? Esta ação não pode ser desfeita.';
                    break;
                default:
                    $mensagem = 'Tem certeza que deseja executar esta ação?';
            }
        }

        $this->mensagemConfirmacao = $mensagem;
        $this->confirmacaoAcao = true;

        $this->dispatch('open-confirmacao-modal', $mensagem);
    }

    public function confirmarAcao()
    {
        if (!$this->acaoParaConfirmar || !$this->itemParaConfirmar) {
            return;
        }

        $this->confirmacaoLoading = true;

        try {
            switch ($this->acaoParaConfirmar) {
                case 'revogar_funcao':
                    $this->revogarFuncao($this->itemParaConfirmar);
                    break;
                case 'reativar_funcao':
                    $this->reativarFuncao($this->itemParaConfirmar);
                    break;
                case 'excluir_permissao':
                    $this->excluirPermissao($this->itemParaConfirmar);
                    break;
                case 'excluir_funcao':
                    $this->excluirFuncao($this->itemParaConfirmar);
                    break;
            }
        } finally {
            $this->confirmacaoLoading = false;
            $this->cancelarAcao();
        }
    }

    public function cancelarAcao()
    {
        $this->acaoParaConfirmar = '';
        $this->itemParaConfirmar = null;
        $this->mensagemConfirmacao = '';
        $this->confirmacaoAcao = false;

        $this->dispatch('close-confirmacao-modal');
    }

    public function render()
    {
        // Buscar dados apenas da aba ativa para evitar objetos complexos no Livewire
        $dados = [];

        // Sempre buscar permissões (para modal)
        $permissoesQuery = $this->getPermissoesQuery();
        $dados['permissoes'] = $permissoesQuery->paginate(15);

        // Buscar dados específicos da aba ativa
        switch ($this->abaAtiva) {
            case 'funcoes':
                $funcoesQuery = $this->getFuncoesQuery();
                $dados['funcoes'] = $funcoesQuery->paginate(15);
                break;
            case 'atribuicoes':
                $atribuicoesQuery = $this->getAtribuicoesQuery();
                $dados['atribuicoes'] = $atribuicoesQuery->paginate(15);
                break;
            case 'logs':
                $logsQuery = $this->getLogsQuery();
                $dados['logs'] = $logsQuery->paginate(20);
                break;
        }

        return view('rbac-control.rbac-control', array_merge($dados, [
            'permissoesDisponiveis' => $this->permissoesDisponiveis,
            'membrosDisponiveis' => $this->membrosDisponiveis,
            'usuariosDisponiveis' => $this->usuariosDisponiveis,
            'acoesLog' => $this->acoesLog,
            'abasDisponiveisInfo' => $this->abasDisponiveisInfo,
        ]));
    }
}
