<?php

namespace App\Models\Chats;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IgrejaChatMensagem extends Model
{
    use HasFactory;

    protected $table = 'igreja_chat_mensagens';
    protected $primaryKey = 'id';
    public $incrementing = false; // UUID
    protected $keyType = 'string';

    protected $fillable = [
        'chat_id',
        'autor_id',
        'tipo_mensagem',
        'conteudo',
        'anexo_url',
        'anexo_nome',
        'anexo_tamanho',
        'anexo_tipo',
        'duracao_audio',
        'latitude',
        'longitude',
        'lida_por',
    ];

    protected $casts = [
        'lida_por' => 'array',
        'anexo_tamanho' => 'integer',
        'duracao_audio' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // 🔗 RELACIONAMENTOS
    public function chat(): BelongsTo
    {
        return $this->belongsTo(IgrejaChat::class, 'chat_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    // ========================================
    // HELPERS PARA TIPOS DE MENSAGEM
    // ========================================

    public function isTexto(): bool
    {
        return $this->tipo_mensagem === 'texto';
    }

    public function isImagem(): bool
    {
        return $this->tipo_mensagem === 'imagem';
    }

    public function isAudio(): bool
    {
        return $this->tipo_mensagem === 'audio';
    }

    public function isVideo(): bool
    {
        return $this->tipo_mensagem === 'video';
    }

    public function isArquivo(): bool
    {
        return $this->tipo_mensagem === 'arquivo';
    }

    public function isLocalizacao(): bool
    {
        return $this->tipo_mensagem === 'localizacao';
    }

    public function hasAnexo(): bool
    {
        return !empty($this->anexo_url);
    }

    public function getAnexoUrl(): ?string
    {
        if (!$this->hasAnexo()) {
            return null;
        }

        // Se já for uma URL completa, retorna como está
        if (filter_var($this->anexo_url, FILTER_VALIDATE_URL)) {
            return $this->anexo_url;
        }

        // Caso contrário, assume que é um path no Supabase
        return \Illuminate\Support\Facades\Storage::disk('supabase')->url($this->anexo_url);
    }

    public function getTamanhoFormatado(): string
    {
        if (!$this->anexo_tamanho) {
            return '';
        }

        $bytes = $this->anexo_tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }

    public function getDuracaoFormatada(): string
    {
        if (!$this->duracao_audio) {
            return '';
        }

        $seconds = $this->duracao_audio;
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    public function getConteudoPreview(): string
    {
        if ($this->isTexto()) {
            return $this->conteudo ?? '';
        }

        switch ($this->tipo_mensagem) {
            case 'imagem':
                return '📷 Imagem';
            case 'audio':
                return '🎵 Áudio' . ($this->duracao_audio ? ' (' . $this->getDuracaoFormatada() . ')' : '');
            case 'video':
                return '🎥 Vídeo';
            case 'arquivo':
                return '📎 ' . ($this->anexo_nome ?? 'Arquivo');
            case 'localizacao':
                return '📍 Localização';
            default:
                return 'Mensagem';
        }
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_mensagem', $tipo);
    }

    public function scopeComAnexo($query)
    {
        return $query->whereNotNull('anexo_url');
    }

    public function scopeSemAnexo($query)
    {
        return $query->whereNull('anexo_url');
    }
}
