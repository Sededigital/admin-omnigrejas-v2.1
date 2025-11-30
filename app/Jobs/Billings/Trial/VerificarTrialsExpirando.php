<?php

namespace App\Jobs\Billings\Trial;

use Illuminate\Bus\Queueable;
use App\Mail\TrialExpirandoEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Models\Billings\Trial\TrialLog;
use App\Models\Billings\Trial\TrialUser;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Billings\Trial\TrialAlerta;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class VerificarTrialsExpirando implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Trials que expiram em 4 dias
        $trials4Dias = TrialUser::where('status', 'ativo')
            ->where('data_fim', now()->addDays(4))
            ->get();

        foreach ($trials4Dias as $trial) {
            $this->processarNotificacao($trial, 4);
        }

        // Trials que expiram em 1 dia
        $trials1Dia = TrialUser::where('status', 'ativo')
            ->where('data_fim', now()->addDay())
            ->get();

        foreach ($trials1Dia as $trial) {
            $this->processarNotificacao($trial, 1);
        }
    }

    /**
     * Processar notificação para um trial
     */
    private function processarNotificacao(TrialUser $trial, int $diasRestantes): void
    {
        try {
            // Verificar se já foi notificado hoje
            $jaNotificado = TrialAlerta::where('trial_user_id', $trial->id)
                ->where('tipo_alerta', 'expiracao_proxima')
                ->where('dados->dias_restantes', $diasRestantes)
                ->whereDate('created_at', today())
                ->exists();

            if ($jaNotificado) {
                return; // Já notificado hoje
            }

            // Criar alerta
            $alerta = TrialAlerta::create([
                'trial_user_id' => $trial->id,
                'tipo_alerta' => 'expiracao_proxima',
                'titulo' => $this->getTituloNotificacao($diasRestantes),
                'mensagem' => $this->getMensagemNotificacao($trial, $diasRestantes),
                'dados' => [
                    'dias_restantes' => $diasRestantes,
                    'data_expiracao' => $trial->data_fim->format('d/m/Y'),
                ],
            ]);

            // Log da notificação
            TrialLog::logNotificacaoEnviada($trial, 'expiracao_proxima');

            // Enviar email
            try {

                Mail::to($trial->user->email)->send(new TrialExpirandoEmail($trial, $diasRestantes));

                // Marcar como enviado
                $alerta->update([
                    'email_enviado' => true,
                    'enviado_em' => now()
                    ]);

            } catch (\Exception $e) {
                // Log do erro de email mas não falha o job
                Log::error('Erro ao enviar email de expiração do trial', [
                    'trial_id' => $trial->id,
                    'user_email' => $trial->user->email,
                    'error' => $e->getMessage(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao processar notificação de trial expirando', [
                'trial_id' => $trial->id,
                'dias_restantes' => $diasRestantes,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obter título da notificação
     */
    private function getTituloNotificacao(int $diasRestantes): string
    {
        return match($diasRestantes) {
            4 => 'Seu período de teste expira em 4 dias',
            1 => 'Seu período de teste expira amanhã',
            default => "Seu período de teste expira em {$diasRestantes} dias",
        };
    }

    /**
     * Obter mensagem da notificação
     */
    private function getMensagemNotificacao(TrialUser $trial, int $diasRestantes): string
    {
        $dataExpiracao = $trial->data_fim->format('d/m/Y');
        $nomeUsuario = $trial->user->name;

        return match($diasRestantes) {
            4 => "Olá {$nomeUsuario}, faltam apenas 4 dias para o fim do seu período de teste gratuito. Seu acesso expira em {$dataExpiracao}. Para continuar usando todos os recursos, considere fazer um upgrade para um plano pago.",
            1 => "Olá {$nomeUsuario}, amanhã será o último dia do seu período de teste gratuito. Seu acesso expira em {$dataExpiracao}. Entre em contato conosco para continuar aproveitando todas as funcionalidades.",
            default => "Olá {$nomeUsuario}, faltam {$diasRestantes} dias para o fim do seu período de teste gratuito. Seu acesso expira em {$dataExpiracao}.",
        };
    }
}