<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait HasAuditoria
{
    public static function bootHasAuditoria()
    {
        static::created(function ($model) {
            $model->registrarAuditoria('insert');
        });

        static::updated(function ($model) {
            $model->registrarAuditoria('update');
        });

        static::deleted(function ($model) {
            $model->registrarAuditoria('delete');
        });
    }

    protected function registrarAuditoria(string $acao): void
    {
        try {
            DB::table('auditoria_logs')->insert([
                'id'          => Str::uuid(),
                'tabela'      => $this->getTable(),
                'registro_id' => (string) $this->getKey(),
                'acao'        => $acao,
                'usuario_id'  => Auth::id(),
                'data_acao'   => now(),
                'valores'     => json_encode($this->getAuditoriaValores($acao)),
            ]);
        } catch (\Throwable $e) {
            // ⚠️ Evita quebrar fluxo da app se o log falhar
            report($e);
        }
    }

    protected function getAuditoriaValores(string $acao): array
    {
        if ($acao === 'insert') {
            return $this->attributesToArray();
        }

        if ($acao === 'update') {
            return $this->getChanges();
        }

        if ($acao === 'delete') {
            return $this->attributesToArray();
        }

        return [];
    }
}
