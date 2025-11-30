<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\RBAC\IgrejaPermissao;
use App\Models\RBAC\IgrejaMembroFuncao;
use App\Helpers\RBAC\PermissionHelper;

class RBACServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // PermissionHelper usa métodos estáticos, não precisa de registro
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar Gates para permissões
        $this->registerGates();

        // Registrar diretivas Blade para permissões
        $this->registerBladeDirectives();

        // Registrar helpers globais
        $this->registerGlobalHelpers();
    }

    /**
     * Registra Gates do Laravel para verificação de permissões
     */
    private function registerGates(): void
    {
        // ========================================
        // GATES PARA PERMISSÕES ESPECÍFICAS
        // ========================================

        // Gate dinâmico para todas as permissões baseadas no código
        $permissoes = $this->getAllPermissions();

        foreach ($permissoes as $permissao) {
            Gate::define($permissao->codigo, function (User $user) use ($permissao) {
                return $this->checkPermission($user, $permissao->codigo);
            });
        }

        // ========================================
        // GATES PARA FUNÇÕES (ROLES)
        // ========================================

        // Gate para verificar se usuário tem função específica
        Gate::define('has-role', function (User $user, string $roleName) {
            return $this->checkRole($user, $roleName);
        });

        // Gate para verificar se usuário tem qualquer função
        Gate::define('has-any-role', function (User $user) {
            return $this->checkAnyRole($user);
        });

        // ========================================
        // GATES PARA ACESSO ADMINISTRATIVO
        // ========================================

        // Gate para acesso total (admin/pastor)
        Gate::define('full-access', function (User $user) {
            return PermissionHelper::hasFullAccess($user);
        });

        // Gate para acesso administrativo básico
        Gate::define('admin-access', function (User $user) {
            return PermissionHelper::hasFullAccess($user) || $this->checkAnyRole($user);
        });

        // ========================================
        // GATES PARA MÓDULOS ESPECÍFICOS
        // ========================================

        // Gestão Organizacional
        Gate::define('manage-leadership-body', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_corpo_lideranca');
        });

        // Gestão de Igrejas
        Gate::define('manage-churches', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_igrejas');
        });

        Gate::define('view-churches', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_igrejas') ||
                   $this->checkPermission($user, 'gerenciar_igrejas');
        });

        Gate::define('edit-churches', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'editar_igrejas') ||
                   $this->checkPermission($user, 'gerenciar_igrejas');
        });

        // Gestão de Membros
        Gate::define('manage-members', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_membros');
        });

        Gate::define('view-members', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_membros') ||
                   $this->checkPermission($user, 'gerenciar_membros');
        });

        // Gestão Financeira
        Gate::define('manage-finances', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_financeiro');
        });

        Gate::define('view-finances', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_financeiro') ||
                   $this->checkPermission($user, 'gerenciar_financeiro');
        });

        // Gestão de Eventos
        Gate::define('manage-events', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_eventos');
        });

        Gate::define('view-events', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_eventos') ||
                   $this->checkPermission($user, 'gerenciar_eventos');
        });

        // Gestão de Cursos
        Gate::define('manage-courses', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_cursos');
        });

        Gate::define('view-courses', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_cursos') ||
                   $this->checkPermission($user, 'gerenciar_cursos');
        });

        // Relatórios
        Gate::define('generate-reports', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerar_relatorios');
        });

        Gate::define('view-reports', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_relatorios') ||
                   $this->checkPermission($user, 'gerar_relatorios');
        });

        // Comunicações
        Gate::define('manage-communications', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_comunicacoes');
        });

        Gate::define('view-communications', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_comunicacoes') ||
                   $this->checkPermission($user, 'gerenciar_comunicacoes');
        });

        // Pedidos Especiais
        Gate::define('manage-special-requests', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_pedidos_especiais');
        });

        Gate::define('view-special-requests', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_pedidos') ||
                   $this->checkPermission($user, 'gerenciar_pedidos_especiais');
        });

        // Recursos
        Gate::define('manage-resources', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_recursos');
        });

        Gate::define('view-resources', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_recursos') ||
                   $this->checkPermission($user, 'gerenciar_recursos');
        });

        // Atendimentos Pastorais
        Gate::define('manage-pastoral-care', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_atendimentos_pastorais');
        });

        Gate::define('view-pastoral-care', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_atendimentos') ||
                   $this->checkPermission($user, 'gerenciar_atendimentos_pastorais');
        });

        // ========================================
        // GATES PARA MÓDULOS ADICIONAIS
        // ========================================

        // Gestão de Membros - Cartões
        Gate::define('manage-member-cards', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_cartoes_membros');
        });

        Gate::define('edit-members', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'editar_membros') ||
                   $this->checkPermission($user, 'gerenciar_membros');
        });

        // Gestão de Ministérios
        Gate::define('manage-ministerios', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_ministerios');
        });

        Gate::define('view-ministerios', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_ministerios') ||
                   $this->checkPermission($user, 'gerenciar_ministerios');
        });

        Gate::define('manage-members-ministerios', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_membros_ministerios') ||
                   $this->checkPermission($user, 'gerenciar_ministerios');
        });

        // Alianças
        Gate::define('manage-alliances', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_aliancas');
        });

        Gate::define('view-alliances', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_aliancas') ||
                   $this->checkPermission($user, 'gerenciar_aliancas');
        });

        // Eventos - Escalas e Cultos
        Gate::define('manage-scales', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_escalas');
        });

        Gate::define('manage-cultos', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_cultos');
        });

        // Relatórios e Estatísticas
        Gate::define('manage-talent-map', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_mapa_talentos');
        });

        Gate::define('view-talent-map', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'visualizar_mapa_talentos') ||
                   $this->checkPermission($user, 'gerenciar_mapa_talentos');
        });

        Gate::define('manage-reports', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_relatorios');
        });

        Gate::define('view-reports', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'visualizar_relatorios') ||
                   $this->checkPermission($user, 'gerar_relatorios') ||
                   $this->checkPermission($user, 'gerenciar_relatorios');
        });

        Gate::define('manage-statistics', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_estatisticas');
        });

        Gate::define('view-statistics', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'visualizar_estatisticas') ||
                   $this->checkPermission($user, 'ver_estatisticas') ||
                   $this->checkPermission($user, 'gerenciar_estatisticas');
        });

        Gate::define('manage-calendar', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_calendario');
        });

        Gate::define('view-calendar', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'visualizar_calendario') ||
                   $this->checkPermission($user, 'gerenciar_calendario');
        });

        // Financeiro - Contas e Pagamentos
        Gate::define('manage-accounts', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_contas');
        });

        Gate::define('launch-movements', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'lancar_movimentos') ||
                   $this->checkPermission($user, 'gerenciar_financeiro');
        });

        Gate::define('approve-payments', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'aprovar_pagamentos');
        });

        // Social - Posts, Chats e Mensagens
        Gate::define('manage-posts', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_posts');
        });

        Gate::define('view-posts', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_posts') ||
                   $this->checkPermission($user, 'gerenciar_posts');
        });

        Gate::define('manage-chats', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_chats') ||
                   $this->checkPermission($user, 'gerenciar_chats_igreja');
        });

        Gate::define('manage-private-messages', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_mensagens_privadas');
        });

        // Cursos - Inscrições e Certificados
        Gate::define('enroll-students', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'inscrever_alunos') ||
                   $this->checkPermission($user, 'gerenciar_inscricoes');
        });

        Gate::define('manage-enrollments', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_inscricoes');
        });

        Gate::define('issue-certificates', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'emitir_certificados');
        });

        Gate::define('view-certificates', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'visualizar_certificados') ||
                   $this->checkPermission($user, 'emitir_certificados');
        });

        // Recursos - Voluntários
        Gate::define('manage-volunteers', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_voluntarios');
        });

        // Marketplace - Produtos e Pedidos
        Gate::define('manage-products', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_produtos');
        });

        Gate::define('process-orders', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'processar_pedidos') ||
                   $this->checkPermission($user, 'gerenciar_pedidos');
        });

        Gate::define('manage-marketplace-orders', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_pedidos');
        });

        Gate::define('manage-marketplace-payments', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_pagamentos');
        });

        // Pedidos - Aprovação
        Gate::define('approve-requests', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'aprovar_pedidos');
        });

        // Engajamento - Sistema Geral, Badges, Pontos e Enquetes
        Gate::define('manage-engagement', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_engajamento');
        });

        Gate::define('view-engagement', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_engajamento') ||
                   $this->checkPermission($user, 'gerenciar_engajamento');
        });

        Gate::define('manage-badges', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_badges');
        });

        Gate::define('manage-points', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_pontos');
        });

        Gate::define('manage-polls', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_enquetes');
        });

        // Doações
        Gate::define('manage-donations', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_doacoes') ||
                   $this->checkPermission($user, 'gerenciar_doacoes_online');
        });

        Gate::define('view-donations', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_doacoes') ||
                   $this->checkPermission($user, 'gerenciar_doacoes_online');
        });

        // Sistema - Assinaturas e Definições
        Gate::define('manage-subscriptions', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_assinaturas');
        });

        Gate::define('manage-settings', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_definicoes');
        });

        Gate::define('access-settings', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'acessar_definicoes') ||
                   $this->checkPermission($user, 'gerenciar_definicoes');
        });

        // SMS - Sistema de Mensagens Administrativas
        Gate::define('manage-sms', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                    $this->checkPermission($user, 'gerenciar_sms');
        });

        // Controle de Acesso - Sistema de Controle de Acesso e Permissões
        Gate::define('manage-access-control', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                    $this->checkPermission($user, 'gerenciar_controle_acesso');
        });

        // Migração de Membros
        Gate::define('manage-member-migration', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_migracao_membros');
        });

        Gate::define('view-migration-history', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_historico_migracao') ||
                   $this->checkPermission($user, 'gerenciar_migracao_membros');
        });

        Gate::define('migrate-member', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'migrar_membro') ||
                   $this->checkPermission($user, 'gerenciar_migracao_membros');
        });

        Gate::define('approve-migration', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'aprovar_migracao') ||
                   $this->checkPermission($user, 'gerenciar_migracao_membros');
        });

        // Vitrine de Igrejas
        Gate::define('manage-church-showcase', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'gerenciar_vitrine_igrejas');
        });

        Gate::define('view-church-showcase', function (User $user) {
            return PermissionHelper::hasFullAccess($user) ||
                   $this->checkPermission($user, 'ver_vitrine_igrejas') ||
                   $this->checkPermission($user, 'gerenciar_vitrine_igrejas');
        });
    }

    /**
     * Obtém todas as permissões ativas do sistema
     */
    private function getAllPermissions()
    {
        return Cache::remember('rbac_all_permissions', 3600, function () {
            return IgrejaPermissao::ativas()->get();
        });
    }

    /**
     * Verifica se usuário tem uma permissão específica (com cache)
     */
    private function checkPermission(User $user, string $permissionCode): bool
    {
        $cacheKey = "user_permission_{$user->id}_{$permissionCode}";

        return Cache::remember($cacheKey, 300, function () use ($user, $permissionCode) {
            return PermissionHelper::hasPermissionStatic($user, $permissionCode, false);
        });
    }

    /**
     * Verifica se usuário tem uma função específica (com cache)
     */
    private function checkRole(User $user, string $roleName): bool
    {
        $cacheKey = "user_role_{$user->id}_{$roleName}";

        return Cache::remember($cacheKey, 300, function () use ($user, $roleName) {
            return PermissionHelper::hasRole($user, $roleName, false);
        });
    }

    /**
     * Verifica se usuário tem qualquer função (com cache)
     */
    private function checkAnyRole(User $user): bool
    {
        $cacheKey = "user_has_any_role_{$user->id}";

        return Cache::remember($cacheKey, 300, function () use ($user) {
            return PermissionHelper::hasAnyRole($user, false);
        });
    }

    /**
     * Registra diretivas Blade para facilitar uso de permissões nas views
     */
    private function registerBladeDirectives(): void
    {
        // @hasPermission('codigo_permissao')
        Blade::if('hasPermission', function ($permission) {
            return Auth::check() && PermissionHelper::hasPermissionStatic(Auth::user(), $permission);
        });

        // @hasAnyPermission(['perm1', 'perm2'])
        Blade::if('hasAnyPermission', function ($permissions) {
            return Auth::check() && PermissionHelper::hasAnyPermission(Auth::user(), $permissions);
        });

        // @hasAllPermissions(['perm1', 'perm2'])
        Blade::if('hasAllPermissions', function ($permissions) {
            return Auth::check() && PermissionHelper::hasAllPermissions(Auth::user(), $permissions);
        });

        // @hasRole('nome_funcao')
        Blade::if('hasRole', function ($role) {
            return Auth::check() && PermissionHelper::hasRole(Auth::user(), $role);
        });

        // @hasAnyRole
        Blade::if('hasAnyRole', function () {
            return Auth::check() && PermissionHelper::hasAnyRole(Auth::user());
        });

        // @hasFullAccess
        Blade::if('hasFullAccess', function () {
            return Auth::check() && PermissionHelper::hasFullAccess(Auth::user());
        });
    }

    /**
     * Registra helpers globais para uso em qualquer lugar
     */
    private function registerGlobalHelpers(): void
    {
        // Função global para verificar permissões
        if (!function_exists('hasPermission')) {
            function hasPermission(string $permission): bool
            {
                return Auth::check() && PermissionHelper::hasPermissionStatic(Auth::user(), $permission);
            }
        }

        if (!function_exists('hasAnyPermission')) {
            function hasAnyPermission(array $permissions): bool
            {
                return Auth::check() && PermissionHelper::hasAnyPermission(Auth::user(), $permissions);
            }
        }

        if (!function_exists('hasAllPermissions')) {
            function hasAllPermissions(array $permissions): bool
            {
                return Auth::check() && PermissionHelper::hasAllPermissions(Auth::user(), $permissions);
            }
        }

        if (!function_exists('hasRole')) {
            function hasRole(string $role): bool
            {
                return Auth::check() && PermissionHelper::hasRole(Auth::user(), $role);
            }
        }

        if (!function_exists('hasAnyRole')) {
            function hasAnyRole(): bool
            {
                return Auth::check() && PermissionHelper::hasAnyRole(Auth::user());
            }
        }

        if (!function_exists('hasFullAccess')) {
            function hasFullAccess(): bool
            {
                return Auth::check() && PermissionHelper::hasFullAccess(Auth::user());
            }
        }

        if (!function_exists('getUserPermissions')) {
            function getUserPermissions()
            {
                return Auth::check() ? PermissionHelper::getUserPermissions(Auth::user()) : collect();
            }
        }

        if (!function_exists('getUserRoles')) {
            function getUserRoles()
            {
                return Auth::check() ? PermissionHelper::getUserRoles(Auth::user()) : collect();
            }
        }
    }
}
