<?php

namespace App\Helpers;

class TwoFactorHelper
{
    /**
     * Verifica se o 2FA está ativo para o usuário
     */
    public static function isEnabled($user): bool
    {
        return $user &&
               $user->two_factor_secret &&
               !empty($user->two_factor_secret) &&
               $user->two_factor_confirmed_at;
    }

    /**
     * Verifica se o usuário tem códigos de recuperação válidos
     */
    public static function hasRecoveryCodes($user): bool
    {
        if (!$user || !$user->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode($user->two_factor_recovery_codes, true);
        return is_array($codes) && count($codes) > 0;
    }

    /**
     * Obtém os códigos de recuperação de forma segura
     */
    public static function getRecoveryCodes($user): array
    {
        if (!self::hasRecoveryCodes($user)) {
            return [];
        }

        $codes = json_decode($user->two_factor_recovery_codes, true);
        return is_array($codes) ? $codes : [];
    }

    /**
     * Conta os códigos de recuperação disponíveis
     */
    public static function countRecoveryCodes($user): int
    {
        return count(self::getRecoveryCodes($user));
    }
}

