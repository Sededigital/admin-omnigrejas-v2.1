<?php

namespace App\Console\Commands;

use App\Jobs\Billings\CheckSubscriptionLimits;
use App\Jobs\Billings\SendSubscriptionAlerts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-alerts
                            {--igreja= : ID da igreja específica para verificar}
                            {--force : Forçar verificação mesmo fora do horário}
                            {--skip-send : Pular envio de alertas, só verificar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar limites de assinatura e enviar alertas automáticos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $igrejaId = $this->option('igreja');
        $force = $this->option('force');
        $skipSend = $this->option('skip-send');

        $this->info('🚀 Iniciando verificação de alertas de assinatura...');

        Log::info('CheckSubscriptionAlerts Command: Iniciado', [
            'igreja_id' => $igrejaId,
            'force' => $force,
            'skip_send' => $skipSend
        ]);

        try {
            // 1. Verificar limites de assinatura
            $this->info('📊 Verificando limites de assinatura...');

            CheckSubscriptionLimits::dispatch($igrejaId, $force);

            $this->info('✅ Job de verificação de limites enviado para fila');

            // 2. Enviar alertas (se não pular)
            if (!$skipSend) {
                $this->info('📧 Enviando alertas pendentes...');

                SendSubscriptionAlerts::dispatch($igrejaId, $force);

                $this->info('✅ Job de envio de alertas enviado para fila');
            } else {
                $this->info('⏭️  Envio de alertas pulado (--skip-send)');
            }

            // 3. Estatísticas rápidas (síncronas para feedback imediato)
            $this->mostrarEstatisticas($igrejaId);

            $this->info('🎉 Verificação de alertas concluída com sucesso!');

            Log::info('CheckSubscriptionAlerts Command: Concluído com sucesso');

        } catch (\Exception $e) {
            $this->error('❌ Erro durante verificação: ' . $e->getMessage());

            Log::error('CheckSubscriptionAlerts Command: Erro', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Mostrar estatísticas rápidas
     */
    protected function mostrarEstatisticas($igrejaId = null)
    {
        try {
            // Contar assinaturas ativas
            $assinaturasAtivas = \App\Models\Billings\AssinaturaAtual::where('status', 'ativo')
                ->when($igrejaId, fn($q) => $q->where('igreja_id', $igrejaId))
                ->count();

            // Contar alertas pendentes
            $alertasPendentes = \App\Models\Billings\AssinaturaAlertas::where('status', 'pendente')
                ->when($igrejaId, fn($q) => $q->where('igreja_id', $igrejaId))
                ->count();

            // Contar alertas enviados hoje
            $alertasEnviadosHoje = \App\Models\Billings\AssinaturaAlertas::where('status', 'enviado')
                ->whereDate('enviado_em', today())
                ->when($igrejaId, fn($q) => $q->where('igreja_id', $igrejaId))
                ->count();

            $this->info('📈 Estatísticas:');
            $this->line("   • Assinaturas ativas: {$assinaturasAtivas}");
            $this->line("   • Alertas pendentes: {$alertasPendentes}");
            $this->line("   • Alertas enviados hoje: {$alertasEnviadosHoje}");

        } catch (\Exception $e) {
            $this->warn('⚠️  Não foi possível carregar estatísticas: ' . $e->getMessage());
        }
    }
}
