<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SupabaseHelper
{
    /**
     * Gera o caminho base padronizado para uploads da igreja
     * Formato: IGREJAS/{NOME_IGREJA}/
     */
    public static function gerarCaminhoBaseIgreja(): string
    {
        $user = Auth::user();
        $igreja = $user->getIgreja();

        if (!$igreja) {
            throw new \Exception('Usuário não está associado a uma igreja');
        }

        // Padronizar nome da igreja: maiúsculo, sem acentos, underscores
        $nomeIgreja = self::padronizarNomeIgreja($igreja->nome);

        return "IGREJAS/{$nomeIgreja}/";
    }

    /**
     * Gera caminho completo para um tipo específico
     * Ex: IGREJAS/NOME_IGREJA/profile/ ou IGREJAS/NOME_IGREJA/chat/
     */
    public static function gerarCaminhoTipo(string $tipo): string
    {
        return self::gerarCaminhoBaseIgreja() . trim($tipo, '/') . '/';
    }

    /**
     * Faz upload de arquivo para o Supabase com caminho padronizado
     */
    public static function fazerUpload($arquivo, string $tipo, ?string $nomeArquivo = null): string
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Gerar caminho base
        $caminhoBase = self::gerarCaminhoTipo($tipo);

        // Gerar nome único se não fornecido
        if (!$nomeArquivo) {
            $extensao = $arquivo->getClientOriginalExtension();
            $nomeArquivo = $tipo . '_' . time() . '_' . uniqid() . '.' . $extensao;
        }

        // Fazer upload
        $caminhoCompleto = $caminhoBase . $nomeArquivo;
        $path = Storage::disk('supabase')->putFileAs($caminhoBase, $arquivo, $nomeArquivo);

        if (!$path) {
            throw new \Exception('Falha ao fazer upload para o Supabase');
        }

        return $path;
    }

    /**
     * Remove arquivo do Supabase
     */
    public static function removerArquivo(string $caminhoArquivo): bool
    {
        if (!$caminhoArquivo) {
            return true; // Nada para remover
        }

        try {
            return Storage::disk('supabase')->delete($caminhoArquivo);
        } catch (\Exception $e) {
            // Log silencioso, não interromper fluxo
            // Log::warning('Erro ao remover arquivo do Supabase', [
            //     'caminho' => $caminhoArquivo,
            //     'erro' => $e->getMessage()
            // ]);
            return false;
        }
    }

    /**
     * Obtém URL pública do arquivo no Supabase
     */
    public static function obterUrl(string $caminhoArquivo, ?string $disk = null): string
    {
        if (!$caminhoArquivo) {
            return '';
        }

        // Mantém compatibilidade total
        $disk = $disk ?? 'supabase';

        return Storage::disk($disk)->url($caminhoArquivo);
    }

    public static function supabaseAtivo(): bool
    {
        try {
            // Tenta listar algo simples no bucket como teste de conexão
            Storage::disk('supabase')->exists('');
            return true;
        } catch (\Exception $e) {
            Log::warning('Supabase indisponível', ['erro' => $e->getMessage()]);
            return false;
        }
    }

    private static function uploadComFallback(string $caminho, $content): array
    {
        if (self::supabaseAtivo()) {
            try {
                Storage::disk('supabase')->put($caminho, $content);

                // Verifica se realmente foi salvo
                if (Storage::disk('supabase')->exists($caminho)) {
                    return [
                        'disk' => 'supabase',
                        'caminho' => $caminho
                    ];
                }

                throw new \Exception('Arquivo não apareceu no Supabase');
            } catch (\Throwable $e) {
                Log::warning('Falha no upload Supabase, salvando local', [
                    'erro' => $e->getMessage(),
                    'caminho' => $caminho
                ]);
            }
        } else {
            Log::info('Supabase offline, salvando local', ['caminho' => $caminho]);
        }

        // Fallback offline
        Storage::disk('offline')->put($caminho, $content);

        return [
            'disk' => 'offline',
            'caminho' => $caminho
        ];
    }



    /**
     * Verifica se arquivo existe no Supabase
     */
    public static function arquivoExiste(string $caminhoArquivo): bool
    {
        if (!$caminhoArquivo) {
            return false;
        }

        return Storage::disk('supabase')->exists($caminhoArquivo);
    }

    /**
     * Lista arquivos em um diretório
     */
    public static function listarArquivos(string $diretorio): array
    {
        try {
            return Storage::disk('supabase')->files($diretorio);
        } catch (\Exception $e) {
            // Log::error('Erro ao listar arquivos', [
            //     'diretorio' => $diretorio,
            //     'erro' => $e->getMessage()
            // ]);
            return [];
        }
    }

    /**
     * Padroniza nome da igreja para uso em caminhos
     */
    private static function padronizarNomeIgreja(string $nome): string
    {
        // Converter para maiúsculo
        $nome = strtoupper($nome);

        // Remover acentos
        $nome = self::removerAcentos($nome);

        // Substituir espaços e caracteres especiais por underscore
        $nome = preg_replace('/[^A-Z0-9]/', '_', $nome);

        // Remover underscores múltiplos
        $nome = preg_replace('/_+/', '_', $nome);

        // Remover underscores do início e fim
        $nome = trim($nome, '_');

        return $nome;
    }

    /**
     * Remove acentos de uma string
     */
    private static function removerAcentos(string $string): string
    {
        return strtr($string, [
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
            'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ý' => 'Y',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y'
        ]);
    }

    /**
     * Gera nome único para arquivo
     */
    public static function gerarNomeUnico(string $prefixo = '', string $extensao = ''): string
    {
        $timestamp = time();
        $uniqid = uniqid();
        $random = Str::random(4);

        $nome = $prefixo;
        if ($nome) $nome .= '_';
        $nome .= $timestamp . '_' . $uniqid . '_' . $random;
        if ($extensao) $nome .= '.' . $extensao;

        return $nome;
    }

    /**
     * Valida tipo de arquivo
     */
    public static function validarTipoArquivo($arquivo, array $tiposPermitidos = []): bool
    {
        if (!$arquivo) return false;

        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $mimeType = $arquivo->getMimeType();

        // Se não especificou tipos, aceitar tudo
        if (empty($tiposPermitidos)) return true;

        // Verificar extensão
        if (in_array($extensao, $tiposPermitidos)) return true;

        // Verificar MIME type
        return in_array($mimeType, $tiposPermitidos);
    }

    /**
     * Valida tamanho do arquivo
     */
    public static function validarTamanhoArquivo($arquivo, int $tamanhoMaximoKB = 2048): bool
    {
        if (!$arquivo) return false;

        $tamanhoBytes = $arquivo->getSize();
        $tamanhoKB = $tamanhoBytes / 1024;

        return $tamanhoKB <= $tamanhoMaximoKB;
    }

    /**
     * Limpa nome do chat para uso em caminhos (remove acentos, espaços, etc.)
     */
    public static function limparNomeChat(string $nome): string
    {
        // Substituir caracteres acentuados
        $acentos = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N'
        ];

        $nome = str_replace(array_keys($acentos), array_values($acentos), $nome);

        // Substituir espaços por underscores
        $nome = str_replace(' ', '_', $nome);

        // Remover caracteres especiais restantes
        $nome = preg_replace('/[^A-Za-z0-9\-_]/', '', $nome);

        // Remove underscores múltiplos consecutivos
        $nome = preg_replace('/_+/', '_', $nome);

        // Remove underscores no início e fim
        $nome = trim($nome, '_');

        return $nome;
    }

    /**
     * Gera caminho completo para uploads de chat
     * Formato: IGREJAS/{NOME_IGREJA}/chat/{tipo}_chat/{nome_chat_limpo}/
     */
    public static function gerarCaminhoChat($chatId, string $tipo): string
    {
        // Obter chat
        $chat = \App\Models\Chats\IgrejaChat::find($chatId);
        if (!$chat) {
            throw new \Exception('Chat não encontrado');
        }

        // Limpar nome do chat
        $nomeChatLimpo = self::limparNomeChat($chat->nome);

        // Mapear tipos
        $tipoMapeado = match($tipo) {
            'audio' => 'audio_chat',
            'arquivo', 'ficheiro' => 'ficheiro_chat',
            default => $tipo . '_chat'
        };

        // Caminho base da igreja
        $caminhoBase = self::gerarCaminhoBaseIgreja();

        return $caminhoBase . "chat/{$tipoMapeado}/{$nomeChatLimpo}/";
    }

    /**
     * Faz upload de arquivo para chat no Supabase
     */
    public static function fazerUploadChat($arquivo, $chatId, string $tipo): string
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Gerar caminho para o chat
        $caminhoBase = self::gerarCaminhoChat($chatId, $tipo);

        // Fazer upload
        $path = $arquivo->store($caminhoBase, 'supabase');

        if (!$path) {
            throw new \Exception('Falha ao fazer upload para o Supabase');
        }

        return $path;
    }


    /**
     * Gera caminho completo para uploads de mensagens privadas
     * Formato: IGREJAS/{NOME_IGREJA}/chat/private/{userId1}_{userId2}/{tipo}/
     * Os IDs são ordenados para garantir consistência
     */
    public static function gerarCaminhoMensagemPrivada($userId1, $userId2, string $tipo): string
    {
        // Ordenar IDs para consistência
        $ids = [$userId1, $userId2];
        sort($ids);
        $conversaId = implode('_', $ids);

        // Mapear tipos
        $tipoMapeado = match($tipo) {
            'audio' => 'audio',
            'arquivo', 'ficheiro' => 'ficheiro',
            default => $tipo
        };

        // Caminho base da igreja
        $caminhoBase = self::gerarCaminhoBaseIgreja();

        return $caminhoBase . "chat/private/{$conversaId}/{$tipoMapeado}/";
    }

    /**
     * Faz upload de arquivo para mensagem privada no Supabase
     */
    public static function fazerUploadMensagemPrivada($arquivo, $userId1, $userId2, string $tipo): string
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Gerar caminho para a conversa privada
        $caminhoBase = self::gerarCaminhoMensagemPrivada($userId1, $userId2, $tipo);

        // Fazer upload
        $path = $arquivo->store($caminhoBase, 'supabase');

        if (!$path) {
            throw new \Exception('Falha ao fazer upload para o Supabase');
        }

        return $path;
    }

    /**
     * Gera caminho completo para uploads de posts
     * Formato: IGREJAS/{NOME_IGREJA}/Posts/
     */
    public static function gerarCaminhoPosts(): string
    {
        $caminhoBase = self::gerarCaminhoBaseIgreja();
        return $caminhoBase . 'Posts/';
    }

    /**
     * Faz upload de mídia para posts no Supabase
     */
    public static function fazerUploadPost($arquivo): array
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Gerar caminho para posts
        $caminhoBase = self::gerarCaminhoPosts();

        // Gerar nome único
        $extensao = $arquivo->getClientOriginalExtension();
        $nomeArquivo = 'post_' . time() . '_' . uniqid() . '.' . $extensao;

        // Fazer upload
        $caminhoCompleto = $caminhoBase . $nomeArquivo;
        $path = Storage::disk('supabase')->put($caminhoCompleto, $arquivo->get());

        if (!$path) {
            throw new \Exception('Falha ao fazer upload para o Supabase');
        }

        // Determinar tipo de mídia
        $mimeType = $arquivo->getMimeType();
        $mediaType = 'file';

        if (Str::startsWith($mimeType, 'image/')) {
            $mediaType = 'image';
        } elseif (Str::startsWith($mimeType, 'video/')) {
            $mediaType = 'video';
        } elseif (Str::startsWith($mimeType, 'audio/')) {
            $mediaType = 'audio';
        }

        return [
            'url' => $caminhoCompleto,
            'nome' => $arquivo->getClientOriginalName(),
            'tamanho' => $arquivo->getSize(),
            'mime_type' => $mimeType,
            'tipo' => $mediaType,
            'is_video' => $mediaType === 'video'
        ];
    }

    /**
     * Faz upload de arquivo de perfil (foto ou logo) para o Supabase
     */
    public static function fazerUploadPerfil($arquivo, string $tipo = 'profile'): string
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        $user = Auth::user();
        if (!$user) {
            throw new \Exception('Usuário não autenticado');
        }

        // Determinar nome da pasta baseado no tipo de usuário
        if ($user->isSuperAdmin()) {
            $nomePasta = 'profile';
        } elseif ($user->isIgrejaAdmin() && $igreja = $user->getIgreja()) {
            $nomePasta = strtoupper(str_replace(' ', '_', $igreja->nome));
        } else {
            $nomePasta = 'user_' . $user->id;
        }

        // Gerar nome único para o arquivo
        $extensao = $arquivo->getClientOriginalExtension();
        $nomeArquivo = $tipo . '_' . time() . '_' . uniqid() . '.' . $extensao;

        // Lê o conteúdo do arquivo diretamente
        $content = $arquivo->get();

        // Caminho completo
        $caminhoCompleto = "IGREJAS/{$nomePasta}/{$tipo}/{$nomeArquivo}";

        // Faz upload para o Supabase
        $path = Storage::disk('supabase')->put($caminhoCompleto, $content);

        if (!$path) {
            throw new \Exception('Falha ao fazer upload para o Supabase');
        }

        return $caminhoCompleto;
    }

    /**
     * Limpa arquivos temporários do Supabase que têm mais de X minutos
     */
    public static function limparArquivosTemporarios(int $minutos = 1): int
    {
        // Log::info('Iniciando limpeza de arquivos temporários', [
        //     'diretorio' => 'livewire-tmp',
        //     'minutos' => $minutos
        // ]);

        try {
            $diretorio = 'livewire-tmp';
            $arquivos = self::listarArquivos($diretorio);

            // Log::info('Arquivos encontrados no diretório', [
            //     'diretorio' => $diretorio,
            //     'quantidade' => count($arquivos),
            //     'arquivos' => $arquivos
            // ]);

            $deletados = 0;
            $limite = time() - ($minutos * 60);

            // Log::info('Limite de tempo para exclusão', [
            //     'limite_timestamp' => $limite,
            //     'limite_datetime' => date('Y-m-d H:i:s', $limite)
            // ]);

            foreach ($arquivos as $arquivo) {
                // O arquivo já vem com o caminho completo do listarArquivos
                $caminhoCompleto = $arquivo;

                try {
                    // Obter timestamp da última modificação do arquivo
                    $timestamp = Storage::disk('supabase')->lastModified($caminhoCompleto);

                    // Log::info('Verificando arquivo', [
                    //     'arquivo' => $arquivo,
                    //     'caminho_completo' => $caminhoCompleto,
                    //     'timestamp' => $timestamp,
                    //     'timestamp_datetime' => $timestamp ? date('Y-m-d H:i:s', $timestamp) : null,
                    //     'deve_deletar' => $timestamp && $timestamp < $limite
                    // ]);

                    if ($timestamp && $timestamp < $limite) {
                        $removido = self::removerArquivo($caminhoCompleto);
                        // Log::info('Tentativa de remoção', [
                        //     'arquivo' => $arquivo,
                        //     'removido' => $removido
                        // ]);

                        if ($removido) {
                            $deletados++;
                        }
                    }
                } catch (\Exception $e) {
                    // Log::warning('Erro ao processar arquivo', [
                    //     'arquivo' => $arquivo,
                    //     'erro' => $e->getMessage()
                    // ]);
                    continue;
                }
            }

            // Log::info('Limpeza concluída', [
            //     'deletados' => $deletados,
            //     'total_arquivos' => count($arquivos)
            // ]);

            return $deletados;
        } catch (\Exception $e) {
            // Log do erro de conexão e retornar 0
            // Log::error('Erro na limpeza de arquivos temporários do Supabase', [
            //     'erro' => $e->getMessage(),
            //     'minutos' => $minutos,
            //     'trace' => $e->getTraceAsString()
            // ]);
            return 0;
        }
    }


    /**
     * Faz upload de logo da igreja para o Supabase
     * Cria pasta church-logo dentro da estrutura da igreja
     * Remove logos antigos automaticamente
     *
     * @param mixed $arquivo Arquivo do upload
     * @param string|null $nomeIgreja Nome da igreja (opcional, usa igreja do usuário se não informado)
     * @return string Caminho completo do arquivo no Supabase
     * @throws \Exception
     */
    public static function fazerUploadLogoIgreja($arquivo, ?string $nomeIgreja = null): string
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Determinar qual igreja usar
        if ($nomeIgreja) {


            // Usar nome da igreja fornecido (para criação/edição de igrejas)
            $nomeIgrejaPadronizado = self::padronizarNomeIgreja($nomeIgreja);


        } else {
            // Usar igreja do usuário logado
            $user = Auth::user();
            $igreja = $user->getIgreja();

            if (!$igreja) {
                throw new \Exception('Usuário não está associado a uma igreja');
            }

            $nomeIgrejaPadronizado = self::padronizarNomeIgreja($igreja->nome);
        }

        // Gerar caminho base para logos da igreja
        $caminhoBase = "IGREJAS/{$nomeIgrejaPadronizado}/church-logo/";


        // Remover logos antigos da igreja (se existirem)
        try {
            $arquivosExistentes = self::listarArquivos($caminhoBase);
            foreach ($arquivosExistentes as $arquivoExistente) {
                // Só remover se for realmente um logo (não outros arquivos)
                if (preg_match('/^logo_.*\.(jpg|jpeg|png|gif|webp)$/i', basename($arquivoExistente))) {
                    self::removerArquivo($arquivoExistente);
                }
            }
        } catch (\Exception $e) {
            // Log silencioso - não interromper upload se falhar ao limpar
            // Log::warning('Erro ao limpar logos antigos', [
            //     'igreja' => $nomeIgrejaPadronizado,
            //     'erro' => $e->getMessage()
            // ]);
        }

        // Validar tipo de arquivo (só imagens)
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!self::validarTipoArquivo($arquivo, $tiposPermitidos)) {
            throw new \Exception('Tipo de arquivo não permitido. Use apenas imagens (JPG, PNG, GIF, WebP)');
        }

        // Validar tamanho (máximo 2MB)
        if (!self::validarTamanhoArquivo($arquivo, 2048)) {
            throw new \Exception('Arquivo muito grande. Tamanho máximo: 2MB');
        }

        // Gerar nome único para o logo
        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $nomeArquivo = 'logo_' . time() . '_' . uniqid() . '.' . $extensao;

        // Caminho completo
        $caminhoCompleto = $caminhoBase . $nomeArquivo;

        // Usar mesma abordagem do fazerUploadPerfil que funciona
        $content = $arquivo->get();

        // Fazer upload usando put() com caminho completo (igual ao fazerUploadPerfil)
        $path = Storage::disk('supabase')->put($caminhoCompleto, $content);

        if (!$path) {
            throw new \Exception('Falha ao fazer upload do logo para o Supabase');
        }

        // Retornar o caminho completo, não o resultado booleano do put()
        return $caminhoCompleto;
    }

    // ========================================
    // MÉTODOS PARA SMS SERVICE
    // ========================================

    /**
     * Gera caminho base para arquivos SMS
     * Formato: IGREJAS/{NOME_IGREJA}/SMS_SYSTEM/{USER_ID}/
     */
    public static function gerarCaminhoSmsBase(?int $userId = null): string
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        if (!$user) {
            throw new \Exception('Usuário não encontrado');
        }

        $igreja = $user->getIgreja();
        if (!$igreja) {
            throw new \Exception('Usuário não está associado a uma igreja');
        }

        $nomeIgrejaPadronizado = self::padronizarNomeIgreja($igreja->nome);

        return "IGREJAS/{$nomeIgrejaPadronizado}/SMS_SYSTEM/{$user->id}/";
    }

    /**
     * Gera caminho completo para um tipo específico de arquivo SMS
     * Formato: IGREJAS/{NOME_IGREJA}/SMS_SYSTEM/{USER_ID}/{tipo}/
     */
    public static function gerarCaminhoSmsTipo(string $tipo, ?int $userId = null): string
    {
        return self::gerarCaminhoSmsBase($userId) . trim($tipo, '/') . '/';
    }

    /**
     * Faz upload de anexo para SMS no Supabase
     * Salva na estrutura: IGREJAS/{IGREJA}/SMS_SYSTEM/{USER_ID}/{tipo}/
     */
    public static function fazerUploadSmsAnexo($arquivo, string $tipo, ?int $userId = null): array
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Validar tipo de arquivo baseado no tipo solicitado
        $validacao = self::validarArquivoSms($arquivo, $tipo);
        if (!$validacao['valido']) {
            throw new \Exception($validacao['erro']);
        }

        // Gerar caminho para SMS
        $caminhoBase = self::gerarCaminhoSmsTipo($tipo, $userId);

        // Gerar nome único
        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $nomeArquivo = 'sms_' . $tipo . '_' . time() . '_' . uniqid() . '.' . $extensao;

        // Caminho completo
        $caminhoCompleto = $caminhoBase . $nomeArquivo;

        // Fazer upload
        $content = $arquivo->get();
        $path = Storage::disk('supabase')->put($caminhoCompleto, $content);

        if (!$path) {
            throw new \Exception('Falha ao fazer upload do anexo SMS para o Supabase');
        }

        // Retornar informações do arquivo
        return [
            'caminho_completo' => $caminhoCompleto,
            'nome_original' => $arquivo->getClientOriginalName(),
            'nome_arquivo' => $nomeArquivo,
            'tamanho_bytes' => $arquivo->getSize(),
            'tipo_mime' => $arquivo->getMimeType(),
            'extensao' => $extensao,
            'tipo' => $tipo,
            'url' => self::obterUrl($caminhoCompleto),
        ];
    }

    /**
     * Valida arquivo para SMS baseado no tipo
     */
    public static function validarArquivoSms($arquivo, string $tipo): array
    {
        if (!$arquivo) {
            return ['valido' => false, 'erro' => 'Arquivo não fornecido'];
        }

        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $mimeType = $arquivo->getMimeType();
        $tamanhoMB = $arquivo->getSize() / (1024 * 1024);

        // Regras de validação por tipo
        $regras = [
            'imagem' => [
                'extensoes' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                'tamanho_max' => 5, // 5MB
            ],
            'video' => [
                'extensoes' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
                'mime_types' => ['video/mp4', 'video/avi', 'video/quicktime', 'video/x-ms-wmv', 'video/x-flv', 'video/webm'],
                'tamanho_max' => 50, // 50MB
            ],
            'audio' => [
                'extensoes' => ['mp3', 'wav', 'ogg', 'aac', 'm4a'],
                'mime_types' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/mp4'],
                'tamanho_max' => 10, // 10MB
            ],
            'documento' => [
                'extensoes' => ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx'],
                'mime_types' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                ],
                'tamanho_max' => 10, // 10MB
            ],
            'arquivo' => [
                'extensoes' => ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar'],
                'mime_types' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip',
                    'application/x-rar-compressed'
                ],
                'tamanho_max' => 25, // 25MB
            ],
        ];

        // Verificar se tipo existe
        if (!isset($regras[$tipo])) {
            return ['valido' => false, 'erro' => 'Tipo de arquivo não suportado'];
        }

        $regra = $regras[$tipo];

        // Verificar extensão
        if (!in_array($extensao, $regra['extensoes'])) {
            return [
                'valido' => false,
                'erro' => "Extensão '{$extensao}' não permitida para {$tipo}. Permitidas: " . implode(', ', $regra['extensoes'])
            ];
        }

        // Verificar MIME type
        if (!in_array($mimeType, $regra['mime_types'])) {
            return [
                'valido' => false,
                'erro' => "Tipo MIME '{$mimeType}' não permitido para {$tipo}"
            ];
        }

        // Verificar tamanho
        if ($tamanhoMB > $regra['tamanho_max']) {
            return [
                'valido' => false,
                'erro' => "Arquivo muito grande. Tamanho máximo para {$tipo}: {$regra['tamanho_max']}MB"
            ];
        }

        return ['valido' => true];
    }

    /**
     * Remove anexo de SMS do Supabase
     */
    public static function removerAnexoSms(string $caminhoArquivo): bool
    {
        if (!$caminhoArquivo) {
            return true; // Nada para remover
        }

        // Verificar se é um caminho de SMS válido
        if (!str_contains($caminhoArquivo, '/SMS_SYSTEM/')) {
            throw new \Exception('Caminho fornecido não é válido para anexos SMS');
        }

        return self::removerArquivo($caminhoArquivo);
    }

    /**
     * Lista anexos SMS de um usuário
     */
    public static function listarAnexosSms(?int $userId = null, ?string $tipo = null): array
    {
        $caminhoBase = $tipo
            ? self::gerarCaminhoSmsTipo($tipo, $userId)
            : self::gerarCaminhoSmsBase($userId);

        try {
            return self::listarArquivos($caminhoBase);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Obtém estatísticas de uso do SMS para um usuário
     */
    public static function obterEstatisticasSms(?int $userId = null): array
    {
        $caminhoBase = self::gerarCaminhoSmsBase($userId);

        $estatisticas = [
            'total_arquivos' => 0,
            'espaco_usado' => 0,
            'por_tipo' => []
        ];

        try {
            // Listar todos os tipos de pasta
            $tipos = ['imagem', 'video', 'audio', 'documento', 'arquivo'];

            foreach ($tipos as $tipo) {
                $caminhoTipo = self::gerarCaminhoSmsTipo($tipo, $userId);
                $arquivos = self::listarArquivos($caminhoTipo);

                $estatisticas['por_tipo'][$tipo] = [
                    'quantidade' => count($arquivos),
                    'tamanho_total' => 0
                ];

                // Calcular tamanho total (aproximado)
                foreach ($arquivos as $arquivo) {
                    try {
                        $tamanho = Storage::disk('supabase')->size($arquivo);
                        $estatisticas['por_tipo'][$tipo]['tamanho_total'] += $tamanho;
                        $estatisticas['espaco_usado'] += $tamanho;
                        $estatisticas['total_arquivos']++;
                    } catch (\Exception $e) {
                        // Ignorar erros individuais
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Retornar estatísticas vazias se houver erro
        }

        return $estatisticas;
    }

    /**
     * Limpa anexos SMS antigos de um usuário
     * Remove arquivos com mais de X dias
     */
    public static function limparAnexosSmsAntigos(?int $userId = null, int $dias = 30): int
    {
        $deletados = 0;
        $limite = now()->subDays($dias);

        try {
            $tipos = ['imagem', 'video', 'audio', 'documento', 'arquivo'];

            foreach ($tipos as $tipo) {
                $caminhoTipo = self::gerarCaminhoSmsTipo($tipo, $userId);
                $arquivos = self::listarArquivos($caminhoTipo);

                foreach ($arquivos as $arquivo) {
                    try {
                        $timestamp = Storage::disk('supabase')->lastModified($arquivo);

                        if ($timestamp && $timestamp < $limite->timestamp) {
                            if (self::removerArquivo($arquivo)) {
                                $deletados++;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Log silencioso
        }

        return $deletados;
    }

    /**
     * Faz upload múltiplo de anexos para SMS
     */
    public static function fazerUploadMultiploSmsAnexos(array $arquivos, string $tipo, ?int $userId = null): array
    {
        $resultados = [];

        foreach ($arquivos as $arquivo) {
            try {
                $resultado = self::fazerUploadSmsAnexo($arquivo, $tipo, $userId);
                $resultados[] = [
                    'sucesso' => true,
                    'arquivo' => $arquivo->getClientOriginalName(),
                    'dados' => $resultado
                ];
            } catch (\Exception $e) {
                $resultados[] = [
                    'sucesso' => false,
                    'arquivo' => $arquivo->getClientOriginalName(),
                    'erro' => $e->getMessage()
                ];
            }
        }

        return $resultados;
    }

    // ========================================
    // MÉTODOS PARA ALIANÇAS
    // ========================================

    /**
     * Gera caminho base para arquivos das alianças
     * Formato: IGREJAS/Alliances/{NOME_ALIANCA_PADRONIZADO}/
     */
    public static function gerarCaminhoBaseAlianca(int $aliancaId): string
    {
        // Buscar aliança
        $alianca = \App\Models\Igrejas\AliancaIgreja::find($aliancaId);
        if (!$alianca) {
            throw new \Exception('Aliança não encontrada');
        }

        // Padronizar nome da aliança
        $nomeAliancaPadronizado = self::padronizarNomeIgreja($alianca->nome);

        return "IGREJAS/Alliances/{$nomeAliancaPadronizado}/";
    }

    /**
     * Gera caminho completo para um tipo específico de arquivo da aliança
     * Formato: IGREJAS/Alliances/{NOME_ALIANCA}/ficheiro/ ou IGREJAS/Alliances/{NOME_ALIANCA}/audio/
     */
    public static function gerarCaminhoAliancaTipo(int $aliancaId, string $tipo): string
    {
        $caminhoBase = self::gerarCaminhoBaseAlianca($aliancaId);

        // Mapear tipos para português conforme solicitado
        $tipoMapeado = match($tipo) {
            'audio' => 'audio',
            'arquivo', 'ficheiro' => 'ficheiro',
            default => $tipo
        };

        return $caminhoBase . $tipoMapeado . '/';
    }

    /**
     * Faz upload de arquivo para aliança no Supabase
     * Salva na estrutura: IGREJAS/Alliances/{NOME_ALIANCA}/{tipo}/
     */
    public static function fazerUploadAlianca($arquivo, int $aliancaId, string $tipo): string
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        // Validar tipo de arquivo baseado no tipo solicitado
        $validacao = self::validarArquivoAlianca($arquivo, $tipo);
        if (!$validacao['valido']) {
            throw new \Exception($validacao['erro']);
        }

        // Gerar caminho para a aliança
        $caminhoBase = self::gerarCaminhoAliancaTipo($aliancaId, $tipo);

        // Gerar nome único
        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $nomeArquivo = 'alianca_' . $tipo . '_' . time() . '_' . uniqid() . '.' . $extensao;

        // Caminho completo
        $caminhoCompleto = $caminhoBase . $nomeArquivo;

        // Fazer upload
        $content = $arquivo->get();
        $path = Storage::disk('supabase')->put($caminhoCompleto, $content);

        if (!$path) {
            throw new \Exception('Falha ao fazer upload do arquivo da aliança para o Supabase');
        }

        return $caminhoCompleto;
    }

    /**
     * Valida arquivo para aliança baseado no tipo
     */
    public static function validarArquivoAlianca($arquivo, string $tipo): array
    {
        if (!$arquivo) {
            return ['valido' => false, 'erro' => 'Arquivo não fornecido'];
        }

        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $mimeType = $arquivo->getMimeType();
        $tamanhoMB = $arquivo->getSize() / (1024 * 1024);

        // Regras de validação por tipo
        $regras = [
            'audio' => [
                'extensoes' => ['mp3', 'wav', 'ogg', 'aac', 'm4a', 'webm'],
                'mime_types' => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/aac', 'audio/mp4', 'audio/webm', 'video/webm'],
                'tamanho_max' => 25, // 25MB para áudios
            ],
            'ficheiro' => [
                'extensoes' => ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'gif', 'webp'],
                'mime_types' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/zip',
                    'application/x-rar-compressed',
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp'
                ],
                'tamanho_max' => 50, // 50MB para arquivos
            ],
        ];

        // Verificar se tipo existe
        if (!isset($regras[$tipo])) {
            return ['valido' => false, 'erro' => 'Tipo de arquivo não suportado para alianças'];
        }

        $regra = $regras[$tipo];

        // Verificar extensão
        if (!in_array($extensao, $regra['extensoes'])) {
            return [
                'valido' => false,
                'erro' => "Extensão '{$extensao}' não permitida para {$tipo}. Permitidas: " . implode(', ', $regra['extensoes'])
            ];
        }

        // Verificar MIME type
        if (!in_array($mimeType, $regra['mime_types'])) {
            return [
                'valido' => false,
                'erro' => "Tipo MIME '{$mimeType}' não permitido para {$tipo}"
            ];
        }

        // Verificar tamanho
        if ($tamanhoMB > $regra['tamanho_max']) {
            return [
                'valido' => false,
                'erro' => "Arquivo muito grande. Tamanho máximo para {$tipo}: {$regra['tamanho_max']}MB"
            ];
        }

        return ['valido' => true];
    }

    /**
     * Lista arquivos de uma aliança por tipo
     */
    public static function listarArquivosAlianca(int $aliancaId, ?string $tipo = null): array
    {
        if ($tipo) {
            $caminhoBase = self::gerarCaminhoAliancaTipo($aliancaId, $tipo);
        } else {
            $caminhoBase = self::gerarCaminhoBaseAlianca($aliancaId);
        }

        try {
            return self::listarArquivos($caminhoBase);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Remove arquivo de aliança do Supabase
     */
    public static function removerArquivoAlianca(string $caminhoArquivo): bool
    {
        if (!$caminhoArquivo) {
            return true; // Nada para remover
        }

        // Verificar se é um caminho de aliança válido
        if (!str_contains($caminhoArquivo, '/Alliances/')) {
            throw new \Exception('Caminho fornecido não é válido para arquivos de aliança');
        }

        return self::removerArquivo($caminhoArquivo);
    }

    /**
     * Obtém estatísticas de uso da aliança
     */
    public static function obterEstatisticasAlianca(int $aliancaId): array
    {
        $estatisticas = [
            'total_arquivos' => 0,
            'espaco_usado' => 0,
            'por_tipo' => []
        ];

        try {
            $tipos = ['ficheiro', 'audio'];

            foreach ($tipos as $tipo) {
                $caminhoTipo = self::gerarCaminhoAliancaTipo($aliancaId, $tipo);
                $arquivos = self::listarArquivos($caminhoTipo);

                $estatisticas['por_tipo'][$tipo] = [
                    'quantidade' => count($arquivos),
                    'tamanho_total' => 0
                ];

                // Calcular tamanho total
                foreach ($arquivos as $arquivo) {
                    try {
                        $tamanho = Storage::disk('supabase')->size($arquivo);
                        $estatisticas['por_tipo'][$tipo]['tamanho_total'] += $tamanho;
                        $estatisticas['espaco_usado'] += $tamanho;
                        $estatisticas['total_arquivos']++;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Retornar estatísticas vazias se houver erro
        }

        return $estatisticas;
    }

    // ========================================
    // MÉTODOS PARA ASSINATURAS
    // ========================================

    /**
     * Gera caminho base para arquivos de assinaturas
     * Formato: Assinaturas/{IGREJA_ID}/{PACOTE_NOME}/
     */
    public static function gerarCaminhoBaseAssinatura(string $igrejaNome, string $pacoteNome): string
    {
        // Padronizar nome do pacote
        $igrejaNomePadronizado = self::padronizarNomeIgreja($igrejaNome);
        $pacoteNomePadronizado = self::padronizarNomeIgreja($pacoteNome);

        return "Assinaturas/{$igrejaNomePadronizado}/{$pacoteNomePadronizado}/";
    }

    /**
     * Gera caminho completo para comprovativos de assinatura
     * Formato: Assinaturas/{IGREJA_ID}/{PACOTE_NOME}/comprovativos/
     */
    public static function gerarCaminhoAssinaturaComprovativos(string $igrejaNome, string $pacoteNome): string
    {
        return self::gerarCaminhoBaseAssinatura($igrejaNome, $pacoteNome) . 'comprovativos/';
    }

    /**
     * Faz upload de comprovativo de assinatura para o Supabase
     * Salva na estrutura: Assinaturas/{IGREJA_ID}/{PACOTE_NOME}/comprovativos/
     */
    public static function fazerUploadComprovativoAssinatura($arquivo, string $igrejaNome, string $pacoteNome): array
    {
        if (!$arquivo) {
            throw new \Exception('Arquivo não fornecido');
        }

        $validacao = self::validarComprovativoAssinatura($arquivo);
        if (!$validacao['valido']) {
            throw new \Exception($validacao['erro']);
        }

        $caminhoBase = self::gerarCaminhoAssinaturaComprovativos($igrejaNome, $pacoteNome);

        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $nomeArquivo = 'comprovativo_' . time() . '_' . uniqid() . '.' . $extensao;

        $caminhoCompleto = $caminhoBase . $nomeArquivo;

        $content = $arquivo->get();

        // 🚀 Aqui está o poder
        $upload = self::uploadComFallback($caminhoCompleto, $content);

        return [
            'url' => self::obterUrl($upload['caminho'], $upload['disk']),
            'disk' => $upload['disk'],
            'caminho_completo' => $upload['caminho'],
            'nome_original' => $arquivo->getClientOriginalName(),
            'nome_arquivo' => $nomeArquivo,
            'tamanho_bytes' => $arquivo->getSize(),
            'tipo_mime' => $arquivo->getMimeType(),
            'extensao' => $extensao,
        ];
    }

    /**
     * Valida arquivo de comprovativo de assinatura
     * Aceita: PDF, JPG, JPEG, PNG (máx 5MB)
     */
    public static function validarComprovativoAssinatura($arquivo): array
    {
        if (!$arquivo) {
            return ['valido' => false, 'erro' => 'Arquivo não fornecido'];
        }

        $extensao = strtolower($arquivo->getClientOriginalExtension());
        $mimeType = $arquivo->getMimeType();
        $tamanhoMB = $arquivo->getSize() / (1024 * 1024);

        // Tipos permitidos
        $extensoesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
        $mimeTypesPermitidos = [
            'application/pdf',
            'image/jpeg',
            'image/png'
        ];

        // Verificar extensão
        if (!in_array($extensao, $extensoesPermitidas)) {
            return [
                'valido' => false,
                'erro' => "Extensão '{$extensao}' não permitida. Use apenas: PDF, JPG, JPEG ou PNG"
            ];
        }

        // Verificar MIME type
        if (!in_array($mimeType, $mimeTypesPermitidos)) {
            return [
                'valido' => false,
                'erro' => "Tipo de arquivo '{$mimeType}' não permitido"
            ];
        }

        // Verificar tamanho (máximo 5MB)
        if ($tamanhoMB > 5) {
            return [
                'valido' => false,
                'erro' => 'Arquivo muito grande. Tamanho máximo: 5MB'
            ];
        }

        return ['valido' => true];
    }

    /**
     * Lista comprovativos de uma assinatura específica
     */
    public static function listarComprovativosAssinatura(string $igrejaNome, string $pacoteNome): array
    {
        $caminhoBase = self::gerarCaminhoAssinaturaComprovativos($igrejaNome, $pacoteNome);

        try {
            return self::listarArquivos($caminhoBase);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Remove comprovativo de assinatura do Supabase
     */
    public static function removerComprovativoAssinatura(string $caminhoArquivo): bool
    {
        if (!$caminhoArquivo) {
            return true; // Nada para remover
        }

        // Verificar se é um caminho de comprovativo válido
        if (!str_contains($caminhoArquivo, '/Assinaturas/') || !str_contains($caminhoArquivo, '/comprovativos/')) {
            throw new \Exception('Caminho fornecido não é válido para comprovativos de assinatura');
        }

        return self::removerArquivo($caminhoArquivo);
    }

    /**
     * Obtém estatísticas de comprovativos de uma assinatura
     */
    public static function obterEstatisticasComprovativosAssinatura(string $igrejaNome, string $pacoteNome): array
    {
        $caminhoBase = self::gerarCaminhoAssinaturaComprovativos($igrejaNome, $pacoteNome);

        $estatisticas = [
            'total_comprovativos' => 0,
            'espaco_usado' => 0,
            'tipos_arquivo' => []
        ];

        try {
            $arquivos = self::listarArquivos($caminhoBase);

            foreach ($arquivos as $arquivo) {
                try {
                    $tamanho = Storage::disk('supabase')->size($arquivo);
                    $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));

                    $estatisticas['total_comprovativos']++;
                    $estatisticas['espaco_usado'] += $tamanho;

                    if (!isset($estatisticas['tipos_arquivo'][$extensao])) {
                        $estatisticas['tipos_arquivo'][$extensao] = 0;
                    }
                    $estatisticas['tipos_arquivo'][$extensao]++;

                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Retornar estatísticas vazias se houver erro
        }

        return $estatisticas;
    }

}
