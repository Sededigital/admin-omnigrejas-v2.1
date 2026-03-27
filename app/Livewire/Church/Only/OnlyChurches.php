<?php

namespace App\Livewire\Church\Only;

use App\Models\Igrejas\Igreja;
use App\Models\User;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\Igrejas\AliancaIgreja;
use App\Helpers\RBAC\PermissionHelper;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Helpers\SupabaseHelper;


#[Title('Minhas Igrejas')]
#[Layout('components.layouts.app')]
class OnlyChurches extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Propriedades para filtros e busca
    public $search = '';
    public $statusFilter = '';
    public $tipoFilter = '';
    public $categoriaFilter = '';
    public $orderBy = 'nome';
    public $orderDirection = 'asc';

    // Propriedades para modal
    public $isEditing = false;
    public $igrejaId;

    // Campos do formulário
    public $nome;
    public $nif;
    public $sigla;
    public $descricao;
    public $sobre;
    public $contacto;
    public $localizacao;
    public $tipo = 'independente';
    public $sede_id;
    public $categoria_id;
    public $alianca_id;
    public $logo;


    // Propriedades para modal de admins
    public $showAdminModal = false;
    public $selectedChurchId;
    public $searchUser = '';
    public $selectedUsers = [];
    public $adminUsers = [];

    // Propriedades para modal de confirmação de remoção
    public $showRemoveConfirmModal = false;
    public $adminToRemoveId;
    public $adminToRemove = [];

    // Propriedades para modal de código de acesso
    public $showAccessCodeModal = false;
    public $selectedChurchForCode;
    public $currentAccessCode;

    // Propriedades computadas
    protected $permissionHelper;

    public function boot()
    {
        $this->permissionHelper = new PermissionHelper(Auth::user());
    }

    public function mount()
    {
        // Verificar permissões
        if (!$this->permissionHelper->hasPermission('ver_igrejas')) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }
    }

    public function render()
    {
        $igrejas = $this->getIgrejas();
        $categorias = CategoriaIgreja::where('ativa', true)->get();
        $aliancas = $this->getAliancasPermitidas();
        $sedes = $this->getSedesPermitidas();

        // Métricas específicas do usuário
        $igrejasDoUsuario = $this->getIgrejasDoUsuario();
        $metricas = $this->getMetricasUsuario($igrejasDoUsuario);

        return view('church.only.only-churches', [
            'igrejas' => $igrejas,
            'categorias' => $categorias,
            'aliancas' => $aliancas,
            'sedes' => $sedes,
            'totalIgrejas' => $metricas['total'],
            'igrejasAtivas' => $metricas['ativas'],
            'igrejasNovas' => $metricas['novas'],
            'igrejasRecentes' => $metricas['recentes'],
            'maioresCongregacoes' => $metricas['maiores'],
        ]);
    }

    public function getIgrejas()
    {
        $userId = Auth::id();
        $igrejasPermitidas = $this->getIgrejasPermitidasIds($userId);

        $query = Igreja::with(['categoria', 'sede', 'membros'])
            ->withCount('membros')
            ->whereIn('id', $igrejasPermitidas);

        // Aplicar filtros
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nome', 'ilike', '%' . $this->search . '%')
                  ->orWhere('nif', 'ilike', '%' . $this->search . '%')
                  ->orWhere('localizacao', 'ilike', '%' . $this->search . '%')
                  ->orWhere('contacto', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $statusMap = [
                'ativa' => 'aprovado',
                'inativa' => ['pendente', 'rejeitado']
            ];
            $query->whereIn('status_aprovacao', (array) ($statusMap[$this->statusFilter] ?? $this->statusFilter));
        }

        if ($this->tipoFilter) {
            $query->where('tipo', $this->tipoFilter);
        }

        if ($this->categoriaFilter) {
            $query->where('categoria_id', $this->categoriaFilter);
        }

        // Aplicar ordenação
        switch ($this->orderBy) {
            case 'nome':
                $query->orderBy('nome', $this->orderDirection);
                break;
            case 'data':
                $query->orderBy('created_at', $this->orderDirection);
                break;
            case 'membros':
                $query->orderBy('membros_count', $this->orderDirection);
                break;
        }

        return $query->paginate(15);
    }

    /**
     * Retorna IDs das igrejas que o usuário pode acessar
     */
    private function getIgrejasPermitidasIds($userId)
    {
        // Igrejas onde o usuário é membro
        $igrejasComoMembro = DB::table('igreja_membros')
            ->where('user_id', $userId)
            ->where('deleted_at', null)
            ->pluck('igreja_id')
            ->toArray();

        // Igrejas que o usuário criou
        $igrejasCriadas = Igreja::where('created_by', $userId)
            ->pluck('id')
            ->toArray();

        // Combinar e remover duplicatas
        return array_unique(array_merge($igrejasComoMembro, $igrejasCriadas));
    }

    /**
     * Retorna query base das igrejas do usuário
     */
    private function getIgrejasDoUsuario()
    {
        $userId = Auth::id();
        $igrejasPermitidas = $this->getIgrejasPermitidasIds($userId);

        return Igreja::whereIn('id', $igrejasPermitidas);
    }

    /**
     * Retorna sedes que o usuário pode usar para criar filiais
     */
    private function getSedesPermitidas()
    {
        $userId = Auth::id();
        $igrejasPermitidas = $this->getIgrejasPermitidasIds($userId);

        return Igreja::whereIn('id', $igrejasPermitidas)
            ->whereIn('tipo', ['sede', 'independente'])
            ->get();
    }

    /**
     * Retorna alianças que o usuário pode acessar
     */
    private function getAliancasPermitidas()
    {
        $userId = Auth::id();

        // Alianças onde o usuário é líder
        $aliancasComoLider = DB::table('alianca_lideres')
            ->where('membro_id', function($query) use ($userId) {
                $query->select('id')
                      ->from('igreja_membros')
                      ->where('user_id', $userId)
                      ->limit(1);
            })
            ->where('ativo', true)
            ->pluck('igreja_alianca_id')
            ->toArray();

        // Alianças onde igrejas do usuário fazem parte
        $igrejasPermitidas = $this->getIgrejasPermitidasIds($userId);
        $aliancasDasIgrejas = DB::table('igreja_aliancas')
            ->whereIn('igreja_id', $igrejasPermitidas)
            ->where('status', 'ativo')
            ->pluck('alianca_id')
            ->toArray();

        // Combinar IDs únicos
        $aliancaIdsPermitidas = array_unique(array_merge($aliancasComoLider, $aliancasDasIgrejas));

        return AliancaIgreja::whereIn('id', $aliancaIdsPermitidas)
            ->where('ativa', true)
            ->get();
    }

    /**
     * Calcula métricas específicas do usuário
     */
    private function getMetricasUsuario($igrejasQuery)
    {
        return [
            'total' => (clone $igrejasQuery)->count(),
            'ativas' => (clone $igrejasQuery)->where('status_aprovacao', 'aprovado')->count(),
            'novas' => (clone $igrejasQuery)->where('created_at', '>=', now()->startOfMonth())->count(),
            'recentes' => (clone $igrejasQuery)->withCount('membros')
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get(),
            'maiores' => (clone $igrejasQuery)->withCount('membros')
                ->orderBy('membros_count', 'desc')
                ->take(4)
                ->get(),
        ];
    }

    public function sortBy($column)
    {
        if ($this->orderBy === $column) {
            $this->orderDirection = $this->orderDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->orderBy = $column;
            $this->orderDirection = 'asc';
        }
    }

    public function editIgreja($igrejaId)
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas')) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Você não tem permissão para gerenciar igrejas."
            ]);

            return;
        }



        $this->resetValidation();
        $this->resetForm();

        // Verificar se o usuário pode editar esta igreja
        if (!$this->podeGerenciarIgreja($igrejaId)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Você não tem permissão para editar esta igreja."
            ]);
            return;
        }

        $this->isEditing = true;
        $this->igrejaId = $igrejaId;
        $this->loadIgreja($igrejaId);

        // Emitir evento para abrir modal
        $this->dispatch('open-church-modal');
    }

    public function modalClosed()
    {
        $this->resetForm();
        $this->resetValidation();
        $this->isEditing = false;
        $this->igrejaId = null;
    }

    public function loadIgreja($id)
    {
        // Verificar se o usuário pode acessar esta igreja
        if (!$this->podeGerenciarIgreja($id)) {
            abort(403, 'Você não tem permissão para acessar esta igreja.');
        }

        $igreja = Igreja::findOrFail($id);

        $this->nome = $igreja->nome;
        $this->nif = $igreja->nif;
        $this->sigla = $igreja->sigla;
        $this->descricao = $igreja->descricao;
        $this->sobre = $igreja->sobre;
        $this->contacto = $igreja->contacto;
        $this->localizacao = $igreja->localizacao;
        $this->tipo = $igreja->tipo;
        $this->sede_id = $igreja->sede_id;
        $this->categoria_id = $igreja->categoria_id;
        $this->alianca_id = $igreja->alianca_id ?? null;
    }

    public function saveChurch()
    {
        // Verificar permissões básicas
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas')) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Você não tem permissão para gerenciar igrejas."
            ]);

            return;
        }

        // Verificar se pode editar esta igreja específica (se estiver editando)
        if ($this->isEditing && !$this->podeGerenciarIgreja($this->igrejaId)) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Você não tem permissão para editar esta igreja."
            ]);

            return;
        }

        // Validações
        $rules = [
            'nome' => 'required|string|max:255',
            'nif' => ['required', 'string', 'max:50', Rule::unique('igrejas')->ignore($this->igrejaId)],
            'sigla' => 'nullable|string|max:20',
            'descricao' => 'nullable|string',
            'sobre' => 'nullable|string',
            'contacto' => 'nullable|string',
            'localizacao' => 'nullable|string',
            'tipo' => 'required|in:sede,filial,independente',
            'categoria_id' => 'nullable|exists:categorias_igrejas,id',
            'alianca_id' => 'nullable|exists:aliancas_igrejas,id',
        ];

        // Validações específicas para filiais
        if ($this->tipo === 'filial') {
            $rules['sede_id'] = 'required|exists:igrejas,id';

            // Verificar se a sede existe e é do tipo correto
            $sede = Igreja::find($this->sede_id);
            if (!$sede || !in_array($sede->tipo, ['sede', 'independente'])) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => "A sede selecionada não é válida."
                ]);
                return;
            }

            // Verificar se o usuário pode usar esta sede (deve ser uma sede que ele controla)
            if (!$this->podeGerenciarIgreja($this->sede_id)) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => "Você não tem permissão para criar filiais nesta sede"
                ]);
                return;
            }

            // Verificar limites do plano para filiais
          //  $limiteOk = DB::select('SELECT verificar_limite_igreja(?, ?) as permitido',
          //      [$this->sede_id, 'filial'])[0]->permitido ?? true;

          //  if (!$limiteOk) {
          //      $this->addError('limite', 'Limite de filiais atingido para esta sede.');
          ///      return;
          //   }
        }

        $this->validate($rules);

        try {
            DB::beginTransaction();

            $data = [
                'nome' => $this->nome,
                'nif' => $this->nif,
                'sigla' => $this->sigla,
                'descricao' => $this->descricao,
                'sobre' => $this->sobre,
                'contacto' => $this->contacto,
                'localizacao' => $this->localizacao,
                'tipo' => $this->tipo,
                'sede_id' => $this->tipo === 'filial' ? $this->sede_id : null,
                'categoria_id' => $this->categoria_id,
                'alianca_id' => $this->alianca_id,
                'created_by' => Auth::id(),
            ];

            // Upload do logo se foi enviado
            if ($this->logo) {
                try {
                    // Usar o novo método inteligente que cria pasta church-logo
                    $path = SupabaseHelper::fazerUploadLogoIgreja($this->logo, $this->nome);
                    $data['logo'] = $path;
                    // Limpar campo após upload
                    $this->logo = null;
                } catch (\Exception $e) {
                    throw $e;
                }
            }

            if ($this->isEditing) {
                $igreja = Igreja::findOrFail($this->igrejaId);
                $igreja->update($data);
                $message = 'Igreja atualizada com sucesso!';
            } else {
                $igreja = Igreja::create($data);

                // Criar permissões padrão para a nova igreja
                DB::select('SELECT criar_permissoes_padrao(?)', [$igreja->id]);


                $message = 'Igreja criada com sucesso!';
            }

            DB::commit();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $message
            ]);
            $this->dispatch('close-church-modal');
            $this->resetPage();

        } catch (\Exception $e) {
            DB::rollBack();
          //  Log::error('Erro ao salvar igreja', [
          //      'error' => $e->getMessage(),
          //      'user_id' => Auth::id(),
          //      'data' => $data ?? null
          //  ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => "Erro ao salvar igreja:". $e->getMessage()
            ]);


        }
    }

    public function deleteIgreja($id)
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas')) {
            $this->addError('permission', 'Você não tem permissão para excluir igrejas.');
            return;
        }

        // Verificar se pode gerenciar esta igreja específica
        if (!$this->podeGerenciarIgreja($id)) {
            $this->addError('permission', 'Você não tem permissão para excluir esta igreja.');
            return;
        }

        try {
            $igreja = Igreja::findOrFail($id);

            // Verificar se tem filiais dependentes (apenas se for sede que o usuário controla)
            $filiais = Igreja::where('sede_id', $id)->get();
            foreach ($filiais as $filial) {
                if ($this->podeGerenciarIgreja($filial->id)) {
                    $this->addError('delete', 'Não é possível excluir uma sede que possui filiais que você administra.');
                    return;
                }
            }

            // Verificar se tem membros ativos
            if ($igreja->membros()->where('status', 'ativo')->count() > 0) {
                $this->addError('delete', 'Não é possível excluir uma igreja que possui membros ativos.');
                return;
            }

            $igreja->delete();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Igreja excluída com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao excluir igreja', [
                'igreja_id' => $id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            $this->addError('delete', 'Erro ao excluir igreja.');
        }
    }

    /**
     * Verifica se o usuário pode gerenciar uma igreja específica
     */
    private function podeGerenciarIgreja($igrejaId)
    {
        $userId = Auth::id();
        $igrejasPermitidas = $this->getIgrejasPermitidasIds($userId);

        return in_array($igrejaId, $igrejasPermitidas);
    }

    public function resetForm()
    {
        $this->nome = '';
        $this->nif = '';
        $this->sigla = '';
        $this->descricao = '';
        $this->sobre = '';
        $this->contacto = '';
        $this->localizacao = '';
        $this->tipo = 'independente';
        $this->sede_id = null;
        $this->categoria_id = null;
        $this->alianca_id = null;
        $this->logo = null;
    }

    // Métodos auxiliares para a view
    public function getCorAvatar($index)
    {
        $cores = ['bg-primary', 'bg-success', 'bg-info text-light', 'bg-warning', 'bg-danger', 'bg-secondary'];
        return $cores[$index % count($cores)];
    }

    public function getIniciais($nome)
    {
        $palavras = explode(' ', trim($nome));
        $iniciais = '';

        foreach ($palavras as $palavra) {
            $iniciais .= strtoupper(substr($palavra, 0, 1));
            if (strlen($iniciais) >= 2) break;
        }

        return str_pad($iniciais, 2, 'I');
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'aprovado' => 'bg-success',
            'pendente' => 'bg-warning',
            'rejeitado' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function getStatusText($status)
    {
        return match($status) {
            'aprovado' => 'Ativa',
            'pendente' => 'Pendente',
            'rejeitado' => 'Rejeitada',
            default => 'Desconhecido'
        };
    }

    public function getPastorPrincipal($igreja)
    {
        return $igreja->membros()
            ->where('cargo', 'pastor')
            ->with('user')
            ->first()?->user;
    }

    #[On('refreshComponent')]
    public function refreshComponent()
    {
        $this->resetPage();
    }

    // ========================================
    // MÉTODOS PARA MODAL DE ADMINS
    // ========================================

    public function openAdminModal($churchId)
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas')) {
            $this->addError('permission', 'Você não tem permissão para gerenciar admins.');
            return;
        }

        if (!$this->podeGerenciarIgreja($churchId)) {
            $this->addError('permission', 'Você não tem permissão para gerenciar admins desta igreja.');
            return;
        }

        $this->selectedChurchId = $churchId;
        $this->loadAdminUsers();
        $this->showAdminModal = true;
    }

    public function closeAdminModal()
    {
        $this->showAdminModal = false;
        $this->selectedChurchId = null;
        $this->searchUser = '';
        $this->selectedUsers = [];
        $this->adminUsers = [];
    }

    public function loadAdminUsers()
    {
        if (!$this->selectedChurchId) return;

        // Carregar usuários que já são admins desta igreja com informações de quem criou
        $results = DB::table('igreja_membros')
            ->join('users', 'igreja_membros.user_id', '=', 'users.id')
            ->where('igreja_membros.igreja_id', $this->selectedChurchId)
            ->whereIn('igreja_membros.cargo', ['admin', 'pastor' ])
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'igreja_membros.cargo',
                'igreja_membros.created_by',
                'igreja_membros.created_at'
            )
            ->get();

        // Converter para array manualmente
        $this->adminUsers = [];
        foreach ($results as $result) {
            $this->adminUsers[] = [
                'id' => $result->id,
                'name' => $result->name,
                'email' => $result->email,
                'cargo' => $result->cargo,
                'atribuido_por' => $result->created_by,
                'atribuido_em' => $result->created_at,
            ];
        }
    }

    public function getAvailableUsers()
    {
        if (!$this->selectedChurchId) return collect();

        $userId = Auth::id();
        $igrejasPermitidas = $this->getIgrejasPermitidasIds($userId);

        // Buscar usuários que são membros das igrejas do admin logado
        // e que têm os cargos admin, pastor ou ministro
        $query = User::where('is_active', true)
            ->whereNotIn('id', array_column($this->adminUsers, 'id'))
            ->whereExists(function($subQuery) use ($igrejasPermitidas) {
                $subQuery->select(DB::raw(1))
                    ->from('igreja_membros')
                    ->whereColumn('igreja_membros.user_id', 'users.id')
                    ->whereIn('igreja_membros.igreja_id', $igrejasPermitidas)
                    ->whereIn('igreja_membros.cargo', ['admin', 'pastor', 'ministro' ])
                    ->where('igreja_membros.deleted_at', null);
            });

        if ($this->searchUser) {
            $query->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchUser . '%')
                  ->orWhere('email', 'ilike', '%' . $this->searchUser . '%');
            });
        }

        return $query->limit(10)->get();
    }

    public function addAdmin($userId)
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas') || !$this->podeGerenciarIgreja($this->selectedChurchId)) {
            $this->addError('permission', 'Você não tem permissão para adicionar admins.');
            return;
        }

        try {
            DB::beginTransaction();

            // Verificar se o usuário já é membro da igreja
            $existingMember = DB::table('igreja_membros')
                ->where('igreja_id', $this->selectedChurchId)
                ->where('user_id', $userId)
                ->first();

            if (!$existingMember) {
                // Adicionar como membro admin
                DB::table('igreja_membros')->insert([
                    'id' => Str::uuid(),
                    'igreja_id' => $this->selectedChurchId,
                    'user_id' => $userId,
                    'cargo' => 'admin',
                    'status' => 'ativo',
                    'data_entrada' => now(),
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Atualizar cargo para admin
                DB::table('igreja_membros')
                    ->where('igreja_id', $this->selectedChurchId)
                    ->where('user_id', $userId)
                    ->update([
                        'cargo' => 'admin',
                        'updated_at' => now(),
                    ]);
            }

            // Log da ação
            DB::table('igreja_permissao_logs')->insert([
                'id' => Str::uuid(),
                'igreja_id' => $this->selectedChurchId,
                'membro_id' => $existingMember ? $existingMember->id : DB::table('igreja_membros')->where('igreja_id', $this->selectedChurchId)->where('user_id', $userId)->value('id'),
                'acao' => 'atribuir_funcao',
                'detalhes' => json_encode(['funcao' => 'admin', 'tipo' => 'admin_igreja']),
                'realizado_por' => Auth::id(),
                'realizado_em' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->loadAdminUsers();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Admin adicionado com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao adicionar admin', [
                'igreja_id' => $this->selectedChurchId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'user_id_log' => Auth::id()
            ]);
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => "Erro ao adicionar Admin:". $e->getMessage()
            ]);


        }
    }

    public function removeAdmin($userId)
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas') || !$this->podeGerenciarIgreja($this->selectedChurchId)) {
            $this->addError('permission', 'Você não tem permissão para remover admins.');
            return;
        }

        try {
            DB::beginTransaction();

            // Buscar membro
            $member = DB::table('igreja_membros')
                ->where('igreja_id', $this->selectedChurchId)
                ->where('user_id', $userId)
                ->first();

            if ($member) {
                // Alterar cargo para membro comum
                DB::table('igreja_membros')
                    ->where('id', $member->id)
                    ->update([
                        'cargo' => 'membro',
                        'updated_at' => now(),
                    ]);

                // Log da ação
                DB::table('igreja_permissao_logs')->insert([
                    'id' => Str::uuid(),
                    'igreja_id' => $this->selectedChurchId,
                    'membro_id' => $member->id,
                    'acao' => 'revogar_funcao',
                    'detalhes' => json_encode(['funcao_antiga' => 'admin', 'funcao_nova' => 'membro']),
                    'realizado_por' => Auth::id(),
                    'realizado_em' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();

            $this->loadAdminUsers();
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Admin removido com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao remover admin', [
                'igreja_id' => $this->selectedChurchId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'user_id_log' => Auth::id()
            ]);
            $this->addError('admin', 'Erro ao remover admin.');
        }
    }

    public function confirmRemoveAdmin($userId)
    {
        // Encontrar o admin na lista
        $admin = collect($this->adminUsers)->firstWhere('id', $userId);

        if (!$admin) {
            $this->addError('admin', 'Admin não encontrado.');
            return;
        }

        $this->adminToRemoveId = $userId;
        $this->adminToRemove = $admin;
        $this->showRemoveConfirmModal = true;
    }

    public function closeRemoveConfirmModal()
    {
        $this->showRemoveConfirmModal = false;
        $this->adminToRemoveId = null;
        $this->adminToRemove = [];
    }

    public function removeAdminConfirmed()
    {
        if (!$this->adminToRemoveId) return;

        $this->removeAdmin($this->adminToRemoveId);
        $this->closeRemoveConfirmModal();
    }

    public function canRemoveAdmin($userId)
    {
        $currentUserId = Auth::id();

        // Não pode remover a si mesmo
        if ($userId === $currentUserId) {
            return false;
        }

        // Encontrar o admin na lista
        $admin = collect($this->adminUsers)->firstWhere('id', $userId);

        if (!$admin) {
            return false;
        }

        // Verificar se este admin é o criador da igreja
        $igreja = Igreja::find($this->selectedChurchId);
        if ($igreja && $igreja->created_by === $userId) {
            // Não pode remover o admin que criou a igreja
            return false;
        }

        // Se o usuário atual é super_admin, pode remover qualquer um
        if (Auth::user()->role === 'super_admin') {
            return true;
        }

        // Se o admin foi criado por um super_admin, outros admins não podem removê-lo
        if ($admin['atribuido_por']) {
            $criador = User::find($admin['atribuido_por']);
            if ($criador && $criador->role === 'super_admin') {
                return false;
            }
        }

        // Se o admin foi criado pelo usuário atual, ele pode removê-lo
        if ($admin['atribuido_por'] === $currentUserId) {
            return true;
        }

        // Por padrão, permitir remoção (pode ser ajustado conforme regras de negócio)
        return true;
    }

    // ========================================
    // MÉTODOS PARA MODAL DE CÓDIGO DE ACESSO
    // ========================================

    public function openAccessCodeModal($churchId)
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas')) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => "Você não tem permissão para gerenciar códigos desta igreja."
            ]);

            return;
        }

        if (!$this->podeGerenciarIgreja($churchId)) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => "Você não tem permissão para gerenciar códigos desta igreja."
            ]);
            return;
        }

        $this->selectedChurchForCode = $churchId;
        $this->loadCurrentAccessCode();
        $this->showAccessCodeModal = true;
    }

    public function closeAccessCodeModal()
    {
        $this->showAccessCodeModal = false;
        $this->selectedChurchForCode = null;
        $this->currentAccessCode = null;
    }

    public function loadCurrentAccessCode()
    {
        if (!$this->selectedChurchForCode) return;

        $igreja = Igreja::find($this->selectedChurchForCode);
        $this->currentAccessCode = $igreja ? $igreja->code_access : null;
    }

    public function generateAccessCode()
    {
        if (!$this->permissionHelper->hasPermission('gerenciar_igrejas') || !$this->podeGerenciarIgreja($this->selectedChurchForCode)) {

            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Você não tem permissão para gerar códigos de acesso."
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            // Verificar regras de segurança para geração de códigos
            $igreja = Igreja::find($this->selectedChurchForCode);
            $userId = Auth::id();
            $hoje = now()->toDateString();

            // 1. Verificar se já foi gerado código nos últimos 10 dias
            $ultimaGeracao = DB::table('auditoria_logs')
                ->where('tabela', 'igrejas')
                ->where('registro_id', $this->selectedChurchForCode)
                ->where('acao', 'update')
                ->where('usuario_id', $userId)
                ->where('valores->campo_alterado', 'code_access')
                ->where('data_acao', '>=', now()->subDays(10))
                ->orderBy('data_acao', 'desc')
                ->first();

            if ($ultimaGeracao) {
                $diasRestantes = 10 - now()->diffInDays($ultimaGeracao->data_acao);
                $formatado = number_format($diasRestantes);

                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => "Você só pode gerar um novo código a cada 10 dias. Próxima geração
                    disponível em {$formatado} dia(s)."
                ]);


                return;
            }

            // 2. Verificar limite diário de 5 gerações por dia
            $geracoesHoje = DB::table('auditoria_logs')
                ->where('tabela', 'igrejas')
                ->where('acao', 'update')
                ->where('usuario_id', $userId)
                ->where('valores->campo_alterado', 'code_access')
                ->whereDate('data_acao', $hoje)
                ->count();

            if ($geracoesHoje >= 5) {

                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Você atingiu o limite de 5 gerações de código por dia. Tente novamente amanhã.!'
                ]);

                return;
            }

            // Gerar código único de 6 dígitos (mínimo 6, sem caracteres especiais)
            do {
                $codigo = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $exists = Igreja::where('code_access', $codigo)->exists();
            } while ($exists);

            // Atualizar a igreja com o novo código
            Igreja::where('id', $this->selectedChurchForCode)
                ->update([
                    'code_access' => $codigo,
                    'updated_at' => now()
                ]);

            // Registrar na auditoria com detalhes específicos
            DB::table('auditoria_logs')->insert([
                'id' => Str::uuid(),
                'tabela' => 'igrejas',
                'registro_id' => $this->selectedChurchForCode,
                'acao' => 'update',
                'usuario_id' => $userId,
                'data_acao' => now(),
                'valores' => json_encode([
                    'campo_alterado' => 'code_access',
                    'codigo_antigo' => $igreja->code_access,
                    'codigo_novo' => $codigo,
                    'tipo_alteracao' => 'geracao_codigo_acesso',
                    'regras_seguranca' => [
                        'intervalo_minimo_dias' => 10,
                        'limite_diario' => 5,
                        'geracoes_hoje' => $geracoesHoje + 1
                    ]
                ])
            ]);

            DB::commit();

            $this->currentAccessCode = $codigo;

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Código de acesso gerado com sucesso!'
            ]);

            Log::info('Código de acesso gerado com controle de segurança', [
                'igreja_id' => $this->selectedChurchForCode,
                'codigo' => $codigo,
                'user_id' => $userId,
                'geracoes_hoje' => $geracoesHoje + 1,
                'ultima_geracao' => $ultimaGeracao ? $ultimaGeracao->data_acao : null
            ]);

        } catch (\Exception $e) {
            
            DB::rollBack();

            Log::error('Erro ao gerar código de acesso', [
                'igreja_id' => $this->selectedChurchForCode,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => "Erro ao gerar código de acesso."
            ]);

        }
    }
}

