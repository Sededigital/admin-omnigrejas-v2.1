<?php

namespace App\Models\Admin;

use App\Models\Igrejas\Igreja;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RelatorioCache extends Model
{
    use HasFactory;

    protected $table = 'relatorios_cache';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'tipo',
        'igreja_id',
        'periodo',
        'dados',
    ];

    protected $casts = [
        'dados' => 'array',
    ];

    // 🔗 RELACIONAMENTOS
    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }
}
