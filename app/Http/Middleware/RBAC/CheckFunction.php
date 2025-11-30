<?php

namespace App\Http\Middleware\RBAC;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\RBAC\IgrejaMembroFuncao;
use App\Models\RBAC\IgrejaPermissaoLog;
use App\Helpers\RBAC\PermissionHelper;
use Symfony\Component\HttpFoundation\Response;

class CheckFunction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Verificar se usuário está autenticado
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        // 2. ✅ VERIFICAÇÃO DE SEGURANÇA: Verificar se usuário ainda tem funções ativas
        if (!$this->temFuncaoAtiva($user)) {
            // Usuário perdeu as funções - encerrar sessão por segurança
            Auth::logout();
            $this->logSessionTerminated($user, $request);

            return response()->json([
                'error' => 'Sessão encerrada',
                'message' => 'Suas permissões foram alteradas. Faça login novamente.',
                'redirect' => route('login')
            ], 403);
        }

        // 3. Verificar se tem acesso administrativo (admin/pastor ou função atribuída)
        if (Gate::allows('admin-access', $user)) {
            $this->logAdminAccess($user, $request);
            $this->logAccessGranted($user, $request);
            return $next($request);
        }

        // 4. Acesso negado - usuário não tem função atribuída
        $this->logAccessDenied($user, $request);
        return response()->json([
            'error' => 'Acesso negado',
            'message' => 'Você precisa ter uma função atribuída para acessar o sistema administrativo.'
        ], 403);
    }

    /**
     * Verifica se o usuário tem acesso total (admin/pastor)
     */
    private function hasFullAccess($user): bool
    {
        return in_array($user->role, ['admin', 'pastor', 'root', 'super_admin' ]);
    }

    /**
     * Verifica se o usuário tem pelo menos uma função ativa e válida
     */
    private function temFuncaoAtiva($user): bool
    {
        // Admin/Pastor têm acesso total independente de funções
        if ($this->hasFullAccess($user)) {
            return true;
        }

        // Buscar membro do usuário
        $membro = $user->membros()->where('principal', true)->orWhere('principal', false)->first();
        if (!$membro) {
            return false;
        }

        return IgrejaMembroFuncao::where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->exists();
    }

    /**
     * Registra log de acesso administrativo
     */
    private function logAdminAccess($user, Request $request): void
    {
        IgrejaPermissaoLog::create([
            'igreja_id' => $user->getIgrejaId(),
            'acao' => 'acesso_administrativo_funcao',
            'detalhes' => [
                'role_usuario' => $user->role,
                'tipo_acesso' => 'funcao_verificada_admin',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }

    /**
     * Registra log de acesso negado
     */
    private function logAccessDenied($user, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->orWhere('principal', false)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'acesso_negado_funcao',
            'detalhes' => [
                'motivo' => 'sem_funcao_ativa',
                'role_usuario' => $user->role,
                'tipo_acesso' => 'funcao_requerida',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }

    /**
     * Registra log de sessão terminada por perda de permissões
     */
    private function logSessionTerminated($user, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->orWhere('principal', false)->orWhere('principal', false)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'sessao_terminada_permissao_revogada',
            'detalhes' => [
                'motivo' => 'perda_de_funcoes_ativas',
                'role_usuario' => $user->role,
                'tipo_encerramento' => 'middleware_seguranca',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }

    /**
     * Registra log de acesso autorizado para membro
     */
    private function logAccessGranted($user, Request $request): void
    {
        $membro = $user->membros()->where('principal', true)->orWhere('principal', false)->first();

        IgrejaPermissaoLog::create([
            'igreja_id' => $membro ? $membro->igreja_id : null,
            'membro_id' => $membro ? $membro->id : null,
            'acao' => 'acesso_autorizado_funcao',
            'detalhes' => [
                'role_usuario' => $user->role,
                'tipo_acesso' => 'funcao_verificada',
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'realizado_por' => $user->id,
            'realizado_em' => now(),
        ]);
    }

}
