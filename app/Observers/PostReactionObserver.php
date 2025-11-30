<?php

namespace App\Observers;


use App\Models\Chats\PostReaction;
use Illuminate\Support\Facades\Log;
use App\Services\EngajamentoService;

class PostReactionObserver
{
    protected $engajamentoService;

    public function __construct(EngajamentoService $engajamentoService)
    {
        $this->engajamentoService = $engajamentoService;
    }

    /**
     * Handle the PostReaction "created" event.
     */
    public function created(PostReaction $reaction): void
    {
        try {
            if ($reaction->user) {
                $this->engajamentoService->registrarPontos(
                    $reaction->user,
                    'reacao_post',
                    null,
                    'Reagiu a um post'
                );
            }
        } catch (\Exception $e) {
            Log::error("Erro ao registrar pontos por reação: " . $e->getMessage());
        }
    }
}
