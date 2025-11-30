<?php

namespace App\Models\SmsService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class SmsMessageRead extends Model
{
    use HasFactory;

    protected $table = 'ge';

    protected $fillable = [
        'message_id',
        'user_id',
        'lida_em',
    ];

    protected $casts = [
        'lida_em' => 'datetime',
        'created_at' => 'datetime',
    ];

    // A tabela sms_message_reads não tem updated_at
    public $timestamps = false;

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function message(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeDaMensagem($query, $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecentes($query, $minutos = 60)
    {
        return $query->where('lida_em', '>=', now()->subMinutes($minutos));
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('lida_em', today());
    }

    // ========================================
    // HELPERS
    // ========================================

    public function foiLidaRecentemente(): bool
    {
        return $this->lida_em->diffInMinutes(now()) < 5;
    }

    public function getTempoDecorrido(): string
    {
        return $this->lida_em->diffForHumans();
    }

    // ========================================
    // BOOT METHOD
    // ========================================

    protected static function boot()
    {
        parent::boot();

        // Definir lida_em automaticamente se não fornecida
        static::creating(function ($model) {
            if (!$model->lida_em) {
                $model->lida_em = now();
            }
        });
    }
}
