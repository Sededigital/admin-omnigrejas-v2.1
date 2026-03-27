-- =========================================================
-- SMS SERVICE • SISTEMA DE COMUNICAÇÃO ADMINISTRATIVA
-- Comunicação entre Admin/Pastor (igreja) ↔ Super Admin
-- Data: 2025-10-07
-- Versão: 1.0
-- =========================================================

-- Extensões necessárias
CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS citext;

SET TIMEZONE = 'Africa/Luanda';

-- ==================== ENUMs PARA SMS SERVICE ====================
DROP TYPE IF EXISTS sms_message_type_enum CASCADE;
CREATE TYPE sms_message_type_enum AS ENUM (
    'texto',
    'arquivo',
    'imagem',
    'video',
    'audio',
    'documento'
);

DROP TYPE IF EXISTS sms_message_status_enum CASCADE;
CREATE TYPE sms_message_status_enum AS ENUM (
    'enviada',
    'entregue',
    'lida',
    'respondida',
    'arquivada'
);

DROP TYPE IF EXISTS sms_conversation_status_enum CASCADE;
CREATE TYPE sms_conversation_status_enum AS ENUM (
    'ativa',
    'arquivada',
    'fechada'
);

DROP TYPE IF EXISTS sms_priority_enum CASCADE;
CREATE TYPE sms_priority_enum AS ENUM (
    'baixa',
    'normal',
    'alta',
    'urgente'
);

-- ==================== TABELA: CONVERSAS SMS ====================
-- Agrupa mensagens por assunto/igreja
CREATE TABLE sms_conversations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,

    -- Relacionamentos
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    iniciada_por UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,

    -- Controle
    status sms_conversation_status_enum DEFAULT 'ativa',
    prioridade sms_priority_enum DEFAULT 'normal',
    categoria VARCHAR(100), -- financeiro, tecnico, pastoral, etc.

    -- Timestamps
    primeira_mensagem_em TIMESTAMPTZ DEFAULT now(),
    ultima_mensagem_em TIMESTAMPTZ DEFAULT now(),
    resolvida_em TIMESTAMPTZ NULL,

    -- Metadados
    resolvida_por UUID REFERENCES users(id) ON DELETE SET NULL,
    tags JSONB DEFAULT '[]'::jsonb, -- tags para categorização

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade: uma conversa por igreja + titulo (se ativa)
    UNIQUE(igreja_id, titulo, status) DEFERRABLE INITIALLY DEFERRED
);

-- ==================== TABELA: MENSAGENS SMS ====================
-- Mensagens individuais da conversa
CREATE TABLE sms_messages (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    conversation_id UUID NOT NULL REFERENCES sms_conversations(id) ON DELETE CASCADE,

    -- Conteúdo da mensagem
    tipo sms_message_type_enum DEFAULT 'texto',
    conteudo TEXT, -- pode ser NULL para mensagens com mídia
    assunto VARCHAR(255), -- opcional, para mensagens importantes

    -- Remetente/Destinatário
    remetente_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    destinatario_tipo VARCHAR(20) CHECK (destinatario_tipo IN ('igreja_admin', 'super_admin')),
    igreja_destino_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE, -- se para admin da igreja

    -- Status e controle
    status sms_message_status_enum DEFAULT 'enviada',
    prioridade sms_priority_enum DEFAULT 'normal',
    lida_em TIMESTAMPTZ NULL,
    respondida_em TIMESTAMPTZ NULL,

    -- Arquivos anexados (se tipo != texto)
    anexo_url TEXT, -- URL no Supabase
    anexo_nome TEXT, -- nome original
    anexo_tamanho BIGINT, -- tamanho em bytes
    anexo_tipo_mime TEXT, -- MIME type
    anexo_extensao VARCHAR(10), -- extensão do arquivo

    -- Metadados específicos por tipo
    duracao_audio INTEGER, -- segundos (para áudio)
    dimensoes_imagem VARCHAR(20), -- "1920x1080" (para imagens)
    paginas_documento INTEGER, -- número de páginas (para PDFs)

    -- Controle de resposta
    resposta_para UUID REFERENCES sms_messages(id) ON DELETE SET NULL,
    thread_id UUID, -- agrupa mensagens relacionadas

    -- Timestamps
    enviada_em TIMESTAMPTZ DEFAULT now(),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ==================== TABELA: LEITURA DE MENSAGENS ====================
-- Controle individual de leitura por usuário
CREATE TABLE sms_message_reads (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    message_id UUID NOT NULL REFERENCES sms_messages(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    lida_em TIMESTAMPTZ NOT NULL DEFAULT now(),

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    -- Unicidade: usuário só pode marcar uma mensagem como lida uma vez
    UNIQUE(message_id, user_id)
);

-- ==================== TABELA: ANEXOS DE MENSAGENS ====================
-- Metadados detalhados dos anexos
CREATE TABLE sms_attachments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    message_id UUID NOT NULL REFERENCES sms_messages(id) ON DELETE CASCADE,

    -- Informações do arquivo
    nome_original TEXT NOT NULL,
    nome_arquivo TEXT NOT NULL, -- nome no storage
    caminho_completo TEXT NOT NULL, -- caminho completo no Supabase
    tamanho_bytes BIGINT NOT NULL,
    tipo_mime TEXT NOT NULL,
    extensao VARCHAR(10),

    -- Metadados específicos
    largura INTEGER, -- para imagens
    altura INTEGER, -- para imagens
    duracao_segundos INTEGER, -- para áudio/vídeo
    codec TEXT, -- codec de áudio/vídeo
    bitrate INTEGER, -- bitrate do arquivo

    -- Hash para verificação de integridade
    hash_sha256 VARCHAR(64),

    -- Controle de processamento
    processado BOOLEAN DEFAULT FALSE,
    processado_em TIMESTAMPTZ NULL,
    erro_processamento TEXT NULL,

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ==================== TABELA: NOTIFICAÇÕES SMS ====================
-- Notificações push/email para mensagens não lidas
CREATE TABLE sms_notifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    message_id UUID NOT NULL REFERENCES sms_messages(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,

    -- Tipo de notificação
    tipo VARCHAR(20) CHECK (tipo IN ('push', 'email', 'sms')),
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,

    -- Status
    enviada BOOLEAN DEFAULT FALSE,
    enviada_em TIMESTAMPTZ NULL,
    lida BOOLEAN DEFAULT FALSE,
    lida_em TIMESTAMPTZ NULL,

    -- Metadados
    dados_extras JSONB DEFAULT '{}'::jsonb,

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    UNIQUE(message_id, user_id, tipo)
);

-- ==================== TABELA: CONFIGURAÇÕES SMS ====================
-- Configurações por usuário/igreja
CREATE TABLE sms_settings (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),

    -- Escopo da configuração
    tipo VARCHAR(20) CHECK (tipo IN ('user', 'igreja', 'global')),
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,

    -- Configurações
    notificacoes_push BOOLEAN DEFAULT TRUE,
    notificacoes_email BOOLEAN DEFAULT TRUE,
    notificacoes_sms BOOLEAN DEFAULT FALSE,
    som_notificacao BOOLEAN DEFAULT TRUE,
    vibracao BOOLEAN DEFAULT TRUE,

    -- Preferências de exibição
    mostrar_imagens BOOLEAN DEFAULT TRUE,
    auto_download_arquivos BOOLEAN DEFAULT FALSE,
    tamanho_max_download BIGINT DEFAULT 10485760, -- 10MB

    -- Privacidade
    mostrar_online BOOLEAN DEFAULT TRUE,
    mostrar_digitando BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade por escopo
    UNIQUE(tipo, user_id, igreja_id)
);

-- ==================== ÍNDICES PARA PERFORMANCE ====================

-- Índices para sms_conversations
CREATE INDEX idx_sms_conversations_igreja ON sms_conversations(igreja_id);
CREATE INDEX idx_sms_conversations_status ON sms_conversations(status);
CREATE INDEX idx_sms_conversations_prioridade ON sms_conversations(prioridade);
CREATE INDEX idx_sms_conversations_iniciada_por ON sms_conversations(iniciada_por);
CREATE INDEX idx_sms_conversations_ultima_msg ON sms_conversations(ultima_mensagem_em);

-- Índices para sms_messages
CREATE INDEX idx_sms_messages_conversation ON sms_messages(conversation_id);
CREATE INDEX idx_sms_messages_remetente ON sms_messages(remetente_id);
CREATE INDEX idx_sms_messages_tipo ON sms_messages(tipo);
CREATE INDEX idx_sms_messages_status ON sms_messages(status);
CREATE INDEX idx_sms_messages_enviada_em ON sms_messages(enviada_em);
CREATE INDEX idx_sms_messages_thread ON sms_messages(thread_id);
CREATE INDEX idx_sms_messages_igreja_destino ON sms_messages(igreja_destino_id);

-- Índices para sms_message_reads
CREATE INDEX idx_sms_reads_message ON sms_message_reads(message_id);
CREATE INDEX idx_sms_reads_user ON sms_message_reads(user_id);
CREATE INDEX idx_sms_reads_lida_em ON sms_message_reads(lida_em);

-- Índices para sms_attachments
CREATE INDEX idx_sms_attachments_message ON sms_attachments(message_id);
CREATE INDEX idx_sms_attachments_tipo ON sms_attachments(tipo_mime);

-- Índices para sms_notifications
CREATE INDEX idx_sms_notifications_message ON sms_notifications(message_id);
CREATE INDEX idx_sms_notifications_user ON sms_notifications(user_id);
CREATE INDEX idx_sms_notifications_tipo ON sms_notifications(tipo);
CREATE INDEX idx_sms_notifications_enviada ON sms_notifications(enviada);

-- ==================== TRIGGERS PARA ATUALIZAÇÃO AUTOMÁTICA ====================

-- Trigger para atualizar ultima_mensagem_em na conversa
CREATE OR REPLACE FUNCTION update_conversation_last_message()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE sms_conversations
    SET ultima_mensagem_em = NEW.enviada_em,
        updated_at = now()
    WHERE id = NEW.conversation_id;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_update_conversation_last_message ON sms_messages;
CREATE TRIGGER trg_update_conversation_last_message
    AFTER INSERT ON sms_messages
    FOR EACH ROW
    EXECUTE FUNCTION update_conversation_last_message();

-- Trigger para marcar conversa como ativa quando recebe nova mensagem
CREATE OR REPLACE FUNCTION activate_conversation_on_message()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE sms_conversations
    SET status = 'ativa',
        updated_at = now()
    WHERE id = NEW.conversation_id AND status != 'ativa';

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_activate_conversation_on_message ON sms_messages;
CREATE TRIGGER trg_activate_conversation_on_message
    AFTER INSERT ON sms_messages
    FOR EACH ROW
    EXECUTE FUNCTION activate_conversation_on_message();

-- ==================== VIEWS ÚTEIS ====================

-- View: Conversas com informações completas
CREATE OR REPLACE VIEW view_sms_conversations_detailed AS
SELECT
    sc.*,
    i.nome as igreja_nome,
    u.name as iniciado_por_nome,
    ur.name as resolvido_por_nome,

    -- Estatísticas da conversa
    COUNT(sm.id) as total_mensagens,
    COUNT(CASE WHEN sm.status = 'lida' THEN 1 END) as mensagens_lidas,
    COUNT(CASE WHEN sm.tipo != 'texto' THEN 1 END) as mensagens_com_anexo,

    -- Última mensagem
    MAX(sm.enviada_em) as ultima_atividade,
    (
        SELECT sm2.conteudo
        FROM sms_messages sm2
        WHERE sm2.conversation_id = sc.id
        ORDER BY sm2.enviada_em DESC
        LIMIT 1
    ) as ultima_mensagem_preview

FROM sms_conversations sc
JOIN igrejas i ON i.id = sc.igreja_id
JOIN users u ON u.id = sc.iniciada_por
LEFT JOIN users ur ON ur.id = sc.resolvida_por
LEFT JOIN sms_messages sm ON sm.conversation_id = sc.id
GROUP BY sc.id, i.nome, u.name, ur.name;

-- View: Mensagens não lidas por usuário
CREATE OR REPLACE VIEW view_sms_unread_messages AS
SELECT
    sm.*,
    sc.titulo as conversation_titulo,
    sc.igreja_id,
    i.nome as igreja_nome,
    u.name as remetente_nome,
    u.photo_url as remetente_foto,

    -- Status de leitura para o usuário atual (será filtrado na query)
    CASE WHEN smr.lida_em IS NOT NULL THEN TRUE ELSE FALSE END as lida

FROM sms_messages sm
JOIN sms_conversations sc ON sc.id = sm.conversation_id
JOIN igrejas i ON i.id = sc.igreja_id
JOIN users u ON u.id = sm.remetente_id
LEFT JOIN sms_message_reads smr ON smr.message_id = sm.id
WHERE sm.status IN ('enviada', 'entregue')
ORDER BY sm.enviada_em DESC;

-- ==================== FUNÇÕES ÚTEIS ====================

-- Função para enviar mensagem SMS
CREATE OR REPLACE FUNCTION enviar_sms_message(
    p_conversation_id UUID,
    p_remetente_id UUID,
    p_tipo sms_message_type_enum DEFAULT 'texto',
    p_conteudo TEXT DEFAULT NULL,
    p_assunto VARCHAR(255) DEFAULT NULL,
    p_destinatario_tipo VARCHAR(20) DEFAULT 'super_admin',
    p_igreja_destino_id BIGINT DEFAULT NULL,
    p_prioridade sms_priority_enum DEFAULT 'normal',
    p_anexo_url TEXT DEFAULT NULL,
    p_anexo_nome TEXT DEFAULT NULL,
    p_anexo_tamanho BIGINT DEFAULT NULL,
    p_anexo_tipo_mime TEXT DEFAULT NULL
) RETURNS UUID AS $$
DECLARE
    v_message_id UUID;
    v_super_admin_id UUID;
BEGIN
    -- Inserir mensagem
    INSERT INTO sms_messages (
        conversation_id, tipo, conteudo, assunto,
        remetente_id, destinatario_tipo, igreja_destino_id,
        prioridade, anexo_url, anexo_nome, anexo_tamanho, anexo_tipo_mime
    ) VALUES (
        p_conversation_id, p_tipo, p_conteudo, p_assunto,
        p_remetente_id, p_destinatario_tipo, p_igreja_destino_id,
        p_prioridade, p_anexo_url, p_anexo_nome, p_anexo_tamanho, p_anexo_tipo_mime
    ) RETURNING id INTO v_message_id;

    -- Se for para super_admin, buscar ID do super admin
    IF p_destinatario_tipo = 'super_admin' THEN
        SELECT id INTO v_super_admin_id
        FROM users
        WHERE role = 'super_admin'
        LIMIT 1;

        -- Criar notificação para super admin
        INSERT INTO sms_notifications (message_id, user_id, tipo, titulo, mensagem)
        VALUES (
            v_message_id,
            v_super_admin_id,
            'push',
            COALESCE(p_assunto, 'Nova mensagem SMS'),
            COALESCE(LEFT(p_conteudo, 100), 'Mensagem com anexo')
        );
    END IF;

    RETURN v_message_id;
END;
$$ LANGUAGE plpgsql;

-- Função para marcar mensagem como lida
CREATE OR REPLACE FUNCTION marcar_sms_lida(p_message_id UUID, p_user_id UUID)
RETURNS BOOLEAN AS $$
BEGIN
    INSERT INTO sms_message_reads (message_id, user_id)
    VALUES (p_message_id, p_user_id)
    ON CONFLICT (message_id, user_id) DO NOTHING;

    UPDATE sms_messages
    SET status = 'lida', lida_em = now(), updated_at = now()
    WHERE id = p_message_id;

    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

-- ==================== PERMISSÕES RBAC PARA SMS SERVICE ====================

-- Adicionar permissões SMS ao sistema RBAC existente
-- Estas permissões serão adicionadas via função criar_permissoes_padrao()

-- Módulo: SMS Service
-- (igreja_id_param, 'gerenciar_sms', 'Gerenciar SMS', 'Gerenciar sistema de mensagens SMS', 'sms', 'admin', 8),

-- ==================== COMENTÁRIOS DAS TABELAS ====================

COMMENT ON TABLE sms_conversations IS 'Conversas SMS entre admin/pastor de igreja e super admin';
COMMENT ON COLUMN sms_conversations.titulo IS 'Título/assunto da conversa';
COMMENT ON COLUMN sms_conversations.status IS 'Status da conversa: ativa, arquivada, fechada';
COMMENT ON COLUMN sms_conversations.tags IS 'Tags JSON para categorização e busca';

COMMENT ON TABLE sms_messages IS 'Mensagens individuais das conversas SMS';
COMMENT ON COLUMN sms_messages.destinatario_tipo IS 'Tipo de destinatário: igreja_admin ou super_admin';
COMMENT ON COLUMN sms_messages.thread_id IS 'Agrupamento de mensagens relacionadas em thread';
COMMENT ON COLUMN sms_messages.anexo_url IS 'URL do arquivo anexado no Supabase';

COMMENT ON TABLE sms_message_reads IS 'Controle de leitura individual das mensagens';
COMMENT ON TABLE sms_attachments IS 'Metadados detalhados dos arquivos anexados';
COMMENT ON TABLE sms_notifications IS 'Notificações push/email para mensagens não lidas';
COMMENT ON TABLE sms_settings IS 'Configurações personalizadas do serviço SMS';

-- ==================== FIM DO SMS SERVICE ====================

-- Próximos passos:
-- 1. Executar este script no banco de dados
-- 2. Adicionar permissões SMS em modulos_existentes.md
-- 3. Registrar permissões em criar_permissoes_padrao()
-- 4. Adicionar Gates em RBACServiceProvider.php
-- 5. Implementar interface no frontend
