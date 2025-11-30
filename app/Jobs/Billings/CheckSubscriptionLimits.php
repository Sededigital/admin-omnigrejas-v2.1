<?php

namespace App\Jobs\Billings;

use Illuminate\Bus\Queueable;
use App\Models\Billings\Pacote;
use App\Helpers\Billings\SubscriptionHelper;
use Illuminate\Support\Facades\Log;
use App\Models\Billings\IgrejaConsumo;
use Illuminate\Queue\SerializesModels;
use App\Models\Billings\AssinaturaAtual;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckSubscriptionLimits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $igrejaId;
    protected $forceCheck;

    /**
     * Create a new job instance.
     */
    public function __construct($igrejaId = null, $forceCheck = false)
    {
        $this->igrejaId = $igrejaId;
        $this->forceCheck = $forceCheck;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subscriptionHelper = app(SubscriptionHelper::class);

        try {
            Log::info('CheckSubscriptionLimits: Iniciando verificação de limites', [
                'igreja_id' => $this->igrejaId,
                'force_check' => $this->forceCheck
            ]);

            // Buscar assinaturas ativas para verificar
            $query = AssinaturaAtual::with(['igreja', 'pacote'])
                ->where('status', 'ativo')
                ->where('data_fim', '>', now());

            if ($this->igrejaId) {
                $query->where('igreja_id', $this->igrejaId);
            }

            $assinaturas = $query->get();

            Log::info('CheckSubscriptionLimits: Encontradas assinaturas ativas', [
                'total' => $assinaturas->count()
            ]);

            $alertasGerados = 0;

            foreach ($assinaturas as $assinatura) {
                try {
                    // Verificar se deve alertar sobre limites
                    $deveAlertar = $subscriptionHelper->shouldAlertAboutLimits($assinatura->igreja_id);

                    if ($deveAlertar) {
                        // Criar alerta automático
                        $subscriptionHelper->createAlert(
                            $assinatura->igreja_id,
                            'limite_proximo',
                            'Limite de recursos próximo',
                            'Seus limites de recursos estão próximos de serem atingidos.',
                            [
                                'assinatura_id' => $assinatura->id,
                                'pacote_nome' => $assinatura->pacote->nome ?? 'N/A',
                                'data_fim' => $assinatura->data_fim->format('d/m/Y')
                            ]
                        );

                        $alertasGerados++;
                        Log::info('CheckSubscriptionLimits: Alerta gerado', [
                            'igreja_id' => $assinatura->igreja_id,
                            'assinatura_id' => $assinatura->id
                        ]);
                    }

                    // Verificar assinatura próxima da expiração (7 dias)
                    $diasParaExpirar = now()->diffInDays($assinatura->data_fim, false);
                    if ($diasParaExpirar <= 7 && $diasParaExpirar > 0) {
                        $subscriptionHelper->createAlert(
                            $assinatura->igreja_id,
                            'assinatura_expirando',
                            'Assinatura expirando em breve',
                            "Sua assinatura expira em {$diasParaExpirar} dias.",
                            [
                                'assinatura_id' => $assinatura->id,
                                'dias_restantes' => $diasParaExpirar,
                                'data_fim' => $assinatura->data_fim->format('d/m/Y')
                            ]
                        );

                        $alertasGerados++;
                        Log::info('CheckSubscriptionLimits: Alerta de expiração gerado', [
                            'igreja_id' => $assinatura->igreja_id,
                            'dias_restantes' => $diasParaExpirar
                        ]);
                    }

                } catch (\Exception $e) {
                    Log::error('CheckSubscriptionLimits: Erro ao verificar assinatura', [
                        'assinatura_id' => $assinatura->id,
                        'igreja_id' => $assinatura->igreja_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('CheckSubscriptionLimits: Verificação concluída', [
                'assinaturas_verificadas' => $assinaturas->count(),
                'alertas_gerados' => $alertasGerados
            ]);

        } catch (\Exception $e) {
            Log::error('CheckSubscriptionLimits: Erro geral na verificação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CheckSubscriptionLimits: Job falhou', [
            'error' => $exception->getMessage(),
            'igreja_id' => $this->igrejaId
        ]);
    }
}
