<?php

namespace App\Livewire\Church\Members;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Igrejas\Ministerio;
use Livewire\WithoutUrlPagination;
use App\Models\Igrejas\IgrejaMembro;
use App\Models\Igrejas\MembroPerfil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Igrejas\IgrejaMembrosHistorico;
use App\Models\Igrejas\IgrejaMembrosMinisterio;
use App\Services\MemberDeletionService;
use App\Mail\MemberCredentials;
use Illuminate\Support\Facades\Mail;

#[Title('Membros | Portal da Igreja')]
#[Layout('components.layouts.app')]
class Members extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedMinistry = '';
    public $selectedStatus = '';
    public $perPage = 10;

    // Controle de envio de credenciais
    public $envioCredenciaisAtivado = false;

    // Modal de exclusão
    public $showDeleteModal = false;
    public $memberToDelete = null;
    public $deletePassword = '';
    public $deleteError = '';

    // Modal properties for ministries
    public $selectedMemberForMinistry = null;
    public $selectedMinistryToAdd = '';

    // Modal properties
    public $showModal = false;
    public $editingMember = null;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $data_nascimento = '';
    public $genero = '';
    public $cargo = '';
    public $endereco = '';
    public $data_entrada = '';
    public $status = 'ativo';
    public $observacoes = '';

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|phone:AO',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,feminino,nao_informado',
            'cargo' => 'required|in:membro,diacono,obreiro,ministro,pastor',
            'endereco' => 'nullable|string|max:500',
            'data_entrada' => 'required|date',
            'status' => 'required|in:ativo,inativo,falecido,transferido',
            'observacoes' => 'nullable|string|max:500',
        ];

        if ($this->editingMember) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->editingMember->user_id;
        } else {
            $rules['email'] = 'required|email|unique:users,email';
        }

        return $rules;
    }

    protected function messages(){

        $messages = [
            'phone.validation'=>'O número de telefone deve ser um número válido'
        ];

        return $messages;
    }


    protected $listeners = [
        'refreshMembers' => '$refresh',
        'clearDeleteModalFields' => 'clearDeleteModalFields'
    ];



    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedMinistry()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function setMinistryFilter($ministry)
    {
        $this->selectedMinistry = $ministry;
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->selectedStatus = $status;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedMinistry = '';
        $this->selectedStatus = '';
        $this->resetPage();
    }

    public function openMinistryModal($memberId)
    {
        $this->selectedMemberForMinistry = IgrejaMembro::with(['user', 'ministerios'])->find($memberId);
        $this->selectedMinistryToAdd = '';
        $this->resetValidation();
    }

    public function addMemberToMinistry()
    {
        $this->validate([
            'selectedMinistryToAdd' => 'required|exists:ministerios,id',
        ]);

        if (!$this->selectedMemberForMinistry) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Membro não encontrado.'
            ]);
            return;
        }

        try {
            // Log para debug
            Log::info('Adicionando membro ao ministério', [
                'membro_id' => $this->selectedMemberForMinistry->id,
                'ministerio_id' => $this->selectedMinistryToAdd,
            ]);

            // Verificar se o membro já está no ministério
            $alreadyInMinistry = $this->selectedMemberForMinistry->ministerios()
                ->where('ministerios.id', $this->selectedMinistryToAdd)
                ->exists();

            if ($alreadyInMinistry) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Este membro já faz parte deste ministério.'
                ]);
                return;
            }

            // Adicionar membro ao ministério usando o modelo diretamente
            IgrejaMembrosMinisterio::create([
                'membro_id' => $this->selectedMemberForMinistry->id,
                'ministerio_id' => $this->selectedMinistryToAdd,
                'funcao' => null, // Pode ser definido depois se necessário
            ]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Membro adicionado ao ministério com sucesso!'
            ]);

            // Fechar modal e resetar
            $this->selectedMemberForMinistry = null;
            $this->selectedMinistryToAdd = '';
            $this->dispatch('refreshMembers');

        } catch (\Exception $e) {
            Log::error('Erro ao adicionar membro ao ministério', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'membro_id' => $this->selectedMemberForMinistry?->id,
                'ministerio_id' => $this->selectedMinistryToAdd,
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao adicionar membro ao ministério: ' . $e->getMessage()
            ]);
        }
    }

    public function openModal($memberId = null)
    {
        if ($memberId) {
            $member = IgrejaMembro::with(['user', 'membroPerfil'])->find($memberId);
            if ($member) {
                $this->editingMember = $member;
                $this->name = $member->user->name ?? '';
                $this->email = $member->user->email ?? '';
                $this->phone = $member->user->phone ?? '';
                $this->data_nascimento = $member->membroPerfil ? ($member->membroPerfil->data_nascimento ? $member->membroPerfil->data_nascimento->format('Y-m-d') : '') : '';
                $this->genero = $member->membroPerfil ? $member->membroPerfil->genero : '';
                $this->cargo = $member->cargo;
                $this->endereco = $member->membroPerfil ? $member->membroPerfil->endereco : '';
                $this->data_entrada = $member->data_entrada ? $member->data_entrada->format('Y-m-d') : '';
                $this->status = $member->status;
                $this->observacoes = $member->membroPerfil ? $member->membroPerfil->observacoes : '';
            }
        } else {
            $this->resetModal();
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetModal();
    }

    private function resetModal()
    {
        $this->editingMember = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->data_nascimento = '';
        $this->genero = '';
        $this->cargo = '';
        $this->endereco = '';
        $this->data_entrada = '';
        $this->status = 'ativo';
        $this->observacoes = '';
        $this->resetValidation();
    }

    public function saveMember()
    {
        $this->validate();

        // Verificar se há erros de validação
        if ($this->getErrorBag()->isNotEmpty()) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Por favor, corrija os erros antes de salvar.'
            ]);
            return;
        }

        try {
            // Obter a igreja do usuário logado
            $igrejaId = Auth::user()->getIgrejaId();

            if (!$igrejaId) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Usuário não está associado a nenhuma Igreja ativa'
                ]);
                return;
            }

            if ($this->editingMember) {
                // Lógica para editar (atualizar os registros relacionados)
                $user = $this->editingMember->user;
                $user->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'role' => $this->cargo,
                ]);

                $this->editingMember->update([
                    'cargo' => $this->cargo,
                    'status' => $this->status,
                    'data_entrada' => $this->data_entrada,
                ]);

                if ($this->editingMember->membroPerfil) {

                    $this->editingMember->membroPerfil->update([
                        'genero' => $this->genero ?: 'nao_informado',
                        'data_nascimento' => $this->data_nascimento ?: null,
                        'endereco' => $this->endereco ?: null,
                        'observacoes' => $this->observacoes ?: null,
                    ]);
                } else {

                    // Create MembroPerfil if it doesn't exist
                    MembroPerfil::create([
                        'id' => (string) Str::uuid(),
                        'igreja_membro_id' => $this->editingMember->id,
                        'genero' => $this->genero ?: 'nao_informado',
                        'data_nascimento' => $this->data_nascimento ?: null,
                        'endereco' => $this->endereco ?: null,
                        'observacoes' => $this->observacoes ?: null,
                        'created_by' => Auth::id(),
                    ]);
                }

                $this->dispatch('toast', ['message' => 'Membro atualizado com sucesso!', 'type' => 'success']);

            } else {

                // Gerar senha segura para o novo membro
                $senhaGerada = $this->gerarSenhaMembro();

                // Criar novo usuário
                $user = User::create([
                    'id' => (string) Str::uuid(),
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password' => Hash::make($senhaGerada),
                    'role' => $this->cargo,
                    'is_active' => true,
                    'created_by' => Auth::id(),
                ]);

                // Criar IgrejaMembro
                $igrejaMembro = IgrejaMembro::create([
                    'id' => (string) Str::uuid(),
                    'igreja_id' => $igrejaId,
                    'user_id' => $user->id,
                    'cargo' => $this->cargo,
                    'status' => $this->status,
                    'data_entrada' => $this->data_entrada,
                    'created_by' => Auth::id(),
                ]);

                // Criar MembroPerfil
                MembroPerfil::create([
                    'id' => (string) Str::uuid(),
                    'igreja_membro_id' => $igrejaMembro->id,
                    'genero' => $this->genero ?: 'nao_informado',
                    'data_nascimento' => $this->data_nascimento ?: null,
                    'endereco' => $this->endereco ?: null,
                    'observacoes' => $this->observacoes ?: null,
                    'created_by' => Auth::id(),
                ]);

                // Criar IgrejaMembrosHistorico
                IgrejaMembrosHistorico::create([
                    'id' => (string) Str::uuid(),
                    'igreja_membro_id' => $igrejaMembro->id,
                    'cargo' => $this->cargo,
                    'inicio' => $this->data_entrada,
                    'fim' => null,
                ]);

                // Enviar email com credenciais automaticamente
                try {

                    Mail::to($user->email)->send(
                        new MemberCredentials($user, $senhaGerada, $igrejaMembro->igreja->nome)
                    );

                    $this->dispatch('toast', [
                        'message' => 'Membro cadastrado com sucesso! Credenciais enviadas por email.',
                        'type' => 'success'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erro ao enviar email de credenciais', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ]);

                    $this->dispatch('toast', [
                        'message' => 'Membro cadastrado, mas houve erro no envio do email. Senha gerada: ' . $senhaGerada,
                        'type' => 'warning'
                    ]);
                }
            }

            $this->closeModal();
            $this->dispatch('refreshMembers');
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao salvar membro: ' . $e->getMessage()
            ]);
        }
    }

    public function openDeleteModal($memberId)
    {

        $this->memberToDelete = IgrejaMembro::with(['user', 'igreja'])->find($memberId);
        $this->deletePassword = '';
        $this->deleteError = '';
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->memberToDelete = null;
        $this->deletePassword = '';
        $this->deleteError = '';
    }

    public function clearDeleteModalFields()
    {
        $this->deletePassword = '';
        $this->deleteError = '';
        $this->memberToDelete = null;
        $this->showDeleteModal = false;
        $this->resetValidation();
    }

    public function confirmDeleteMember()
    {
        $this->validate([
            'deletePassword' => 'required|string',
        ], [
            'deletePassword.required' => 'A senha é obrigatória para confirmar a exclusão.',
        ]);

        // Verificar senha do usuário logado
        if (!Hash::check($this->deletePassword, Auth::user()->password)) {
            $this->deleteError = 'Senha incorreta. Tente novamente.';
            return;
        }

        if (!$this->memberToDelete) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Membro não encontrado.'
            ]);
            $this->closeDeleteModal();
            return;
        }

        try {
            // Usar o serviço de exclusão de membros
            $deletionService = new MemberDeletionService();

            // VALIDAR PERMISSÕES ANTES DA EXCLUSÃO
            $deletionService->validateDeletionPermission(Auth::user(), $this->memberToDelete);

            if ($deletionService->wasCreatedByChurchMember($this->memberToDelete)) {
                // EXCLUSÃO COMPLETA: Membro foi adicionado por outro usuário da igreja
                $deletionService->deleteMemberCompletely(
                    $this->memberToDelete->user,
                    $this->memberToDelete,
                    Auth::user()
                );

                $mensagem = 'Membro removido completamente do sistema!';

            } else {
                // REMOÇÃO DA IGREJA: Membro se registrou sozinho
                $deletionService->removeMemberFromChurch(
                    $this->memberToDelete,
                    Auth::user()
                );

                $mensagem = 'Membro removido da igreja! O usuário permanece no sistema como anônimo.';
            }

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagem
            ]);

            // Fechar modal do Bootstrap
            $this->dispatch('closeDeleteModal');

            $this->showDeleteModal = false;
            $this->memberToDelete = null;
            $this->deletePassword = '';
            $this->deleteError = '';
            $this->dispatch('refreshMembers');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erro ao excluir membro', [
                'membro_id' => $this->memberToDelete->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao excluir membro: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleMemberStatus($memberId)
    {

        $member = IgrejaMembro::find($memberId);
        if ($member) {
            $newStatus = $member->status === 'ativo' ? 'inativo' : 'ativo';
            $member->update(['status' => $newStatus]);
            $this->dispatch('toast', ['message' => 'Status do membro alterado com sucesso!', 'type' => 'success']);
            $this->dispatch('refreshMembers');
        }
    }

    /**
     * Gera uma senha segura para o membro
     * Requisitos: mínimo 6 dígitos, sem caracteres especiais
     * Opções: apenas números ou "omin" + números
     */
    private function gerarSenhaMembro()
    {
        // 70% chance de gerar senha com "omin" + números
        // 30% chance de gerar apenas números
        if (rand(1, 10) <= 7) {
            // Formato: omin + 3-4 números (total mínimo 8 caracteres)
            $numeros = str_pad(rand(0, 9999), rand(3, 4), '0', STR_PAD_LEFT);
            return 'omin' . $numeros;
        } else {
            // Apenas números (6-8 dígitos)
            return str_pad(rand(100000, 99999999), rand(6, 8), '0', STR_PAD_LEFT);
        }
    }

    /**
     * Alterna o status de envio de credenciais
     */
    public function toggleEnvioCredenciais()
    {
        $this->envioCredenciaisAtivado = !$this->envioCredenciaisAtivado;

        $this->dispatch('toast', [
            'type' =>$this->envioCredenciaisAtivado ? 'info' : 'warning',
            'message' => $this->envioCredenciaisAtivado
                ? 'Envio de credenciais ativado. Os botões de envio estão disponíveis.'
                : 'Envio de credenciais desativado. Os botões de envio estão bloqueados.'
        ]);
    }

    /**
     * Envia email com credenciais para o membro
     */
    public function enviarCredenciais($memberId)
    {
        // Verificar se o envio está ativado
        if (!$this->envioCredenciaisAtivado) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Envio de credenciais está desativado. Ative o envio primeiro.'
            ]);
            return;
        }

        try {
            $member = IgrejaMembro::with(['user', 'igreja'])->find($memberId);

            if (!$member) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Membro não encontrado.'
                ]);
                return;
            }

            // Gerar nova senha
            $novaSenha = $this->gerarSenhaMembro();

            // Atualizar senha do usuário
            $member->user->update([
                'password' => Hash::make($novaSenha)
            ]);

            // Enviar email
            Mail::to($member->user->email)->send(
                new MemberCredentials($member->user, $novaSenha, $member->igreja->nome)
            );

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Credenciais enviadas com sucesso para ' . $member->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao enviar credenciais', [
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao enviar credenciais: ' . $e->getMessage()
            ]);
        }
    }

    public function generateMemberCard($memberId)
    {
        try {
            $member = IgrejaMembro::with(['user', 'igreja', 'membroPerfil', 'ministerios'])->findOrFail($memberId);

            // Verificar se o usuário tem permissão para gerar ficha deste membro
            $userIgrejaId = Auth::user()->getIgrejaId();
            if ($member->igreja_id !== $userIgrejaId) {
                $this->dispatch('toast', [
                    'type' => 'danger',
                    'message' => 'Você não tem permissão para gerar ficha deste membro.'
                ]);
                return;
            }

            // Gerar PDF usando DomPDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('church.members.pdf.member-card', [
                'member' => $member,
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
            $fileName = 'ficha-membro-' . $member->user->name . '-' . now()->format('Y-m-d') . '.pdf';

            // Salvar PDF temporariamente para anexar no email
            $tempPath = storage_path('app/temp/' . $fileName);
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            file_put_contents($tempPath, $pdf->output());

            // Enviar email com o PDF anexado
            try {
                
                \Illuminate\Support\Facades\Mail::to($member->user->email)->send(
                    new \App\Mail\MemberCardMail($member, $tempPath)
                );

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Ficha de membro gerada e enviada por email com sucesso!'
                ]);

                // Limpar arquivo temporário após envio
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

            } catch (\Exception $emailError) {
                Log::error('Erro ao enviar email da ficha de membro', [
                    'member_id' => $memberId,
                    'email' => $member->user->email,
                    'error' => $emailError->getMessage()
                ]);

                // Mesmo com erro no email, permitir download do PDF
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Ficha gerada, mas houve erro no envio por email. Fazendo download...'
                ]);
            }

            // Retornar download do PDF
            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, $fileName);

        } catch (\Exception $e) {
            // Log do erro mas não interromper o fluxo
            Log::error('Erro ao gerar ficha de membro', [
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            // Mostrar aviso ao usuário
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao gerar ficha de membro: ' . $e->getMessage()
            ]);
        }
    }

    public function getMembers()
    {
        // Obter a igreja do usuário logado
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Usuário não está associado a nenhuma Igreja ativa'
            ]);
            return IgrejaMembro::query()->whereRaw('1=0')->paginate($this->perPage);
        }

        $query = IgrejaMembro::query()
            ->with(['user', 'igreja', 'membroPerfil', 'ministerios'])
            ->where('igreja_id', $igrejaId)
            ->whereHas('user', function ($q) {
                $q->where('role', '!=', 'admin');
            });

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedMinistry) {
            $query->whereHas('ministerios', function ($q) {
                $q->where('ministerios.id', $this->selectedMinistry);
            });
        }

        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($this->perPage);
    }

    public function getMemberStats()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        if (!$igrejaId) {
            return [
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'new_this_month' => 0,
            ];
        }

        $totalMembers = IgrejaMembro::where('igreja_id', $igrejaId)->count();
        $activeMembers = IgrejaMembro::where('igreja_id', $igrejaId)->where('status', 'ativo')->count();
        $inactiveMembers = IgrejaMembro::where('igreja_id', $igrejaId)->where('status', 'inativo')->count();
        $newMembersThisMonth = IgrejaMembro::where('igreja_id', $igrejaId)
                                          ->whereMonth('created_at', now()->month)
                                          ->whereYear('created_at', now()->year)
                                          ->count();

        return [
            'total' => $totalMembers,
            'active' => $activeMembers,
            'inactive' => $inactiveMembers,
            'new_this_month' => $newMembersThisMonth,
        ];
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'transferido' => 'Transferido',
            'falecido' => 'Falecido',
            default => 'Desconhecido'
        };
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'ativo' => 'success',
            'inativo' => 'secondary',
            'transferido' => 'warning',
            'falecido' => 'dark',
            default => 'secondary'
        };
    }

    public function render()
    {
        $igrejaId = Auth::user()->getIgrejaId();

        $ministerios = $igrejaId ? Ministerio::where('igreja_id', $igrejaId)->orderBy('nome')->get() : collect();

        // Ministérios disponíveis para o membro selecionado (que ele ainda não faz parte)
        $availableMinistries = collect();
        if ($this->selectedMemberForMinistry) {
            $memberMinistryIds = $this->selectedMemberForMinistry->ministerios->pluck('id');
            $availableMinistries = $ministerios->whereNotIn('id', $memberMinistryIds);
        }

        return view('church.members.members', [
            'members' => $this->getMembers(),
            'stats' => $this->getMemberStats(),
            'ministerios' => $ministerios,
            'availableMinistries' => $availableMinistries,
        ]);
    }
}
