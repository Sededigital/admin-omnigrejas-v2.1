<?php

namespace App\Helpers\RBAC;

use App\Models\User;
use App\Models\RBAC\IgrejaPermissao;
use App\Models\RBAC\IgrejaFuncao;
use App\Models\RBAC\IgrejaMembroFuncao;
use App\Models\RBAC\IgrejaPermissaoLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PermissionHelper
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    //** ========================================
    //** Verifica se o usuário tem uma permissão específica
    //** ========================================
    public function hasPermission(string $permissionCode, bool $useCache = true): bool
    {
        return self::hasPermissionStatic($this->user, $permissionCode, $useCache);
    }

    //** ========================================
    //** Verifica se o usuário tem uma permissão específica (método estático)
    //** ========================================
    public static function hasPermissionStatic(User $user, string $permissionCode, bool $useCache = true): bool
    {
        // Cache key
        $cacheKey = "user_permissions_{$user->id}_{$permissionCode}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $hasPermission = self::checkPermission($user, $permissionCode);

        // Cache por 5 minutos
        if ($useCache) {
            Cache::put($cacheKey, $hasPermission, now()->addMinutes(5));
        }

        return $hasPermission;
    }

    //** ========================================
    //** Verifica se o usuário tem qualquer uma das permissões
    //** ========================================
    public static function hasAnyPermission(User $user, array $permissions, bool $useCache = true): bool
    {
        foreach ($permissions as $permission) {
            if (self::hasPermissionStatic($user, $permission, $useCache)) {
                return true;
            }
        }
        return false;
    }

    //** ========================================
    //** Verifica se o usuário tem todas as permissões
    //** ========================================
    public static function hasAllPermissions(User $user, array $permissions, bool $useCache = true): bool
    {
        foreach ($permissions as $permission) {
            if (!self::hasPermissionStatic($user, $permission, $useCache)) {
                return false;
            }
        }
        return true;
    }

    //** ========================================
    //** Verifica se o usuário tem uma função específica
    //** ========================================
    public static function hasRole(User $user, string $roleName, bool $useCache = true): bool
    {
        $cacheKey = "user_roles_{$user->id}_{$roleName}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $hasRole = self::checkRole($user, $roleName);

        if ($useCache) {
            Cache::put($cacheKey, $hasRole, now()->addMinutes(5));
        }

        return $hasRole;
    }

    //** ========================================
    //** Verifica se o usuário tem função atribuída (qualquer uma)
    //** ========================================
    public static function hasAnyRole(User $user, bool $useCache = true): bool
    {
        $cacheKey = "user_has_any_role_{$user->id}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $hasRole = self::checkAnyRole($user);

        if ($useCache) {
            Cache::put($cacheKey, $hasRole, now()->addMinutes(5));
        }

        return $hasRole;
    }

    //** ========================================
    //** Verifica se o usuário tem acesso total (admin/pastor)
    //** ========================================
    public static function hasFullAccess(User $user): bool
    {
        return in_array($user->role, ['admin', 'pastor','ministro', 'root', 'super_admin' ]);
    }

    //** ========================================
    //** Obtém todas as permissões do usuário
    //** ========================================
    public static function getUserPermissions(User $user, bool $useCache = true): Collection
    {
        $cacheKey = "user_all_permissions_{$user->id}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $permissions = self::getUserPermissionsFromDB($user);

        if ($useCache) {
            Cache::put($cacheKey, $permissions, now()->addMinutes(5));
        }

        return $permissions;
    }

    //** ========================================
    //** Obtém todas as funções do usuário
    //** ========================================
    public static function getUserRoles(User $user, bool $useCache = true): Collection
    {
        $cacheKey = "user_all_roles_{$user->id}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $roles = self::getUserRolesFromDB($user);

        if ($useCache) {
            Cache::put($cacheKey, $roles, now()->addMinutes(5));
        }

        return $roles;
    }

    //** ========================================
    //** Atribui função a um membro
    //** ========================================
    public static function assignRole(User $member, IgrejaFuncao $role, User $assignedBy, array $options = []): bool
    {
        try {
            $memberRecord = $member->membros()->first();
            if (!$memberRecord) {
                return false;
            }

            IgrejaMembroFuncao::create([
                'membro_id' => $memberRecord->id,
                'funcao_id' => $role->id,
                'igreja_id' => $role->igreja_id,
                'atribuido_por' => $assignedBy->id,
                'atribuido_em' => now(),
                'valido_ate' => $options['valido_ate'] ?? null,
                'status' => 'ativo',
                'motivo_atribuicao' => $options['motivo'] ?? 'Atribuição de função',
                'observacoes' => $options['observacoes'] ?? null,
            ]);

            // Log da atribuição
            IgrejaPermissaoLog::logAtribuicaoFuncao(
                $memberRecord,
                $role,
                $assignedBy,
                [
                    'motivo' => $options['motivo'] ?? 'Atribuição de função',
                    'valido_ate' => $options['valido_ate'] ?? null,
                ]
            );

            // Limpar cache
            self::clearUserCache($member->id);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    //** ========================================
    //** Remove função de um membro
    //** ========================================
    public static function revokeRole(User $member, IgrejaFuncao $role, User $revokedBy, string $motivo = null): bool
    {
        try {
            $memberRecord = $member->membros()->first();
            if (!$memberRecord) {
                return false;
            }

            $membroFuncao = IgrejaMembroFuncao::where('membro_id', $memberRecord->id)
                ->where('funcao_id', $role->id)
                ->where('status', 'ativo')
                ->first();

            if ($membroFuncao) {
                $membroFuncao->update([
                    'status' => 'revogado',
                    'observacoes' => $motivo ? ($membroFuncao->observacoes . "\n\nRevogação: " . $motivo) : $membroFuncao->observacoes
                ]);

                // Log da revogação
                IgrejaPermissaoLog::logRevogacaoFuncao(
                    $memberRecord,
                    $role,
                    $revokedBy,
                    ['motivo' => $motivo ?? 'Revogação de função']
                );

                // Limpar cache
                self::clearUserCache($member->id);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    //** ========================================
    //** Limpa o cache do usuário
    //** ========================================
    public static function clearUserCache(int|string $userId): void
    {
        $userId = (int) $userId; // Garantir que seja int

        $patterns = [
            "user_permissions_{$userId}_*",
            "user_roles_{$userId}_*",
            "user_has_any_role_{$userId}",
            "user_all_permissions_{$userId}",
            "user_all_roles_{$userId}",
        ];

        foreach ($patterns as $pattern) {
            # Cache::forget() não suporta wildcards, então usamos tags ou deletamos individualmente
            # Como estamos usando file cache, vamos deletar padrões específicos
            Cache::forget($pattern);
        }

        # Também limpar cache global de permissões se necessário
        Cache::forget('rbac_all_permissions');
    }

    //** ========================================
    //** Limpa cache de todos os usuários (usar com cuidado)
    //** ========================================
    public static function clearAllUsersCache(): void
    {
        Cache::forget('rbac_all_permissions');
        # Nota: Em produção, considere usar tags de cache para limpeza mais eficiente
    }

    //** ========================================
    //** Método auxiliar para invalidar cache quando permissões são alteradas
    //** ========================================
    public static function invalidatePermissionCache(): void
    {
        Cache::forget('rbac_all_permissions');
    }

    //** ========================================
    //** MÉTODOS PRIVADOS
    //** ========================================

    private static function checkPermission(User $user, string $permissionCode): bool
    {
        # Admin/Pastor têm acesso total
        if (self::hasFullAccess($user)) {
            return true;
        }

        # Buscar membro principal
        $membro = $user->membros()->first();
        if (!$membro) {
            return false;
        }

        # ✅ APENAS: Verificar funções atribuídas ao membro (sistema RBAC completo)
        return IgrejaMembroFuncao::where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->whereHas('funcao.permissoes', function($q) use ($permissionCode) {
                $q->where('codigo', $permissionCode)->where('ativo', true);
            })
            ->exists();
    }


    private static function checkRole(User $user, string $roleName): bool
    {
        # Admin/Pastor têm acesso total
        if (self::hasFullAccess($user)) {
            return true;
        }

        # Buscar primeiro membro ativo do usuário
        $membro = $user->membros()->where('status', 'ativo')->first();
        if (!$membro) {
            return false;
        }

        # Verificar se tem a função específica
        return IgrejaMembroFuncao::where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->whereHas('funcao', function($q) use ($roleName) {
                $q->where('nome', $roleName)->where('ativo', true);
            })
            ->exists();
    }

    private static function checkAnyRole(User $user): bool
    {
        # Admin/Pastor têm acesso total
        if (self::hasFullAccess($user)) {
            //**/ \Illuminate\Support\Facades\Log::info('PermissionHelper: Usuário tem acesso total (admin/pastor)', [
            //**/     'user_id' => $user->id,
            //**/     'user_role' => $user->role,
            //**/ ]);
            return true;
        }

        # Buscar membro principal
        $membro = $user->membros()->first();
        if (!$membro) {
            //**/ \Illuminate\Support\Facades\Log::warning('PermissionHelper: Usuário não tem membro', [
            //**/     'user_id' => $user->id,
            //**/     'user_role' => $user->role,
            //**/ ]);
            return false;
        }

        //**/ \Illuminate\Support\Facades\Log::info('PermissionHelper: Verificando funções do membro', [
        //**/     'user_id' => $user->id,
        //**/     'membro_id' => $membro->id,
        //**/     'membro_status' => $membro->status,
        //**/     'membro_cargo' => $membro->cargo,
        //** ]);

        # Verificar se tem qualquer função ativa
        $hasRole = IgrejaMembroFuncao::where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->exists();

        // 🔍 LOG DETALHADO: Mostrar funções encontradas
        if ($hasRole) {
            $funcoes = IgrejaMembroFuncao::with('funcao')
                ->where('membro_id', $membro->id)
                ->where('status', 'ativo')
                ->where(function($query) {
                    $query->whereNull('valido_ate')
                          ->orWhere('valido_ate', '>', now());
                })
                ->get();

            /*
            $logMessage = "🔍 PERMISSION HELPER - FUNCTIONS FOUND\n" .
                "User ID: {$user->id}\n" .
                "User Email: {$user->email}\n" .
                "Member ID: {$membro->id}\n" .
                "Member Cargo: {$membro->cargo}\n" .
                "Functions Found: {$funcoes->count()}\n";

            foreach ($funcoes as $funcao) {
                $logMessage .= "  - Function: {$funcao->funcao->nome} (ID: {$funcao->funcao->id})\n";
                $logMessage .= "    Status: {$funcao->status}\n";
                $logMessage .= "    Atribuido em: {$funcao->atribuido_em}\n";
                if ($funcao->valido_ate) {
                    $logMessage .= "    Valido até: {$funcao->valido_ate}\n";
                }
                $logMessage .= "\n";
            }

            $logMessage .= str_repeat("=", 50);
            \Illuminate\Support\Facades\Log::info($logMessage);
            */
        } else {
            /*
            \Illuminate\Support\Facades\Log::warning("🔍 PERMISSION HELPER - NO FUNCTIONS FOUND\n" .
                "User ID: {$user->id}\n" .
                "User Email: {$user->email}\n" .
                "Member ID: {$membro->id}\n" .
                "Member Cargo: {$membro->cargo}\n" .
                "Functions Found: 0\n" .
                "Status: NO ACTIVE FUNCTIONS\n" .
                str_repeat("=", 50));
            */
        }

        /*
        \Illuminate\Support\Facades\Log::info('PermissionHelper: Resultado da verificação de funções', [
            'user_id' => $user->id,
             'membro_id' => $membro->id,
            'hasRole' => $hasRole,
             ]);
        */

        return $hasRole;
    }

    private static function getUserPermissionsFromDB(User $user): Collection
    {
        # Admin/Pastor têm todas as permissões
        if (self::hasFullAccess($user)) {
            return IgrejaPermissao::ativas()->get();
        }

        # Buscar membro principal
        $membro = $user->membros()->first();
        if (!$membro) {
            return collect();
        }

        # ✅ APENAS: Buscar permissões das funções ativas atribuídas (RBAC completo)
        return IgrejaPermissao::whereHas('funcoes', function($q) use ($membro) {
            $q->whereHas('membroFuncoes', function($mq) use ($membro) {
                $mq->where('membro_id', $membro->id)
                   ->where('status', 'ativo')
                   ->where(function($query) {
                       $query->whereNull('valido_ate')
                             ->orWhere('valido_ate', '>', now());
                   });
            });
        })->get();
    }

    private static function getUserRolesFromDB(User $user): Collection
    {
        # Admin/Pastor têm acesso total
        if (self::hasFullAccess($user)) {
            return collect(['admin_full_access']); # Representa acesso total
        }

        # Buscar membro principal
        $membro = $user->membros()->first();
        if (!$membro) {
            return collect();
        }

        # Buscar funções ativas
        return IgrejaMembroFuncao::with('funcao')
            ->where('membro_id', $membro->id)
            ->where('status', 'ativo')
            ->where(function($query) {
                $query->whereNull('valido_ate')
                      ->orWhere('valido_ate', '>', now());
            })
            ->get()
            ->pluck('funcao');
    }


    //** ========================================
    //** Obtém permissões legadas para um cargo específico da base de dados
    //** ========================================
    private static function getLegacyPermissionsForRole(string $cargo): \Illuminate\Support\Collection
    {
        $cacheKey = "legacy_permissions_{$cargo}";

        return Cache::remember($cacheKey, 3600, function () use ($cargo) {
            # Hierarquia de cargos (mais permissões para cargos superiores)
            $cargoHierarchy = [
                'admin' => 10,    # Acesso total
                'pastor' => 10,    # Acesso total
                'ministro' => 10,  # Acesso total
                'obreiro' => 5,   # Intermediário-alto
                'diacono' => 5,   # Intermediário
                'membro' => 4    # Básico
            ];

            $nivelCargo = $cargoHierarchy[$cargo] ?? 4; # Padrão = membro

            if ($nivelCargo >= 10) {
                # Admin tem todas as permissões ativas
                return \App\Models\RBAC\IgrejaPermissao::ativas()->get();
            }

            # Buscar permissões baseadas no nível hierárquico do cargo
            return \App\Models\RBAC\IgrejaPermissao::where('ativo', true)
                ->where('nivel_hierarquia', '<=', $nivelCargo)
                ->get();
        });
    }
}
