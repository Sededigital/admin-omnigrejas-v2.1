<?php

namespace App\Jobs\Billings\Trial;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Mail\TrialExpiradoEmail;
use App\Models\Billings\Trial\TrialUser;
use App\Models\Billings\Trial\TrialAlerta;
use App\Models\Billings\Trial\TrialLog;
use App\Services\Trial\TrialCleanupService;

class ExpirarTrials implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Executa o job de expiração automática dos trials.
     */
    public function handle(): void
    {
        Log::info('=== JOB EXPIRAR & LIMPAR TRIALS - INÍCIO ===', [
            'hora_execucao' => now()->format('Y-m-d H:i:s'),
        ]);

        // 1️⃣ Trials que passaram da data_fim (devem ser expirados)
        $trialsParaExpirar = TrialUser::where('status', 'ativo')
            ->whereDate('data_fim', '<=', now())
            ->get();

        Log::info('Trials encontrados para expiração', [
            'quantidade' => $trialsParaExpirar->count(),
        ]);

        foreach ($trialsParaExpirar as $trial) {
            try {
                
                Log::info('Expirando trial vencido', [
                    'trial_id' => $trial->id,
                    'user_email' => $trial->user->email ?? 'N/A',
                    'data_fim' => $trial->data_fim->format('Y-m-d'),
                ]);

                $trial->update([
                    'status' => 'expirado'
                ]);

                // Log + alerta + e-mail
                $this->registrarExpiracao($trial);
            } catch (\Throwable $e) {
                Log::error('Erro ao expirar trial', [
                    'trial_id' => $trial->id,
                    'erro' => $e->getMessage(),
                ]);
            }
        }

        // 2️⃣ Trials expirados há mais de 3 dias (devem ser limpos)
        $trialsParaLimpar = TrialUser::where('status', 'expirado')
            ->whereDate('data_fim', '<=', now()->subDays(3))
            ->get();

        Log::info('Trials encontrados para limpeza', [
            'quantidade' => $trialsParaLimpar->count(),
        ]);

        foreach ($trialsParaLimpar as $trial) {
            try {
                Log::info('Limpando trial expirado há mais de 3 dias', [
                    'trial_id' => $trial->id,
                    'user_email' => $trial->user->email ?? 'N/A',
                ]);

                $simulacao = TrialCleanupService::simularLimpeza($trial);
                TrialCleanupService::limparTrialCompletamente($trial);

                TrialLog::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'trial_user_id' => $trial->id,
                    'acao' => 'limpeza_completa',
                    'descricao' => 'Limpeza automática de trial após 3 dias de expiração',
                    'dados' => [
                        'dados_criados' => $simulacao['dados_criados'] ?? [],
                        'tabelas_afetadas' => $simulacao['tabelas_afetadas'] ?? [],
                    ],
                    'realizado_em' => now(),
                ]);

                Log::info('Trial limpo com sucesso', ['trial_id' => $trial->id]);
            } catch (\Throwable $e) {
                Log::error('Erro ao limpar trial', [
                    'trial_id' => $trial->id,
                    'erro' => $e->getMessage(),
                ]);
            }
        }

        Log::info('=== JOB EXPIRAR & LIMPAR TRIALS - FINALIZADO ===');
    }

    /**
     * Cria log, alerta e e-mail de expiração.
     */
    private function registrarExpiracao(TrialUser $trial): void
    {
        try {
            $mensagem = $this->mensagemExpiracao($trial);

            // Criar alerta
            TrialAlerta::create([
                'trial_user_id' => $trial->id,
                'tipo_alerta' => 'expirado',
                'titulo' => 'Seu período de teste expirou',
                'mensagem' => $mensagem,
                'dados' => [
                    'data_expiracao' => $trial->data_fim->format('d/m/Y'),
                    'dias_ativos' => $trial->diasDesdeCriacao() ?? 0,
                    'pode_reativar' => $trial->podeSerReativado() ?? false,
                ],
            ]);

            // Log
            TrialLog::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'trial_user_id' => $trial->id,
                'acao' => 'expirado',
                'descricao' => 'Trial expirado automaticamente',
                'realizado_em' => now(),
            ]);

            // E-mail
            Mail::to($trial->user->email)->send(new TrialExpiradoEmail($trial));

            Log::info('Email de expiração enviado com sucesso', [
                'trial_id' => $trial->id,
                'user_email' => $trial->user->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('Erro ao registrar expiração', [
                'trial_id' => $trial->id,
                'erro' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Mensagem para o alerta e email.
     */
    private function mensagemExpiracao(TrialUser $trial): string
    {
        $nome = $trial->user->name ?? 'Usuário';
        $dataFim = $trial->data_fim?->format('d/m/Y') ?? 'data desconhecida';
        $diasAtivos = $trial->diasDesdeCriacao() ?? 0;

        $msg = "Olá {$nome}, seu período de teste gratuito expirou em {$dataFim}. ";
        $msg .= "Durante {$diasAtivos} dias de uso, você aproveitou as funcionalidades principais.";

        if ($trial->podeSerReativado()) {
            $msg .= " Você ainda tem {$trial->periodo_graca_dias} dias para reativar sua conta e continuar usando o sistema.";
        } else {
            $msg .= " Entre em contato conosco para ativar um plano e continuar usando todas as funcionalidades.";
        }

        return $msg;
    }
}
