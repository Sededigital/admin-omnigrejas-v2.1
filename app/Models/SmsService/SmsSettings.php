<?php

namespace App\Models\SmsService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Igrejas\Igreja;

class SmsSettings extends Model
{
    use HasFactory;

    protected $table = 'sms_settings';

    protected $fillable = [
        'tipo',
        'user_id',
        'igreja_id',
        'notificacoes_push',
        'notificacoes_email',
        'notificacoes_sms',
        'som_notificacao',
        'vibracao',
        'mostrar_imagens',
        'auto_download_arquivos',
        'tamanho_max_download',
        'mostrar_online',
        'mostrar_digitando',
    ];

    protected $casts = [
        'notificacoes_push' => 'boolean',
        'notificacoes_email' => 'boolean',
        'notificacoes_sms' => 'boolean',
        'som_notificacao' => 'boolean',
        'vibracao' => 'boolean',
        'mostrar_imagens' => 'boolean',
        'auto_download_arquivos' => 'boolean',
        'tamanho_max_download' => 'integer',
        'mostrar_online' => 'boolean',
        'mostrar_digitando' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Tipos de configuração
    const TIPO_USER = 'user';
    const TIPO_IGREJA = 'igreja';
    const TIPO_GLOBAL = 'global';

    // ========================================
    // RELACIONAMENTOS
    // ========================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function igreja(): BelongsTo
    {
        return $this->belongsTo(Igreja::class, 'igreja_id');
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeDoTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeDoUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDaIgreja($query, $igrejaId)
    {
        return $query->where('igreja_id', $igrejaId);
    }

    public function scopeGlobais($query)
    {
        return $query->where('tipo', self::TIPO_GLOBAL);
    }

    // ========================================
    // HELPERS
    // ========================================

    public function ehConfiguracaoUsuario(): bool
    {
        return $this->tipo === self::TIPO_USER;
    }

    public function ehConfiguracaoIgreja(): bool
    {
        return $this->tipo === self::TIPO_IGREJA;
    }

    public function ehConfiguracaoGlobal(): bool
    {
        return $this->tipo === self::TIPO_GLOBAL;
    }

    public function notificacoesHabilitadas(): bool
    {
        return $this->notificacoes_push || $this->notificacoes_email || $this->notificacoes_sms;
    }

    public function podeReceberNotificacaoPush(): bool
    {
        return $this->notificacoes_push;
    }

    public function podeReceberNotificacaoEmail(): bool
    {
        return $this->notificacoes_email;
    }

    public function podeReceberNotificacaoSms(): bool
    {
        return $this->notificacoes_sms;
    }

    public function podeMostrarImagens(): bool
    {
        return $this->mostrar_imagens;
    }

    public function podeAutoDownload(): bool
    {
        return $this->auto_download_arquivos;
    }

    public function podeDownloadArquivo(int $tamanhoBytes): bool
    {
        if (!$this->auto_download_arquivos) {
            return false;
        }

        return $tamanhoBytes <= $this->tamanho_max_download;
    }

    public function deveMostrarOnline(): bool
    {
        return $this->mostrar_online;
    }

    public function deveMostrarDigitando(): bool
    {
        return $this->mostrar_digitando;
    }

    public function getTamanhoMaxDownloadFormatado(): string
    {
        $bytes = $this->tamanho_max_download;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getTipoLabel(): string
    {
        return match($this->tipo) {
            self::TIPO_USER => 'Usuário',
            self::TIPO_IGREJA => 'Igreja',
            self::TIPO_GLOBAL => 'Global',
            default => 'Desconhecido'
        };
    }

    // ========================================
    // MÉTODOS ESTÁTICOS
    // ========================================

    public static function getTipoOptions(): array
    {
        return [
            self::TIPO_USER => 'Configuração do Usuário',
            self::TIPO_IGREJA => 'Configuração da Igreja',
            self::TIPO_GLOBAL => 'Configuração Global',
        ];
    }

    public static function getConfiguracaoUsuario($userId): ?self
    {
        return self::doTipo(self::TIPO_USER)
            ->doUsuario($userId)
            ->first();
    }

    public static function getConfiguracaoIgreja($igrejaId): ?self
    {
        return self::doTipo(self::TIPO_IGREJA)
            ->daIgreja($igrejaId)
            ->first();
    }

    public static function getConfiguracaoGlobal(): ?self
    {
        return self::globais()->first();
    }

    public static function getConfiguracaoPadrao(): array
    {
        return [
            'notificacoes_push' => true,
            'notificacoes_email' => true,
            'notificacoes_sms' => false,
            'som_notificacao' => true,
            'vibracao' => true,
            'mostrar_imagens' => true,
            'auto_download_arquivos' => false,
            'tamanho_max_download' => 10485760, // 10MB
            'mostrar_online' => true,
            'mostrar_digitando' => true,
        ];
    }

    public static function criarConfiguracaoUsuario($userId, array $dados = []): self
    {
        $dadosPadrao = self::getConfiguracaoPadrao();
        $dados = array_merge($dadosPadrao, $dados);

        return self::create(array_merge($dados, [
            'tipo' => self::TIPO_USER,
            'user_id' => $userId,
        ]));
    }

    public static function criarConfiguracaoIgreja($igrejaId, array $dados = []): self
    {
        $dadosPadrao = self::getConfiguracaoPadrao();
        $dados = array_merge($dadosPadrao, $dados);

        return self::create(array_merge($dados, [
            'tipo' => self::TIPO_IGREJA,
            'igreja_id' => $igrejaId,
        ]));
    }

    public static function criarConfiguracaoGlobal(array $dados = []): self
    {
        $dadosPadrao = self::getConfiguracaoPadrao();
        $dados = array_merge($dadosPadrao, $dados);

        return self::create(array_merge($dados, [
            'tipo' => self::TIPO_GLOBAL,
        ]));
    }

    public static function obterConfiguracaoUsuario($userId): array
    {
        $configuracao = self::getConfiguracaoUsuario($userId);

        if ($configuracao) {
            return $configuracao->toArray();
        }

        // Retornar configuração padrão se não existir
        return self::getConfiguracaoPadrao();
    }

    public static function obterConfiguracaoCompleta($userId, $igrejaId = null): array
    {
        // Prioridade: Usuário > Igreja > Global > Padrão

        $configuracaoUsuario = self::getConfiguracaoUsuario($userId);
        $configuracaoIgreja = $igrejaId ? self::getConfiguracaoIgreja($igrejaId) : null;
        $configuracaoGlobal = self::getConfiguracaoGlobal();

        $configuracaoFinal = self::getConfiguracaoPadrao();

        // Aplicar configurações na ordem de prioridade
        if ($configuracaoGlobal) {
            $configuracaoFinal = array_merge($configuracaoFinal, $configuracaoGlobal->toArray());
        }

        if ($configuracaoIgreja) {
            $configuracaoFinal = array_merge($configuracaoFinal, $configuracaoIgreja->toArray());
        }

        if ($configuracaoUsuario) {
            $configuracaoFinal = array_merge($configuracaoFinal, $configuracaoUsuario->toArray());
        }

        return $configuracaoFinal;
    }
}