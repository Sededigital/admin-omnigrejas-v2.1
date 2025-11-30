<?php

namespace App\Models;

use App\Models\Igrejas\AliancaIgreja;
use App\Models\Igrejas\IgrejaMembro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AliancaMensagem extends Model
{
    use HasFactory;

    protected $table = 'alianca_mensagens';

    protected $fillable = [
        'uuid',
        'alianca_id',
        'remetente_id',
        'tipo_mensagem',
        'mensagem',
        'anexos',
        'lida_em',
    ];

    protected $casts = [
        'anexos' => 'array',
        'lida_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // 🔗 RELACIONAMENTOS
    public function alianca(): BelongsTo
    {
        return $this->belongsTo(AliancaIgreja::class, 'alianca_id');
    }

    public function remetente(): BelongsTo
    {
        return $this->belongsTo(IgrejaMembro::class, 'remetente_id');
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(AliancaMensagemLeitura::class, 'mensagem_id');
    }

    // 🔗 HELPERS
    public function foiLidaPor(IgrejaMembro $membro): bool
    {
        return $this->leituras()->where('membro_id', $membro->id)->exists();
    }

    public function marcarComoLida(IgrejaMembro $membro): void
    {
        if (!$this->foiLidaPor($membro)) {
            $this->leituras()->create([
                'membro_id' => $membro->id,
                'lida_em' => now(),
            ]);
        }
    }

    public function getTotalLeituras(): int
    {
        return $this->leituras()->count();
    }

    public function getMensagemResumida($limite = 100): string
    {
        return strlen($this->mensagem) > $limite
            ? substr($this->mensagem, 0, $limite) . '...'
            : $this->mensagem;
    }

    public function isDoUsuarioAtual(): bool
    {
        // Este método deve ser usado no contexto do componente Livewire
        // onde o usuário atual estará disponível
        return false; // Será implementado no componente
    }

    public function temAnexos(): bool
    {
        return !empty($this->anexos) && is_array($this->anexos);
    }

    public function getTipoMensagem(): string
    {
        // Se já tem tipo definido, retorna ele
        if (!empty($this->tipo_mensagem)) {
            return $this->tipo_mensagem;
        }

        // Determina baseado nos anexos
        if ($this->temAnexos()) {
            $primeiroAnexo = $this->anexos[0];
            if (isset($primeiroAnexo['tipo_arquivo'])) {
                return $primeiroAnexo['tipo_arquivo'];
            }

            // Fallback baseado no tipo MIME
            if (isset($primeiroAnexo['tipo'])) {
                $tipo = $primeiroAnexo['tipo'];
                if (str_contains($tipo, 'image/')) {
                    return 'imagem';
                } elseif (str_contains($tipo, 'audio/')) {
                    return 'audio';
                } elseif (str_contains($tipo, 'video/')) {
                    return 'video';
                } else {
                    return 'arquivo';
                }
            }
        }

        return 'texto';
    }

    // 🔗 SCOPES
    public function scopeDaAlianca($query, $aliancaId)
    {
        return $query->where('alianca_id', $aliancaId);
    }

    public function scopeNaoLidasPor($query, $membroId)
    {
        return $query->whereDoesntHave('leituras', function($q) use ($membroId) {
            $q->where('membro_id', $membroId);
        });
    }

    public function scopeRecentes($query, $limite = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limite);
    }
}
