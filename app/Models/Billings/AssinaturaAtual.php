<?php

namespace App\Models\Billings;

use App\Models\Igrejas\Igreja;
use App\Models\Billings\Pacote;
use App\Models\Billings\AssinaturaLog;
use Illuminate\Database\Eloquent\Model;

use App\Models\Billings\AssinaturaCiclo;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\AssinaturaAutoRenovacao;
use App\Models\Billings\AssinaturaPagamentoFalha;
use App\Models\Billings\Trial\TrialUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaAtual extends Model
{
    protected $table = 'assinatura_atual';
    protected $primaryKey = 'igreja_id';
    public $incrementing = false; // chave primária é FK
    protected $keyType = 'int';

    protected $fillable = [
        'igreja_id',
        'pacote_id',
        'data_inicio',
        'data_fim',
        'status',
        'trial_fim',
        'duracao_meses_custom',
        'vitalicio',
    ];

    protected $casts = [
        'data_inicio'          => 'date',
        'data_fim'             => 'date',
        'trial_fim'            => 'date',
        'duracao_meses_custom' => 'integer',
        'vitalicio'            => 'boolean',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    public function pacote(): BelongsTo
    {
        return $this->belongsTo(Pacote::class, 'pacote_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(AssinaturaPagamento::class, 'igreja_id', 'igreja_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AssinaturaLog::class, 'igreja_id', 'igreja_id');
    }

    public function ciclos(): HasMany
    {
        return $this->hasMany(AssinaturaCiclo::class, 'assinatura_id');
    }

    public function autoRenovacao(): HasOne
    {
        return $this->hasOne(AssinaturaAutoRenovacao::class, 'igreja_id', 'igreja_id');
    }

    public function pagamentosFalha(): HasMany
    {
        return $this->hasMany(AssinaturaPagamentoFalha::class, 'igreja_id', 'igreja_id');
    }

    // 🔗 RELACIONAMENTO COM TRIAL
    public function trialAtivo(): HasOne
    {
        return $this->hasOne(TrialUser::class, 'igreja_id', 'igreja_id')
                    ->where('status', 'ativo')
                    ->where('data_fim', '>=', now());
    }

    // Helpers
    public function isExpired(): bool
    {
        if ($this->vitalicio) {
            return false;
        }
        return $this->data_fim && $this->data_fim->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if ($this->vitalicio) {
            return false;
        }
        return $this->data_fim && $this->data_fim->diffInDays(now(), false) <= $days;
    }

    public function getDaysUntilExpiration(): int
    {
        if ($this->vitalicio) {
            return 9999; // vitalício => nunca expira
        }
        return $this->data_fim ? $this->data_fim->diffInDays(now()) : 0;
    }

    // 🔗 MÉTODOS AUXILIARES PARA CONTROLE SAAS
    public function getLimiteRecurso($recursoTipo)
    {
        return $this->pacote?->getLimiteRecurso($recursoTipo);
    }

    public function temNivel($nivel): bool
    {
        return $this->pacote?->temNivel($nivel);
    }

    public function getNivelMaximo()
    {
        return $this->pacote?->getNivelMaximo();
    }

    public function estaAtiva(): bool
    {
        if ($this->status !== 'Ativo') return false;

        if ($this->vitalicio) return true;

        return $this->data_fim && $this->data_fim->isFuture();
    }

    public function diasParaExpirar(): ?int
    {
        if ($this->vitalicio || !$this->data_fim) return null;

        return now()->diffInDays($this->data_fim, false);
    }

    public function isTrialAtivo(): bool
    {
        // Primeiro verifica se há trial ativo através do relacionamento
        $trialAtivo = $this->trialAtivo;

        if ($trialAtivo) {
            return $trialAtivo->isAtivo();
        }

        // Fallback: usa o campo trial_fim da assinatura (compatibilidade)
        return $this->trial_fim && $this->trial_fim->isFuture();
    }

    public function diasTrialRestantes(): ?int
    {
        if (!$this->isTrialAtivo()) return null;

        // Primeiro tenta obter do relacionamento com TrialUser
        $trialAtivo = $this->trialAtivo;
        if ($trialAtivo) {
            return $trialAtivo->diasRestantes();
        }

        // Fallback: calcula baseado no campo trial_fim
        return now()->diffInDays($this->trial_fim, false);
    }

    public function getStatusDetalhado(): array
    {
        $status = [
            'ativo' => $this->estaAtiva(),
            'status' => $this->status,
            'vitalicio' => $this->vitalicio,
            'expirado' => $this->isExpired(),
            'expirando_em_breve' => $this->isExpiringSoon(7),
            'dias_para_expirar' => $this->diasParaExpirar(),
        ];

        // Verificar trial através do relacionamento ou campo legacy
        $trialAtivo = $this->trialAtivo;
        if ($trialAtivo && $trialAtivo->isAtivo()) {
            $status['em_trial'] = true;
            $status['dias_trial_restantes'] = $trialAtivo->diasRestantes();
        } elseif ($this->trial_fim && $this->trial_fim->isFuture()) {
            // Fallback para compatibilidade
            $status['em_trial'] = true;
            $status['dias_trial_restantes'] = now()->diffInDays($this->trial_fim, false);
        } else {
            $status['em_trial'] = false;
        }

        return $status;
    }

    public function getPacoteInfo(): array
    {
        if (!$this->pacote) {
            return ['error' => 'Pacote não encontrado'];
        }

        return [
            'id' => $this->pacote->id,
            'nome' => $this->pacote->nome,
            'preco' => $this->pacote->preco,
            'preco_formatado' => $this->pacote->getPrecoFormatado(),
            'duracao_meses' => $this->pacote->duracao_meses,
            'trial_dias' => $this->pacote->trial_dias,
            'recursos' => $this->pacote->getRecursosAtivos(),
            'niveis' => $this->pacote->getNiveisOrdenados(),
        ];
    }

    public function podeUsarRecurso($recursoTipo, $quantidade = 1): bool
    {
        $limite = $this->getLimiteRecurso($recursoTipo);

        if ($limite === null) return true; // Ilimitado

        // Verificar consumo atual
        $consumoAtual = $this->igreja?->getConsumoAtual($recursoTipo)?->consumo_atual ?? 0;

        return ($consumoAtual + $quantidade) <= $limite;
    }

    public function getUsoRecursos(): array
    {
        if (!$this->igreja) {
            return ['error' => 'Igreja não encontrada'];
        }

        $recursos = [];
        $pacoteRecursos = $this->pacote?->getRecursosAtivos() ?? [];

        foreach ($pacoteRecursos as $recurso) {
            $consumo = $this->igreja->getConsumoAtual($recurso->recurso_tipo);

            $recursos[$recurso->recurso_tipo] = [
                'limite' => $recurso->limite_valor,
                'consumo' => $consumo?->consumo_atual ?? 0,
                'disponivel' => $recurso->limite_valor ?
                    max(0, $recurso->limite_valor - ($consumo?->consumo_atual ?? 0)) : null,
                'percentual' => $recurso->limite_valor ?
                    round((($consumo?->consumo_atual ?? 0) / $recurso->limite_valor) * 100, 1) : 0,
                'unidade' => $recurso->unidade,
                'icone' => $recurso->getIcone(),
            ];
        }

        return $recursos;
    }

    public function renovarAutomaticamente(): bool
    {
        if (!$this->autoRenovacao?->isAtivo()) {
            return false;
        }

        // Lógica de renovação automática
        // Implementar conforme necessidade
        return false;
    }

    public function cancelar($motivo = null): bool
    {
        try {
            $this->update([
                'status' => 'Cancelado',
                'data_fim' => now(),
            ]);

            // Log do cancelamento
            $this->logs()->create([
                'acao' => 'cancelado',
                'descricao' => $motivo ?? 'Cancelamento manual',
                'data_acao' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function reativar(): bool
    {
        try {
            $this->update([
                'status' => 'Ativo',
                'data_fim' => $this->vitalicio ? null : now()->addMonths($this->pacote?->duracao_meses ?? 1),
            ]);

            // Log da reativação
            $this->logs()->create([
                'acao' => 'reativado',
                'descricao' => 'Assinatura reativada',
                'data_acao' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
