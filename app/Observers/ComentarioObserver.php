<?php

namespace App\Observers;


use App\Models\Chats\Comentario;
use Illuminate\Support\Facades\Log;
use App\Services\EngajamentoService;

class ComentarioObserver
{
    protected $engajamentoService;

    public function __construct(EngajamentoService $engajamentoService)
    {
        $this->engajamentoService = $engajamentoService;
    }

    /**
     * Handle the Comentario "created" event.
     */
    public function created(Comentario $comentario): void
    {
        try {
            if ($comentario->user) {
                $this->engajamentoService->registrarPontos(
                    $comentario->user,
                    'comentario_post',
                    null,
                    'Comentou em um post'
                );
            }
        } catch (\Exception $e) {
            Log::error("Erro ao registrar pontos por comentário: " . $e->getMessage());
        }
    }
}
