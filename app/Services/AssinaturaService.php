<?php

namespace App\Services;


use Carbon\Carbon;
use App\Models\Billings\Pacote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Billings\AssinaturaLog;
use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaUpgrade;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;

class AssinaturaService
{
    /**
     * Criar uma nova assinatura
     */
    public function criarAssinatura(int $igrejaId, int $pacoteId, ?int $mesesCustom = null, bool $vitalicio = false): AssinaturaAtual
    {
        return DB::transaction(function () use ($igrejaId, $pacoteId, $mesesCustom, $vitalicio) {
            $pacote = Pacote::findOrFail($pacoteId);

            $dataInicio = Carbon::now();
            $dataFim = $vitalicio
                ? null
                : ($mesesCustom
                    ? $dataInicio->copy()->addMonths($mesesCustom)
                    : $dataInicio->copy()->addMonths($pacote->duracao_meses));

            // Assinatura Atual
            $assinaturaAtual = AssinaturaAtual::updateOrCreate(
                ['igreja_id' => $igrejaId],
                [
                    'pacote_id'            => $pacoteId,
                    'data_inicio'          => $dataInicio,
                    'data_fim'             => $dataFim,
                    'status'               => 'Ativo',
                    'duracao_meses_custom' => $mesesCustom,
                    'vitalicio'            => $vitalicio,
                    'updated_at'           => now(),
                ]
            );

            // Histórico
            AssinaturaHistorico::create([
                'igreja_id'            => $igrejaId,
                'pacote_id'            => $pacoteId,
                'data_inicio'          => $dataInicio,
                'data_fim'             => $dataFim,
                'valor'                => $pacote->preco,
                'status'               => 'Ativo',
                'duracao_meses_custom' => $mesesCustom,
                'vitalicio'            => $vitalicio,
            ]);

            // Log
            AssinaturaLog::create([
                'igreja_id'  => $igrejaId,
                'pacote_id'  => $pacoteId,
                'acao'       => 'criado',
                'descricao'  => 'Nova assinatura criada',
                'data_acao'  => now(),
            ]);

            return $assinaturaAtual;
        });
    }

    /**
     * Renovar assinatura existente
     */
    public function renovarAssinatura(int $igrejaId, ?int $mesesCustom = null): bool
    {
        return DB::transaction(function () use ($igrejaId, $mesesCustom) {
            $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)->firstOrFail();
            $pacote = $assinatura->pacote;

            $duracao = $mesesCustom ?? $assinatura->duracao_meses_custom ?? $pacote->duracao_meses;

            $assinatura->update([
                'data_inicio' => now(),
                'data_fim'    => now()->addMonths($duracao),
                'status'      => 'Ativo',
            ]);

            AssinaturaHistorico::create([
                'igreja_id'   => $igrejaId,
                'pacote_id'   => $pacote->id,
                'data_inicio' => now(),
                'data_fim'    => now()->addMonths($duracao),
                'valor'       => $pacote->preco,
                'status'      => 'Ativo',
            ]);

            AssinaturaLog::create([
                'igreja_id' => $igrejaId,
                'pacote_id' => $pacote->id,
                'acao'      => 'renovado',
                'descricao' => 'Assinatura renovada',
                'data_acao' => now(),
            ]);

            return true;
        });
    }

    /**
     * Cancelar assinatura
     */
    public function cancelarAssinatura(int $igrejaId, ?string $motivo = null): bool
    {
        return DB::transaction(function () use ($igrejaId, $motivo) {
            $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)->firstOrFail();

            $assinatura->update([
                'status'    => 'Cancelado',
                'data_fim'  => now(),
                'updated_at'=> now(),
            ]);

            AssinaturaLog::create([
                'igreja_id' => $igrejaId,
                'pacote_id' => $assinatura->pacote_id,
                'acao'      => 'cancelado',
                'descricao' => $motivo ?? 'Cancelamento manual',
                'data_acao' => now(),
            ]);

            return true;
        });
    }

    /**
     * Upgrade/Downgrade de plano
     */
    public function upgradeAssinatura(int $igrejaId, int $novoPacoteId, ?string $motivo = null): bool
    {
        return DB::transaction(function () use ($igrejaId, $novoPacoteId, $motivo) {
            $assinatura = AssinaturaAtual::where('igreja_id', $igrejaId)->firstOrFail();
            $novoPacote = Pacote::findOrFail($novoPacoteId);

            AssinaturaUpgrade::create([
                'assinatura_id'  => $assinatura->id ?? null,
                'pacote_anterior'=> $assinatura->pacote_id,
                'pacote_novo'    => $novoPacoteId,
                'valor_diferenca'=> $novoPacote->preco - $assinatura->pacote->preco,
                'motivo'         => $motivo,
                'usuario_id'     => Auth::user() ?? null,
            ]);

            $assinatura->update([
                'pacote_id' => $novoPacoteId,
                'data_inicio' => now(),
                'data_fim' => now()->addMonths($novoPacote->duracao_meses),
                'updated_at' => now(),
            ]);

            AssinaturaHistorico::create([
                'igreja_id'   => $igrejaId,
                'pacote_id'   => $novoPacoteId,
                'data_inicio' => now(),
                'data_fim'    => now()->addMonths($novoPacote->duracao_meses),
                'valor'       => $novoPacote->preco,
                'status'      => 'Ativo',
            ]);

            AssinaturaLog::create([
                'igreja_id' => $igrejaId,
                'pacote_id' => $novoPacoteId,
                'acao'      => 'upgrade',
                'descricao' => 'Assinatura alterada para novo plano',
                'data_acao' => now(),
            ]);

            return true;
        });
    }

    /**
     * Registrar pagamento
     */
    public function registrarPagamento(int $assinaturaId, int $igrejaId, float $valor, string $metodo, ?string $referencia = null): AssinaturaPagamento
    {
        return DB::transaction(function () use ($assinaturaId, $igrejaId, $valor, $metodo, $referencia) {
            $pagamento = AssinaturaPagamento::create([
                'assinatura_id'   => $assinaturaId,
                'igreja_id'       => $igrejaId,
                'valor'           => $valor,
                'metodo_pagamento'=> $metodo,
                'referencia'      => $referencia,
                'status'          => 'confirmado',
                'data_pagamento'  => now(),
            ]);

            AssinaturaLog::create([
                'igreja_id' => $igrejaId,
                'acao'      => 'pagamento',
                'descricao' => 'Pagamento registrado no sistema',
                'data_acao' => now(),
                'detalhes'  => [
                    'valor'     => $valor,
                    'metodo'    => $metodo,
                    'referencia'=> $referencia,
                ],
            ]);

            return $pagamento;
        });
    }
}
