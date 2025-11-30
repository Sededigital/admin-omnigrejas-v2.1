<?php

namespace App\Models\Eventos;

use App\Models\User;
use App\Models\Eventos\Agenda;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Agendamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agendamentos';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'titulo',
        'descricao',
        'tipo',
        'data_agendamento',
        'hora_inicio',
        'hora_fim',
        'local',
        'modalidade',
        'link_reuniao',
        'status',
        'organizador_id',
        'responsavel_id',
        'convidado_id',
        'igreja_id',
        'alianca_id',
        'observacoes',
        'lembretes',
        'data_confirmacao',
        'motivo_cancelamento',
    ];

    protected $casts = [
        'data_agendamento' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
        'lembretes' => 'array',
        'data_confirmacao' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function organizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function convidado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'convidado_id');
    }

    // Novos relacionamentos para alianças e igrejas
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Igrejas\Igreja::class, 'igreja_id');
    }

    public function alianca(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Igrejas\AliancaIgreja::class, 'alianca_id');
    }

    public function lembretes(): HasMany
    {
        return $this->hasMany(Agenda::class, 'agendamento_id');
    }

    // 🔗 SCOPES
    public function scopeAgendados($query)
    {
        return $query->where('status', 'agendado');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('status', 'confirmado');
    }

    public function scopeRealizados($query)
    {
        return $query->where('status', 'realizado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('status', 'cancelado');
    }

    public function scopeDoOrganizador($query, $userId)
    {
        return $query->where('organizador_id', $userId);
    }

    public function scopeDoConvidado($query, $userId)
    {
        return $query->where('convidado_id', $userId);
    }

    public function scopeFuturos($query)
    {
        return $query->where('data_agendamento', '>=', now()->toDateString());
    }

    public function scopePassados($query)
    {
        return $query->where('data_agendamento', '<', now()->toDateString());
    }

    // Novos scopes para alianças e igrejas
    public function scopeDaIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeDaAlianca($query, $aliancaId)
    {
        return $query->where('alianca_id', $aliancaId);
    }

    public function scopeDaIgrejaOuAlianca($query, $igrejaId, $aliancaId = null)
    {
        return $query->where(function($q) use ($igrejaId, $aliancaId) {
            $q->where('igreja_id', $igrejaId);
            if ($aliancaId) {
                $q->orWhere('alianca_id', $aliancaId);
            }
        });
    }

    public function scopeSemIgrejaOuAlianca($query)
    {
        return $query->whereNull('igreja_id')->whereNull('alianca_id');
    }

    public function scopeComIgrejaOuAlianca($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('igreja_id')->orWhereNotNull('alianca_id');
        });
    }

    // 🔗 HELPERS
    public function isAgendado(): bool
    {
        return $this->status === 'agendado';
    }

    public function isConfirmado(): bool
    {
        return $this->status === 'confirmado';
    }

    public function isRealizado(): bool
    {
        return $this->status === 'realizado';
    }

    public function isCancelado(): bool
    {
        return $this->status === 'cancelado';
    }

    public function isOnline(): bool
    {
        return $this->modalidade === 'online';
    }

    public function isPresencial(): bool
    {
        return $this->modalidade === 'presencial';
    }

    public function isHibrido(): bool
    {
        return $this->modalidade === 'hibrido';
    }

    // Novos helpers para alianças e igrejas
    public function temIgreja(): bool
    {
        return !is_null($this->igreja_id);
    }

    public function temAlianca(): bool
    {
        return !is_null($this->alianca_id);
    }

    public function isDaIgreja($igrejaId): bool
    {
        return $this->igreja_id === $igrejaId;
    }

    public function isDaAlianca($aliancaId): bool
    {
        return $this->alianca_id === $aliancaId;
    }

    public function getContextoAttribute(): string
    {
        if ($this->temAlianca()) {
            return 'Aliança: ' . ($this->alianca->nome ?? 'N/A');
        } elseif ($this->temIgreja()) {
            return 'Igreja: ' . ($this->igreja->nome ?? 'N/A');
        }
        return 'Geral';
    }

    public function getDuracaoAttribute(): string
    {
        if (!$this->hora_fim) {
            return 'Duração não definida';
        }

        $inicio = strtotime($this->hora_inicio);
        $fim = strtotime($this->hora_fim);

        $diferenca = $fim - $inicio;
        $horas = floor($diferenca / 3600);
        $minutos = floor(($diferenca % 3600) / 60);

        if ($horas > 0) {
            return $horas . 'h' . ($minutos > 0 ? ' ' . $minutos . 'min' : '');
        }

        return $minutos . ' minutos';
    }

    public function getDataHoraFormatadaAttribute(): string
    {
        return $this->data_agendamento->format('d/m/Y') . ' às ' . $this->hora_inicio->format('H:i');
    }

    // ========================================
    // MÉTODOS PARA ATUALIZAÇÃO DE STATUS
    // ========================================

    /**
     * Atualiza automaticamente o status dos agendamentos baseado na data/hora atual
     *
     * @return int Número de registros atualizados
     */
    public static function atualizarStatusAutomaticamente(): int
    {
        $agora = now();

        $atualizados = self::whereIn('status', ['agendado', 'confirmado'])
            ->where(function($query) use ($agora) {
                $query->where(function($q) use ($agora) {
                    // Se tem hora_fim, usar ela como referência
                    $q->whereNotNull('hora_fim')
                      ->whereRaw("(data_agendamento + hora_fim::interval) < ?", [$agora]);
                })->orWhere(function($q) use ($agora) {
                    // Se não tem hora_fim, assumir 2 horas após o início
                    $q->whereNull('hora_fim')
                      ->whereRaw("(data_agendamento + hora_inicio::interval + interval '2 hours') < ?", [$agora]);
                });
            })
            ->update([
                'status' => 'realizado',
                'updated_at' => $agora
            ]);

        return $atualizados;
    }

    /**
     * Verifica se o agendamento já deveria ter terminado
     *
     * @return bool
     */
    public function jaTerminou(): bool
    {
        $agora = now();

        if ($this->hora_fim) {
            // Se tem hora_fim, usar ela como referência
            $dataHoraFim = $this->data_agendamento->format('Y-m-d') . ' ' . $this->hora_fim->format('H:i:s');
            return $agora->gt(\Carbon\Carbon::parse($dataHoraFim));
        } else {
            // Se não tem hora_fim, assumir 2 horas após o início
            $dataHoraInicio = $this->data_agendamento->format('Y-m-d') . ' ' . $this->hora_inicio->format('H:i:s');
            $dataHoraFimEstimada = \Carbon\Carbon::parse($dataHoraInicio)->addHours(2);
            return $agora->gt($dataHoraFimEstimada);
        }
    }

    /**
     * Verifica se o agendamento está em andamento
     *
     * @return bool
     */
    public function estaEmAndamento(): bool
    {
        $agora = now();

        $dataHoraInicio = $this->data_agendamento->format('Y-m-d') . ' ' . $this->hora_inicio->format('H:i:s');
        $inicioCarbon = \Carbon\Carbon::parse($dataHoraInicio);

        if ($this->hora_fim) {
            $dataHoraFim = $this->data_agendamento->format('Y-m-d') . ' ' . $this->hora_fim->format('H:i:s');
            $fimCarbon = \Carbon\Carbon::parse($dataHoraFim);
            return $agora->gte($inicioCarbon) && $agora->lte($fimCarbon);
        } else {
            // Se não tem hora_fim, assumir 2 horas de duração
            $fimEstimado = $inicioCarbon->copy()->addHours(2);
            return $agora->gte($inicioCarbon) && $agora->lte($fimEstimado);
        }
    }

    /**
     * Atualiza o status deste agendamento específico se necessário
     *
     * @return bool True se foi atualizado, false caso contrário
     */
    public function atualizarStatusSeNecessario(): bool
    {
        if (in_array($this->status, ['agendado', 'confirmado']) && $this->jaTerminou()) {
            $this->update([
                'status' => 'realizado',
                'updated_at' => now()
            ]);
            return true;
        }

        return false;
    }

    /**
     * Scope para agendamentos que deveriam estar realizados
     */
    public function scopeDeveriamEstarRealizados($query)
    {
        $agora = now();

        return $query->whereIn('status', ['agendado', 'confirmado'])
            ->where(function($q) use ($agora) {
                $q->where(function($subQ) use ($agora) {
                    // Se tem hora_fim, usar ela como referência
                    $subQ->whereNotNull('hora_fim')
                         ->whereRaw("(data_agendamento + hora_fim::interval) < ?", [$agora]);
                })->orWhere(function($subQ) use ($agora) {
                    // Se não tem hora_fim, assumir 2 horas após o início
                    $subQ->whereNull('hora_fim')
                         ->whereRaw("(data_agendamento + hora_inicio::interval + interval '2 hours') < ?", [$agora]);
                });
            });
    }

    /**
     * Scope para agendamentos em andamento
     */
    public function scopeEmAndamento($query)
    {
        $agora = now();

        return $query->whereIn('status', ['agendado', 'confirmado'])
            ->where(function($q) use ($agora) {
                $q->where(function($subQ) use ($agora) {
                    // Se tem hora_fim, verificar se está entre início e fim
                    $subQ->whereNotNull('hora_fim')
                         ->whereRaw("(data_agendamento + hora_inicio::interval) <= ?", [$agora])
                         ->whereRaw("(data_agendamento + hora_fim::interval) >= ?", [$agora]);
                })->orWhere(function($subQ) use ($agora) {
                    // Se não tem hora_fim, assumir 2 horas de duração
                    $subQ->whereNull('hora_fim')
                         ->whereRaw("(data_agendamento + hora_inicio::interval) <= ?", [$agora])
                         ->whereRaw("(data_agendamento + hora_inicio::interval + interval '2 hours') >= ?", [$agora]);
                });
            });
    }
}
