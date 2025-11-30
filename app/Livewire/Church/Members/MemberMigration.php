<?php

namespace App\Livewire\Church\Members;

use Livewire\Component;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\User;
use App\Models\Igrejas\Ministerio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Helpers\RBAC\PermissionHelper;

#[Title('Migração de Membros | Portal da Igreja')]
#[Layout('components.layouts.app')]
class MemberMigration extends Component
{
    public $selectedMember;
    public $targetChurch;
    public $targetChurchName = ''; // Para igrejas que não existem no sistema
    public $newRole = null;
    public $reason = '';
    public $searchMember = '';
    public $searchChurch = '';
    public $showModal = false;
    public $migrationType = 'existing_church'; // 'existing_church' ou 'new_church'
    public $printTransferForm = true; // Checkbox para imprimir ficha de transferência

    public $loading = false;

    // Sistema de Abas
    public $abaAtiva = 'membros'; // membros, historico

    // Filtros
    public $filterStatus = 'ativo';
    
    protected $rules = [
        'selectedMember' => 'required|exists:igreja_membros,id',
        'targetChurch' => 'nullable|required_if:migrationType,existing_church|exists:igrejas,id',
        'targetChurchName' => 'required_if:migrationType,new_church|string|max:255',
        'newRole' => 'nullable|in:membro,obreiro,diacono,ministro,pastor,admin',
        'reason' => 'nullable|string|max:500',
        'migrationType' => 'required|in:existing_church,new_church',
    ];

    protected $messages = [
        'selectedMember.required' => 'Selecione um membro para migrar.',
        'targetChurch.required_if' => 'Selecione a igreja de destino.',
        'targetChurchName.required_if' => 'Informe o nome da igreja de destino.',
        'targetChurch.different' => 'A igreja de destino deve ser diferente da atual.',
        'reason.max' => 'O motivo deve ter no máximo 500 caracteres.',
        'migrationType.required' => 'Selecione o tipo de migração.',
    ];

    public function mount()
    {
        // Verificar permissões usando PermissionHelper
        $permissionHelper = new PermissionHelper(Auth::user());
        if (!$permissionHelper->hasPermission('gerenciar_migracao_membros')) {
            abort(403, 'Você não tem permissão para acessar este módulo.');
        }
    }

    public function openMigrationModal($memberId)
    {

        $this->selectedMember = $memberId;
        $this->targetChurch = null;
        $this->targetChurchName = '';
        $this->newRole = null;
        $this->reason = '';
        $this->migrationType = 'existing_church';
        $this->printTransferForm = true; // Sempre marcado por padrão
        $this->showModal = true;

        // Pequeno delay para garantir que o modal seja aberto primeiro
        $this->dispatch('open-migration-modal');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['selectedMember', 'targetChurch', 'targetChurchName', 'newRole', 'reason', 'migrationType', 'printTransferForm']);
    }

    public function migrateMember()
    {

        $this->validate();
       
        try {

            DB::beginTransaction();

            // Buscar dados do membro
            $member = IgrejaMembro::with(['igreja', 'user'])->findOrFail($this->selectedMember);

            

            // Verificar se o membro está ativo
            if ($member->status !== 'ativo') {
                $this->addError('selectedMember', 'Apenas membros ativos podem ser migrados.');
                return;
            }


            // Verificar se a igreja de destino é diferente (apenas para igrejas existentes)
            if ($this->migrationType === 'existing_church' && $member->igreja_id == $this->targetChurch) {
                $this->addError('targetChurch', 'A igreja de destino deve ser diferente da atual.');
                return;
            }

            // Verificar se a igreja de destino tem limite de membros (apenas para igrejas existentes)
            if ($this->migrationType === 'existing_church') {
                $targetChurch = Igreja::findOrFail($this->targetChurch);
                if (!$this->checkMemberLimit($targetChurch)) {
                    $this->addError('targetChurch', 'A igreja de destino atingiu o limite de membros.');
                    return;
                }
            }



            // Executar migração baseada no tipo
            if ($this->migrationType === 'existing_church') {
                // Migração para igreja existente
                $this->performMigrationToExistingChurch($member, $this->targetChurch, $this->newRole, true, $this->reason);
            } else {
                // Migração para igreja externa (não cadastrada no sistema)
                $this->performMigrationToExternalChurch($member, $this->targetChurchName, $this->newRole, true, $this->reason);
            }

            DB::commit();

            // Gerar ficha de transferência se solicitado
            if ($this->printTransferForm) {
                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Membro migrado com sucesso! Gerando ficha de transferência...'
                ]);

                $church = $this->migrationType === 'existing_church' ? Igreja::find($this->targetChurch) : null;
                $migrationType = $this->migrationType;
                $newRole = $this->newRole; 
                $reason = $this->reason;

                $this->dispatch('member-migrated');
                
                return $this->generateTransferForm($member, $church, $this->targetChurchName, $migrationType, $newRole, $reason);
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Membro migrado com sucesso!'
            ]);
            $this->closeModal();
            $this->dispatch('member-migrated');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao migrar membro: ' . $e->getMessage()
            ]);
          
            Log::error('Erro na migração de membro', [
                'member_id' => $this->selectedMember,
                'target_church' => $this->targetChurch,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function checkMemberLimit(Igreja $church)
    {
        // Verificar limites de assinatura da igreja
        $assinaturaAtual = $church->assinaturaAtual;

        if (!$assinaturaAtual) {
            return true; // Permitir se não houver assinatura (modo básico)
        }

        $limiteMembros = $assinaturaAtual->limite_membros_custom ??
                         $assinaturaAtual->pacote->limite_membros_total ??
                         0;

        if ($limiteMembros <= 0) {
            return true; // Sem limite
        }

        $membrosAtivos = $church->membrosAtivos()->count();

        return $membrosAtivos < $limiteMembros;
    }

    private function performMigrationToExistingChurch($member, $targetChurchId, $newRole, $reason)
    {
        // Verificar se a função do banco existe e se a tabela histórica tem a estrutura correta
        $functionExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.routines
            WHERE routine_name = 'migrar_membro_igreja'
        ) as exists")[0]->exists;

        $historicoTableOk = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_name = 'igreja_membros_historico' AND column_name = 'cargo'
        ) as exists")[0]->exists;

        if ($functionExists && $historicoTableOk) {
            try {
                // Usar função do banco
                $result = DB::select('SELECT migrar_membro_igreja(?, ?, ?, ?, ?) as result',
                    [
                        $member->id,
                        $targetChurchId,
                        Auth::id(), // p_usuario_migracao
                        $newRole ?: $member->cargo, // p_novo_cargo
                        $reason ?: 'Migração solicitada pelo usuário' // p_motivo
                    ]
                );

                $migrationResult = json_decode($result[0]->result, true);

                if (!$migrationResult['sucesso']) {
                    throw new \Exception($migrationResult['erro'] ?? 'Erro desconhecido na migração');
                }

                // Log de sucesso
              //  Log::info('Migração realizada com sucesso via função do banco', [
              //      'member_id' => $member->id,
              //      'target_church' => $targetChurchId,
              //      'migration_id' => $migrationResult['migracao_id'] ?? null
              //  ]);

                return; // Sucesso, sair da função
            } catch (\Exception $e) {
                // Se a função do banco falhar, fazer fallback para migração manual
                Log::warning('Função do banco falhou, fazendo fallback para migração manual', [
                    'error' => $e->getMessage(),
                    'member_id' => $member->id
                ]);
            }
        }

        // Migração manual para igreja existente (fallback)
        $this->performManualMigrationToExistingChurch($member, $targetChurchId, $newRole, $reason);
    }

    private function performMigrationToExternalChurch($member, $targetChurchName, $newRole, $reason)
    {
        // Migração para igreja externa (não cadastrada no sistema)
        // O membro permanece na igreja atual, mas registramos a migração

        // 1. Registrar na tabela membro_migracoes se existir
        $tableExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.tables
            WHERE table_name = 'membro_migracoes'
        ) as exists")[0]->exists;

        if ($tableExists) {
            DB::table('membro_migracoes')->insert([
                'membro_user_id' => $member->user_id,
                'igreja_origem_id' => $member->igreja_id,
                'igreja_origem_nome' => $member->igreja->nome,
                'membro_origem_id' => $member->id,
                'numero_membro_origem' => $member->numero_membro,
                'cargo_origem' => $member->cargo,
                'igreja_destino_nome' => $targetChurchName,
                'cargo_destino' => $newRole ?: $member->cargo,
                'tipo_migracao' => 'transferencia',
                'motivo' => $reason,
                'observacoes' => 'Migração para igreja externa (não cadastrada no sistema)',
                'migrado_por' => Auth::id(),
                'data_migracao' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // 2. Atualizar status do membro para 'transferido'
        $member->update([
            'status' => 'transferido',
            'updated_at' => now()
        ]);

        // 3. Registrar log de auditoria
        DB::table('auditoria_logs')->insert([
            'tabela' => 'igreja_membros',
            'registro_id' => $member->id,
            'acao' => 'update', // Usar 'update' pois o constraint só permite insert/update/delete
            'usuario_id' => Auth::id(),
            'valores' => json_encode([
                'igreja_origem' => $member->igreja_id,
                'igreja_destino_nome' => $targetChurchName,
                'tipo_migracao' => 'transferencia_externa',
                'motivo' => $reason
            ]),
            'data_acao' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function performManualMigrationToExistingChurch($member, $targetChurchId, $newRole, $reason)
    {
        // Migração manual básica para igreja existente
        $targetChurch = Igreja::findOrFail($targetChurchId);

        // 1. Atualizar membro para a nova igreja
        $updateData = [
            'igreja_id' => $targetChurchId,
            'numero_membro' => null, // Será gerado automaticamente pelo trigger
            'principal' => true, // Nova igreja se torna principal
            'updated_at' => now()
        ];

        // Verificar se a coluna cargo existe antes de tentar atualizar
        $cargoColumnExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_name = 'igreja_membros' AND column_name = 'cargo'
        ) as exists")[0]->exists;

        if ($cargoColumnExists && $newRole) {
            $updateData['cargo'] = $newRole;
        }

        // Atualizar o membro
        $member->update($updateData);

        // Forçar refresh do modelo para pegar o numero_membro gerado pelo trigger
        $member->refresh();



        // 3. Registrar na tabela membro_migracoes se existir
        $tableExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.tables
            WHERE table_name = 'membro_migracoes'
        ) as exists")[0]->exists;

        if ($tableExists) {
            // Verificar quais colunas existem na tabela
            $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'membro_migracoes'");
            $columnNames = array_column($columns, 'column_name');

            $insertData = [
                'membro_user_id' => $member->user_id,
                'igreja_origem_id' => $member->igreja_id,
                'igreja_origem_nome' => $member->igreja->nome,
                'membro_origem_id' => $member->id,
                'numero_membro_origem' => $member->numero_membro,
                'igreja_destino_id' => $targetChurchId,
                'igreja_destino_nome' => $targetChurch->nome,
                'membro_destino_id' => $member->id,
                'numero_membro_destino' => $member->fresh()->numero_membro,
                'tipo_migracao' => 'transferencia',
                'motivo' => $reason,
                'migrado_por' => Auth::id(),
                'data_migracao' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Adicionar campos que podem ser nulos apenas se existirem e tiverem valores
            if (in_array('cargo_origem', $columnNames) && isset($member->cargo) && !empty($member->cargo)) {
                $insertData['cargo_origem'] = $member->cargo;
            }
            if (in_array('cargo_destino', $columnNames)) {
                $cargoDestino = $newRole ?: $member->cargo;
                if (!empty($cargoDestino)) {
                    $insertData['cargo_destino'] = $cargoDestino;
                } else {
                    // Se a coluna é NOT NULL e não temos valor, não incluir no insert
                    // Isso causará erro, então vamos tentar usar um valor padrão
                    $insertData['cargo_destino'] = 'membro'; // valor padrão
                }
            }

            if (in_array('manteve_perfil', $columnNames)) {
                $insertData['manteve_perfil'] = true;
            }
            if (in_array('status', $columnNames)) {
                $insertData['status'] = 'concluida';
            }
            if (in_array('referencia_externa', $columnNames)) {
                $insertData['referencia_externa'] = null;
            }

            DB::table('membro_migracoes')->insert($insertData);
        }

        // 4. Registrar log de auditoria
        DB::table('auditoria_logs')->insert([
            'tabela' => 'igreja_membros',
            'registro_id' => $member->id,
            'acao' => 'update', // Usar 'update' pois o constraint só permite insert/update/delete
            'usuario_id' => Auth::id(),
            'valores' => json_encode([
                'igreja_origem' => $member->getOriginal('igreja_id'),
                'igreja_destino' => $targetChurchId,
                'tipo_migracao' => 'transferencia',
                'motivo' => $reason
            ]),
            'data_acao' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function determineDocumentType($member, $targetChurch, $migrationType)
    {
        // Verificar se existe histórico de migrações para determinar o tipo correto
        $tableExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.tables
            WHERE table_name = 'membro_migracoes'
        ) as exists")[0]->exists;

        if ($tableExists) {
            // Buscar a migração mais recente do membro
            $latestMigration = DB::table('membro_migracoes')
                ->where('membro_user_id', $member->user_id)
                ->orderBy('data_migracao', 'desc')
                ->first();

            if ($latestMigration) {
                // Se a migração mais recente foi há menos de 5 minutos, usar o tipo dela
                if ($latestMigration->data_migracao >= now()->subMinutes(5)) {
                    return $latestMigration->tipo_migracao;
                }

                // Verificar se é uma reintegração baseada no histórico
                $previousMigrations = DB::table('membro_migracoes')
                    ->where('membro_user_id', $member->user_id)
                    ->where('data_migracao', '<', $latestMigration->data_migracao)
                    ->orderBy('data_migracao', 'desc')
                    ->limit(1)
                    ->first();

                if ($previousMigrations && $previousMigrations->tipo_migracao === 'transferencia') {
                    // Se a migração anterior foi transferência e agora está voltando, é reintegração
                    return 'reintegracao';
                }

                // Retornar o tipo da migração mais recente se não for reintegração
                return $latestMigration->tipo_migracao;
            }
        }

        // Fallback para lógica baseada no contexto atual
        if ($migrationType === 'existing_church') {
            // Migração para igreja existente
            if ($member->igreja_id === $targetChurch->id) {
                // Mesmo membro, mudança de cargo
                return 'mudanca_cargo';
            } elseif ($member->status === 'transferido') {
                // Membro transferido retornando
                return 'reintegracao';
            } else {
                // Transferência normal
                return 'transferencia';
            }
        } elseif ($migrationType === 'new_church') {
            // Migração para igreja externa
            if ($member->status === 'transferido') {
                return 'reintegracao';
            } else {
                return 'transferencia';
            }
        }

        // Fallback
        return 'transferencia';
    }

    private function generateTransferForm($member, $targetChurch  = null, $targetChurchName = null, $migrationType, $newRole, $reason)
    {
        try {
            // Determinar o tipo de documento baseado na migração
            $documentType = $this->determineDocumentType($member, $targetChurch, $migrationType);

            // Gerar PDF usando DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.transfer-form', [
                'member' => $member,
                'sourceChurch' => $member->igreja,
                'targetChurch' => $targetChurch,
                'targetChurchName' => $targetChurchName,
                'migrationType' => $documentType, // Passar o tipo correto do documento
                'newRole' => $newRole,
                'reason' => $reason,
                'migratedBy' => Auth::user(),
                'migrationDate' => now(),
            ]);

            Log::info('Capturar os ID: ', [
                'member_id' => $member->id,
                'igreja_id'=> $member->igreja->id
            ]);

            // Configurar PDF
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96
            ]);

            // Nome do arquivo baseado no tipo de documento
            $fileNamePrefix = match($documentType) {
                'reintegracao' => 'ficha-reintegracao',
                'mudanca_cargo' => 'ficha-mudanca-cargo',
                default => 'ficha-transferencia'
            };
            $fileName = $fileNamePrefix . '-' . $member->user->name . '-' . now()->format('Y-m-d') . '.pdf';

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Migração realizada com sucesso.'
            ]);
            // Retornar download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (\Exception $e) {
            // Log do erro mas não interromper o fluxo
            Log::error('Erro ao gerar ficha de transferência', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);

            // Mostrar aviso ao usuário
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Migração realizada, mas houve erro ao gerar a ficha de transferência.'
            ]);
        }
    }

    public function getMembersProperty()
    {
        $query = IgrejaMembro::with(['user', 'igreja'])
            ->where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', $this->filterStatus);

        // Verificar se o usuário logado é admin, pastor ou ministro na igreja atual
        $userMember = IgrejaMembro::where('user_id', Auth::id())
            ->where('igreja_id', Auth::user()->getIgrejaId())
            ->where('status', 'ativo')
            ->first();

        $isAdminOrHigher = $userMember && in_array($userMember->cargo, ['admin', 'pastor', 'ministro' ]);

        // Se NÃO for admin ou superior, filtrar apenas membros comuns
        if (!$isAdminOrHigher) {
            $query->whereIn('cargo', ['membro', 'obreiro', 'diacono']);
        }
        // Se for admin ou superior, traz todos os membros (sem filtro de cargo)

        if ($this->searchMember) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'ilike', '%' . $this->searchMember . '%')
                  ->orWhere('email', 'ilike', '%' . $this->searchMember . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function getChurchesProperty()
    {
        // Verificar se a coluna status_aprovacao existe
        $columnExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.columns
            WHERE table_name = 'igrejas' AND column_name = 'status_aprovacao'
        ) as exists")[0]->exists;

        $query = Igreja::where('id', '!=', Auth::user()->getIgrejaId());

        // Só aplicar filtro se a coluna existir
        if ($columnExists) {
            $query->where('status_aprovacao', 'aprovado');
        }

        // Se há um membro selecionado, filtrar apenas igrejas onde ele NÃO faz parte
        if ($this->selectedMember) {
            $member = IgrejaMembro::find($this->selectedMember);
            if ($member) {
                // Buscar todas as igrejas onde o usuário (não o membro) faz parte
                $userChurchIds = IgrejaMembro::where('user_id', $member->user_id)
                    ->where('status', 'ativo')
                    ->pluck('igreja_id')
                    ->toArray();

                // Excluir essas igrejas da lista disponível
                $query->whereNotIn('id', $userChurchIds);
            }
        }

        if ($this->searchChurch) {
            $query->where('nome', 'ilike', '%' . $this->searchChurch . '%')
                  ->orWhere('sigla', 'ilike', '%' . $this->searchChurch . '%');
        }

        return $query->orderBy('nome')->get();
    }


    public function getMigrationHistoryProperty()
    {
        // Verificar se a tabela existe antes de consultar
        $tableExists = DB::select("SELECT EXISTS (
            SELECT 1 FROM information_schema.tables
            WHERE table_name = 'membro_migracoes'
        ) as exists")[0]->exists;

        if (!$tableExists) {
            return collect(); // Retorna coleção vazia se a tabela não existir
        }

        return DB::table('membro_migracoes')
            ->join('users', 'membro_migracoes.membro_user_id', '=', 'users.id')
            ->leftJoin('igrejas as origem', 'membro_migracoes.igreja_origem_id', '=', 'origem.id')
            ->leftJoin('igrejas as destino', 'membro_migracoes.igreja_destino_id', '=', 'destino.id')
            ->where(function($query) {
                $query->where('membro_migracoes.igreja_origem_id', Auth::user()->getIgrejaId())
                      ->orWhere('membro_migracoes.igreja_destino_id', Auth::user()->getIgrejaId())
                      ->orWhere(function($subQuery) {
                          // Incluir migrações externas onde a igreja origem é desta igreja
                          $subQuery->where('membro_migracoes.igreja_origem_id', Auth::user()->getIgrejaId())
                                   ->whereNull('membro_migracoes.igreja_destino_id');
                      });
            })
            ->select([
                'membro_migracoes.*',
                'users.name as membro_nome',
                'origem.nome as igreja_origem_nome',
                DB::raw('COALESCE(destino.nome, membro_migracoes.igreja_destino_nome) as igreja_destino_nome')
            ])
            ->orderBy('membro_migracoes.data_migracao', 'desc')
            ->limit(20)
            ->get();
    }

    public function printTransferFormForMigration($migrationId)
    {
        try {
            // Buscar dados da migração
            $migration = DB::table('membro_migracoes')
                ->join('users', 'membro_migracoes.membro_user_id', '=', 'users.id')
                ->leftJoin('igrejas as origem', 'membro_migracoes.igreja_origem_id', '=', 'origem.id')
                ->leftJoin('igrejas as destino', 'membro_migracoes.igreja_destino_id', '=', 'destino.id')
                ->where('membro_migracoes.id', $migrationId)
                ->select([
                    'membro_migracoes.*',
                    'users.name as membro_nome',
                    'users.email as membro_email',
                    'origem.nome as igreja_origem_nome',
                    'origem.sigla as igreja_origem_sigla',
                    'origem.nif as igreja_origem_nif',
                    'origem.contacto as igreja_origem_contacto',
                    'origem.logo as igreja_origem_logo',
                    'destino.nome as igreja_destino_nome',
                    'destino.sigla as igreja_destino_sigla',
                    'destino.nif as igreja_destino_nif',
                    'destino.contacto as igreja_destino_contacto',
                    'destino.logo as igreja_destino_logo'
                ])
                ->first();

            if (!$migration) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Migração não encontrada.'
                ]);
                return;
            }

            // Buscar dados do membro atual (se ainda existir na igreja)
            $member = IgrejaMembro::with(['user', 'igreja'])
                ->where('user_id', $migration->membro_user_id)
                ->where('igreja_id', $migration->igreja_destino_id ?? $migration->igreja_origem_id)
                ->first();

            // Se não encontrou o membro atual, criar um objeto simulado
            if (!$member) {
                $member = (object) [
                    'id' => $migration->membro_destino_id ?? $migration->membro_origem_id,
                    'numero_membro' => $migration->numero_membro_destino ?? $migration->numero_membro_origem,
                    'cargo' => $migration->cargo_destino ?? $migration->cargo_origem ?? 'membro',
                    'data_entrada' => $migration->data_migracao,
                    'user' => (object) [
                        'name' => $migration->membro_nome,
                        'email' => $migration->membro_email
                    ],
                    'igreja' => (object) [
                        'nome' => $migration->igreja_origem_nome,
                        'sigla' => $migration->igreja_origem_sigla,
                        'nif' => $migration->igreja_origem_nif,
                        'contacto' => $migration->igreja_origem_contacto,
                        'logo' => $migration->igreja_origem_logo
                    ]
                ];
            }

            // Criar objetos das igrejas
            $sourceChurch = (object) [
                'nome' => $migration->igreja_origem_nome,
                'sigla' => $migration->igreja_origem_sigla,
                'nif' => $migration->igreja_origem_nif,
                'contacto' => $migration->igreja_origem_contacto,
                'logo' => $migration->igreja_origem_logo
            ];

            $targetChurch = null;
            $targetChurchName = null;

            if ($migration->igreja_destino_id) {
                $targetChurch = (object) [
                    'nome' => $migration->igreja_destino_nome,
                    'sigla' => $migration->igreja_destino_sigla,
                    'nif' => $migration->igreja_destino_nif,
                    'contacto' => $migration->igreja_destino_contacto,
                    'logo' => $migration->igreja_destino_logo
                ];
            } else {
                $targetChurchName = $migration->igreja_destino_nome;
            }

            // Determinar o tipo de migração para o documento
            $migrationType = $migration->tipo_migracao;
            $newRole = $migration->cargo_destino ?? $migration->cargo_origem;
            $reason = $migration->motivo ?? 'Migração histórica';

            // Gerar PDF usando DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.transfer-form', [
                'member' => $member,
                'sourceChurch' => $sourceChurch,
                'targetChurch' => $targetChurch,
                'targetChurchName' => $targetChurchName,
                'migrationType' => $migrationType,
                'newRole' => $newRole,
                'reason' => $reason,
                'migratedBy' => Auth::user(),
                'migrationDate' => \Carbon\Carbon::parse($migration->data_migracao),
            ]);

            // Configurar PDF
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96
            ]);

            // Nome do arquivo baseado no tipo de documento
            $fileNamePrefix = match($migrationType) {
                'reintegracao' => 'ficha-reintegracao',
                'mudanca_cargo' => 'ficha-mudanca-cargo',
                default => 'ficha-transferencia'
            };
            $fileName = $fileNamePrefix . '-' . $member->user->name . '-' . \Carbon\Carbon::parse($migration->data_migracao)->format('Y-m-d') . '.pdf';

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Ficha de transferência gerada com sucesso!'
            ]);

            // Retornar download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar ficha de transferência para migração', [
                'migration_id' => $migrationId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao gerar ficha: ' . $e->getMessage()
            ]);
        }
    }

    public function printTransferStats()
    {
        try {



            // Buscar estatísticas reais da igreja logada
            $churchId = Auth::user()->getIgrejaId();
            $church = Igreja::find($churchId);

            if (!$church) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Igreja não encontrada.'
                ]);
                return;
            }

            // Calcular estatísticas do último mês
            $lastMonth = now()->subMonth();
            $lastQuarter = now()->subMonths(3);
            $lastYear = now()->subYear();

            $stats = [
                // Nova Adesão - membros que entraram na igreja
                'nova_adesao_mes' => DB::table('membro_migracoes')
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'nova_adesao')
                    ->where('data_migracao', '>=', $lastMonth)
                    ->count(),

                'nova_adesao_trimestre' => DB::table('membro_migracoes')
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'nova_adesao')
                    ->where('data_migracao', '>=', $lastQuarter)
                    ->count(),

                'nova_adesao_ano' => DB::table('membro_migracoes')
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'nova_adesao')
                    ->where('data_migracao', '>=', $lastYear)
                    ->count(),

                // Transferência (Saída) - membros que saíram desta igreja
                'transferencia_saida_mes' => DB::table('membro_migracoes')
                    ->where('igreja_origem_id', $churchId)
                    ->where('tipo_migracao', 'transferencia')
                    ->where('data_migracao', '>=', $lastMonth)
                    ->count(),

                'transferencia_saida_trimestre' => DB::table('membro_migracoes')
                    ->where('igreja_origem_id', $churchId)
                    ->where('tipo_migracao', 'transferencia')
                    ->where('data_migracao', '>=', $lastQuarter)
                    ->count(),

                'transferencia_saida_ano' => DB::table('membro_migracoes')
                    ->where('igreja_origem_id', $churchId)
                    ->where('tipo_migracao', 'transferencia')
                    ->where('data_migracao', '>=', $lastYear)
                    ->count(),

                // Reintegração - membros que retornaram para esta igreja
                'reintegracao_mes' => DB::table('membro_migracoes')
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'reintegracao')
                    ->where('data_migracao', '>=', $lastMonth)
                    ->count(),

                'reintegracao_trimestre' => DB::table('membro_migracoes')
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'reintegracao')
                    ->where('data_migracao', '>=', $lastQuarter)
                    ->count(),

                'reintegracao_ano' => DB::table('membro_migracoes')
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'reintegracao')
                    ->where('data_migracao', '>=', $lastYear)
                    ->count(),

                // Mudança de Cargo - mudanças dentro da mesma igreja
                'mudanca_cargo_mes' => DB::table('membro_migracoes')
                    ->where('igreja_origem_id', $churchId)
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'mudanca_cargo')
                    ->where('data_migracao', '>=', $lastMonth)
                    ->count(),

                'mudanca_cargo_trimestre' => DB::table('membro_migracoes')
                    ->where('igreja_origem_id', $churchId)
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'mudanca_cargo')
                    ->where('data_migracao', '>=', $lastQuarter)
                    ->count(),

                'mudanca_cargo_ano' => DB::table('membro_migracoes')
                    ->where('igreja_origem_id', $churchId)
                    ->where('igreja_destino_id', $churchId)
                    ->where('tipo_migracao', 'mudanca_cargo')
                    ->where('data_migracao', '>=', $lastYear)
                    ->count(),
            ];

            // Gerar PDF usando DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.transfer-stats', [
                'stats' => $stats,
                'church' => $church,
            ]);

            // Configurar PDF
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 96
            ]);

            // Nome do arquivo
            $fileName = 'relatorio-estatisticas-migracao-' . $church->nome . '-' . now()->format('Y-m-d') . '.pdf';

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Relatório de estatísticas gerado com sucesso!'
            ]);

            // Retornar download
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório de estatísticas', [
                'error' => $e->getMessage(),
                'church_id' => Auth::user()->getIgrejaId()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao gerar relatório: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('church.members.migration', [
            'members' => $this->members,
            'churches' => $this->churches,
            'migrationHistory' => $this->migrationHistory,
            'abaAtiva' => $this->abaAtiva,
        ]);
    }
}

