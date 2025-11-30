<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class View extends Model
{
    protected $table = null;
    public $timestamps = false;

    /**
     * View: Assinaturas Ativas
     */
    public static function assinaturasAtivas()
    {
        return DB::table('view_assinaturas_ativas');
    }

    /**
     * View: Assinaturas Inativas
     */
    public static function assinaturasInativas()
    {
        return DB::table('view_assinaturas_inativas');
    }

    /**
     * View: Pagamentos Confirmados
     */
    public static function pagamentosConfirmados()
    {
        return DB::table('view_assinatura_pagamentos_confirmados');
    }

    /**
     * View: Assinaturas em Atraso
     */
    public static function assinaturasEmAtraso()
    {
        return DB::table('view_assinaturas_em_atraso');
    }

    /**
     * View: Logs de Assinaturas
     */
    public static function logsAssinaturas()
    {
        return DB::table('view_assinatura_logs');
    }

    /**
     * Métodos de conveniência para queries comuns
     */
    public static function getAssinaturasAtivas()
    {
        return self::assinaturasAtivas()->get();
    }

    public static function getAssinaturasInativas()
    {
        return self::assinaturasInativas()->get();
    }

    public static function getPagamentosConfirmados()
    {
        return self::pagamentosConfirmados()->get();
    }

    public static function getAssinaturasEmAtraso()
    {
        return self::assinaturasEmAtraso()->get();
    }

    public static function getLogsAssinaturas()
    {
        return self::logsAssinaturas()->get();
    }

    /**
     * Métodos para queries com filtros
     */
    public static function getAssinaturasAtivasPorIgreja($igrejaId)
    {
        return self::assinaturasAtivas()->where('igreja_id', $igrejaId)->get();
    }

    public static function getAssinaturasAtivasPorPacote($pacoteId)
    {
        return self::assinaturasAtivas()->where('pacote_id', $pacoteId)->get();
    }

    public static function getPagamentosConfirmadosPorPeriodo($inicio, $fim)
    {
        return self::pagamentosConfirmados()
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->get();
    }

    public static function getAssinaturasEmAtrasoPorDias($dias = 7)
    {
        return self::assinaturasEmAtraso()
            ->where('fim', '<', now()->subDays($dias))
            ->get();
    }

    public static function getLogsAssinaturasPorAcao($acao)
    {
        return self::logsAssinaturas()->where('acao', $acao)->get();
    }

    public static function getLogsAssinaturasPorIgreja($igrejaId)
    {
        return self::logsAssinaturas()->where('igreja_id', $igrejaId)->get();
    }

    public static function getLogsAssinaturasPorUsuario($usuarioId)
    {
        return self::logsAssinaturas()->where('usuario_id', $usuarioId)->get();
    }

    /**
     * Métodos para estatísticas
     */
    public static function getTotalAssinaturasAtivas()
    {
        return self::assinaturasAtivas()->count();
    }

    public static function getTotalAssinaturasInativas()
    {
        return self::assinaturasInativas()->count();
    }

    public static function getTotalPagamentosConfirmados()
    {
        return self::pagamentosConfirmados()->count();
    }

    public static function getTotalAssinaturasEmAtraso()
    {
        return self::assinaturasEmAtraso()->count();
    }

    public static function getReceitaTotalConfirmada()
    {
        return self::pagamentosConfirmados()->sum('valor');
    }

    public static function getReceitaTotalConfirmadaPorPeriodo($inicio, $fim)
    {
        return self::pagamentosConfirmados()
            ->whereBetween('data_pagamento', [$inicio, $fim])
            ->sum('valor');
    }

    /**
     * Métodos para análises avançadas
     */
    public static function getPerformancePacotes()
    {
        return self::assinaturasAtivas()
            ->select('pacote_id', 'pacote_nome', DB::raw('COUNT(*) as total'))
            ->groupBy('pacote_id', 'pacote_nome')
            ->orderBy('total', 'desc')
            ->get();
    }

    public static function getDistribuicaoGeografica()
    {
        return self::assinaturasAtivas()
            ->select('igreja_nome', 'localizacao', DB::raw('COUNT(*) as total'))
            ->groupBy('igreja_nome', 'localizacao')
            ->orderBy('total', 'desc')
            ->get();
    }

    public static function getMetodosPagamentoMaisUsados()
    {
        return self::pagamentosConfirmados()
            ->select('metodo_pagamento', DB::raw('COUNT(*) as total'))
            ->groupBy('metodo_pagamento')
            ->orderBy('total', 'desc')
            ->get();
    }

    public static function getTaxaChurn($periodo = 30)
    {
        $cancelados = self::logsAssinaturas()
            ->where('acao', 'cancelado')
            ->where('data_acao', '>=', now()->subDays($periodo))
            ->count();

        $total = self::getTotalAssinaturasAtivas();

        return $total > 0 ? round(($cancelados / $total) * 100, 2) : 0;
    }

    public static function getTaxaRetencao($periodo = 30)
    {
        return 100 - self::getTaxaChurn($periodo);
    }
}
