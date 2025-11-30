<?php

namespace App\Models\SmsService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsAttachment extends Model
{
    use HasFactory;

    protected $table = 'sms_attachments';

    protected $fillable = [
        'message_id',
        'nome_original',
        'nome_arquivo',
        'caminho_completo',
        'tamanho_bytes',
        'tipo_mime',
        'extensao',
        'largura',
        'altura',
        'duracao_segundos',
        'codec',
        'bitrate',
        'hash_sha256',
        'processado',
        'processado_em',
        'erro_processamento',
    ];

    protected $casts = [
        'tamanho_bytes' => 'integer',
        'largura' => 'integer',
        'altura' => 'integer',
        'duracao_segundos' => 'integer',
        'bitrate' => 'integer',
        'processado' => 'boolean',
        'processado_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function message(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class, 'message_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeDaMensagem($query, $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    public function scopeProcessados($query)
    {
        return $query->where('processado', true);
    }

    public function scopeNaoProcessados($query)
    {
        return $query->where('processado', false);
    }

    public function scopeComErro($query)
    {
        return $query->whereNotNull('erro_processamento');
    }

    public function scopeDoTipo($query, $tipoMime)
    {
        return $query->where('tipo_mime', 'LIKE', $tipoMime . '%');
    }

    public function scopeImagens($query)
    {
        return $query->where('tipo_mime', 'LIKE', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('tipo_mime', 'LIKE', 'video/%');
    }

    public function scopeAudios($query)
    {
        return $query->where('tipo_mime', 'LIKE', 'audio/%');
    }

    public function scopeDocumentos($query)
    {
        return $query->whereIn('tipo_mime', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ]);
    }

    public function scopeGrandes($query, $tamanhoMinimo = 10485760) // 10MB
    {
        return $query->where('tamanho_bytes', '>=', $tamanhoMinimo);
    }

    // ========================================
    // HELPERS
    // ========================================

    public function foiProcessado(): bool
    {
        return $this->processado;
    }

    public function temErro(): bool
    {
        return !is_null($this->erro_processamento);
    }

    public function ehImagem(): bool
    {
        return str_starts_with($this->tipo_mime, 'image/');
    }

    public function ehVideo(): bool
    {
        return str_starts_with($this->tipo_mime, 'video/');
    }

    public function ehAudio(): bool
    {
        return str_starts_with($this->tipo_mime, 'audio/');
    }

    public function ehDocumento(): bool
    {
        return in_array($this->tipo_mime, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
        ]);
    }

    public function getTamanhoFormatado(): string
    {
        $bytes = $this->tamanho_bytes;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDimensoesFormatadas(): string
    {
        if (!$this->largura || !$this->altura) {
            return '';
        }

        return $this->largura . 'x' . $this->altura;
    }

    public function getDuracaoFormatada(): string
    {
        if (!$this->duracao_segundos) {
            return '';
        }

        $horas = floor($this->duracao_segundos / 3600);
        $minutos = floor(($this->duracao_segundos % 3600) / 60);
        $segundos = $this->duracao_segundos % 60;

        if ($horas > 0) {
            return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
        }

        return sprintf('%02d:%02d', $minutos, $segundos);
    }

    public function getTipoLabel(): string
    {
        if ($this->ehImagem()) {
            return 'Imagem';
        } elseif ($this->ehVideo()) {
            return 'Vídeo';
        } elseif ($this->ehAudio()) {
            return 'Áudio';
        } elseif ($this->ehDocumento()) {
            return 'Documento';
        } else {
            return 'Arquivo';
        }
    }

    public function getUrlCompleta(): string
    {
        // Retornar URL completa para acesso via Supabase
        return $this->caminho_completo;
    }

    public function marcarComoProcessado(): void
    {
        $this->update([
            'processado' => true,
            'processado_em' => now(),
            'erro_processamento' => null,
        ]);
    }

    public function marcarErroProcessamento(string $erro): void
    {
        $this->update([
            'processado' => false,
            'erro_processamento' => $erro,
        ]);
    }

    public function podeSerVisualizado(): bool
    {
        return $this->ehImagem() || $this->ehVideo() || $this->ehAudio();
    }

    public function podeSerBaixado(): bool
    {
        return true; // Todos os arquivos podem ser baixados
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function getEstatisticasPorTipo(): \Illuminate\Support\Collection
    {
        return self::selectRaw('
                CASE
                    WHEN tipo_mime LIKE "image/%" THEN "Imagem"
                    WHEN tipo_mime LIKE "video/%" THEN "Vídeo"
                    WHEN tipo_mime LIKE "audio/%" THEN "Áudio"
                    WHEN tipo_mime IN ("application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "text/plain") THEN "Documento"
                    ELSE "Outro"
                END as tipo,
                COUNT(*) as quantidade,
                SUM(tamanho_bytes) as tamanho_total
            ')
            ->groupBy('tipo')
            ->get();
    }

    public static function getTotalEspacoUsado(): int
    {
        return self::sum('tamanho_bytes');
    }

    public static function getAnexosMaisRecentes($limite = 10): \Illuminate\Support\Collection
    {
        return self::with('message.conversation')
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }
}