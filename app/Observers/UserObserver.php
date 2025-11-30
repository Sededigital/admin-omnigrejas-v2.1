<?php

namespace App\Observers;

use App\Models\User;
use App\Services\EngajamentoService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    protected $engajamentoService;

    public function __construct(EngajamentoService $engajamentoService)
    {
        $this->engajamentoService = $engajamentoService;
    }

    /**
     * Handle the User "updating" event.
     * Usado para detectar login (quando last_login é atualizado)
     */
    public function updating(User $user): void
    {
        // Se o usuário está fazendo login (podemos detectar por algum campo)
        // Por enquanto, vamos registrar login quando o usuário é autenticado
        // Isso será chamado no AuthController
    }

    /**
     * Método auxiliar para registrar login diário
     */
    public function registrarLoginDiario(User $user): void
    {
        try {
            $this->engajamentoService->registrarLoginDiario($user);
        } catch (\Exception $e) {
            Log::error("Erro ao registrar login diário: " . $e->getMessage());
        }
    }
}
