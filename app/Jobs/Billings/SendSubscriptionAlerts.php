<?php

namespace App\Jobs\Billings;

use App\Helpers\Billings\SubscriptionHelper;
use App\Models\Billings\AssinaturaAlertas;
use App\Models\Igrejas\Igreja;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendSubscriptionAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $igrejaId;
    protected $forceSend;

    /**
     * Create a new job instance.
     */
    public function __construct($igrejaId = null, $forceSend = false)
    {
        $this->igrejaId = $igrejaId;
        $this->forceSend = $forceSend;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('SendSubscriptionAlerts: Iniciando envio de alertas', [
                'igreja_id' => $this->igrejaId,
                'force_send' => $this->forceSend
            ]);

            // Buscar alertas não enviados
            $query = AssinaturaAlertas::with(['igreja'])
                ->where('status', 'pendente')
                ->where('enviado_em', null);

            if ($this->igrejaId) {
                $query->where('igreja_id', $this->igrejaId);
            }

            // Se não for force, só enviar alertas criados há mais de 1 hora
            if (!$this->forceSend) {
                $query->where('created_at', '<', now()->subHour());
            }

            $alertas = $query->get();

            Log::info('SendSubscriptionAlerts: Alertas encontrados', [
                'total' => $alertas->count()
            ]);

            $alertasEnviados = 0;

            foreach ($alertas as $alerta) {
                try {
                    $this->enviarAlerta($alerta);
                    $alertasEnviados++;

                    Log::info('SendSubscriptionAlerts: Alerta enviado', [
                        'alerta_id' => $alerta->id,
                        'igreja_id' => $alerta->igreja_id,
                        'tipo' => $alerta->tipo
                    ]);

                } catch (\Exception $e) {
                    Log::error('SendSubscriptionAlerts: Erro ao enviar alerta', [
                        'alerta_id' => $alerta->id,
                        'igreja_id' => $alerta->igreja_id,
                        'error' => $e->getMessage()
                    ]);

                    // Marcar como falhou após 3 tentativas
                    if ($alerta->tentativas_envio >= 3) {
                        $alerta->update([
                            'status' => 'falhou',
                            'erro_envio' => $e->getMessage()
                        ]);
                    } else {
                        $alerta->increment('tentativas_envio');
                    }
                }
            }

            Log::info('SendSubscriptionAlerts: Envio concluído', [
                'alertas_processados' => $alertas->count(),
                'alertas_enviados' => $alertasEnviados
            ]);

        } catch (\Exception $e) {
            Log::error('SendSubscriptionAlerts: Erro geral no envio', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Enviar alerta específico
     */
    protected function enviarAlerta(AssinaturaAlertas $alerta): void
    {
        // Buscar administradores da igreja
        $admins = User::whereHas('membros', function($query) use ($alerta) {
            $query->where('igreja_id', $alerta->igreja_id)
                  ->where('cargo', 'admin')
                  ->orWhere('cargo', 'pastor')
                  ->orWhere('cargo', 'ministro')
                  ->orWhere('cargo' )
                  ->where('status', 'ativo');
        })->get();

        if ($admins->isEmpty()) {
            Log::warning('SendSubscriptionAlerts: Nenhum admin encontrado', [
                'igreja_id' => $alerta->igreja_id
            ]);
            return;
        }

        // Preparar dados do alerta
        $dados = [
            'titulo' => $alerta->titulo,
            'mensagem' => $alerta->mensagem,
            'tipo' => $alerta->tipo,
            'igreja' => $alerta->igreja->nome ?? 'N/A',
            'dados_adicionais' => $alerta->dados_adicionais ?? [],
            'data_alerta' => $alerta->created_at->format('d/m/Y H:i')
        ];

        // Enviar para cada admin
        foreach ($admins as $admin) {
            try {
                // Enviar email
                $this->enviarEmailAlerta($admin, $dados);

                // Criar notificação no sistema
                $this->criarNotificacaoSistema($admin, $alerta);

                Log::info('SendSubscriptionAlerts: Alerta enviado para admin', [
                    'admin_id' => $admin->id,
                    'admin_email' => $admin->email,
                    'alerta_id' => $alerta->id
                ]);

            } catch (\Exception $e) {
                Log::error('SendSubscriptionAlerts: Erro ao enviar para admin', [
                    'admin_id' => $admin->id,
                    'alerta_id' => $alerta->id,
                    'error' => $e->getMessage()
                ]);
                throw $e; // Re-throw para marcar como falhou
            }
        }

        // Marcar alerta como enviado
        $alerta->update([
            'status' => 'enviado',
            'enviado_em' => now(),
            'destinatarios' => $admins->pluck('email')->toArray()
        ]);
    }

    /**
     * Enviar email de alerta
     */
    protected function enviarEmailAlerta(User $admin, array $dados): void
    {
        // Aqui você pode usar uma classe de email específica
        // Por exemplo: Mail::to($admin->email)->send(new SubscriptionAlertMail($dados));

        Log::info('SendSubscriptionAlerts: Email seria enviado', [
            'email' => $admin->email,
            'titulo' => $dados['titulo']
        ]);

        // TODO: Implementar envio real de email quando tiver a classe de mail
    }

    /**
     * Criar notificação no sistema
     */
    protected function criarNotificacaoSistema(User $admin, AssinaturaAlertas $alerta): void
    {
        // Criar notificação no sistema
        \App\Models\Chats\Notificacao::create([
            'user_id' => $admin->id,
            'tipo' => 'assinatura_alerta',
            'referencia_id' => $alerta->id,
            'titulo' => $alerta->titulo,
            'mensagem' => $alerta->mensagem,
            'lida' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendSubscriptionAlerts: Job falhou', [
            'error' => $exception->getMessage(),
            'igreja_id' => $this->igrejaId
        ]);
    }
}
