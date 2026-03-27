-- =========================================================
-- SISTEMA DE TRIAL USERS - OMNIGREJAS
-- Tabelas para controle de usuários de teste temporários
-- Versão: 2025-11-01
-- =========================================================

-- Extensões necessárias
CREATE EXTENSION IF NOT EXISTS pgcrypto;

SET TIMEZONE = 'Africa/Luanda';

-- ==================== TRIAL_USERS ====================
-- Controle principal dos usuários trial
CREATE TABLE IF NOT EXISTS trial_users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,

    -- Controle temporal
    data_inicio DATE NOT NULL DEFAULT CURRENT_DATE,
    data_fim DATE NOT NULL,
    periodo_dias INT NOT NULL DEFAULT 30,

    -- Status do trial
    status VARCHAR(20) NOT NULL DEFAULT 'ativo' CHECK (status IN ('ativo', 'expirando', 'expirado', 'bloqueado', 'cancelado')),
    motivo_cancelamento TEXT,

    -- Controle de reativação
    pode_reativar BOOLEAN DEFAULT TRUE,
    reativado_em TIMESTAMPTZ,
    reativado_por UUID REFERENCES users(id) ON DELETE SET NULL,

    -- Período de graça
    periodo_graca_dias INT DEFAULT 30,
    data_limite_graca DATE,

    -- Estatísticas de uso
    total_membros_criados INT DEFAULT 0,
    total_posts_criados INT DEFAULT 0,
    total_eventos_criados INT DEFAULT 0,
    ultimo_acesso TIMESTAMPTZ,

    -- Controle
    criado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),

    -- Campos para preservar dados após deleção do usuário
    user_nome_deletado VARCHAR(255),
    user_email_deletado VARCHAR(255),
    user_telefone_deletado VARCHAR(20),
    deletado_em TIMESTAMPTZ,

    UNIQUE(user_id),
    UNIQUE(igreja_id)
);

-- Índices para trial_users
CREATE INDEX IF NOT EXISTS idx_trial_users_user_id ON trial_users(user_id);
CREATE INDEX IF NOT EXISTS idx_trial_users_igreja_id ON trial_users(igreja_id);
CREATE INDEX IF NOT EXISTS idx_trial_users_status ON trial_users(status);
CREATE INDEX IF NOT EXISTS idx_trial_users_data_fim ON trial_users(data_fim);
CREATE INDEX IF NOT EXISTS idx_trial_users_data_limite_graca ON trial_users(data_limite_graca);
CREATE INDEX IF NOT EXISTS idx_trial_users_criado_por ON trial_users(criado_por);

-- ==================== TRIAL_ALERTAS ====================
-- Histórico de notificações enviadas aos usuários trial
CREATE TABLE IF NOT EXISTS trial_alertas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    trial_user_id UUID NOT NULL REFERENCES trial_users(id) ON DELETE CASCADE,

    tipo_alerta VARCHAR(50) NOT NULL, -- 'expiracao_proxima', 'expirado', 'bloqueado'
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    dados JSONB DEFAULT '{}'::jsonb,

    enviado_em TIMESTAMPTZ,
    lido_em TIMESTAMPTZ,
    email_enviado BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para trial_alertas
CREATE INDEX IF NOT EXISTS idx_trial_alertas_trial_user_id ON trial_alertas(trial_user_id);
CREATE INDEX IF NOT EXISTS idx_trial_alertas_tipo_alerta ON trial_alertas(tipo_alerta);
CREATE INDEX IF NOT EXISTS idx_trial_alertas_enviado_em ON trial_alertas(enviado_em);
CREATE INDEX IF NOT EXISTS idx_trial_alertas_lido_em ON trial_alertas(lido_em);

-- ==================== TRIAL_DADOS_CRIADOS ====================
-- Mapeamento de todos os dados criados durante o período trial
CREATE TABLE IF NOT EXISTS trial_dados_criados (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    trial_user_id UUID NOT NULL REFERENCES trial_users(id) ON DELETE CASCADE,

    tabela VARCHAR(100) NOT NULL, -- 'users', 'igrejas', 'posts', etc.
    registro_id TEXT NOT NULL, -- ID do registro criado
    tipo_dado VARCHAR(50), -- 'membro', 'post', 'evento', etc.

    criado_em TIMESTAMPTZ DEFAULT now(),
    soft_deleted BOOLEAN DEFAULT FALSE,
    deleted_em TIMESTAMPTZ,

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para trial_dados_criados
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_trial_user_id ON trial_dados_criados(trial_user_id);
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_tabela ON trial_dados_criados(tabela);
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_tipo_dado ON trial_dados_criados(tipo_dado);
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_soft_deleted ON trial_dados_criados(soft_deleted);
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_criado_em ON trial_dados_criados(criado_em);

-- ==================== TRIAL_LOGS ====================
-- Logs de auditoria de todas as ações relacionadas aos trials
CREATE TABLE IF NOT EXISTS trial_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    trial_user_id UUID REFERENCES trial_users(id) ON DELETE SET NULL,

    acao VARCHAR(50) NOT NULL, -- 'criado', 'acessado', 'expirado', 'bloqueado', 'reativado'
    descricao TEXT,
    dados JSONB DEFAULT '{}'::jsonb,

    realizado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    realizado_em TIMESTAMPTZ DEFAULT now(),

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para trial_logs
CREATE INDEX IF NOT EXISTS idx_trial_logs_trial_user_id ON trial_logs(trial_user_id);
CREATE INDEX IF NOT EXISTS idx_trial_logs_acao ON trial_logs(acao);
CREATE INDEX IF NOT EXISTS idx_trial_logs_realizado_por ON trial_logs(realizado_por);
CREATE INDEX IF NOT EXISTS idx_trial_logs_realizado_em ON trial_logs(realizado_em);

-- ==================== COMENTÁRIOS DAS TABELAS ====================

COMMENT ON TABLE trial_users IS 'Controle principal dos usuários em período de teste';
COMMENT ON COLUMN trial_users.user_id IS 'Referência ao usuário trial';
COMMENT ON COLUMN trial_users.igreja_id IS 'Igreja criada automaticamente para o trial';
COMMENT ON COLUMN trial_users.data_fim IS 'Data de expiração do trial';
COMMENT ON COLUMN trial_users.status IS 'Status atual do trial';
COMMENT ON COLUMN trial_users.periodo_graca_dias IS 'Dias de período de graça após expiração';
COMMENT ON COLUMN trial_users.data_limite_graca IS 'Data limite para reativação';
COMMENT ON COLUMN trial_users.user_nome_deletado IS 'Nome do usuário preservado após deleção';
COMMENT ON COLUMN trial_users.user_email_deletado IS 'Email do usuário preservado após deleção';
COMMENT ON COLUMN trial_users.user_telefone_deletado IS 'Telefone do usuário preservado após deleção';
COMMENT ON COLUMN trial_users.deletado_em IS 'Data/hora em que o usuário foi deletado';

COMMENT ON TABLE trial_alertas IS 'Histórico de notificações enviadas aos usuários trial';
COMMENT ON COLUMN trial_alertas.tipo_alerta IS 'Tipo da notificação enviada';
COMMENT ON COLUMN trial_alertas.dados IS 'Dados adicionais em JSON';

COMMENT ON TABLE trial_dados_criados IS 'Mapeamento de todos os dados criados durante o trial';
COMMENT ON COLUMN trial_dados_criados.tabela IS 'Nome da tabela onde o dado foi criado';
COMMENT ON COLUMN trial_dados_criados.registro_id IS 'ID do registro na tabela original';
COMMENT ON COLUMN trial_dados_criados.tipo_dado IS 'Tipo do dado criado (membro, post, evento, etc.)';

COMMENT ON TABLE trial_logs IS 'Logs de auditoria das ações dos usuários trial';
COMMENT ON COLUMN trial_logs.acao IS 'Tipo de ação realizada';
COMMENT ON COLUMN trial_logs.dados IS 'Dados adicionais da ação em JSON';

-- ==================== TRIGGERS ====================

-- Trigger para atualizar estatísticas quando dados são criados
CREATE OR REPLACE FUNCTION atualizar_estatisticas_trial()
RETURNS TRIGGER AS $$
BEGIN
    -- Atualizar contadores baseado no tipo de dado criado
    IF NEW.tabela = 'igreja_membros' AND NEW.tipo_dado = 'membro' THEN
        UPDATE trial_users
        SET total_membros_criados = total_membros_criados + 1,
            updated_at = now()
        WHERE id = NEW.trial_user_id;

    ELSIF NEW.tabela = 'posts' AND NEW.tipo_dado = 'post' THEN
        UPDATE trial_users
        SET total_posts_criados = total_posts_criados + 1,
            updated_at = now()
        WHERE id = NEW.trial_user_id;

    ELSIF NEW.tabela = 'eventos' AND NEW.tipo_dado = 'evento' THEN
        UPDATE trial_users
        SET total_eventos_criados = total_eventos_criados + 1,
            updated_at = now()
        WHERE id = NEW.trial_user_id;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Criar trigger
DROP TRIGGER IF EXISTS trg_atualizar_estatisticas_trial ON trial_dados_criados;
CREATE TRIGGER trg_atualizar_estatisticas_trial
    AFTER INSERT ON trial_dados_criados
    FOR EACH ROW
    EXECUTE FUNCTION atualizar_estatisticas_trial();

-- ==================== FUNÇÕES ÚTEIS ====================

-- Função para verificar se um usuário é trial ativo
CREATE OR REPLACE FUNCTION usuario_eh_trial_ativo(p_user_id UUID)
RETURNS BOOLEAN AS $$
BEGIN
    RETURN EXISTS(
        SELECT 1 FROM trial_users
        WHERE user_id = p_user_id
          AND status = 'ativo'
          AND data_fim >= CURRENT_DATE
    );
END;
$$ LANGUAGE plpgsql;

-- Função para obter dias restantes do trial
CREATE OR REPLACE FUNCTION dias_restantes_trial(p_user_id UUID)
RETURNS INTEGER AS $$
DECLARE
    v_data_fim DATE;
BEGIN
    SELECT data_fim INTO v_data_fim
    FROM trial_users
    WHERE user_id = p_user_id
      AND status = 'ativo';

    IF v_data_fim IS NULL THEN
        RETURN 0;
    END IF;

    RETURN GREATEST(0, v_data_fim - CURRENT_DATE);
END;
$$ LANGUAGE plpgsql;

-- Função para verificar se trial está em período de graça
CREATE OR REPLACE FUNCTION trial_em_periodo_graca(p_user_id UUID)
RETURNS BOOLEAN AS $$
BEGIN
    RETURN EXISTS(
        SELECT 1 FROM trial_users
        WHERE user_id = p_user_id
          AND status = 'expirado'
          AND data_limite_graca >= CURRENT_DATE
    );
END;
$$ LANGUAGE plpgsql;

-- ==================== VIEWS ====================

-- View para trials ativos
CREATE OR REPLACE VIEW view_trials_ativos AS
SELECT
    tu.id,
    tu.user_id,
    u.name,
    u.email,
    tu.igreja_id,
    i.nome AS igreja_nome,
    tu.data_inicio,
    tu.data_fim,
    tu.periodo_dias,
    tu.total_membros_criados,
    tu.total_posts_criados,
    tu.total_eventos_criados,
    tu.ultimo_acesso,
    dias_restantes_trial(tu.user_id) AS dias_restantes
FROM trial_users tu
JOIN users u ON u.id = tu.user_id
JOIN igrejas i ON i.id = tu.igreja_id
WHERE tu.status = 'ativo'
  AND tu.data_fim >= CURRENT_DATE;

-- View para trials expirados
CREATE OR REPLACE VIEW view_trials_expirados AS
SELECT
    tu.id,
    tu.user_id,
    u.name,
    u.email,
    tu.igreja_id,
    i.nome AS igreja_nome,
    tu.data_fim,
    tu.data_limite_graca,
    CASE
        WHEN CURRENT_DATE <= tu.data_limite_graca THEN 'em_graca'
        ELSE 'expirado_definitivamente'
    END AS status_graca,
    tu.total_membros_criados,
    tu.total_posts_criados,
    tu.total_eventos_criados
FROM trial_users tu
JOIN users u ON u.id = tu.user_id
JOIN igrejas i ON i.id = tu.igreja_id
WHERE tu.status = 'expirado';

-- View para estatísticas de uso dos trials
CREATE OR REPLACE VIEW view_trial_estatisticas AS
SELECT
    COUNT(*) AS total_trials,
    COUNT(CASE WHEN status = 'ativo' THEN 1 END) AS trials_ativos,
    COUNT(CASE WHEN status = 'expirado' THEN 1 END) AS trials_expirados,
    AVG(total_membros_criados) AS media_membros_criados,
    AVG(total_posts_criados) AS media_posts_criados,
    AVG(total_eventos_criados) AS media_eventos_criados,
    AVG(periodo_dias) AS periodo_medio_dias
FROM trial_users;

-- ==================== TRIAL_REQUESTS ====================
-- Controle de solicitações de trial pendentes de aprovação
CREATE TABLE IF NOT EXISTS trial_requests (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    igreja_nome VARCHAR(255) NOT NULL,
    denominacao VARCHAR(255) DEFAULT 'Evangélica',
    telefone VARCHAR(20),
    cidade VARCHAR(255),
    provincia VARCHAR(100),
    periodo_dias INT DEFAULT 10,
    status VARCHAR(20) NOT NULL DEFAULT 'pendente' CHECK (status IN ('pendente', 'aprovado', 'rejeitado')),
    aprovado_por UUID,
    aprovado_em TIMESTAMPTZ,
    rejeitado_por UUID,
    rejeitado_em TIMESTAMPTZ,
    motivo_rejeicao TEXT,
    observacoes TEXT,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    FOREIGN KEY (aprovado_por) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (rejeitado_por) REFERENCES users(id) ON DELETE SET NULL
);

-- Índices para trial_requests
CREATE INDEX IF NOT EXISTS idx_trial_requests_status_created_at ON trial_requests(status, created_at);
CREATE INDEX IF NOT EXISTS idx_trial_requests_email ON trial_requests(email);

-- Comentários para trial_requests
COMMENT ON TABLE trial_requests IS 'Controle de solicitações de período de teste pendentes de aprovação';
COMMENT ON COLUMN trial_requests.status IS 'Status da solicitação: pendente, aprovado, rejeitado';
COMMENT ON COLUMN trial_requests.aprovado_por IS 'Usuário que aprovou a solicitação';
COMMENT ON COLUMN trial_requests.rejeitado_por IS 'Usuário que rejeitou a solicitação';
COMMENT ON COLUMN trial_requests.motivo_rejeicao IS 'Motivo da rejeição da solicitação';

-- =========================================================
-- ÍNDICES ADICIONAIS PARA OTIMIZAÇÃO DE PERFORMANCE (TRIAL)
-- Adicionados em 2025-12-23 para melhorar consultas frequentes
-- =========================================================

-- Índices adicionais para trial_users
CREATE INDEX IF NOT EXISTS idx_trial_users_ultimo_acesso ON trial_users(ultimo_acesso);
CREATE INDEX IF NOT EXISTS idx_trial_users_pode_reativar ON trial_users(pode_reativar);
CREATE INDEX IF NOT EXISTS idx_trial_users_reativado_por ON trial_users(reativado_por);

-- Índices adicionais para trial_alertas
CREATE INDEX IF NOT EXISTS idx_trial_alertas_email_enviado ON trial_alertas(email_enviado);

-- Índices adicionais para trial_dados_criados
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_registro_id ON trial_dados_criados(registro_id);
CREATE INDEX IF NOT EXISTS idx_trial_dados_criados_deleted_em ON trial_dados_criados(deleted_em);

-- Índices adicionais para trial_logs
CREATE INDEX IF NOT EXISTS idx_trial_logs_dados ON trial_logs USING GIN(dados);

-- Índices adicionais para trial_requests
CREATE INDEX IF NOT EXISTS idx_trial_requests_aprovado_por ON trial_requests(aprovado_por);
CREATE INDEX IF NOT EXISTS idx_trial_requests_rejeitado_por ON trial_requests(rejeitado_por);
CREATE INDEX IF NOT EXISTS idx_trial_requests_aprovado_em ON trial_requests(aprovado_em);
CREATE INDEX IF NOT EXISTS idx_trial_requests_rejeitado_em ON trial_requests(rejeitado_em);
CREATE INDEX IF NOT EXISTS idx_trial_requests_cidade ON trial_requests(cidade);
CREATE INDEX IF NOT EXISTS idx_trial_requests_provincia ON trial_requests(provincia);

-- =========================================================
-- FIM DOS ÍNDICES ADICIONAIS (TRIAL)
-- =========================================================

-- =========================================================
-- FIM DO SISTEMA DE TRIAL USERS
-- =========================================================