<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssinaturaVerificacoes extends Model
{
    use HasFactory;

    protected $table = 'assinatura_verificacoes';

    protected $fillable = [
        'igreja_id',
        'recurso_solicitado',
        'acao_solicitada',
        'status_verificacao',
        'detalhes',
        'verificado_em',
        'usuario_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'detalhes' => 'array',
        'verificado_em' => 'datetime',
        'created_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // 🔗 SCOPES
    public function scopePermitidas($query)
    {
        return $query->where('status_verificacao', 'permitido');
    }

    public function scopeBloqueadas($query)
    {
        return $query->where('status_verificacao', 'bloqueado_assinatura');
    }

    public function scopeLimiteExcedido($query)
    {
        return $query->where('status_verificacao', 'limite_excedido');
    }

    public function scopeComErro($query)
    {
        return $query->where('status_verificacao', 'erro');
    }

    public function scopePorRecurso($query, $recurso)
    {
        return $query->where('recurso_solicitado', $recurso);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao_solicitada', $acao);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('verificado_em', '>=', now()->subDays($dias));
    }

    public function scopeDoUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // 🔗 HELPERS
    public function isPermitido(): bool
    {
        return $this->status_verificacao === 'permitido';
    }

    public function isBloqueado(): bool
    {
        return $this->status_verificacao === 'bloqueado_assinatura';
    }

    public function isLimiteExcedido(): bool
    {
        return $this->status_verificacao === 'limite_excedido';
    }

    public function isErro(): bool
    {
        return $this->status_verificacao === 'erro';
    }

    public function getStatusFormatado(): string
    {
        return match($this->status_verificacao) {
            'permitido' => 'Permitido',
            'bloqueado_assinatura' => 'Bloqueado - Assinatura',
            'limite_excedido' => 'Limite Excedido',
            'erro' => 'Erro',
            default => ucfirst($this->status_verificacao)
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status_verificacao) {
            'permitido' => 'success',
            'bloqueado_assinatura' => 'danger',
            'limite_excedido' => 'warning',
            'erro' => 'secondary',
            default => 'secondary'
        };
    }

    public function getRecursoFormatado(): string
    {
        try {
            $permissao = \App\Models\RBAC\IgrejaPermissao::where('codigo', $this->recurso_solicitado)->first();
            return $permissao ? $permissao->nome : ucfirst($this->recurso_solicitado);
        } catch (\Exception $e) {
            return ucfirst($this->recurso_solicitado);
        }
    }

    public function getAcaoFormatada(): string
    {
        return match($this->acao_solicitada) {
            'adicionar' => 'Adicionar',
            'enviar' => 'Enviar',
            'upload' => 'Upload',
            'acessar' => 'Acessar',
            default => ucfirst($this->acao_solicitada)
        };
    }

    public function getDetalhesFormatados(): array
    {
        return $this->detalhes ?? [];
    }

    public function getVerificadoEmFormatado(): string
    {
        return $this->verificado_em->format('d/m/Y H:i:s');
    }

    public function getVerificadoEmRelativo(): string
    {
        return $this->verificado_em->diffForHumans();
    }

    public function getIpAddressFormatado(): string
    {
        return $this->ip_address ?? 'N/A';
    }

    public function getUserAgentResumido($limite = 50): string
    {
        if (!$this->user_agent) {
            return 'N/A';
        }

        if (strlen($this->user_agent) <= $limite) {
            return $this->user_agent;
        }

        return substr($this->user_agent, 0, $limite) . '...';
    }

    public function getDescricaoCompleta(): string
    {
        $recurso = $this->getRecursoFormatado();
        $acao = $this->getAcaoFormatada();
        $status = $this->getStatusFormatado();

        return "Tentativa de {$acao} {$recurso} - {$status}";
    }

    // 🔗 MÉTODOS ESTÁTICOS PARA LOG
    public static function logVerificacao(
        $igrejaId,
        $recurso,
        $acao,
        $status,
        $detalhes = [],
        $usuarioId = null,
        $ipAddress = null,
        $userAgent = null
    ): self {
        return static::create([
            'igreja_id' => $igrejaId,
            'recurso_solicitado' => $recurso,
            'acao_solicitada' => $acao,
            'status_verificacao' => $status,
            'detalhes' => $detalhes,
            'verificado_em' => now(),
            'usuario_id' => $usuarioId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    public static function logPermitido($igrejaId, $recurso, $acao, $detalhes = [], $usuarioId = null, $ipAddress = null, $userAgent = null): self
    {
        return self::logVerificacao($igrejaId, $recurso, $acao, 'permitido', $detalhes, $usuarioId, $ipAddress, $userAgent);
    }

    public static function logBloqueado($igrejaId, $recurso, $acao, $motivo, $detalhes = [], $usuarioId = null, $ipAddress = null, $userAgent = null): self
    {
        $detalhes = array_merge($detalhes, ['motivo' => $motivo]);
        return self::logVerificacao($igrejaId, $recurso, $acao, 'bloqueado_assinatura', $detalhes, $usuarioId, $ipAddress, $userAgent);
    }

    public static function logLimiteExcedido($igrejaId, $recurso, $acao, $limite, $consumo, $detalhes = [], $usuarioId = null, $ipAddress = null, $userAgent = null): self
    {
        $detalhes = array_merge($detalhes, [
            'limite' => $limite,
            'consumo' => $consumo,
            'excesso' => $consumo - $limite
        ]);
        return self::logVerificacao($igrejaId, $recurso, $acao, 'limite_excedido', $detalhes, $usuarioId, $ipAddress, $userAgent);
    }

    public static function logErro($igrejaId, $recurso, $acao, $erro, $detalhes = [], $usuarioId = null, $ipAddress = null, $userAgent = null): self
    {
        $detalhes = array_merge($detalhes, ['erro' => $erro]);
        return self::logVerificacao($igrejaId, $recurso, $acao, 'erro', $detalhes, $usuarioId, $ipAddress, $userAgent);
    }
}
