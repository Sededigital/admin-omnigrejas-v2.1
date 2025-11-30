<?php

namespace App\Observers;


use App\Models\Chats\Post;
use Illuminate\Support\Facades\Log;
use App\Services\EngajamentoService;

class PostObserver
{
    protected $engajamentoService;

    public function __construct(EngajamentoService $engajamentoService)
    {
        $this->engajamentoService = $engajamentoService;
    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        try {
            if ($post->author) {
                $this->engajamentoService->registrarPontos(
                    $post->author,
                    'post_criado',
                    null,
                    'Criou um novo post'
                );
            }
        } catch (\Exception $e) {
            Log::error("Erro ao registrar pontos por post criado: " . $e->getMessage());
        }
    }
}
