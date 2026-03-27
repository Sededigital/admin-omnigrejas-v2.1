-- Habilita extensão necessária para UUID
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- =========================================================
-- OMNIGREJAS • MÓDULO DE ASSINATURAS E PACOTES (BILLING)
-- Versão: 2025-09-01
-- =========================================================

-- ==================== MÓDULOS ====================
CREATE TABLE modulos (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(255) UNIQUE NOT NULL, -- Ex: 'financeiro', 'igrejas', 'cursos'
    descricao TEXT,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);
-- ==================== PACOTES ====================
CREATE TABLE pacote (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL UNIQUE,
    descricao TEXT,
    preco NUMERIC(10,2) NOT NULL,
    preco_vitalicio NUMERIC(10,2),     -- preço especial para assinatura vitalícia
    duracao_meses INT NOT NULL,       -- duração padrão em meses
    trial_dias INT DEFAULT 0,         -- número de dias de trial padrão
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);

-- ==================== PERMISSÕES DE PACOTES ====================
CREATE TABLE pacote_permissao (
    id BIGSERIAL PRIMARY KEY,
    pacote_id BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    modulo_id BIGINT NOT NULL REFERENCES modulos(id) ON DELETE CASCADE,
    permissao TEXT NOT NULL CHECK (permissao IN ('leitura','escrita','nenhuma')),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL,
    UNIQUE (pacote_id, modulo_id)
);

-- ==================== HISTÓRICO DE ASSINATURAS ====================
CREATE TABLE assinatura_historico (
    id BIGSERIAL PRIMARY KEY,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    pacote_id BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    valor NUMERIC(10,2) NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('Ativo','Cancelado','Expirado')),
    forma_pagamento TEXT,
    transacao_id TEXT,
    trial_fim DATE,                   -- data final real do trial
    duracao_meses_custom INT,         -- se cliente escolheu duração diferente
    vitalicio BOOLEAN DEFAULT FALSE,  -- assinatura vitalícia
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== ASSINATURA ATUAL ====================
CREATE TABLE assinatura_atual (
    igreja_id BIGINT PRIMARY KEY REFERENCES igrejas(id) ON DELETE CASCADE,
    pacote_id BIGINT NOT NULL REFERENCES pacote(id),
    data_inicio DATE NOT NULL,
    data_fim DATE NULL,
    status TEXT NOT NULL CHECK (status IN ('Ativo','Cancelado','Expirado')) DEFAULT 'Ativo',
    trial_fim DATE,                   -- data final do trial atual
    duracao_meses_custom INT,         -- caso esteja diferente do pacote
    vitalicio BOOLEAN DEFAULT FALSE,  -- se for vitalício
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== PAGAMENTOS DE ASSINATURAS ====================
CREATE TABLE assinatura_pagamentos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    assinatura_id BIGINT NOT NULL REFERENCES assinatura_historico(id) ON DELETE CASCADE,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    valor NUMERIC(10,2) NOT NULL,
    metodo_pagamento TEXT NOT NULL CHECK (metodo_pagamento IN ('deposito','multicaixa_express','tpa','transferencia','outro')),
    referencia TEXT,
    status TEXT NOT NULL CHECK (status IN ('pendente','confirmado','falhou','estornado')) DEFAULT 'pendente',
    data_pagamento TIMESTAMPTZ DEFAULT now(),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_assinatura_pagamentos_igreja ON assinatura_pagamentos(igreja_id);
CREATE INDEX idx_assinatura_pagamentos_status ON assinatura_pagamentos(status);

-- ==================== IGREJAS ASSINADAS ====================
CREATE TABLE igrejas_assinadas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    pacote_id BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    data_adesao TIMESTAMPTZ DEFAULT now(),
    data_cancelamento TIMESTAMPTZ,
    observacoes TEXT,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE (igreja_id, pacote_id, ativo)
);

-- ==================== LOGS DE ASSINATURAS ====================
CREATE TABLE assinatura_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    pacote_id BIGINT REFERENCES pacote(id) ON DELETE SET NULL,
    acao TEXT NOT NULL CHECK (acao IN ('criado','upgrade','downgrade','cancelado','renovado','pagamento','expirado')),
    descricao TEXT,
    usuario_id UUID REFERENCES users(id) ON DELETE SET NULL,
    data_acao TIMESTAMPTZ DEFAULT now(),
    detalhes JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);
CREATE INDEX idx_assinatura_logs_igreja ON assinatura_logs(igreja_id);

-- ==================== CICLOS DE COBRANÇA ====================
CREATE TABLE assinatura_ciclos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    assinatura_id BIGINT NOT NULL REFERENCES assinatura_historico(id) ON DELETE CASCADE,
    inicio DATE NOT NULL,
    fim DATE NOT NULL,
    valor NUMERIC(10,2) NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('pendente','pago','atrasado','falhou')),
    gerado_em TIMESTAMPTZ DEFAULT now(),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== MÉTODOS DE PAGAMENTO ====================
CREATE TABLE igrejas_metodos_pagamento (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    tipo TEXT NOT NULL CHECK (tipo IN ('cash','multicaixa_express','tpa','transferencia','deposito')),
    detalhes JSONB DEFAULT '{}'::jsonb,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== CUPONS DE DESCONTO ====================
CREATE TABLE assinatura_cupons (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    codigo VARCHAR(50) UNIQUE NOT NULL,
    descricao TEXT,
    desconto_percentual INT CHECK (desconto_percentual BETWEEN 0 AND 100),
    desconto_valor NUMERIC(10,2),
    valido_de DATE,
    valido_ate DATE,
    uso_max INT DEFAULT 1,
    usado INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE assinatura_cupons_uso (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    assinatura_id BIGINT NOT NULL REFERENCES assinatura_historico(id) ON DELETE CASCADE,
    cupom_id UUID NOT NULL REFERENCES assinatura_cupons(id) ON DELETE CASCADE,
    usado_em TIMESTAMPTZ DEFAULT now(),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== RENOVAÇÃO AUTOMÁTICA ====================
CREATE TABLE assinatura_auto_renovacao (
    igreja_id BIGINT PRIMARY KEY REFERENCES igrejas(id) ON DELETE CASCADE,
    ativo BOOLEAN DEFAULT TRUE,
    metodo_pagamento_id UUID REFERENCES igrejas_metodos_pagamento(id) ON DELETE SET NULL,
    ultima_tentativa TIMESTAMPTZ,
    proxima_tentativa TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== FALHAS DE PAGAMENTO ====================
CREATE TABLE assinatura_pagamentos_falhas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pagamento_id UUID REFERENCES assinatura_pagamentos(id) ON DELETE CASCADE,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    motivo TEXT NOT NULL,
    data TIMESTAMPTZ DEFAULT now(),
    resolvido BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== NOTIFICAÇÕES DE COBRANÇA ====================
CREATE TABLE assinatura_notificacoes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    assinatura_id BIGINT NOT NULL REFERENCES assinatura_historico(id) ON DELETE CASCADE,
    tipo TEXT NOT NULL CHECK (tipo IN ('lembrete','atraso','cancelamento')),
    titulo TEXT NOT NULL,
    mensagem TEXT,
    enviada_em TIMESTAMPTZ,
    lida_em TIMESTAMPTZ,
    status TEXT CHECK (status IN ('enviada','lida','ignorada')) DEFAULT 'enviada',
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== HISTÓRICO DE MUDANÇAS DE PLANO ====================
CREATE TABLE assinatura_upgrades (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    assinatura_id BIGINT NOT NULL REFERENCES assinatura_historico(id) ON DELETE CASCADE,
    pacote_anterior BIGINT REFERENCES pacote(id) ON DELETE SET NULL,
    pacote_novo BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    valor_diferenca NUMERIC(10,2),
    data_upgrade DATE NOT NULL DEFAULT CURRENT_DATE,
    motivo TEXT,
    usuario_id UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== FATURAS ====================
CREATE TABLE assinatura_faturas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    assinatura_id BIGINT NOT NULL REFERENCES assinatura_historico(id) ON DELETE CASCADE,
    numero_fatura VARCHAR(50) UNIQUE NOT NULL,
    valor_total NUMERIC(10,2) NOT NULL,
    moeda VARCHAR(10) DEFAULT 'AKZ',
    status TEXT CHECK (status IN ('pendente','paga','cancelada','estornada')) DEFAULT 'pendente',
    data_emissao TIMESTAMPTZ DEFAULT now(),
    data_vencimento DATE,
    data_pagamento TIMESTAMPTZ,
    detalhes JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== ÍNDICES AUXILIARES ====================
CREATE INDEX idx_assinatura_notificacoes_assinatura ON assinatura_notificacoes(assinatura_id);
CREATE INDEX idx_assinatura_notificacoes_tipo ON assinatura_notificacoes(tipo);
CREATE INDEX idx_assinatura_upgrades_assinatura ON assinatura_upgrades(assinatura_id);
CREATE INDEX idx_assinatura_upgrades_data ON assinatura_upgrades(data_upgrade);



-- =========================================================
-- OMNIGREJAS • SISTEMA DE CONTROLE DE ASSINATURAS SAAS
-- Expansão para controle granular de recursos e limites
-- Versão: 2025-10-16
-- =========================================================

-- ==================== PACOTE RECURSOS ====================
-- Define limites de recursos por pacote de assinatura
CREATE TABLE pacote_recursos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pacote_id BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    recurso_tipo VARCHAR(50) NOT NULL, -- gerenciar_igrejas, gerenciar_membro
    limite_valor INT NULL, -- NULL = ilimitado
    unidade VARCHAR(20), -- 'quantidade', 'gb', 'mensal', etc.
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(pacote_id, recurso_tipo)
);

-- Índices para pacote_recursos
CREATE INDEX idx_pacote_recursos_pacote_tipo ON pacote_recursos(pacote_id, recurso_tipo);
CREATE INDEX idx_pacote_recursos_ativo ON pacote_recursos(ativo);

-- ==================== PACOTE NÍVEIS ====================
-- Define níveis hierárquicos dos pacotes
CREATE TABLE pacote_niveis (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    pacote_id BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    nivel VARCHAR(50) NOT NULL, -- 'basico', 'profissional', 'premium', 'enterprise'
    prioridade INT NOT NULL, -- 1 = mais baixo, 10 = mais alto
    recursos_extras JSONB DEFAULT '{}'::jsonb, -- recursos específicos do nível
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(pacote_id, nivel)
);

-- Índices para pacote_niveis
CREATE INDEX idx_pacote_niveis_pacote_nivel ON pacote_niveis(pacote_id, nivel);
CREATE INDEX idx_pacote_niveis_prioridade ON pacote_niveis(prioridade);

-- ==================== IGREJA CONSUMO ====================
-- Rastreia consumo mensal de recursos por igreja
CREATE TABLE igreja_consumo (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    recurso_tipo VARCHAR(50) NOT NULL,
    consumo_atual INT DEFAULT 0,
    limite_atual INT, -- NULL = ilimitado
    periodo_referencia DATE NOT NULL, -- mês/ano de referência
    reset_automatico BOOLEAN DEFAULT TRUE,
    ultimo_reset TIMESTAMPTZ DEFAULT now(),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(igreja_id, recurso_tipo, periodo_referencia)
);

-- Índices para igreja_consumo
CREATE INDEX idx_igreja_consumo_igreja_recurso ON igreja_consumo(igreja_id, recurso_tipo);
CREATE INDEX idx_igreja_consumo_periodo ON igreja_consumo(periodo_referencia);
CREATE INDEX idx_igreja_consumo_atual_limite ON igreja_consumo(consumo_atual, limite_atual);

-- ==================== IGREJA RECURSOS BLOQUEADOS ====================
-- Controle de recursos bloqueados por igreja
CREATE TABLE igreja_recursos_bloqueados (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    recurso_tipo VARCHAR(50) NOT NULL,
    motivo_bloqueio TEXT NOT NULL,
    bloqueado_em TIMESTAMPTZ DEFAULT now(),
    desbloqueado_em TIMESTAMPTZ,
    bloqueado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para igreja_recursos_bloqueados
CREATE INDEX idx_recursos_bloqueados_igreja ON igreja_recursos_bloqueados(igreja_id);
CREATE INDEX idx_recursos_bloqueados_tipo ON igreja_recursos_bloqueados(recurso_tipo);
CREATE INDEX idx_recursos_bloqueados_ativo ON igreja_recursos_bloqueados(ativo);

-- ==================== ASSINATURA VERIFICAÇÕES ====================
-- Log detalhado de todas as verificações de assinatura
CREATE TABLE assinatura_verificacoes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    recurso_solicitado VARCHAR(100) NOT NULL,
    acao_solicitada VARCHAR(100) NOT NULL,
    status_verificacao VARCHAR(20) NOT NULL CHECK (status_verificacao IN ('permitido', 'bloqueado_assinatura', 'limite_excedido', 'erro')),
    detalhes JSONB DEFAULT '{}'::jsonb,
    verificado_em TIMESTAMPTZ DEFAULT now(),
    usuario_id UUID REFERENCES users(id) ON DELETE SET NULL,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para assinatura_verificacoes
CREATE INDEX idx_verificacoes_igreja ON assinatura_verificacoes(igreja_id);
CREATE INDEX idx_verificacoes_recurso ON assinatura_verificacoes(recurso_solicitado);
CREATE INDEX idx_verificacoes_status ON assinatura_verificacoes(status_verificacao);
CREATE INDEX idx_verificacoes_data ON assinatura_verificacoes(verificado_em);

-- ==================== ASSINATURA ALERTAS ====================
-- Sistema de alertas para limites e expirações
CREATE TABLE assinatura_alertas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    tipo_alerta VARCHAR(50) NOT NULL, -- 'expiracao_proxima', 'limite_proximo', 'expirado'
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    dados JSONB DEFAULT '{}'::jsonb,
    lido BOOLEAN DEFAULT FALSE,
    lido_em TIMESTAMPTZ,
    criado_em TIMESTAMPTZ DEFAULT now(),
    expires_at TIMESTAMPTZ, -- quando o alerta expira
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para assinatura_alertas
CREATE INDEX idx_alertas_igreja ON assinatura_alertas(igreja_id);
CREATE INDEX idx_alertas_tipo ON assinatura_alertas(tipo_alerta);
CREATE INDEX idx_alertas_lido ON assinatura_alertas(lido);
CREATE INDEX idx_alertas_expires ON assinatura_alertas(expires_at);

-- =========================================================
-- COMENTÁRIOS DAS NOVAS TABELAS
-- =========================================================

COMMENT ON TABLE pacote_recursos IS 'Define limites de recursos por pacote de assinatura (membros, SMS, emails, etc.)';
COMMENT ON COLUMN pacote_recursos.recurso_tipo IS 'Tipo de recurso: membros, sms, emails, armazenamento';
COMMENT ON COLUMN pacote_recursos.limite_valor IS 'Limite do recurso (NULL = ilimitado)';
COMMENT ON COLUMN pacote_recursos.unidade IS 'Unidade de medida: quantidade, gb, mensal';

COMMENT ON TABLE pacote_niveis IS 'Define níveis hierárquicos dos pacotes (basico, premium, enterprise)';
COMMENT ON COLUMN pacote_niveis.nivel IS 'Nome do nível do pacote';
COMMENT ON COLUMN pacote_niveis.prioridade IS 'Prioridade hierárquica (1-10, 10 = mais alto)';
COMMENT ON COLUMN pacote_niveis.recursos_extras IS 'Recursos específicos do nível em JSON';

COMMENT ON TABLE igreja_consumo IS 'Rastreia consumo mensal de recursos por igreja';
COMMENT ON COLUMN igreja_consumo.consumo_atual IS 'Quantidade já consumida no período';
COMMENT ON COLUMN igreja_consumo.limite_atual IS 'Limite atual do recurso';
COMMENT ON COLUMN igreja_consumo.periodo_referencia IS 'Período de referência (YYYY-MM-01)';

COMMENT ON TABLE igreja_recursos_bloqueados IS 'Controle de recursos bloqueados por igreja';
COMMENT ON COLUMN igreja_recursos_bloqueados.motivo_bloqueio IS 'Motivo pelo qual o recurso foi bloqueado';
COMMENT ON COLUMN igreja_recursos_bloqueados.ativo IS 'Status do bloqueio (ativo/inativo)';

COMMENT ON TABLE assinatura_verificacoes IS 'Log detalhado de todas as verificações de assinatura e limites';
COMMENT ON COLUMN assinatura_verificacoes.status_verificacao IS 'Resultado: permitido, bloqueado_assinatura, limite_excedido, erro';
COMMENT ON COLUMN assinatura_verificacoes.detalhes IS 'Informações adicionais em JSON';

COMMENT ON TABLE assinatura_alertas IS 'Sistema de alertas para limites e expirações de assinatura';
COMMENT ON COLUMN assinatura_alertas.tipo_alerta IS 'Tipo: expiracao_proxima, limite_proximo, expirado';
COMMENT ON COLUMN assinatura_alertas.dados IS 'Dados específicos do alerta em JSON';
COMMENT ON COLUMN assinatura_alertas.expires_at IS 'Quando o alerta deixa de ser relevante';

-- ==================== PAGAMENTO_ASSINATURA_IGREJA ====================
-- Tabela para armazenar pagamentos de assinaturas das igrejas
CREATE TABLE pagamento_assinatura_igreja (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    pacote_id BIGINT NOT NULL REFERENCES pacote(id) ON DELETE CASCADE,
    valor NUMERIC(10,2) NOT NULL,
    preco_vitalicio NUMERIC(10,2),     -- preço especial para assinatura vitalícia
    duracao_meses INT NOT NULL,
    is_vitalicio BOOLEAN,
    pacote_nome VARCHAR(255),
    metodo_pagamento TEXT NOT NULL CHECK (metodo_pagamento IN ('deposito','multicaixa_express','tpa','transferencia','outro')),
    referencia TEXT,
    comprovativo_url TEXT, -- URL do arquivo no Supabase
    comprovativo_nome TEXT, -- Nome original do arquivo
    comprovativo_tipo TEXT, -- Tipo MIME do arquivo
    comprovativo_tamanho BIGINT, -- Tamanho em bytes
    status TEXT NOT NULL DEFAULT 'pendente' CHECK (status IN ('pendente','confirmado','rejeitado','expirado')),
    data_pagamento TIMESTAMPTZ DEFAULT now(),
    data_confirmacao TIMESTAMPTZ,
    confirmado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    observacoes TEXT,
    motivo_rejeicao TEXT,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices para pagamento_assinatura_igreja
CREATE INDEX idx_pagamento_assinatura_igreja_igreja ON pagamento_assinatura_igreja(igreja_id);
CREATE INDEX idx_pagamento_assinatura_igreja_pacote ON pagamento_assinatura_igreja(pacote_id);
CREATE INDEX idx_pagamento_assinatura_igreja_status ON pagamento_assinatura_igreja(status);
CREATE INDEX idx_pagamento_assinatura_igreja_data_pagamento ON pagamento_assinatura_igreja(data_pagamento);
CREATE INDEX idx_pagamento_assinatura_igreja_confirmado_por ON pagamento_assinatura_igreja(confirmado_por);

-- =========================================================
-- FIM DO SISTEMA DE CONTROLE DE ASSINATURAS SAAS
-- =========================================================








-- =========================================================
-- OMNIGREJAS • VIEWS DE ASSINATURAS / BILLING
-- Versão: 2025-09-01
-- =========================================================

-- ==================== VIEW: ASSINATURAS ATIVAS ====================
CREATE OR REPLACE VIEW view_assinaturas_ativas AS
SELECT
    i.id AS igreja_id,
    i.nome AS igreja_nome,
    a.pacote_id,
    p.nome AS pacote_nome,
    a.data_inicio,
    a.data_fim,
    a.trial_fim,
    a.vitalicio,
    a.status
FROM assinatura_atual a
JOIN igrejas i ON i.id = a.igreja_id
JOIN pacote p ON p.id = a.pacote_id
WHERE a.status = 'Ativo'
  AND (a.vitalicio = TRUE OR a.data_fim IS NULL OR a.data_fim >= CURRENT_DATE);

-- ==================== VIEW: ASSINATURAS INATIVAS ====================
CREATE OR REPLACE VIEW view_assinaturas_inativas AS
SELECT
    i.id AS igreja_id,
    i.nome AS igreja_nome,
    a.pacote_id,
    p.nome AS pacote_nome,
    a.data_inicio,
    a.data_fim,
    a.trial_fim,
    a.vitalicio,
    a.status
FROM assinatura_atual a
JOIN igrejas i ON i.id = a.igreja_id
JOIN pacote p ON p.id = a.pacote_id
WHERE (a.status IN ('Cancelado','Expirado'))
   OR (a.vitalicio = FALSE AND a.data_fim < CURRENT_DATE);

-- ==================== VIEW: PAGAMENTOS CONFIRMADOS ====================
CREATE OR REPLACE VIEW view_assinatura_pagamentos_confirmados AS
SELECT
    ap.id AS pagamento_id,
    ap.igreja_id,
    i.nome AS igreja_nome,
    ap.valor,
    ap.metodo_pagamento,
    ap.status,
    ap.data_pagamento
FROM assinatura_pagamentos ap
JOIN igrejas i ON i.id = ap.igreja_id
WHERE ap.status = 'confirmado'
ORDER BY ap.data_pagamento DESC;

-- ==================== VIEW: ASSINATURAS EM ATRASO ====================
CREATE OR REPLACE VIEW view_assinaturas_em_atraso AS
SELECT
    ac.id AS ciclo_id,
    ac.assinatura_id,
    ah.igreja_id,
    i.nome AS igreja_nome,
    ah.pacote_id,
    p.nome AS pacote_nome,
    ac.inicio,
    ac.fim,
    ac.valor,
    ac.status
FROM assinatura_ciclos ac
JOIN assinatura_historico ah ON ah.id = ac.assinatura_id
JOIN igrejas i ON i.id = ah.igreja_id
JOIN pacote p ON p.id = ah.pacote_id
WHERE ac.status IN ('pendente','atrasado')
  AND ac.fim < CURRENT_DATE;

-- ==================== VIEW: LOGS DE ASSINATURAS ====================
CREATE OR REPLACE VIEW view_assinatura_logs AS
SELECT
    al.id AS log_id,
    al.igreja_id,
    i.nome AS igreja_nome,
    al.pacote_id,
    p.nome AS pacote_nome,
    al.acao,
    al.descricao,
    u.name AS usuario,
    al.data_acao,
    al.detalhes
FROM assinatura_logs al
JOIN igrejas i ON i.id = al.igreja_id
LEFT JOIN pacote p ON p.id = al.pacote_id
LEFT JOIN users u ON u.id = al.usuario_id
ORDER BY al.data_acao DESC;

-- =========================================================
-- FIM DAS VIEWS DE ASSINATURAS
-- =========================================================



-- =========================================================
-- OMNIGREJAS • FUNCTIONS (Billing)
-- Versão: 2025-09-01
-- =========================================================

-- ==================== FUNCTION: RENOVAR ASSINATURA ====================
CREATE OR REPLACE FUNCTION renovar_assinatura(p_igreja BIGINT, p_meses INT)
RETURNS VOID AS $$
BEGIN
    UPDATE assinatura_atual
    SET data_inicio = CURRENT_DATE,
        data_fim = CASE WHEN vitalicio THEN data_fim ELSE CURRENT_DATE + (p_meses || ' months')::INTERVAL END,
        status = 'Ativo',
        updated_at = now()
    WHERE igreja_id = p_igreja;

    INSERT INTO assinatura_historico (igreja_id, pacote_id, data_inicio, data_fim, valor, status, vitalicio)
    SELECT igreja_id, pacote_id, CURRENT_DATE,
           CASE WHEN vitalicio THEN CURRENT_DATE ELSE CURRENT_DATE + (p_meses || ' months')::INTERVAL END,
           0, 'Ativo', vitalicio
    FROM assinatura_atual
    WHERE igreja_id = p_igreja;

    INSERT INTO assinatura_logs (igreja_id, pacote_id, acao, descricao, data_acao)
    SELECT igreja_id, pacote_id, 'renovado', 'Renovação automática', now()
    FROM assinatura_atual
    WHERE igreja_id = p_igreja;
END;
$$ LANGUAGE plpgsql;

-- ==================== FUNCTION: CANCELAR ASSINATURA ====================
CREATE OR REPLACE FUNCTION cancelar_assinatura(p_igreja BIGINT, p_motivo TEXT)
RETURNS VOID AS $$
BEGIN
    UPDATE assinatura_atual
    SET status = 'Cancelado',
        data_fim = CURRENT_DATE,
        updated_at = now()
    WHERE igreja_id = p_igreja;

    UPDATE igrejas_assinadas
    SET ativo = FALSE,
        data_cancelamento = now(),
        observacoes = p_motivo
    WHERE igreja_id = p_igreja
      AND ativo = TRUE;

    INSERT INTO assinatura_logs (igreja_id, acao, descricao, data_acao)
    VALUES (p_igreja, 'cancelado', p_motivo, now());
END;
$$ LANGUAGE plpgsql;

-- ==================== FUNCTION: REGISTRAR PAGAMENTO ====================
CREATE OR REPLACE FUNCTION registrar_pagamento(
    p_assinatura BIGINT,
    p_igreja BIGINT,
    p_valor NUMERIC,
    p_metodo TEXT,
    p_ref TEXT
) RETURNS UUID AS $$
DECLARE
    v_pagamento_id UUID;
BEGIN
    INSERT INTO assinatura_pagamentos (assinatura_id, igreja_id, valor, metodo_pagamento, referencia, status, data_pagamento)
    VALUES (p_assinatura, p_igreja, p_valor, p_metodo, p_ref, 'confirmado', now())
    RETURNING id INTO v_pagamento_id;

    INSERT INTO assinatura_logs (igreja_id, pacote_id, acao, descricao, data_acao, detalhes)
    SELECT p_igreja, ah.pacote_id, 'pagamento', 'Pagamento confirmado', now(),
           jsonb_build_object('valor', p_valor, 'metodo', p_metodo, 'referencia', p_ref)
    FROM assinatura_historico ah
    WHERE ah.id = p_assinatura;

    RETURN v_pagamento_id;
END;
$$ LANGUAGE plpgsql;


-- =========================================================
-- FIM DO ARQUIVO DE FUNCTIONS DE ASSINATURAS
-- =========================================================


-- =========================================================
-- OMNIGREJAS • TRIGGERS (Billing)
-- Versão: 2025-09-01
-- =========================================================


-- ==================== TRIGGER: Expirar assinatura ====================
CREATE OR REPLACE FUNCTION trg_expirar_assinatura()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.vitalicio = FALSE AND NEW.data_fim < CURRENT_DATE AND NEW.status = 'Ativo' THEN
        NEW.status := 'Expirado';
        INSERT INTO assinatura_logs (igreja_id, pacote_id, acao, descricao, data_acao)
        VALUES (NEW.igreja_id, NEW.pacote_id, 'expirado', 'Assinatura expirada automaticamente', now());
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_assinatura_expirar
BEFORE UPDATE ON assinatura_atual
FOR EACH ROW
EXECUTE FUNCTION trg_expirar_assinatura();

-- ==================== TRIGGER: Cancelamento explícito ====================
CREATE OR REPLACE FUNCTION trg_cancelar_assinatura_log()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.status = 'Cancelado' AND OLD.status <> 'Cancelado' THEN
        INSERT INTO assinatura_logs (igreja_id, pacote_id, acao, descricao, data_acao)
        VALUES (NEW.igreja_id, NEW.pacote_id, 'cancelado', 'Cancelamento manual registrado por trigger', now());
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_assinatura_cancelada
AFTER UPDATE ON assinatura_atual
FOR EACH ROW
EXECUTE FUNCTION trg_cancelar_assinatura_log();


-- =========================================================
-- FIM DO ARQUIVO DE TRIGGERS
-- =========================================================

-- =========================================================
-- ÍNDICES ADICIONAIS PARA OTIMIZAÇÃO DE PERFORMANCE (ASSIGNATURE)
-- Adicionados em 2025-12-23 para melhorar consultas frequentes
-- =========================================================

-- Índices para tabela assinatura_historico
CREATE INDEX IF NOT EXISTS idx_assinatura_historico_igreja_id ON assinatura_historico(igreja_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_historico_pacote_id ON assinatura_historico(pacote_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_historico_status ON assinatura_historico(status);
CREATE INDEX IF NOT EXISTS idx_assinatura_historico_data_fim ON assinatura_historico(data_fim);
CREATE INDEX IF NOT EXISTS idx_assinatura_historico_data_inicio ON assinatura_historico(data_inicio);

-- Índices para tabela assinatura_atual
CREATE INDEX IF NOT EXISTS idx_assinatura_atual_pacote_id ON assinatura_atual(pacote_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_atual_status ON assinatura_atual(status);
CREATE INDEX IF NOT EXISTS idx_assinatura_atual_data_fim ON assinatura_atual(data_fim);

-- Índices para tabela igrejas_assinadas
CREATE INDEX IF NOT EXISTS idx_igrejas_assinadas_igreja_id ON igrejas_assinadas(igreja_id);
CREATE INDEX IF NOT EXISTS idx_igrejas_assinadas_pacote_id ON igrejas_assinadas(pacote_id);
CREATE INDEX IF NOT EXISTS idx_igrejas_assinadas_ativo ON igrejas_assinadas(ativo);

-- Índices para tabela assinatura_ciclos
CREATE INDEX IF NOT EXISTS idx_assinatura_ciclos_assinatura_id ON assinatura_ciclos(assinatura_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_ciclos_status ON assinatura_ciclos(status);
CREATE INDEX IF NOT EXISTS idx_assinatura_ciclos_inicio ON assinatura_ciclos(inicio);
CREATE INDEX IF NOT EXISTS idx_assinatura_ciclos_fim ON assinatura_ciclos(fim);

-- Índices para tabela igrejas_metodos_pagamento
CREATE INDEX IF NOT EXISTS idx_igrejas_metodos_pagamento_igreja_id ON igrejas_metodos_pagamento(igreja_id);
CREATE INDEX IF NOT EXISTS idx_igrejas_metodos_pagamento_tipo ON igrejas_metodos_pagamento(tipo);
CREATE INDEX IF NOT EXISTS idx_igrejas_metodos_pagamento_ativo ON igrejas_metodos_pagamento(ativo);

-- Índices para tabela assinatura_cupons
CREATE INDEX IF NOT EXISTS idx_assinatura_cupons_codigo ON assinatura_cupons(codigo);
CREATE INDEX IF NOT EXISTS idx_assinatura_cupons_ativo ON assinatura_cupons(ativo);
CREATE INDEX IF NOT EXISTS idx_assinatura_cupons_valido_ate ON assinatura_cupons(valido_ate);

-- Índices para tabela assinatura_cupons_uso
CREATE INDEX IF NOT EXISTS idx_assinatura_cupons_uso_cupom_id ON assinatura_cupons_uso(cupom_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_cupons_uso_assinatura_id ON assinatura_cupons_uso(assinatura_id);

-- Índices para tabela assinatura_auto_renovacao
CREATE INDEX IF NOT EXISTS idx_assinatura_auto_renovacao_metodo_pagamento_id ON assinatura_auto_renovacao(metodo_pagamento_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_auto_renovacao_ativo ON assinatura_auto_renovacao(ativo);

-- Índices para tabela assinatura_pagamentos_falhas
CREATE INDEX IF NOT EXISTS idx_assinatura_pagamentos_falhas_igreja_id ON assinatura_pagamentos_falhas(igreja_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_pagamentos_falhas_pagamento_id ON assinatura_pagamentos_falhas(pagamento_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_pagamentos_falhas_resolvido ON assinatura_pagamentos_falhas(resolvido);

-- Índices para tabela assinatura_faturas
CREATE INDEX IF NOT EXISTS idx_assinatura_faturas_assinatura_id ON assinatura_faturas(assinatura_id);
CREATE INDEX IF NOT EXISTS idx_assinatura_faturas_status ON assinatura_faturas(status);
CREATE INDEX IF NOT EXISTS idx_assinatura_faturas_data_emissao ON assinatura_faturas(data_emissao);
CREATE INDEX IF NOT EXISTS idx_assinatura_faturas_data_vencimento ON assinatura_faturas(data_vencimento);

-- Índices para tabela modulos
CREATE INDEX IF NOT EXISTS idx_modulos_nome ON modulos(nome);

-- Índices para tabela pacote
CREATE INDEX IF NOT EXISTS idx_pacote_nome ON pacote(nome);
CREATE INDEX IF NOT EXISTS idx_pacote_preco ON pacote(preco);

-- Índices para tabela pacote_permissao
CREATE INDEX IF NOT EXISTS idx_pacote_permissao_modulo_id ON pacote_permissao(modulo_id);
CREATE INDEX IF NOT EXISTS idx_pacote_permissao_permissao ON pacote_permissao(permissao);

-- =========================================================
-- FIM DOS ÍNDICES ADICIONAIS (ASSIGNATURE)
-- =========================================================

SET TIMEZONE = 'Africa/Luanda';
