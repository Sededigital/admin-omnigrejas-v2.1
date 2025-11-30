<?php

namespace App\Jobs\Billings\Trial;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\Trial\TrialService;
use App\Services\Trial\TrialCleanupService;
use Illuminate\Queue\SerializesModels;
use App\Models\Billings\Trial\TrialLog;
use App\Models\Billings\Trial\TrialUser;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class LimparTrialsExpirados implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $trialsParaLimpar = TrialService::getTrialsParaLimpar();

        Log::info('Iniciando limpeza de trials expirados', [
            'quantidade' => $trialsParaLimpar->count(),
        ]);

        // Log detalhado dos trials encontrados
        foreach ($trialsParaLimpar as $trial) {
            Log::info('Trial encontrado para limpeza', [
                'trial_id' => $trial->id,
                'user_email' => $trial->user->email ?? 'N/A',
                'status' => $trial->status,
                'data_fim' => $trial->data_fim,
                'dias_expirado' => $trial->data_fim->diffInDays(now()),
            ]);
        }

        foreach ($trialsParaLimpar as $trial) {
            $this->limparTrial($trial);
        }

        Log::info('Limpeza de trials expirados finalizada', [
            'trials_processados' => $trialsParaLimpar->count(),
        ]);
    }

    /**
     * Limpar dados de um trial expirado completamente
     */
    private function limparTrial(TrialUser $trial): void
    {
        try {
            // Simular limpeza primeiro para log
            $simulacao = TrialCleanupService::simularLimpeza($trial);

            Log::info('Iniciando limpeza completa do trial', [
                'trial_id' => $trial->id,
                'user_email' => $trial->user->email,
                'igreja_nome' => $trial->igreja->nome,
                'dias_expirado' => $trial->data_fim->diffInDays(now()),
                'dados_criados' => $simulacao['dados_criados'],
                'tabelas_afetadas' => count($simulacao['tabelas_afetadas']),
            ]);

            // Executar limpeza completa
            TrialCleanupService::limparTrialCompletamente($trial);

            // Log de sucesso
            TrialLog::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'trial_user_id' => $trial->id,
                'acao' => 'limpeza_completa',
                'descricao' => 'Limpeza completa do trial executada automaticamente - usuário e igreja removidos',
                'dados' => [
                    'dias_expirado' => $trial->data_fim->diffInDays(now()),
                    'user_email' => $trial->user->email,
                    'igreja_nome' => $trial->igreja->nome,
                ],
                'realizado_em' => now(),
            ]);

            Log::info('Trial limpo completamente com sucesso', [
                'trial_id' => $trial->id,
                'user_email' => $trial->user->email,
                'igreja_nome' => $trial->igreja->nome,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao limpar trial expirado completamente', [
                'trial_id' => $trial->id,
                'user_email' => $trial->user->email ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Tentar log mesmo em caso de erro
            try {
                TrialLog::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'trial_user_id' => $trial->id,
                    'acao' => 'erro_limpeza',
                    'descricao' => 'Erro na limpeza completa do trial: ' . $e->getMessage(),
                    'dados' => [
                        'error' => $e->getMessage(),
                        'user_email' => $trial->user->email ?? 'N/A',
                    ],
                    'realizado_em' => now(),
                ]);
            } catch (\Exception $logError) {
                Log::error('Erro ao criar log de erro de limpeza', [
                    'trial_id' => $trial->id,
                    'original_error' => $e->getMessage(),
                    'log_error' => $logError->getMessage(),
                ]);
            }
        }
    }
}