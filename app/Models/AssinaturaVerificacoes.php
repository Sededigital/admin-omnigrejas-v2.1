<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Igrejas\Igreja;
use App\Models\User;

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
        'updated_at' => 'datetime',
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
        return $query->whereIn('status_verificacao', ['bloqueado_assinatura', 'limite_excedido']);
    }

    public function scopeErros($query)
    {
        return $query->where('status_verificacao', 'erro');
    }

    public function scopePorPeriodo($query, $inicio, $fim)
    {
        return $query->whereBetween('verificado_em', [$inicio, $fim]);
    }

    public function scopePorRecurso($query, $recurso)
    {
        return $query->where('recurso_solicitado', $recurso);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao_solicitada', $acao);
    }

    public function scopeRecentes($query, $horas = 24)
    {
        return $query->where('verificado_em', '>=', now()->subHours($horas));
    }

    // 🔗 HELPERS
    public function isPermitida(): bool
    {
        return $this->status_verificacao === 'permitido';
    }

    public function isBloqueada(): bool
    {
        return in_array($this->status_verificacao, ['bloqueado_assinatura', 'limite_excedido']);
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
            'limite_excedido' => 'Bloqueado - Limite Excedido',
            'erro' => 'Erro',
            default => ucfirst($this->status_verificacao)
        };
    }

    public function getStatusClass(): string
    {
        return match($this->status_verificacao) {
            'permitido' => 'success',
            'bloqueado_assinatura' => 'danger',
            'limite_excedido' => 'warning',
            'erro' => 'secondary',
            default => 'secondary'
        };
    }

    public function getStatusIcone(): string
    {
        return match($this->status_verificacao) {
            'permitido' => 'fas fa-check-circle',
            'bloqueado_assinatura' => 'fas fa-times-circle',
            'limite_excedido' => 'fas fa-exclamation-triangle',
            'erro' => 'fas fa-exclamation-circle',
            default => 'fas fa-question-circle'
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
            'criar' => 'Criar',
            'upload' => 'Upload',
            'acessar' => 'Acessar',
            default => ucfirst($this->acao_solicitada)
        };
    }

    public function getVerificadoEmFormatado(): string
    {
        return $this->verificado_em->format('d/m/Y H:i:s');
    }

    public function getVerificadoEmRelativo(): string
    {
        return $this->verificado_em->diffForHumans();
    }

    public function getDetalhesFormatados(): array
    {
        if (is_string($this->detalhes)) {
            return json_decode($this->detalhes, true) ?? [];
        }

        return $this->detalhes ?? [];
    }

    public function getValorDetalhes($chave, $padrao = null)
    {
        $detalhes = $this->getDetalhesFormatados();
        return $detalhes[$chave] ?? $padrao;
    }

    public function getDescricaoCompleta(): string
    {
        $recurso = $this->getRecursoFormatado();
        $acao = $this->getAcaoFormatada();
        $status = $this->getStatusFormatado();

        return "{$acao} {$recurso} - {$status}";
    }

    public function foiVerificadoRecentemente($minutos = 5): bool
    {
        return $this->verificado_em->diffInMinutes(now()) <= $minutos;
    }

    public function getIpAddressAnonymized(): string
    {
        if (!$this->ip_address) return 'N/A';

        $parts = explode('.', $this->ip_address);
        if (count($parts) === 4) {
            return $parts[0] . '.' . $parts[1] . '.' . $parts[2] . '.***';
        }

        return $this->ip_address;
    }

    public function getUserAgentResumido($limite = 50): string
    {
        if (!$this->user_agent) return 'N/A';

        if (strlen($this->user_agent) <= $limite) {
            return $this->user_agent;
        }

        return substr($this->user_agent, 0, $limite) . '...';
    }
}
