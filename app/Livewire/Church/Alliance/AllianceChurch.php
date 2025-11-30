<?php

namespace App\Livewire\Church\Alliance;

use Livewire\Component;
use App\Models\Igrejas\AliancaIgreja;
use App\Models\Igrejas\CategoriaIgreja;
use App\Models\Igrejas\Igreja;
use App\Models\Igrejas\IgrejaAlianca;
use App\Models\Igrejas\AliancaLider;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('Alianças | Portal da Igreja')]
#[Layout('components.layouts.app')]
class AllianceChurch extends Component
{
    // Propriedades para filtros e busca
    public $search = '';
    public $categoriaFilter = '';
    public $statusFilter = 'aprovada';
    public $orderBy = 'created_at';
    public $orderDirection = 'desc';
    public $perPage = 12;

    // Propriedades para funcionalidades
    public $isSearching = false;
    public $showCompatibleOnly = false;
    public $showMyAlliances = true; // Mostrar alianças próprias por padrão
    public $compatibleAlliances = [];

    // Propriedades para modais
    public $showConfirmModal = false;
    public $showExitModal = false;
    public $selectedAliancaId = null;
    public $selectedAlianca = null;
    public $isJoining = false; // Para controlar o spinner

    // Propriedades para gerenciamento
    public $novoLiderId = null;
    public $senhaConfirmacao = '';
    public $selectedLiderId = null;

    // Propriedades da igreja atual
    public $igreja;
    public $minhasAliancas = [];

    public function mount()
    {

        $this->carregarIgreja();
        $this->carregarMinhasAliancas();
        $this->atualizarContadoresAliancas(); // ✅ Atualizar contadores no carregamento da página
    }

    protected function carregarIgreja()
    {
        $this->igreja = Auth::user()->getIgreja();
    }

    protected function carregarMinhasAliancas()
    {
        if ($this->igreja) {
            // Buscar alianças das quais a igreja participa (ativo ou inativo) OU que criou
            $this->minhasAliancas = AliancaIgreja::where(function($query) {
                $query->whereHas('participacoes', function($subQuery) {
                    $subQuery->where('igreja_id', $this->igreja->id)
                            ->whereIn('status', ['ativo', 'inativo']); // ✅ Incluir participações inativas
                })
                ->orWhere('created_by', Auth::id()); // ✅ Incluir alianças criadas pelo usuário
            })
            ->with(['categoria', 'criador'])
            ->get();
        }
    }

    protected function atualizarContadoresAliancas()
    {
        if ($this->igreja) {
            // Buscar todas as alianças das quais a igreja participa
            $aliancas = AliancaIgreja::whereHas('participacoes', function($query) {
                $query->where('igreja_id', $this->igreja->id)
                      ->where('status', 'ativo');
            })->get();

            // Atualizar contador de cada aliança
            foreach ($aliancas as $alianca) {
                $alianca->fresh()->atualizarContadorAderentes();
            }
        }
    }

    public function procurarAliancasCompativeis()
    {
        $this->isSearching = true;

        // Simular delay para mostrar spinner
        sleep(1);

        if (!$this->igreja || !$this->igreja->categoria_id) {
            $this->compatibleAlliances = [];
            $this->isSearching = false;
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Sua igreja precisa ter uma categoria definida para encontrar alianças compatíveis.'
            ]);
            return;
        }

        // Buscar alianças das quais a igreja já participa (para excluir)
        $aliancasParticipando = IgrejaAlianca::where('igreja_id', $this->igreja->id)
            ->where('status', 'ativo')
            ->pluck('alianca_id')
            ->toArray();

        // Buscar alianças compatíveis (mesma categoria) - incluindo rascunhos para adesão
        $this->compatibleAlliances = AliancaIgreja::where('categoria_id', $this->igreja->categoria_id)
            ->where('ativa', true)
            ->where('created_by', '!=', Auth::id()) // Excluir alianças próprias
            ->whereNotIn('id', $aliancasParticipando) // ✅ Excluir alianças que já participa
            ->where(function($query) {
                $query->where('status', 'aprovada')
                      ->orWhere('status', 'pronta_aprovacao')
                      ->orWhere('status', 'rascunho'); // ✅ Incluir rascunhos para adesão
            })
            ->with(['categoria', 'criador'])
            ->orderBy('aderentes_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $this->isSearching = false;
        $this->showCompatibleOnly = true;

        // Feedback para o usuário
        if (empty($this->compatibleAlliances)) {
            $this->dispatch('toast', [
                'type' => 'info',
                'message' => 'Nenhuma aliança compatível encontrada no momento.'
            ]);
        } else {
            $this->dispatch('toast', [
                'type' => 'success',
                'message' => count($this->compatibleAlliances) . ' aliança(s) compatível(is) encontrada(s)!'
            ]);
        }
    }

    public function limparBusca()
    {
        $this->showCompatibleOnly = false;
        $this->compatibleAlliances = [];
        $this->search = '';
        $this->categoriaFilter = '';
    }

    public function mostrarMinhasAliancas()
    {
        $this->showMyAlliances = true;
        $this->showCompatibleOnly = false;
        $this->compatibleAlliances = [];
        $this->search = '';
        $this->categoriaFilter = '';
    }

    public function mostrarTodasAliancas()
    {
        $this->showMyAlliances = false;
        $this->showCompatibleOnly = false;
        $this->compatibleAlliances = [];
    }

    public function solicitarSairDaAlianca($aliancaId)
    {
        $this->selectedAliancaId = $aliancaId;
        $this->selectedAlianca = AliancaIgreja::find($aliancaId);
        $this->showExitModal = true;
    }

    public function fecharModalSaida()
    {
        $this->showExitModal = false;
        $this->selectedAliancaId = null;
        $this->selectedAlianca = null;
        $this->senhaConfirmacao = '';
    }

    public function adicionarLider()
    {
        if (!$this->novoLiderId || !$this->selectedAliancaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Selecione um líder para adicionar.'
            ]);
            return;
        }

        $participacao = IgrejaAlianca::where('igreja_id', $this->igreja->id)
            ->where('alianca_id', $this->selectedAliancaId)
            ->where('status', 'ativo')
            ->first();

        if (!$participacao) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Participação não encontrada.'
            ]);
            return;
        }

        $membro = IgrejaMembro::find($this->novoLiderId);
        if (!$membro || $membro->igreja_id !== $this->igreja->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Membro não encontrado ou não pertence à sua igreja.'
            ]);
            return;
        }

        // Verificar se já existe um registro (ativo ou inativo)
        $liderExistente = AliancaLider::where('igreja_alianca_id', $participacao->id)
            ->where('membro_id', $this->novoLiderId)
            ->first();

        if ($liderExistente) {
            // Se já existe, apenas reativar
            if ($liderExistente->ativo) {
                $this->dispatch('toast', [
                    'type' => 'warning',
                    'message' => 'Este membro já é líder ativo nesta aliança.'
                ]);
                return;
            } else {
                // Reativar o líder existente
                $liderExistente->update([
                    'ativo' => true,
                    'data_adesao' => now(),
                    'data_desligamento' => null,
                ]);

                $this->novoLiderId = null;

                $this->dispatch('toast', [
                    'type' => 'success',
                    'message' => 'Líder reativado com sucesso!'
                ]);
                return;
            }
        }

        // Criar novo registro se não existir
        AliancaLider::create([
            'igreja_alianca_id' => $participacao->id,
            'membro_id' => $this->novoLiderId,
            'cargo_na_alianca' => $this->determinarCargoNaAlianca($membro),
            'ativo' => true,
            'data_adesao' => now(),
        ]);

        $this->novoLiderId = null;

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Líder adicionado com sucesso!'
        ]);
    }

    public function removerLider($liderId)
    {
        $lider = AliancaLider::with('membro')->find($liderId);
        if (!$lider) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Líder não encontrado.'
            ]);
            return;
        }

        // Verificar se o líder pertence à igreja do usuário atual
        if ($lider->membro->igreja_id !== $this->igreja->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Você só pode remover líderes da sua própria igreja.'
            ]);
            return;
        }

        // Buscar participação da igreja nesta aliança
        $participacao = IgrejaAlianca::where('igreja_id', $this->igreja->id)
            ->where('alianca_id', $lider->igrejaAlianca->alianca_id)
            ->where('status', 'ativo')
            ->first();

        if (!$participacao) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Participação não encontrada.'
            ]);
            return;
        }

        // Verificar se deve remover toda a igreja ou apenas o líder
        $removerTodaIgreja = $this->deveRemoverTodaIgreja($participacao, $lider->membro);

        if ($removerTodaIgreja) {
            // ✅ Remover toda a igreja (comportamento atual)
            // Log::info('Removendo toda a igreja devido à remoção do último líder sênior', [
            //     'lider_id' => $lider->id,
            //     'membro_id' => $lider->membro->id,
            //     'igreja_id' => $this->igreja->id,
            //     'alianca_id' => $lider->igrejaAlianca->alianca_id,
            //     'cargo' => $lider->cargo_na_alianca,
            //     'user_id' => Auth::id()
            // ]);

            // Desligar todos os líderes desta participação
            AliancaLider::where('igreja_alianca_id', $participacao->id)
                ->update(['ativo' => false, 'data_desligamento' => now()]);

            // Desligar participação
            $participacao->desligar();

            // Recarregar minhas alianças
            $this->carregarMinhasAliancas();

            // Atualizar contador de aderentes
            $lider->igrejaAlianca->alianca->fresh()->atualizarContadorAderentes();

            $mensagem = 'Líder removido e igreja saiu da aliança (era o último líder sênior).';

        } else {
            // ✅ Remover apenas o líder específico
            // Log::info('Removendo apenas líder específico da aliança', [
            //     'lider_id' => $lider->id,
            //     'membro_id' => $lider->membro->id,
            //     'igreja_id' => $this->igreja->id,
            //     'alianca_id' => $lider->igrejaAlianca->alianca_id,
            //     'cargo' => $lider->cargo_na_alianca,
            //     'user_id' => Auth::id()
            // ]);

            // Remover apenas o líder específico
            $this->removerLiderDaAlianca($participacao, $lider->membro);

            $mensagem = 'Líder removido com sucesso! Outros membros continuam na aliança.';
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function abdicarCargo($liderId)
    {
        $lider = AliancaLider::with('membro')->find($liderId);
        if (!$lider) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Líder não encontrado.'
            ]);
            return;
        }

        // Verificar se é o próprio usuário
        if ($lider->membro->user_id !== Auth::id()) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Você só pode abdicar do seu próprio cargo.'
            ]);
            return;
        }

        // Buscar participação da igreja nesta aliança
        $participacao = IgrejaAlianca::where('igreja_id', $this->igreja->id)
            ->where('alianca_id', $lider->igrejaAlianca->alianca_id)
            ->where('status', 'ativo')
            ->first();

        if (!$participacao) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Participação não encontrada.'
            ]);
            return;
        }

        // Verificar se deve remover toda a igreja ou apenas o líder
        $removerTodaIgreja = $this->deveRemoverTodaIgreja($participacao, $lider->membro);

        if ($removerTodaIgreja) {
            // ✅ Remover toda a igreja (comportamento atual)
            // Log::info('Removendo toda a igreja devido à abdicação do último líder sênior', [
            //     'lider_id' => $lider->id,
            //     'membro_id' => $lider->membro->id,
            //     'igreja_id' => $this->igreja->id,
            //     'alianca_id' => $lider->igrejaAlianca->alianca_id,
            //     'cargo' => $lider->cargo_na_alianca,
            //     'user_id' => Auth::id()
            // ]);

            // Desligar todos os líderes desta participação
            AliancaLider::where('igreja_alianca_id', $participacao->id)
                ->update(['ativo' => false, 'data_desligamento' => now()]);

            // Desligar participação
            $participacao->desligar();

            // Recarregar minhas alianças
            $this->carregarMinhasAliancas();

            // Atualizar contador de aderentes
            $lider->igrejaAlianca->alianca->fresh()->atualizarContadorAderentes();

            $mensagem = 'Você abdicou do cargo e sua igreja saiu da aliança (era o último líder sênior).';

        } else {
            // ✅ Remover apenas o líder específico
            // Log::info('Removendo apenas líder específico devido à abdicação', [
            //     'lider_id' => $lider->id,
            //     'membro_id' => $lider->membro->id,
            //     'igreja_id' => $this->igreja->id,
            //     'alianca_id' => $lider->igrejaAlianca->alianca_id,
            //     'cargo' => $lider->cargo_na_alianca,
            //     'user_id' => Auth::id()
            // ]);

            // Remover apenas o líder específico
            $this->removerLiderDaAlianca($participacao, $lider->membro);

            $mensagem = 'Você abdicou do cargo com sucesso! Outros membros da sua igreja continuam na aliança.';
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $mensagem
        ]);
    }

    public function fecharModalConfirmacao()
    {
        $this->showConfirmModal = false;
        $this->selectedAliancaId = null;
        $this->selectedAlianca = null;
    }

    public function fecharModalConfirmacaoAcao()
    {
        $this->selectedLiderId = null;
    }

    public function confirmarAbdicarCargo()
    {
        if (!$this->selectedLiderId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Líder não selecionado.'
            ]);
            return;
        }

        $this->abdicarCargo($this->selectedLiderId);
        $this->selectedLiderId = null;
    }

    public function confirmarRemoverLider()
    {
        if (!$this->selectedLiderId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Líder não selecionado.'
            ]);
            return;
        }

        $this->removerLider($this->selectedLiderId);
        $this->selectedLiderId = null;
    }


    public function sairDaAlianca()
    {
        if (!$this->selectedAlianca || !$this->igreja) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Dados inválidos.'
            ]);
            return;
        }

        // Verificar senha
        if (!Hash::check($this->senhaConfirmacao, Auth::user()->password)) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Senha incorreta.'
            ]);
            return;
        }

        DB::beginTransaction();

        try {
            $participacao = IgrejaAlianca::where('igreja_id', $this->igreja->id)
                ->where('alianca_id', $this->selectedAlianca->id)
                ->where('status', 'ativo')
                ->first();

            if (!$participacao) {
                throw new \Exception('Participação não encontrada.');
            }

            // Buscar o membro que está saindo (usuário atual)
            $membroSaindo = IgrejaMembro::where('igreja_id', $this->igreja->id)
                ->where('user_id', Auth::id())
                ->where('status', 'ativo')
                ->first();

            // Verificar se deve remover toda a igreja ou apenas o líder
            $removerTodaIgreja = $this->deveRemoverTodaIgreja($participacao, $membroSaindo);

            if ($removerTodaIgreja) {
                // ✅ LÓGICA TRADICIONAL: Remover toda a igreja
                // Log::info('Removendo toda a igreja da aliança', [
                //     'igreja_id' => $this->igreja->id,
                //     'alianca_id' => $this->selectedAlianca->id,
                //     'motivo' => 'único líder sênior ou saída geral',
                //     'user_id' => Auth::id()
                // ]);

                // Desligar todos os líderes desta participação
                AliancaLider::where('igreja_alianca_id', $participacao->id)
                    ->update(['ativo' => false, 'data_desligamento' => now()]);

                // Desligar participação
                $participacao->desligar();

                $mensagemSucesso = 'Sua igreja saiu da aliança com sucesso.';

            } else {
                // ✅ LÓGICA INTELIGENTE: Remover apenas o líder específico
                // Log::info('Removendo apenas líder específico da aliança', [
                //     'igreja_id' => $this->igreja->id,
                //     'alianca_id' => $this->selectedAlianca->id,
                //     'membro_id' => $membroSaindo->id ?? null,
                //     'cargo' => $membroSaindo->cargo ?? null,
                //     'motivo' => 'múltiplos líderes sênior disponíveis',
                //     'user_id' => Auth::id()
                // ]);

                // Remover apenas o líder específico
                $this->removerLiderDaAlianca($participacao, $membroSaindo);

                $mensagemSucesso = 'Você saiu da aliança com sucesso. Outros membros da sua igreja continuam participando.';
            }

            // Recarregar minhas alianças
            $this->carregarMinhasAliancas();

            // Atualizar contador de aderentes
            $this->selectedAlianca->fresh()->atualizarContadorAderentes();

            $this->atualizarContadoresAliancas();

            DB::commit();

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => $mensagemSucesso
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            // Log de debug para investigar erros na saída da aliança
            Log::error('Erro ao sair da aliança', [
                'igreja_id' => $this->igreja->id ?? null,
                'alianca_id' => $this->selectedAlianca->id ?? null,
                'user_id' => Auth::id(),
                'remover_toda_igreja' => $removerTodaIgreja ?? null,
                'membro_saindo_id' => $membroSaindo->id ?? null,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao sair da aliança: ' . $e->getMessage()
            ]);
        }
    }

    public function solicitarEntrarNaAlianca($aliancaId)
    {
        $this->selectedAliancaId = $aliancaId;
        $this->selectedAlianca = AliancaIgreja::find($aliancaId);
        $this->showConfirmModal = true;

        // Log::info('Abrindo modal de confirmação', [
        //     'alianca_id' => $aliancaId,
        //     'igreja_id' => $this->igreja->id ?? null,
        //     'user_id' => Auth::id()
        // ]);

        $this->dispatch('showConfirmModal');
    }

    public function confirmarEntrarNaAlianca()
    {
        if (!$this->igreja || !$this->selectedAliancaId) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro: Dados inválidos.'
            ]);
            return;
        }

        $this->isJoining = true;
        $this->showConfirmModal = false;

        try {

            $this->processarEntradaNaAlianca($this->selectedAliancaId);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Sua igreja entrou na aliança com sucesso! Líderes e administradores foram adicionados automaticamente.'
            ]);

            // Fechar o modal confirmModal
            // Log::info('Disparando evento closeConfirmModal', [
            //     'igreja_id' => $this->igreja->id ?? null,
            //     'alianca_id' => $this->selectedAliancaId ?? null,
            //     'user_id' => Auth::id()
            // ]);
            $this->dispatch('closeConfirmModal');

        } catch (\Exception $e) {

            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Erro ao entrar na aliança: ' . $e->getMessage()
            ]);
        } finally {
            $this->isJoining = false;
            $this->selectedAliancaId = null;
            $this->selectedAlianca = null;
        }
    }

    protected function processarEntradaNaAlianca($aliancaId)
    {
        DB::beginTransaction();
        try {
            // Verificar se já está participando ativamente
            $participacaoAtiva = IgrejaAlianca::where('igreja_id', $this->igreja->id)
                ->where('alianca_id', $aliancaId)
                ->where('status', 'ativo')
                ->first();

            if ($participacaoAtiva) {
                throw new \Exception('Sua igreja já faz parte desta aliança.');
            }

            // Verificar se há participação inativa (desligada) para reativar
            $participacaoInativa = IgrejaAlianca::where('igreja_id', $this->igreja->id)
                ->where('alianca_id', $aliancaId)
                ->where('status', 'inativo')
                ->first();

            if ($participacaoInativa) {
                // Reativar participação existente
                $participacaoInativa->reativar();
                $participacao = $participacaoInativa;

                // Reativar líderes existentes
                $this->reativarLideresNaAlianca($participacao);
            } else {
                // Criar nova participação da igreja na aliança
                $participacao = IgrejaAlianca::create([
                    'igreja_id' => $this->igreja->id,
                    'alianca_id' => $aliancaId,
                    'status' => 'ativo',
                    'data_adesao' => now(),
                    'created_by' => Auth::id(),
                ]);

                // Adicionar todos os membros da igreja como participantes da aliança
                $this->adicionarMembrosNaAlianca($participacao);
            }

            // Recarregar minhas alianças
            $this->carregarMinhasAliancas();

            // Atualizar contador de aderentes da aliança
            $alianca = AliancaIgreja::find($aliancaId);
            $alianca->fresh()->atualizarContadorAderentes();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function adicionarMembrosNaAlianca($participacao)
    {
        // Buscar apenas líderes/admin/pastores/ministros da igreja
        $membrosLideres = IgrejaMembro::where('igreja_id', $this->igreja->id)
            ->where('status', 'ativo')
            ->whereIn('cargo', ['admin', 'pastor', 'ministro' ])
            ->with('user')
            ->get();

        foreach ($membrosLideres as $membro) {
            // Verificar se já existe um registro (ativo ou inativo)
            $liderExistente = AliancaLider::where('igreja_alianca_id', $participacao->id)
                ->where('membro_id', $membro->id)
                ->first();

            if ($liderExistente) {
                // Se já existe, apenas reativar se estiver inativo
                if (!$liderExistente->ativo) {
                    $liderExistente->update([
                        'ativo' => true,
                        'data_adesao' => now(),
                        'data_desligamento' => null,
                        'cargo_na_alianca' => $this->determinarCargoNaAlianca($membro),
                    ]);
                }
            } else {
                // Criar novo registro se não existir
                AliancaLider::create([
                    'igreja_alianca_id' => $participacao->id,
                    'membro_id' => $membro->id,
                    'cargo_na_alianca' => $this->determinarCargoNaAlianca($membro),
                    'ativo' => true,
                    'data_adesao' => now(),
                ]);
            }
        }

        // Sempre adicionar o usuário atual se não estiver na lista acima
        $usuarioAtual = Auth::user();
        $membroUsuario = IgrejaMembro::where('igreja_id', $this->igreja->id)
            ->where('user_id', $usuarioAtual->id)
            ->where('status', 'ativo')
            ->first();

        if ($membroUsuario) {
            $liderUsuarioExistente = AliancaLider::where('igreja_alianca_id', $participacao->id)
                ->where('membro_id', $membroUsuario->id)
                ->first();

            if ($liderUsuarioExistente) {
                // Reativar se estiver inativo
                if (!$liderUsuarioExistente->ativo) {
                    $liderUsuarioExistente->update([
                        'ativo' => true,
                        'data_adesao' => now(),
                        'data_desligamento' => null,
                        'cargo_na_alianca' => $this->determinarCargoNaAlianca($membroUsuario),
                    ]);
                }
            } else {
                // Criar novo se não existir
                AliancaLider::create([
                    'igreja_alianca_id' => $participacao->id,
                    'membro_id' => $membroUsuario->id,
                    'cargo_na_alianca' => $this->determinarCargoNaAlianca($membroUsuario),
                    'ativo' => true,
                    'data_adesao' => now(),
                ]);
            }
        }
    }

    protected function reativarLideresNaAlianca($participacao)
    {
        // Buscar todos os líderes inativos desta participação
        $lideresInativos = AliancaLider::where('igreja_alianca_id', $participacao->id)
            ->where('ativo', false)
            ->with('membro')
            ->get();

        foreach ($lideresInativos as $lider) {
            // Verificar se o membro ainda está ativo na igreja
            if ($lider->membro && $lider->membro->status === 'ativo') {
                $lider->update([
                    'ativo' => true,
                    'data_adesao' => now(),
                    'data_desligamento' => null,
                    'cargo_na_alianca' => $this->determinarCargoNaAlianca($lider->membro),
                ]);
            }
        }

        // Verificar se há novos líderes que não estavam na aliança antes
        $this->adicionarMembrosNaAlianca($participacao);
    }

    protected function determinarCargoNaAlianca($membro)
    {
        // Determinar cargo baseado no cargo do membro na igreja
        return match($membro->cargo) {
            'pastor' => 'pastor',
            'admin' => 'admin',
            'ministro' => 'ministro',
            'diacono' => 'diacono',
            default => 'membro'
        };
    }

    /**
     * Verifica se deve remover toda a igreja ou apenas o líder específico
     * quando um admin/pastor/ministro sai da aliança
     */
    protected function deveRemoverTodaIgreja($participacao, $membroSaindo = null)
    {
        // Se não há membro específico saindo, verificar se é uma saída geral
        if (!$membroSaindo) {
            return true; // Comportamento padrão: remover toda igreja
        }

        // Verificar se o membro saindo é admin, pastor ou ministro
        if (!in_array($membroSaindo->cargo, ['admin', 'pastor', 'ministro' ])) {
            return false; // Não é líder sênior, apenas desliga ele
        }

        // Contar quantos admin/pastor/ministro ativos existem na aliança para esta igreja
        $lideresSeniorAtivos = AliancaLider::where('igreja_alianca_id', $participacao->id)
            ->where('ativo', true)
            ->whereHas('membro', function($query) {
                $query->where('igreja_id', $this->igreja->id)
                      ->whereIn('cargo', ['admin', 'pastor', 'ministro' ]);
            })
            ->count();

        // Se há apenas 1 líder sênior ativo, remover toda a igreja
        // Se há mais de 1, apenas remover o líder específico
        return $lideresSeniorAtivos <= 1;
    }

    /**
     * Remove apenas um líder específico da aliança
     */
    protected function removerLiderDaAlianca($participacao, $membro)
    {
        $lider = AliancaLider::where('igreja_alianca_id', $participacao->id)
            ->where('membro_id', $membro->id)
            ->first();

        if ($lider) {
            $lider->desligar();

            // Log da ação
            // Log::info('Líder removido da aliança (saída individual)', [
            //     'lider_id' => $lider->id,
            //     'membro_id' => $membro->id,
            //     'igreja_id' => $this->igreja->id,
            //     'alianca_id' => $participacao->alianca_id,
            //     'user_id' => Auth::id()
            // ]);
        }
    }

    public function adicionarMembroNaAlianca($participacaoId, $membroId)
    {
        $participacao = IgrejaAlianca::find($participacaoId);
        if (!$participacao || $participacao->igreja_id !== $this->igreja->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Participação não encontrada ou sem permissão.'
            ]);
            return;
        }

        $membro = IgrejaMembro::find($membroId);
        if (!$membro || $membro->igreja_id !== $this->igreja->id) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Membro não encontrado ou não pertence à sua igreja.'
            ]);
            return;
        }

        // Verificar se já está na aliança
        if (AliancaLider::where('igreja_alianca_id', $participacaoId)
            ->where('membro_id', $membroId)->exists()) {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => 'Este membro já faz parte desta aliança.'
            ]);
            return;
        }

        AliancaLider::create([
            'igreja_alianca_id' => $participacaoId,
            'membro_id' => $membroId,
            'cargo_na_alianca' => $this->determinarCargoNaAlianca($membro),
            'ativo' => true,
            'data_adesao' => now(),
        ]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Membro adicionado à aliança com sucesso!'
        ]);
    }

    public function removerMembroDaAlianca($participacaoId, $membroId)
    {
        $lider = AliancaLider::where('igreja_alianca_id', $participacaoId)
            ->where('membro_id', $membroId)
            ->first();

        if (!$lider) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Membro não encontrado nesta aliança.'
            ]);
            return;
        }

        // Verificar se é o último admin/pastor
        $participacao = IgrejaAlianca::find($participacaoId);
        $lideresAtivos = AliancaLider::where('igreja_alianca_id', $participacaoId)
            ->where('ativo', true)
            ->whereIn('cargo_na_alianca', ['admin', 'pastor' ])
            ->count();

        if ($lideresAtivos <= 1 && in_array($lider->cargo_na_alianca, ['admin', 'pastor' ])) {
            $this->dispatch('toast', [
                'type' => 'danger',
                'message' => 'Não é possível remover o último administrador/pastor da aliança.'
            ]);
            return;
        }

        $lider->desligar();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Membro removido da aliança.'
        ]);
    }

    public function getAliancasProperty()
    {
        // Se estamos mostrando apenas alianças próprias
        if ($this->showMyAlliances) {
            return collect($this->minhasAliancas);
        }

        $query = AliancaIgreja::with(['categoria', 'criador', 'aprovador'])
            ->where('ativa', true);

        // Aplicar filtro de status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Aplicar filtro de categoria
        if ($this->categoriaFilter) {
            $query->where('categoria_id', $this->categoriaFilter);
        }

        // Aplicar filtro de busca
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nome', 'ilike', '%' . $this->search . '%')
                  ->orWhere('descricao', 'ilike', '%' . $this->search . '%')
                  ->orWhere('sigla', 'ilike', '%' . $this->search . '%');
            });
        }

        // Mostrar apenas alianças compatíveis se ativado
        if ($this->showCompatibleOnly && $this->igreja) {
            $query->where('categoria_id', $this->igreja->categoria_id)
                  ->where('created_by', '!=', Auth::id());
        }

        // Excluir alianças das quais o usuário já participa ativamente
        $aliancasParticipando = IgrejaAlianca::where('igreja_id', $this->igreja->id ?? null)
            ->where('status', 'ativo')
            ->pluck('alianca_id')
            ->toArray();

        if (!empty($aliancasParticipando)) {
            $query->whereNotIn('id', $aliancasParticipando);
        }

        // Aplicar ordenação
        $query->orderBy($this->orderBy, $this->orderDirection);

        return $query->paginate($this->perPage);
    }

    public function getCategoriasProperty()
    {
        return CategoriaIgreja::where('ativa', true)->orderBy('nome')->get();
    }

    public function getMembrosDisponiveisProperty()
    {
        if (!$this->igreja || !$this->selectedAliancaId) {
            return collect();
        }

        // Buscar participação da igreja nesta aliança
        $participacao = IgrejaAlianca::where('igreja_id', $this->igreja->id)
            ->where('alianca_id', $this->selectedAliancaId)
            ->where('status', 'ativo')
            ->first();

        if (!$participacao) {
            return collect();
        }

        // Buscar membros que ainda não são líderes nesta aliança
        $lideresIds = AliancaLider::where('igreja_alianca_id', $participacao->id)
            ->where('ativo', true)
            ->pluck('membro_id')
            ->toArray();

        return IgrejaMembro::where('igreja_id', $this->igreja->id)
            ->where('status', 'ativo')
            ->whereIn('cargo', ['admin', 'pastor', 'ministro' ])
            ->whereNotIn('id', $lideresIds)
            ->with('user')
            ->get();
    }

    public function getEhCriadorProperty()
    {
        return $this->selectedAlianca && $this->selectedAlianca->created_by === Auth::id();
    }

    public function getEhAdminProperty()
    {
        return $this->igreja && IgrejaMembro::where('igreja_id', $this->igreja->id)
            ->where('user_id', Auth::id())
            ->where('cargo', 'admin')
            ->where('status', 'ativo')
            ->exists();
    }

    public function render()
    {
        return view('church.alliance.alliance-church', [
            'aliancas' => $this->aliancas,
            'categorias' => $this->categorias,
        ]);
    }
}
