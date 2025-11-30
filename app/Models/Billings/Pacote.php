<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Model;
use App\Models\Billings\AssinaturaAtual;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Billings\PacotePermissao;
use App\Models\Billings\AssinaturaHistorico;

class Pacote extends Model
{
    use HasFactory;

    protected $table = 'pacote';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'preco_vitalicio',
        'duracao_meses',
        'trial_dias',
    ];

    protected $casts = [
        'preco'         => 'decimal:2',
        'preco_vitalicio' => 'decimal:2',
        'duracao_meses' => 'integer',
        'trial_dias'    => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function permissoes(): HasMany
    {
        return $this->hasMany(PacotePermissao::class, 'pacote_id');
    }

    public function assinaturasAtuais(): HasMany
    {
        return $this->hasMany(AssinaturaAtual::class, 'pacote_id');
    }

    public function assinaturasHistorico(): HasMany
    {
        return $this->hasMany(AssinaturaHistorico::class, 'pacote_id');
    }

    // Novos relacionamentos para controle SaaS
    public function recursos(): HasMany
    {
        return $this->hasMany(\App\Models\Billings\PacoteRecursos::class, 'pacote_id');
    }

    public function niveis(): HasMany
    {
        return $this->hasMany(\App\Models\Billings\PacoteNiveis::class, 'pacote_id');
    }

    // 🔗 MÉTODOS AUXILIARES PARA CONTROLE SAAS
    public function getLimiteRecurso($recursoTipo)
    {
        return $this->recursos()
            ->where('recurso_tipo', $recursoTipo)
            ->where('ativo', true)
            ->value('limite_valor');
    }

    public function getNivelMaximo()
    {
        return $this->niveis()->orderBy('prioridade', 'desc')->first();
    }

    public function temNivel($nivel): bool
    {
        return $this->niveis()->where('nivel', $nivel)->exists();
    }

    public function getRecursosAtivos()
    {
        return $this->recursos()->where('ativo', true)->get();
    }

    public function getNiveisOrdenados()
    {
        return $this->niveis()->orderBy('prioridade')->get();
    }

    public function temRecurso($recursoTipo): bool
    {
        return $this->recursos()
            ->where('recurso_tipo', $recursoTipo)
            ->where('ativo', true)
            ->exists();
    }

    public function getPrecoFormatado(): string
    {
        return 'Kz ' . number_format($this->preco, 2, ',', '.');
    }

    public function getPrecoVitalicioFormatado(): string
    {
        if (!$this->preco_vitalicio) return 'N/A';
        return 'Kz ' . number_format($this->preco_vitalicio, 2, ',', '.');
    }

    public function getDuracaoFormatada(): string
    {
        if ($this->duracao_meses == 1) {
            return '1 mês';
        }

        return $this->duracao_meses . ' meses';
    }

    public function getTrialFormatado(): string
    {
        if ($this->trial_dias == 0) {
            return 'Sem trial';
        }

        if ($this->trial_dias == 1) {
            return '1 dia';
        }

        return $this->trial_dias . ' dias';
    }

    public function isPopular(): bool
    {
        // O pacote popular é sempre o segundo mais barato (depois do mais barato)
        $pacotesOrdenados = self::orderBy('preco', 'asc')->get();

        if ($pacotesOrdenados->count() < 2) {
            // Se há menos de 2 pacotes, nenhum é popular
            return false;
        }

        // O segundo pacote mais barato é o popular
        $pacotePopular = $pacotesOrdenados->skip(1)->first();

        return $pacotePopular && $pacotePopular->id === $this->id;
    }

    public function getEconomiaMensal(): ?float
    {
        if (!$this->preco_vitalicio || $this->duracao_meses == 0) {
            return null;
        }

        $precoMensal = $this->preco;
        $precoVitalicioMensal = $this->preco_vitalicio / $this->duracao_meses;

        if ($precoVitalicioMensal >= $precoMensal) {
            return null;
        }

        return round((($precoMensal - $precoVitalicioMensal) / $precoMensal) * 100, 1);
    }
}
