<?php

namespace App\Livewire\Billings;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Mail\PagamentoAprovadoMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Billings\AssinaturaLog;
use App\Models\Billings\IgrejaAssinada;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\Trial\TrialUser;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\PagamentoAssinaturaIgreja;

#[Title('Pedidos de assinaturas| Aprovação e Processamento')]
#[Layout('components.layouts.app')]
class Subscribers extends Component
{
    public $pagamentosPendentes;
    public $pagamentoSelecionado;
    public $mostrarModalConfirmacao = false;
    public $observacoesConfirmacao = '';
    public $observacoesRejeicao = '';
    public $processando = false;
    public $mostrarModalRejeicao = false;

    public function mount()
    {
        $this->carregarPagamentosPendentes();
    }

    public function carregarPagamentosPendentes()
    {
        $this->pagamentosPendentes = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
            ->pendentes()
            ->orderBy('data_pagamento', 'desc')
            ->get();
    }

    public function selecionarPagamento($pagamentoId)
    {
        $this->pagamentoSelecionado = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
            ->where('id', $pagamentoId) // Usar where ao invés de find para UUID
            ->first();

        $this->mostrarModalConfirmacao = true;

        // Buscar informações da assinatura atual/trial da igreja
        $assinaturaAtual = $this->pagamentoSelecionado->igreja->assinaturaAtual;

        // Verificar se algum usuário da igreja tem trial ativo
        $trial = null;
        $membros = $this->pagamentoSelecionado->igreja->membros()->with('user.trial')->get();

        foreach ($membros as $membro) {
            if ($membro->user && $membro->user->trial && $membro->user->trial->isAtivo()) {
                $trial = $membro->user->trial;
                break;
            }
        }

        $assinaturaInfo = null;
        if ($assinaturaAtual && $assinaturaAtual->estaAtiva()) {
            $assinaturaInfo = [
                'tipo' => 'assinatura',
                'pacote' => $assinaturaAtual->pacote->nome ?? 'N/A',
                'status' => $assinaturaAtual->status,
                'data_inicio' => $assinaturaAtual->data_inicio?->format('d/m/Y'),
                'data_fim' => $assinaturaAtual->data_fim?->format('d/m/Y') ?? 'Vitalício',
                'dias_restantes' => $assinaturaAtual->data_fim ? now()->diffInDays($assinaturaAtual->data_fim, false) : null,
            ];
        } elseif ($trial && $trial->isAtivo()) {
            $assinaturaInfo = [
                'tipo' => 'trial',
                'pacote' => 'Trial Gratuito',
                'status' => $trial->status,
                'data_inicio' => $trial->data_inicio?->format('d/m/Y'),
                'data_fim' => $trial->data_fim?->format('d/m/Y'),
                'dias_restantes' => $trial->diasRestantes(),
            ];
        }

        // Disparar evento para mostrar modal com dados
        $this->dispatch('mostrarModalConfirmacao', [
            'igreja' => $this->pagamentoSelecionado->igreja->nome ?? 'N/A',
            'pacote' => $this->pagamentoSelecionado->pacote_nome ?? 'N/A',
            'valor' => $this->pagamentoSelecionado->valor ?? 0,
            'data_pagamento' => $this->pagamentoSelecionado->data_pagamento ?? null,
            'assinatura_atual' => $assinaturaInfo,
        ]);
    }

    public function fecharModal()
    {
        $this->mostrarModalConfirmacao = false;
        $this->mostrarModalRejeicao = false;
        $this->pagamentoSelecionado = null;
        $this->observacoesConfirmacao = '';
        $this->observacoesRejeicao = '';
    }

    public function selecionarPagamentoRejeicao($pagamentoId)
    {
        $this->pagamentoSelecionado = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
            ->where('id', $pagamentoId)
            ->first();

        $this->mostrarModalRejeicao = true;

        // Disparar evento para mostrar modal de rejeição
        $this->dispatch('mostrarModalRejeicao', [
            'igreja' => $this->pagamentoSelecionado->igreja->nome ?? 'N/A',
            'pacote' => $this->pagamentoSelecionado->pacote_nome ?? 'N/A',
            'valor' => $this->pagamentoSelecionado->valor ?? 0,
            'data_pagamento' => $this->pagamentoSelecionado->data_pagamento ?? null,
        ]);
    }

    public function rejeitarPagamento()
    {
        $this->validate([
            'observacoesRejeicao' => 'required|string|max:500'
        ], [
            'observacoesRejeicao.required' => 'O motivo da rejeição é obrigatório.'
        ]);

        // Validar se o pagamento ainda pode ser rejeitado
        if (!$this->pagamentoSelecionado || !$this->pagamentoSelecionado->isPendente()) {
            $this->dispatch('show-error', 'Este pagamento não pode ser rejeitado.');
            return;
        }

        try {
            
            DB::beginTransaction();

            // Rejeitar o pagamento
            $this->pagamentoSelecionado->rejeitar(Auth::user(), $this->observacoesRejeicao);

            // Criar log
            AssinaturaLog::create([
                'igreja_id' => $this->pagamentoSelecionado->igreja_id,
                'pacote_id' => $this->pagamentoSelecionado->pacote_id,
                'acao' => 'rejeitado',
                'descricao' => 'Pagamento rejeitado: ' . $this->observacoesRejeicao,
                'usuario_id' => Auth::id(),
                'data_acao' => now(),
                'detalhes' => [
                    'pagamento_id' => $this->pagamentoSelecionado->id,
                    'valor' => $this->pagamentoSelecionado->valor,
                    'motivo_rejeicao' => $this->observacoesRejeicao
                ]
            ]);

            DB::commit();

            $this->fecharModal();
            $this->carregarPagamentosPendentes();

            $this->dispatch('show-success', 'Pagamento rejeitado com sucesso.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('show-error', 'Erro ao rejeitar pagamento: ' . $e->getMessage());
        }
    }

    public function iniciarProcessamento()
    {
        $this->validate([
            'observacoesConfirmacao' => 'nullable|string|max:500'
        ]);

        // Validar se o pagamento ainda pode ser aprovado
        if (!$this->pagamentoSelecionado || !$this->pagamentoSelecionado->isPendente()) {
            $this->dispatch('show-error', 'Este pagamento não pode ser aprovado.');
            return;
        }

        $this->processando = true;

        // Disparar evento de processamento
        $this->dispatch('processando');

        // Agendar a execução do processamento após 10 segundos
        $this->dispatch('executarProcessamento', [], 10000);
    }

    public function confirmarPagamento()
    {
        try {
            DB::beginTransaction();

            $this->processarAprovacao();

            DB::commit();

            $this->fecharModal();
            $this->carregarPagamentosPendentes();

            $this->dispatch('show-success', 'Pagamento aprovado com sucesso! Assinatura ativada.');

        } catch (\Exception $e) {
            DB::rollback();
            $this->dispatch('show-error', 'Erro ao processar pagamento: ' . $e->getMessage());
        }

        $this->processando = false;
    }

    private function processarAprovacao()
    {
        $pagamento = $this->pagamentoSelecionado;
        $igreja = $pagamento->igreja;

        // Determinar cenário
        $cenario = $this->determinarCenarioAssinatura($igreja);

        // Calcular datas
        $datas = $this->calcularDatasAssinatura($pagamento, $cenario);

        switch ($cenario) {
            case 'nova':
                return $this->processarNovaAssinatura($pagamento, $datas);
            case 'upgrade':
                return $this->processarUpgradeAssinatura($pagamento, $datas);
            case 'renovacao':
                return $this->processarRenovacaoAssinatura($pagamento, $datas);
            case 'trial':
                return $this->processarConversaoTrial($pagamento, $datas);
        }
    }

    private function calcularDatasAssinatura($pagamento, $cenario)
    {
        $assinaturaAtual = $pagamento->igreja->assinaturaAtual;

        // Data de início base
        $dataInicio = now()->addDays(2);

        // Para renovações/upgrade: usar data fim da assinatura atual + 1 dia
        if (in_array($cenario, ['upgrade', 'renovacao']) && $assinaturaAtual && $assinaturaAtual->estaAtiva()) {
            $dataInicio = $assinaturaAtual->data_fim->copy()->addDays(1);
        }

   

        if ($pagamento->is_vitalicio) {
            Log::info('DEBUG: Assinatura vitalícia detectada');
            return [
                'data_inicio' => $dataInicio,
                'data_fim' => null,
                'duracao_meses' => null,
                'vitalicio' => true
            ];
        }

        // Garantir que duracao_meses é um inteiro válido
        $duracaoMeses = (int) $pagamento->duracao_meses;
        if ($duracaoMeses <= 0) {
            $duracaoMeses = 1; // fallback para 1 mês se valor inválido
        }

        $dataFim = $dataInicio->copy()->addMonths($duracaoMeses);

        return [
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'duracao_meses' => $duracaoMeses,
            'vitalicio' => false
        ];
    }

    private function determinarCenarioAssinatura($igreja)
    {
        $assinaturaAtual = $igreja->assinaturaAtual;

        // Primeiro verificar se há trial ativo na igreja
        $trialAtivo = TrialUser::where('igreja_id', $igreja->id)
            ->where('status', 'ativo')
            ->where('data_fim', '>=', now())
            ->first();

        if ($trialAtivo) {
            return 'trial';
        }

        if (!$assinaturaAtual) {
            return 'nova';
        }

        if ($assinaturaAtual->estaAtiva()) {
            return 'upgrade'; // ou renovação dependendo da lógica
        }

        // Se assinatura existe mas não está ativa (expirada)
        return 'renovacao';
    }

    private function processarNovaAssinatura($pagamento, $datas)
    {
        $igreja = $pagamento->igreja;

    
        // 1. Criar AssinaturaAtual - Usar DB::insert para evitar triggers
        $dadosAssinaturaAtual = [
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'data_inicio' => $datas['data_inicio']->format('Y-m-d'),
            'data_fim' => $datas['data_fim'] ? $datas['data_fim']->format('Y-m-d') : null,
            'status' => 'Ativo',
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

     
        // Usar DB::insert para evitar triggers automáticos
        DB::table('assinatura_atual')->insert($dadosAssinaturaAtual);

        // Buscar o registro criado
        AssinaturaAtual::where('igreja_id', $igreja->id)->first();


        // 2. Criar AssinaturaHistorico
        $dadosAssinaturaHistorico = [
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'data_inicio' => $datas['data_inicio']->format('Y-m-d'),
            'data_fim' => $datas['data_fim'] ? $datas['data_fim']->format('Y-m-d') : null,
            'valor' => $pagamento->valor,
            'status' => 'Ativo',
            'forma_pagamento' => $pagamento->metodo_pagamento,
            'transacao_id' => $pagamento->referencia,
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $assinaturaHistorico = AssinaturaHistorico::create($dadosAssinaturaHistorico);



        // 3. Criar IgrejaAssinada
         IgrejaAssinada::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'ativo' => true,
            'data_adesao' => $datas['data_inicio'],
        ]);

        // 4. Criar AssinaturaPagamento
        AssinaturaPagamento::create([
            'assinatura_id' => $assinaturaHistorico->id,
            'igreja_id' => $igreja->id,
            'valor' => $pagamento->valor,
            'metodo_pagamento' => $pagamento->metodo_pagamento,
            'referencia' => $pagamento->referencia,
            'status' => 'confirmado',
            'data_pagamento' => $pagamento->data_pagamento,
        ]);


        // 5. Atualizar status do pagamento original
        $pagamento->confirmar(Auth::user(), $this->observacoesConfirmacao);


        // 6. Criar logs
        AssinaturaLog::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'acao' => 'criado',
            'descricao' => 'Assinatura criada via aprovação de pagamento',
            'usuario_id' => Auth::id(),
            'data_acao' => now(),
            'detalhes' => [
                'pagamento_id' => $pagamento->id,
                'valor' => $pagamento->valor,
                'metodo' => $pagamento->metodo_pagamento
            ]
        ]);

        
        // 7. Enviar notificação
        $this->enviarNotificacaoAprovacao($igreja, $pagamento, $datas);

        return true;
    }

    private function processarUpgradeAssinatura($pagamento, $datas)
    {
        $igreja = $pagamento->igreja;
        $assinaturaAtual = $igreja->assinaturaAtual;

        // Atualizar assinatura atual
        $assinaturaAtual->update([
            'pacote_id' => $pagamento->pacote_id,
            'data_fim' => $datas['data_fim'],
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
        ]);

        // Criar histórico
        $assinaturaHistorico = AssinaturaHistorico::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'data_inicio' => $datas['data_inicio'],
            'data_fim' => $datas['data_fim'] ? $datas['data_fim'] : null,
            'valor' => $pagamento->valor,
            'status' => 'Ativo',
            'forma_pagamento' => $pagamento->metodo_pagamento,
            'transacao_id' => $pagamento->referencia,
            'duracao_meses_custom' => $datas['duracao_meses'],
            'vitalicio' => $datas['vitalicio'],
        ]);

        // Atualizar IgrejaAssinada
        $igreja->igrejaAssinada->update([
            'pacote_id' => $pagamento->pacote_id,
        ]);

        // Criar pagamento
        AssinaturaPagamento::create([
            'assinatura_id' => $assinaturaHistorico->id,
            'igreja_id' => $igreja->id,
            'valor' => $pagamento->valor,
            'metodo_pagamento' => $pagamento->metodo_pagamento,
            'referencia' => $pagamento->referencia,
            'status' => 'confirmado',
            'data_pagamento' => $pagamento->data_pagamento,
        ]);

        // Atualizar pagamento original
        $pagamento->confirmar(Auth::user(), $this->observacoesConfirmacao);

        // Log
        AssinaturaLog::create([
            'igreja_id' => $igreja->id,
            'pacote_id' => $pagamento->pacote_id,
            'assinatura_id' => $assinaturaHistorico->id,
            'acao' => 'upgrade',
            'descricao' => 'Assinatura atualizada via aprovação de pagamento',
            'usuario_id' => Auth::id(),
            'data_acao' => now(),
        ]);

        // Notificação
        $this->enviarNotificacaoAprovacao($igreja, $pagamento, $datas);

        return true;
    }

    private function processarRenovacaoAssinatura($pagamento, $datas)
    {
        // Similar ao upgrade, mas para renovação
        return $this->processarUpgradeAssinatura($pagamento, $datas);
    }

    private function processarConversaoTrial($pagamento, $datas)
    {
      
        // Processar como nova assinatura
        $this->processarNovaAssinatura($pagamento, $datas);

         // Remover trial do usuário que criou o pagamento (usando created_by)
         $trial = TrialUser::where('user_id', $pagamento->created_by)
         ->where('igreja_id', $pagamento->igreja_id)
         ->where('status', 'ativo')
         ->first();

            if ($trial) {

            $trial->delete();

                // Log da remoção do trial
                AssinaturaLog::create([
                'igreja_id' => $pagamento->igreja_id,
                'pacote_id' => $pagamento->pacote_id,
                'acao' => 'upgrade', // Usando 'upgrade' pois é uma conversão de trial para assinatura paga
                'descricao' => 'Trial convertido para assinatura paga - registro removido',
                'usuario_id' => Auth::id(),
                'data_acao' => now(),
                'detalhes' => [
                    'pagamento_id' => $pagamento->id,
                    'trial_user_id' => $trial->id,
                    'user_id' => $pagamento->created_by,
                    'tipo_conversao' => 'trial_para_assinatura'
                ]
                ]);
            }

        return true;
    }

    private function enviarNotificacaoAprovacao($igreja, $pagamento, $datas)
    {
        // Buscar usuários admin/pastor da igreja
        $usuariosAdmin = $igreja->membros()
            ->whereIn('cargo', ['admin', 'pastor'])
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter();

        foreach ($usuariosAdmin as $usuario) {
            Mail::to($usuario->email)->send(new PagamentoAprovadoMail($pagamento, $datas));
        }
    }

    public function visualizarComprovativo($pagamentoId)
    {
        try {
            $pagamento = PagamentoAssinaturaIgreja::with(['igreja', 'pacote'])
                ->where('id', $pagamentoId)
                ->first();

            if (!$pagamento || !$pagamento->temComprovativo()) {
                $this->dispatch('show-error', 'Comprovativo não encontrado.');
                return;
            }

            // Obter URL do comprovativo - verificar se já é uma URL completa
            if (filter_var($pagamento->comprovativo_url, FILTER_VALIDATE_URL)) {
                // Já é uma URL completa
                $urlComprovativo = $pagamento->comprovativo_url;
                
            } else {
                // É apenas o caminho, obter URL via SupabaseHelper
                $urlComprovativo = \App\Helpers\SupabaseHelper::obterUrl($pagamento->comprovativo_url);
            }

            // Determinar tipo de arquivo
            $tipoArquivo = strtolower($pagamento->comprovativo_tipo);

            // Disparar evento para mostrar modal ou fazer download
            $this->dispatch('mostrarComprovativo', [
                'url' => $urlComprovativo,
                'tipo' => $tipoArquivo,
                'nome' => $pagamento->comprovativo_nome,
                'tamanho' => $pagamento->getComprovativoTamanhoFormatado(),
                'igreja' => $pagamento->igreja->nome,
                'pacote' => $pagamento->pacote_nome,
                'valor' => $pagamento->getValorFormatado(),
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-error', 'Erro ao visualizar comprovativo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('billings.subscribers');
    }
}
