<?php

namespace App\Models\Cursos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoCertificado extends Model
{
    use HasFactory;

    protected $table = 'curso_certificados';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';
    public $timestamps = false; // Apenas created_at no schema

    protected $fillable = [
        'id',
        'matricula_id',
        'numero_certificado',
        'data_emissao',
        'data_conclusao',
        'frequencia_final',
        'template_usado',
        'codigo_verificacao',
        'valido_ate',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'data_conclusao' => 'date',
        'valido_ate' => 'date',
        'frequencia_final' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(CursoMatricula::class, 'matricula_id');
    }

    // 🔗 RELACIONAMENTOS ATRAVÉS DE OUTRAS MODELS
    public function turma()
    {
        return $this->matricula->turma ?? null;
    }

    public function curso()
    {
        return $this->matricula->turma->curso ?? null;
    }

    public function membro()
    {
        return $this->matricula->membro ?? null;
    }

    public function user()
    {
        return $this->matricula->membro->user ?? null;
    }

    public function igreja()
    {
        return $this->matricula->membro->igreja ?? null;
    }

    // ========================================
    // Helpers para checagem rápida
    // ========================================
    public function isValido(): bool
    {
        return $this->valido_ate ? $this->valido_ate >= now()->toDateString() : true;
    }

    public function isExpirado(): bool
    {
        return !$this->isValido();
    }

    public function temCodigoVerificacao(): bool
    {
        return !empty($this->codigo_verificacao);
    }

    // ========================================
    // Scopes
    // ========================================
    public function scopeValidos($query)
    {
        return $query->where(function($q) {
            $q->whereNull('valido_ate')
              ->orWhere('valido_ate', '>=', now()->toDateString());
        });
    }

    public function scopeExpirados($query)
    {
        return $query->where('valido_ate', '<', now()->toDateString());
    }

    public function scopePorMatricula($query, $matriculaId)
    {
        return $query->where('matricula_id', $matriculaId);
    }

    public function scopePorNumero($query, $numero)
    {
        return $query->where('numero_certificado', $numero);
    }

    public function scopePorCodigoVerificacao($query, $codigo)
    {
        return $query->where('codigo_verificacao', $codigo);
    }

    public function scopeEmitidosEm($query, $ano)
    {
        return $query->whereYear('data_emissao', $ano);
    }

    // ========================================
    // Accessors
    // ========================================
    public function getStatusValidadeAttribute(): string
    {
        if (!$this->valido_ate) {
            return 'Válido permanentemente';
        }

        if ($this->isValido()) {
            return 'Válido até ' . \Carbon\Carbon::parse($this->valido_ate)->format('d/m/Y');
        }

        return 'Expirado em ' . \Carbon\Carbon::parse($this->valido_ate)->format('d/m/Y');
    }

    public function getStatusValidadeBadgeClassAttribute(): string
    {
        if (!$this->valido_ate) {
            return 'badge-success';
        }

        return $this->isValido() ? 'badge-success' : 'badge-danger';
    }

    public function getNomeCompletoAttribute(): string
    {
        return $this->membro()->user->name ?? 'Nome não disponível';
    }

    public function getNomeCursoAttribute(): string
    {
        return $this->curso()->nome ?? 'Curso não disponível';
    }

    public function getFrequenciaFormatadaAttribute(): string
    {
        return $this->frequencia_final ? $this->frequencia_final . '%' : 'Não informada';
    }

    public function getDiasValidadeRestantesAttribute(): ?int
    {
        if (!$this->valido_ate) {
            return null;
        }

        $diasRestantes = \Carbon\Carbon::parse($this->valido_ate)->diffInDays(now(), false);
        return $diasRestantes < 0 ? abs($diasRestantes) : 0;
    }

    public function getUrlVerificacaoAttribute(): string
    {
        if (!$this->codigo_verificacao) {
            return '';
        }

        return route('certificados.verificar', ['codigo' => $this->codigo_verificacao]);
    }

    // ========================================
    // Métodos de negócio
    // ========================================
    public function gerarNumero(): string
    {
        $ano = now()->year;
        $curso = $this->curso();
        $igreja = $this->igreja();

        $prefixo = $curso ? strtoupper(substr($curso->nome, 0, 3)) : 'CUR';
        $sufixoIgreja = $igreja ? str_pad($igreja->id, 3, '0', STR_PAD_LEFT) : '000';

        // Buscar o próximo número sequencial para este ano
        $ultimoNumero = self::whereYear('data_emissao', $ano)
            ->where('numero_certificado', 'like', "{$prefixo}-{$ano}-%")
            ->count();

        $sequencial = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefixo}-{$ano}-{$sufixoIgreja}-{$sequencial}";
    }

    public function gerarCodigoVerificacao(): string
    {
        return strtoupper(bin2hex(random_bytes(16)));
    }

    public function definirValidadePermanente(): bool
    {
        $this->valido_ate = null;
        return $this->save();
    }

    public function definirValidadeTemporaria(int $anos = 5): bool
    {
        $this->valido_ate = now()->addYears($anos)->toDateString();
        return $this->save();
    }

    public function renovarValidade(int $anos = 5): bool
    {
        if ($this->isExpirado()) {
            $this->valido_ate = now()->addYears($anos)->toDateString();
        } else {
            $this->valido_ate = \Carbon\Carbon::parse($this->valido_ate)->addYears($anos)->toDateString();
        }

        return $this->save();
    }

    public function revogar(): bool
    {
        $this->valido_ate = now()->subDay()->toDateString();
        return $this->save();
    }

    // ========================================
    // Métodos estáticos
    // ========================================
    public static function verificarPorCodigo(string $codigo): ?self
    {
        return self::where('codigo_verificacao', $codigo)->first();
    }

    public static function buscarPorNumero(string $numero): ?self
    {
        return self::where('numero_certificado', $numero)->first();
    }

    // ========================================
    // Boot method para auto-gerar campos
    // ========================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($certificado) {
            if (empty($certificado->numero_certificado)) {
                $certificado->numero_certificado = $certificado->gerarNumero();
            }

            if (empty($certificado->codigo_verificacao)) {
                $certificado->codigo_verificacao = $certificado->gerarCodigoVerificacao();
            }

            if (empty($certificado->data_emissao)) {
                $certificado->data_emissao = now()->toDateString();
            }
        });
    }
}
