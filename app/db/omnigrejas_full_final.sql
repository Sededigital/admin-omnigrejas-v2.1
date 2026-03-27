-- =========================================================
-- OMNIGREJAS • SCHEMA COMPLETO EXPANDIDO (PostgreSQL)
-- Data: 2025-09-06
-- Última atualização: 2025-10-12 - Sistema de Permissões RBAC COMPLETO + Todas as permissões do sidebar adicionadas + Tabelas igreja_permissoes, igreja_funcoes, igreja_funcao_permissoes, igreja_membro_funcoes, igreja_permissao_logs + Permissões padrão automáticas + Módulo SMS + Campo code_access na tabela igrejas
-- =========================================================

-- Extensões necessárias
CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS citext;

-- ==================== ENUMs ====================
DROP TYPE IF EXISTS role_enum CASCADE;
CREATE TYPE role_enum AS ENUM ('root','super_admin','admin','pastor','ministro','obreiro','diacono','membro','anonymous');


DROP TYPE IF EXISTS approval_status_enum CASCADE;
CREATE TYPE approval_status_enum AS ENUM ('pendente','aprovado','rejeitado');

DROP TYPE IF EXISTS membership_status_enum CASCADE;
CREATE TYPE membership_status_enum AS ENUM ('ativo','inativo', 'falecido','transferido');

DROP TYPE IF EXISTS gender_enum CASCADE;
CREATE TYPE gender_enum AS ENUM ('masculino','feminino','nao_informado');

DROP TYPE IF EXISTS transaction_type_enum CASCADE;
CREATE TYPE transaction_type_enum AS ENUM ('entrada','saida');

DROP TYPE IF EXISTS event_type_enum CASCADE;
CREATE TYPE event_type_enum AS ENUM ('culto','evento','retiro','congresso');

-- Status de pedido
DROP TYPE IF EXISTS pedido_status_enum CASCADE;
CREATE TYPE pedido_status_enum AS ENUM ('pendente','em_andamento','aprovado','rejeitado','concluido');

SET TIMEZONE = 'Africa/Luanda';

-- === HORA PADRÃO ANGOLA ===


-- ==================== USUÁRIOS ====================
CREATE TABLE IF NOT EXISTS users (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  name TEXT NOT NULL,
  email CITEXT UNIQUE,
  email_verified_at TIMESTAMPTZ DEFAULT NULL,
  password TEXT,
  phone TEXT,
  photo_url TEXT,
  role role_enum NOT NULL DEFAULT 'anonymous',
  denomination TEXT DEFAULT 'Geral',
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  status TEXT NOT NULL DEFAULT 'ativo' CHECK (status IN ('ativo', 'inativo', 'suspenso', 'bloqueado')),
  remember_token VARCHAR(300) NULL,
  created_by UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  deleted_at TIMESTAMPTZ NULL
);

ALTER TABLE users ENABLE ROW LEVEL SECURITY;





CREATE TABLE IF NOT EXISTS "cache" (
    "key" VARCHAR(255) PRIMARY KEY,
    "value" TEXT NOT NULL,
    "expiration" INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS "cache_locks" (
    "key" VARCHAR(255) PRIMARY KEY,
    "owner" VARCHAR(255) NOT NULL,
    "expiration" INTEGER NOT NULL
);




-- =========================================================
-- TABELAS DE CLASSIFICAÇÃO DE IGREJAS
-- =========================================================

-- Categorias de Igrejas
DROP TABLE IF EXISTS categorias_igrejas CASCADE;
CREATE TABLE categorias_igrejas (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(100) UNIQUE NOT NULL,     -- Ex.: Católica, Pentecostal, Adventista
    descricao TEXT,
    ativa BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- START INSERT Categorias
INSERT INTO categorias_igrejas (nome, descricao, ativa)
VALUES
    ('Católica', 'Baseada na tradição apostólica e na autoridade do Papa.', TRUE),
    ('Protestante', 'Movimento surgido da Reforma, enfatizando a fé e a Bíblia como autoridade suprema.', TRUE),
    ('Pentecostal', 'Caracteriza-se pela ênfase no Espírito Santo e nos dons espirituais.', TRUE),
    ('Neopentecostal', 'Ramo moderno do pentecostalismo, com foco em prosperidade e libertação espiritual.', TRUE),
    ('Evangélica', 'Igrejas cristãs centradas na Bíblia, na fé em Cristo e na evangelização.', TRUE),
    ('Reformada', 'Tradição teológica calvinista, com foco na soberania de Deus e nas Escrituras.', TRUE),
    ('Luterana', 'Seguidora dos princípios de Martinho Lutero e da justificação pela fé.', TRUE),
    ('Presbiteriana', 'Igrejas governadas por presbíteros e baseadas na teologia reformada.', TRUE),
    ('Metodista', 'Foco na santificação e na vida cristã disciplinada.', TRUE),
    ('Batista', 'Ênfase no batismo de crentes e na autonomia das igrejas locais.', TRUE),
    ('Adventista', 'Crê na segunda vinda de Cristo e guarda o sábado como dia santo.', TRUE),
    ('Carismática', 'Movimento que enfatiza os dons espirituais dentro de igrejas tradicionais.', TRUE),
    ('Episcopal', 'Governança episcopal, com bispos como líderes espirituais.', TRUE),
    ('Anglicana', 'Combina elementos católicos e protestantes sob uma estrutura episcopal.', TRUE),
    ('Ortodoxa', 'Tradição cristã oriental, baseada em rituais e doutrinas antigas.', TRUE),
    ('Comunitária', 'Igrejas locais focadas em comunhão e vida em grupo.', TRUE),
    ('Independente', 'Igrejas autônomas, sem vínculo com denominações tradicionais.', TRUE),
    ('Missionária', 'Voltada para a evangelização e expansão da fé cristã.', TRUE),
    ('Profética', 'Movimentos que destacam revelações e ministérios proféticos.', TRUE),
    ('Apostólica', 'Estrutura baseada em liderança apostólica contemporânea.', TRUE),
    ('Restauracionista', 'Busca restaurar a fé e práticas do cristianismo primitivo.', TRUE),
    ('Histórica', 'Igrejas com herança doutrinária sólida e estrutura antiga.', TRUE),
    ('Contemporânea', 'Igrejas modernas com linguagem e estilo adaptados à cultura atual.', TRUE),
    ('Interdenominacional', 'Reúne membros de várias origens cristãs sem vínculo exclusivo.', TRUE),
    ('Carismático Reformado', 'Mescla teologia reformada com prática carismática.', TRUE),
    ('Tradicional', 'Seguem liturgias e doutrinas clássicas, com pouca inovação.', TRUE),
    ('Conservadora', 'Mantém doutrinas bíblicas rígidas e padrões de moralidade antigos.', TRUE),
    ('Liberal', 'Interpreta as Escrituras de forma aberta e inclusiva.', TRUE),
    ('Indígena', 'Igrejas cristãs que incorporam elementos culturais locais.', TRUE),
    ('Comunitária Urbana', 'Voltada para projetos sociais e evangelismo em áreas urbanas.', TRUE);

-- END INSERT


-- Alianças de Igrejas
DROP TABLE IF EXISTS aliancas_igrejas CASCADE;
CREATE TABLE aliancas_igrejas (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,            -- Nome da aliança
    sigla VARCHAR(20),                     -- Sigla opcional
    descricao TEXT,                        -- Descrição detalhada
    ativa BOOLEAN DEFAULT TRUE,            -- Status ativo/inativo

    -- Sistema de Workflow de Aprovação
    categoria_id BIGINT REFERENCES categorias_igrejas(id) ON DELETE SET NULL,
    status VARCHAR(30) DEFAULT 'rascunho' CHECK (status IN ('rascunho', 'pendente_validacao', 'pronta_aprovacao', 'aprovada', 'rejeitada', 'suspensa')),
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    aprovado_by UUID REFERENCES users(id) ON DELETE SET NULL,
    aprovado_em TIMESTAMPTZ,
    min_aderentes INTEGER DEFAULT 2,
    aderentes_count INTEGER DEFAULT 0,

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL,

    -- Unicidade por categoria
    UNIQUE(nome, categoria_id)
);

-- ==================== IGREJAS ====================
CREATE TABLE IF NOT EXISTS igrejas (
  id BIGSERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  nif VARCHAR(50) NOT NULL UNIQUE,
  sigla VARCHAR(20),
  descricao TEXT,
  sobre TEXT,
  contacto TEXT,
  localizacao TEXT,
  logo TEXT,
  status_aprovacao approval_status_enum NOT NULL DEFAULT 'pendente',
  sede_id BIGINT REFERENCES igrejas(id) ON DELETE SET NULL,
  tipo TEXT CHECK (tipo IN ('sede','filial','independente')) DEFAULT 'independente',
  designacao TEXT,
  -- Novos relacionamentos
  categoria_id BIGINT REFERENCES categorias_igrejas(id) ON DELETE SET NULL,
  code_access TEXT, -- Código de acesso para a igreja
  created_by UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  deleted_at TIMESTAMPTZ NULL,

  CONSTRAINT fk_sede_check CHECK (id IS NULL OR id <> sede_id)
);

-- Índices
CREATE INDEX idx_igrejas_sede_id ON igrejas(sede_id);
CREATE INDEX idx_igrejas_categoria_id ON igrejas(categoria_id);
CREATE INDEX idx_igrejas_code_access ON igrejas(code_access);

-- Comentários da tabela igrejas
COMMENT ON COLUMN igrejas.code_access IS 'Código de acesso único para autenticação/acesso à igreja';

-- Índices para Alianças
CREATE INDEX idx_aliancas_categoria ON aliancas_igrejas(categoria_id);
CREATE INDEX idx_aliancas_status ON aliancas_igrejas(status);
CREATE INDEX idx_aliancas_created_by ON aliancas_igrejas(created_by);
CREATE INDEX idx_aliancas_aprovado_by ON aliancas_igrejas(aprovado_by);



-- ==================== MEMBROS ====================
CREATE TABLE IF NOT EXISTS igreja_membros (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  cargo role_enum NOT NULL CHECK (cargo IN ('admin','pastor','ministro','obreiro','diacono','membro')),
  status membership_status_enum NOT NULL DEFAULT 'ativo',
  data_entrada DATE NOT NULL DEFAULT CURRENT_DATE,
  numero_membro TEXT,
  principal BOOLEAN NOT NULL DEFAULT false, -- igreja principal do usuário
  created_by UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  deleted_at TIMESTAMPTZ NULL
);

-- ==================== CHATS DA IGREJA ====================
CREATE TABLE IF NOT EXISTS igreja_chats (
   id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
   igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
   nome TEXT NOT NULL,
   descricao TEXT,
   criado_por UUID REFERENCES users(id) ON DELETE SET NULL,
   visibilidade VARCHAR(20) DEFAULT 'publico' CHECK (visibilidade IN ('publico', 'privado')),
   created_at TIMESTAMPTZ DEFAULT now(),
   updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== PARTICIPANTES DO CHAT ====================
-- Tabela separada para gerenciar participantes dos chats/grupos (WhatsApp-like)
CREATE TABLE IF NOT EXISTS igreja_chat_participantes (
   id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
   chat_id UUID NOT NULL REFERENCES igreja_chats(id) ON DELETE CASCADE,
   user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
   is_admin BOOLEAN NOT NULL DEFAULT false, -- admin do grupo (pode adicionar/remover membros)
   added_by UUID REFERENCES users(id) ON DELETE SET NULL, -- quem adicionou este participante
   data_entrada TIMESTAMPTZ NOT NULL DEFAULT now(),
   status VARCHAR(20) DEFAULT 'ativo' CHECK (status IN ('ativo', 'removido', 'saiu','pedido')),
   created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
   updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

   -- Unicidade: um usuário não pode participar do mesmo chat duas vezes
   UNIQUE(chat_id, user_id)
);

CREATE UNIQUE INDEX idx_usuario_igreja_principal
ON igreja_membros(user_id)
WHERE principal = true;


CREATE TABLE IF NOT EXISTS membro_perfis (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_membro_id UUID NOT NULL UNIQUE REFERENCES igreja_membros(id) ON DELETE CASCADE,
  genero gender_enum NOT NULL DEFAULT 'nao_informado',
  data_nascimento DATE,
  endereco TEXT,
  observacoes TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  created_by UUID REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS igreja_membros_historico (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
  cargo role_enum NOT NULL,
  inicio DATE NOT NULL DEFAULT CURRENT_DATE,
  fim DATE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ==================== MINISTÉRIOS ====================
CREATE TABLE IF NOT EXISTS ministerios (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  nome TEXT NOT NULL,
  descricao TEXT,
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now(),
  deleted_at TIMESTAMPTZ NULL
);

CREATE TABLE IF NOT EXISTS igreja_membros_ministerios (
  membro_id UUID REFERENCES igreja_membros(id) ON DELETE CASCADE,
  ministerio_id UUID REFERENCES ministerios(id) ON DELETE CASCADE,
  funcao TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  deleted_at TIMESTAMPTZ NULL,
  PRIMARY KEY (membro_id, ministerio_id)
);

-- ==================== FINANCEIRO ====================
CREATE TABLE IF NOT EXISTS financeiro_categorias (
  id BIGSERIAL PRIMARY KEY,
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  nome TEXT NOT NULL,
  tipo transaction_type_enum NOT NULL,
  UNIQUE (igreja_id, nome, tipo),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- =========================================================
-- TABELA: Contas bancárias formais da igreja
-- =========================================================
CREATE TABLE IF NOT EXISTS financeiro_contas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    banco TEXT NOT NULL,
    titular TEXT NOT NULL,              -- nome da igreja ou responsável
    iban VARCHAR(50),                   -- IBAN da conta
    swift VARCHAR(20),                  -- Código SWIFT
    numero_conta VARCHAR(50),           -- Nº de conta bancária
    moeda CHAR(3) DEFAULT 'AOA' CHECK (moeda ~ '^[A-Z]{3}$'), -- ISO 4217 (ex.: AOA, USD, EUR)
    ativa BOOLEAN DEFAULT TRUE,
    observacoes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS financeiro_movimentos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  conta_id UUID REFERENCES financeiro_contas(id) ON DELETE SET NULL,
  tipo transaction_type_enum NOT NULL,
  categoria_id BIGINT REFERENCES financeiro_categorias(id) ON DELETE SET NULL,
  valor NUMERIC(14,2) NOT NULL CHECK (valor >= 0),
  descricao TEXT,
  data_transacao DATE NOT NULL DEFAULT CURRENT_DATE,
  metodo_pagamento TEXT,
  responsavel_id UUID REFERENCES users(id) ON DELETE SET NULL,
  comprovante_url TEXT,
  created_by UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  deleted_at TIMESTAMPTZ NULL
);

CREATE INDEX idx_financeiro_movimentos_igreja_data ON financeiro_movimentos(igreja_id, data_transacao);


-- Índice auxiliar para consultas por igreja
CREATE INDEX IF NOT EXISTS idx_financeiro_contas_igreja ON financeiro_contas(igreja_id);


-- =========================================================
-- TABELA: Canais digitais de pagamento/envio de dinheiro
-- =========================================================
CREATE TABLE IF NOT EXISTS financeiro_canais_digitais (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    tipo TEXT NOT NULL CHECK (tipo IN ('bai_direto','multicaixa_express','bfa_net','unigtel_money','kixi_credito','outro')),
    referencia TEXT NOT NULL,           -- ex.: nº conta virtual, telemóvel, entidade, código ref
    titular TEXT,                       -- nome exibido no canal
    moeda CHAR(3) DEFAULT 'AOA' CHECK (moeda ~ '^[A-Z]{3}$'), -- ISO 4217 (ex.: AOA, USD, EUR)
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índice auxiliar para consultas por igreja
CREATE INDEX IF NOT EXISTS idx_financeiro_canais_igreja ON financeiro_canais_digitais(igreja_id);

-- =========================================================
-- MÓDULO DE AGENDAMENTOS (SaaS) - NOVO
-- Tabela independente de igreja para agendamentos gerais
-- =========================================================

-- ==================== AGENDAMENTOS ====================
CREATE TABLE IF NOT EXISTS agendamentos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    tipo VARCHAR(50) DEFAULT 'reuniao' CHECK (tipo IN ('reuniao', 'consulta', 'acompanhamento', 'outro')),
    data_agendamento DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME,
    local VARCHAR(255),
    modalidade VARCHAR(20) DEFAULT 'presencial' CHECK (modalidade IN ('presencial', 'online', 'hibrido')),
    link_reuniao TEXT,
    status VARCHAR(20) DEFAULT 'agendado' CHECK (status IN ('agendado', 'confirmado', 'realizado', 'cancelado')),

    -- Relacionamentos
    organizador_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    responsavel_id UUID REFERENCES users(id) ON DELETE SET NULL,
    convidado_id UUID REFERENCES users(id) ON DELETE SET NULL,

    -- Novos relacionamentos para alianças e igrejas (campos opcionais)
    igreja_id BIGINT REFERENCES igrejas(id) ON DELETE SET NULL,
    alianca_id BIGINT REFERENCES aliancas_igrejas(id) ON DELETE SET NULL,

    -- Controle
    observacoes TEXT,
    lembretes JSONB DEFAULT '[]'::jsonb,
    data_confirmacao TIMESTAMPTZ,
    motivo_cancelamento TEXT,

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);

-- ===============================================
-- Eventos únicos (inclui cultos especiais, reuniões, ensaios, congressos)
-- ===============================================
DROP TABLE IF EXISTS eventos CASCADE;
CREATE TABLE IF NOT EXISTS eventos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    titulo TEXT NOT NULL,
    tipo TEXT CHECK (tipo IN ('culto','reuniao','ensaio','evento_social','outro')) DEFAULT 'outro',
    data_evento DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fim TIME,
    local_evento TEXT,
    descricao TEXT,
    created_by TEXT NOT NULL,
    responsavel UUID REFERENCES users(id) ON DELETE SET NULL,
    status TEXT CHECK (status IN ('agendado','realizado','cancelado')) DEFAULT 'agendado',
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);

CREATE TABLE IF NOT EXISTS escalas (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  culto_evento_id UUID REFERENCES eventos(id) ON DELETE CASCADE,
  membro_id UUID REFERENCES igreja_membros(id) ON DELETE CASCADE,
  funcao TEXT NOT NULL,
  observacoes TEXT,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== SOCIAL ====================
CREATE TABLE IF NOT EXISTS posts (
  id BIGSERIAL PRIMARY KEY,
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE SET NULL,
  author_id UUID REFERENCES users(id) ON DELETE SET NULL,
  titulo TEXT,
  content TEXT NULL,
  -- Campos para armazenamento de mídia no Supabase
  media_url TEXT,                    -- URL completa do arquivo no Supabase
  media_nome TEXT,                   -- Nome original do arquivo
  media_tamanho BIGINT,              -- Tamanho em bytes
  media_mime_type TEXT,              -- Tipo MIME do arquivo
  media_type TEXT,                   -- Tipo: 'image', 'video', 'audio', 'file'
  is_video BOOLEAN DEFAULT FALSE,    -- Flag específico para vídeos
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS post_reactions (
  post_id BIGINT REFERENCES posts(id) ON DELETE CASCADE,
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  reaction TEXT CHECK (reaction IN ('like','love','haha','wow','sad','angry')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  PRIMARY KEY (post_id, user_id)
);

-- ==================== AUDITORIA ====================
CREATE TABLE IF NOT EXISTS auditoria_logs (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  tabela TEXT NOT NULL,
  registro_id TEXT NOT NULL,
  acao TEXT NOT NULL CHECK (acao IN ('insert','update','delete')),
  usuario_id UUID REFERENCES users(id) ON DELETE SET NULL,
  data_acao TIMESTAMPTZ NOT NULL DEFAULT now(),
  valores JSONB,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ==================== COMUNICAÇÃO INTERNA ====================
CREATE TABLE IF NOT EXISTS comunicacoes (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  titulo TEXT NOT NULL,
  conteudo TEXT NOT NULL,
  tipo TEXT CHECK (tipo IN ('notificacao','aviso','campanha_oracao','diretiva')),
  destino TEXT CHECK (destino IN ('sede','filial','todos')),
  data_envio TIMESTAMPTZ NOT NULL DEFAULT now(),
  enviado_por UUID REFERENCES users(id) ON DELETE SET NULL,
  status TEXT NOT NULL DEFAULT 'enviado' CHECK (status IN ('rascunho','enviado','lido')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS mensagens_privadas (
   id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
   remetente_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
   destinatario_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
   tipo_mensagem VARCHAR(20) DEFAULT 'texto' CHECK (tipo_mensagem IN ('texto', 'imagem', 'audio', 'video', 'arquivo', 'localizacao')),
   conteudo TEXT, -- pode ser NULL para mensagens com mídia
   anexo_url TEXT, -- URL do arquivo no Supabase
   anexo_nome TEXT, -- nome original do arquivo
   anexo_tamanho BIGINT, -- tamanho em bytes
   anexo_tipo TEXT, -- MIME type
   duracao_audio INTEGER, -- duração em segundos (para áudio)
   latitude DECIMAL(10,8), -- para localização
   longitude DECIMAL(11,8), -- para localização
   lida_por JSONB DEFAULT '[]'::jsonb, -- array de IDs que leram a mensagem
   limpada_por_remetente BOOLEAN DEFAULT FALSE, -- indica se o remetente limpou a conversa
   limpada_por_destinatario BOOLEAN DEFAULT FALSE, -- indica se o destinatário limpou a conversa
   created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
   updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS igreja_chat_mensagens (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  chat_id UUID NOT NULL REFERENCES igreja_chats(id) ON DELETE CASCADE,
  autor_id UUID REFERENCES users(id) ON DELETE SET NULL,
  tipo_mensagem VARCHAR(20) DEFAULT 'texto' CHECK (tipo_mensagem IN ('texto', 'imagem', 'audio', 'video', 'arquivo', 'localizacao')),
  conteudo TEXT, -- pode ser NULL para mensagens com mídia
  anexo_url TEXT, -- URL do arquivo no Supabase (para áudio, imagem, etc.)
  anexo_nome TEXT, -- nome original do arquivo
  anexo_tamanho BIGINT, -- tamanho em bytes
  anexo_tipo TEXT, -- MIME type
  duracao_audio INTEGER, -- duração em segundos (para áudio)
  latitude DECIMAL(10,8), -- para localização
  longitude DECIMAL(11,8), -- para localização
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  lida_por JSONB DEFAULT '[]'
);

CREATE TABLE IF NOT EXISTS notificacoes (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  tipo TEXT NOT NULL,
  referencia_id UUID,
  titulo TEXT,
  mensagem TEXT,
  lida BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- =========================================================
-- MÓDULO DE CURSOS PRESENCIAIS (SIMPLIFICADO)
-- =========================================================

-- Tipos de curso
DROP TYPE IF EXISTS curso_tipo_enum CASCADE;
CREATE TYPE curso_tipo_enum AS ENUM (
    'escola_dominical',
    'preparacao_batismo',
    'curso_membros',
    'lideranca',
    'ministerial',
    'casais',
    'jovens',
    'outro'
);

-- Status do curso / turma
DROP TYPE IF EXISTS curso_status_enum CASCADE;
CREATE TYPE curso_status_enum AS ENUM ('planejado','ativo','concluido','suspenso','cancelado');

-- ==================== CURSOS ====================
DROP TABLE IF EXISTS cursos CASCADE;
CREATE TABLE cursos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,

    nome VARCHAR(255) NOT NULL,
    tipo curso_tipo_enum NOT NULL DEFAULT 'outro',
    descricao TEXT,
    objetivo TEXT,

    carga_horaria_total INT,
    duracao_semanas INT,

    status curso_status_enum DEFAULT 'planejado',
    data_inicio DATE,
    data_fim DATE,

    instrutor_principal UUID REFERENCES users(id) ON DELETE SET NULL,
    coordenador UUID REFERENCES users(id) ON DELETE SET NULL,

    vagas_maximo INT,
    certificado_obrigatorio BOOLEAN DEFAULT FALSE,
    frequencia_minima INT DEFAULT 75,

    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);

-- ==================== TURMAS ====================
CREATE TABLE curso_turmas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    curso_id UUID NOT NULL REFERENCES cursos(id) ON DELETE CASCADE,

    nome VARCHAR(100) NOT NULL,   -- ex: "Turma A - 2025"
    codigo VARCHAR(20),           -- ex: "BAT-2025-A"

    data_inicio DATE NOT NULL,
    data_fim DATE,

    dia_semana INT CHECK (dia_semana BETWEEN 0 AND 6), -- 0=Dom, 6=Sáb
    hora_inicio TIME,
    hora_fim TIME,

    local VARCHAR(100),

    vagas_maximo INT DEFAULT 30,
    vagas_ocupadas INT DEFAULT 0,

    status curso_status_enum DEFAULT 'planejado',

    instrutor_id UUID REFERENCES users(id) ON DELETE SET NULL,

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);

-- ==================== MATRÍCULAS ====================
CREATE TABLE curso_matriculas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    turma_id UUID NOT NULL REFERENCES curso_turmas(id) ON DELETE CASCADE,
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,

    data_matricula DATE DEFAULT CURRENT_DATE,
    status VARCHAR(20) CHECK (status IN ('ativo','concluido','desistente','transferido','suspenso')) DEFAULT 'ativo',

    apto BOOLEAN DEFAULT FALSE,
    data_apto DATE,
    certificado_emitido BOOLEAN DEFAULT FALSE,
    data_certificado DATE,

    observacoes TEXT,

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),

    UNIQUE(turma_id, membro_id)
);

-- ==================== CERTIFICADOS (Opcional) ====================
CREATE TABLE curso_certificados (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    matricula_id UUID NOT NULL REFERENCES curso_matriculas(id) ON DELETE CASCADE,

    numero_certificado VARCHAR(50) UNIQUE,
    data_emissao DATE DEFAULT CURRENT_DATE,
    data_conclusao DATE,

    frequencia_final NUMERIC(5,2),

    template_usado VARCHAR(100),
    codigo_verificacao VARCHAR(100) UNIQUE,
    valido_ate DATE,

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== ÍNDICES ====================
CREATE INDEX idx_cursos_igreja ON cursos(igreja_id);
CREATE INDEX idx_cursos_tipo ON cursos(tipo);
CREATE INDEX idx_cursos_status ON cursos(status);

CREATE INDEX idx_turmas_curso ON curso_turmas(curso_id);
CREATE INDEX idx_turmas_data_inicio ON curso_turmas(data_inicio);

CREATE INDEX idx_matriculas_turma ON curso_matriculas(turma_id);
CREATE INDEX idx_matriculas_membro ON curso_matriculas(membro_id);
CREATE INDEX idx_matriculas_status ON curso_matriculas(status);


-- =========================================================
-- TIPOS DE PEDIDO E PEDIDOS ESPECIAIS
-- =========================================================

-- Tipos de pedido configuráveis por categoria
DROP TABLE IF EXISTS pedido_tipos CASCADE;
CREATE TABLE pedido_tipos (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    nome VARCHAR(100) NOT NULL,             -- ex: "Batismo", "Casamento", "Crisma"
    descricao TEXT,
    categoria_id BIGINT REFERENCES categorias_igrejas(id) ON DELETE CASCADE,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE, -- Nova coluna: tipos específicos por igreja
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Pedidos feitos pelos membros
DROP TABLE IF EXISTS pedidos_especiais CASCADE;
CREATE TABLE pedidos_especiais (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,

    pedido_tipo_id UUID NOT NULL REFERENCES pedido_tipos(id) ON DELETE RESTRICT,
    descricao TEXT,
    curso_id UUID REFERENCES cursos(id) ON DELETE SET NULL,

    status pedido_status_enum DEFAULT 'pendente',
    data_pedido DATE DEFAULT CURRENT_DATE,
    data_resolucao DATE,

    responsavel_id UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- Índices
CREATE INDEX idx_pedido_tipo ON pedidos_especiais(pedido_tipo_id);
CREATE INDEX idx_pedido_igreja ON pedidos_especiais(igreja_id);


-- Relatórios e Estatísticas
CREATE TABLE IF NOT EXISTS relatorios_cache (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  tipo TEXT NOT NULL,
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  periodo TEXT,
  dados JSONB,
  gerado_em TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Gestão de Recursos
CREATE TABLE IF NOT EXISTS recursos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  nome TEXT NOT NULL,
  tipo TEXT NOT NULL,
  descricao TEXT,
  disponivel BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS agendamentos_recursos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  recurso_id UUID REFERENCES recursos(id) ON DELETE CASCADE,
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  inicio TIMESTAMPTZ NOT NULL,
  fim TIMESTAMPTZ NOT NULL,
  reservado_por UUID REFERENCES users(id) ON DELETE SET NULL,
  status TEXT DEFAULT 'pendente' CHECK (status IN ('pendente','aprovado','rejeitado','concluido')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Doações Online
CREATE TABLE IF NOT EXISTS doacoes_online (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  user_id UUID REFERENCES users(id) ON DELETE SET NULL,
  valor NUMERIC(14,2) NOT NULL,
  metodo TEXT CHECK (metodo IN ('cash','multicaixa_express','transferencia','deposito','tpa')),
  referencia TEXT,
  data TIMESTAMPTZ DEFAULT now(),
  status TEXT DEFAULT 'confirmado' CHECK (status IN ('pendente','confirmado','falhou')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Voluntariado
CREATE TABLE IF NOT EXISTS voluntarios (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  membro_id UUID REFERENCES igreja_membros(id) ON DELETE CASCADE,
  area_interesse TEXT,
  disponibilidade TEXT,
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS escala_auto (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  voluntario_id UUID REFERENCES voluntarios(id) ON DELETE CASCADE,
  data DATE NOT NULL,
  funcao TEXT,
  status TEXT DEFAULT 'agendado' CHECK (status IN ('agendado','concluido','cancelado')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Módulo Pastoral
CREATE TABLE IF NOT EXISTS atendimentos_pastorais (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  membro_id UUID REFERENCES igreja_membros(id) ON DELETE SET NULL,
  pastor_id UUID REFERENCES users(id) ON DELETE SET NULL,
  tipo TEXT CHECK (tipo IN ('visita','aconselhamento','oracao','encorajamento','outro')),
  descricao TEXT,
  data_atendimento TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS pedidos_oracao (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  membro_id UUID REFERENCES igreja_membros(id) ON DELETE SET NULL,
  pedido TEXT NOT NULL,
  atendido BOOLEAN DEFAULT FALSE,
  data_pedido TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Gamificação
CREATE TABLE IF NOT EXISTS engajamento_pontos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  pontos INT DEFAULT 0,
  motivo TEXT,
  data TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS engajamento_badges (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID REFERENCES users(id) ON DELETE CASCADE,
  igreja_id BIGINT REFERENCES igrejas(id) ON DELETE CASCADE,
  badge TEXT NOT NULL,
  descricao TEXT,
  data TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);


-- =========================================================
-- OMNIGREJAS • MIGRAÇÃO INCREMENTAL
-- Novas tabelas: enquete_denuncias, financeiro_auditoria, agenda
-- =========================================================

-- ==================== ENQUETE_DENUNCIAS ====================
CREATE TABLE IF NOT EXISTS enquete_denuncias (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  texto TEXT NOT NULL,
  data TIMESTAMPTZ NOT NULL DEFAULT now(),
  criado_por UUID REFERENCES users(id) ON DELETE SET NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ==================== FINANCEIRO_AUDITORIA ====================
CREATE TABLE IF NOT EXISTS financeiro_auditoria (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  movimento_id UUID NOT NULL REFERENCES financeiro_movimentos(id) ON DELETE CASCADE,
  valor_anterior NUMERIC(14,2),
  valor_novo NUMERIC(14,2),
  detalhes JSONB DEFAULT '{}'::jsonb,
  alterado_por UUID REFERENCES users(id) ON DELETE SET NULL,
  data_alteracao TIMESTAMPTZ NOT NULL DEFAULT now(),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);


-- ==================== AGENDA ====================
CREATE TABLE IF NOT EXISTS agenda (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  evento_id UUID REFERENCES eventos(id) ON DELETE CASCADE, -- Nullable para lembretes do sistema
  agendamento_id UUID REFERENCES agendamentos(id) ON DELETE CASCADE, -- Nullable para lembretes gerais
  lembrete INTERVAL,
  status TEXT CHECK (status IN ('pendente','confirmado','cancelado')) NOT NULL DEFAULT 'pendente',
  tipo_lembrete VARCHAR(20) DEFAULT 'sistema' CHECK (tipo_lembrete IN ('sistema', 'evento', 'agendamento')),
  titulo_personalizado VARCHAR(255),
  mensagem_personalizada TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);


-- ==================== MARKETPLACE ====================

-- Produtos/Serviços oferecidos por uma igreja
CREATE TABLE IF NOT EXISTS marketplace_produtos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  nome TEXT NOT NULL,
  descricao TEXT,
  preco NUMERIC(14,2) NOT NULL CHECK (preco >= 0),
  estoque INT DEFAULT 0 CHECK (estoque >= 0),
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Pedidos feitos por membros, pastores ou até usuários anônimos
CREATE TABLE IF NOT EXISTS marketplace_pedidos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  produto_id UUID NOT NULL REFERENCES marketplace_produtos(id) ON DELETE CASCADE,
  igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
  comprador_id UUID REFERENCES users(id) ON DELETE SET NULL,
  quantidade INT NOT NULL DEFAULT 1 CHECK (quantidade > 0),
  status TEXT NOT NULL DEFAULT 'pendente' CHECK (status IN ('pendente','pago','enviado','concluido','cancelado')),
  data_pedido TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);

-- Pagamentos associados aos pedidos
CREATE TABLE IF NOT EXISTS marketplace_pagamentos (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  pedido_id UUID NOT NULL REFERENCES marketplace_pedidos(id) ON DELETE CASCADE,
  metodo TEXT CHECK (metodo IN ('multicaixa_express','cash','tpa','deposito')),
  valor NUMERIC(14,2) NOT NULL CHECK (valor >= 0),
  referencia TEXT,
  status TEXT NOT NULL DEFAULT 'pendente' CHECK (status IN ('pendente','confirmado','falhou','estornado')),
  data_pagamento TIMESTAMPTZ DEFAULT now(),
  created_at TIMESTAMPTZ DEFAULT now(),
  updated_at TIMESTAMPTZ DEFAULT now()
);



-- ===============================================
-- Cultos semanais fixos (recorrentes)
-- ===============================================
DROP TABLE IF EXISTS cultos_padrao CASCADE;
CREATE TABLE IF NOT EXISTS cultos_padrao (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    dia_semana INT NOT NULL CHECK (dia_semana BETWEEN 0 AND 6), -- 0=Dom, 6=Sáb
    hora_inicio TIME NOT NULL,
    hora_fim TIME,
    titulo TEXT NOT NULL,
    descricao TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);



-- ===============================================
-- Tabela de Comentários (para posts e eventos)
-- ===============================================
CREATE TABLE IF NOT EXISTS comentarios (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    post_id BIGINT REFERENCES posts(id) ON DELETE CASCADE,
    evento_id UUID REFERENCES eventos(id) ON DELETE CASCADE,
    comentario_pai UUID REFERENCES comentarios(id) ON DELETE CASCADE,
    conteudo TEXT NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);


-- ===============================================
-- Habilidades dos Membros (Mapa de Talentos)
-- ===============================================
CREATE TABLE IF NOT EXISTS habilidades_membros (
  membro_id UUID REFERENCES igreja_membros(id) ON DELETE CASCADE,
  habilidade TEXT NOT NULL,
  nivel TEXT CHECK (nivel IN ('iniciante','intermediario','avancado')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  PRIMARY KEY (membro_id, habilidade)
);




-- Índices auxiliares
CREATE INDEX idx_marketplace_produtos_igreja ON marketplace_produtos(igreja_id);
CREATE INDEX idx_marketplace_pedidos_comprador ON marketplace_pedidos(comprador_id);
CREATE INDEX idx_marketplace_pedidos_igreja ON marketplace_pedidos(igreja_id);
CREATE INDEX idx_marketplace_pagamentos_pedido ON marketplace_pagamentos(pedido_id);

-- Índices para mensagens com mídia
CREATE INDEX IF NOT EXISTS idx_igreja_chat_mensagens_tipo ON igreja_chat_mensagens(tipo_mensagem);
CREATE INDEX IF NOT EXISTS idx_igreja_chat_mensagens_anexo ON igreja_chat_mensagens(anexo_url) WHERE anexo_url IS NOT NULL;
CREATE INDEX IF NOT EXISTS idx_igreja_chat_mensagens_autor_tipo ON igreja_chat_mensagens(autor_id, tipo_mensagem);

-- Índices para mensagens privadas
CREATE INDEX IF NOT EXISTS idx_mensagens_privadas_remetente ON mensagens_privadas(remetente_id);
CREATE INDEX IF NOT EXISTS idx_mensagens_privadas_destinatario ON mensagens_privadas(destinatario_id);
CREATE INDEX IF NOT EXISTS idx_mensagens_privadas_tipo ON mensagens_privadas(tipo_mensagem);
CREATE INDEX IF NOT EXISTS idx_mensagens_privadas_anexo ON mensagens_privadas(anexo_url) WHERE anexo_url IS NOT NULL;
CREATE INDEX IF NOT EXISTS idx_mensagens_privadas_created_at ON mensagens_privadas(created_at);

-- Índices para participantes do chat
CREATE INDEX IF NOT EXISTS idx_igreja_chat_participantes_chat ON igreja_chat_participantes(chat_id);
CREATE INDEX IF NOT EXISTS idx_igreja_chat_participantes_user ON igreja_chat_participantes(user_id);
CREATE INDEX IF NOT EXISTS idx_igreja_chat_participantes_admin ON igreja_chat_participantes(chat_id, is_admin) WHERE is_admin = true;
CREATE INDEX IF NOT EXISTS idx_igreja_chat_participantes_added_by ON igreja_chat_participantes(added_by);
CREATE INDEX IF NOT EXISTS idx_igreja_chat_participantes_status ON igreja_chat_participantes(chat_id, status);



-- ==================== ÍNDICES ====================
CREATE INDEX idx_igreja_membros_user ON igreja_membros(user_id);
CREATE INDEX idx_igreja_membros_igreja ON igreja_membros(igreja_id);
CREATE INDEX idx_posts_igreja ON posts(igreja_id);
CREATE INDEX idx_financeiro_data ON financeiro_movimentos(data_transacao);

-- Função para gerar numero_membro automaticamente
CREATE OR REPLACE FUNCTION gerar_numero_membro()
RETURNS TRIGGER AS $$
DECLARE
    novo_numero INT;
BEGIN
    -- Só gera se o campo estiver vazio
    IF NEW.numero_membro IS NULL THEN
        -- Pega o próximo número dentro da mesma igreja
        SELECT COALESCE(MAX(SPLIT_PART(numero_membro, '-', 2)::INT), 0) + 1
        INTO novo_numero
        FROM igreja_membros
        WHERE igreja_id = NEW.igreja_id;

        -- Define numero_membro no formato Omnigreja-<n>
        NEW.numero_membro := 'Omnigreja-' || novo_numero::TEXT;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;



-- Função para definir igreja principal de um membro
DROP TRIGGER IF EXISTS trg_gerar_numero_membro ON igreja_membros;
CREATE TRIGGER trg_gerar_numero_membro
BEFORE INSERT ON igreja_membros
FOR EACH ROW
EXECUTE FUNCTION gerar_numero_membro();


CREATE OR REPLACE FUNCTION set_principal_igreja_membro()
RETURNS TRIGGER AS $$
BEGIN
  -- Só define como principal se não foi definido explicitamente
  -- Isso permite que o código controle o valor de principal
  IF NEW.principal IS NULL AND (SELECT tipo FROM igrejas WHERE id = NEW.igreja_id) IN ('sede', 'independente') THEN
    NEW.principal := true;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Função para tornar o criador do chat automaticamente admin do grupo
CREATE OR REPLACE FUNCTION set_admin_criador_chat()
RETURNS TRIGGER AS $$
BEGIN
  -- O criador do chat (criado_por da tabela igreja_chats) se torna admin automaticamente
  IF NEW.user_id = (SELECT criado_por FROM igreja_chats WHERE id = NEW.chat_id) THEN
    NEW.is_admin := true;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_set_principal_igreja_membro ON igreja_membros;
CREATE TRIGGER trg_set_principal_igreja_membro
BEFORE INSERT ON igreja_membros
FOR EACH ROW
EXECUTE FUNCTION set_principal_igreja_membro();

-- ========================================
-- TRIGGER PARA GERAR NÚMERO APÓS MIGRAÇÃO
-- ========================================
-- Função para gerar número de membro após migração
CREATE OR REPLACE FUNCTION gerar_numero_membro_migracao()
RETURNS TRIGGER AS $$
DECLARE
    novo_numero INT;
BEGIN
    -- Só executar se houve mudança de igreja E o numero_membro está NULL
    IF (TG_OP = 'UPDATE' AND
        OLD.igreja_id != NEW.igreja_id AND
        (NEW.numero_membro IS NULL OR NEW.numero_membro = '')) THEN

        -- Pega o próximo número dentro da nova igreja
        SELECT COALESCE(MAX(SPLIT_PART(numero_membro, '-', 2)::INT), 0) + 1
        INTO novo_numero
        FROM igreja_membros
        WHERE igreja_id = NEW.igreja_id;

        -- Define numero_membro no formato Omnigreja-<n>
        NEW.numero_membro := 'Omnigreja-' || novo_numero::TEXT;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Criar trigger para migrações
DROP TRIGGER IF EXISTS trg_gerar_numero_migracao ON igreja_membros;
CREATE TRIGGER trg_gerar_numero_migracao
BEFORE UPDATE ON igreja_membros
FOR EACH ROW
EXECUTE FUNCTION gerar_numero_membro_migracao();

-- Criar trigger para participantes do chat
DROP TRIGGER IF EXISTS trg_set_admin_criador_chat ON igreja_chat_participantes;
CREATE TRIGGER trg_set_admin_criador_chat
BEFORE INSERT ON igreja_chat_participantes
FOR EACH ROW
EXECUTE FUNCTION set_admin_criador_chat();




-- Índices para agendamentos
CREATE INDEX idx_agendamentos_data_status ON agendamentos(data_agendamento, status);
CREATE INDEX idx_agendamentos_organizador ON agendamentos(organizador_id, data_agendamento);
CREATE INDEX idx_agendamentos_convidado ON agendamentos(convidado_id, data_agendamento);
CREATE INDEX idx_agendamentos_status ON agendamentos(status);

-- Novos índices para alianças e igrejas
CREATE INDEX idx_agendamentos_igreja_data ON agendamentos(igreja_id, data_agendamento);
CREATE INDEX idx_agendamentos_alianca_data ON agendamentos(alianca_id, data_agendamento);
CREATE INDEX idx_agendamentos_igreja_alianca ON agendamentos(igreja_id, alianca_id);

-- Índices para agenda
CREATE INDEX IF NOT EXISTS idx_agenda_tipo_lembrete_status ON agenda(tipo_lembrete, status);
CREATE INDEX IF NOT EXISTS idx_agenda_agendamento ON agenda(agendamento_id);

-- =========================================================
-- ATUALIZAÇÃO: Sistema de Alianças Refatorado
-- Nova tabela alianca_lideres, limite_membros e igreja_aliancas (pivot)
-- Migração: 2025_09_06_013620_add_limite_membros_to_aliancas_igrejas_table
-- =========================================================
-- ==================== IGREJA_ALIANCAS ====================
-- Tabela pivot para relacionamentos muitos-para-muitos entre igrejas e alianças
CREATE TABLE IF NOT EXISTS igreja_aliancas (
    id BIGSERIAL PRIMARY KEY,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    alianca_id BIGINT NOT NULL REFERENCES aliancas_igrejas(id) ON DELETE CASCADE,
    status VARCHAR(20) DEFAULT 'ativo' CHECK (status IN ('ativo', 'inativo', 'suspenso')),
    data_adesao TIMESTAMPTZ DEFAULT now(),
    data_desligamento TIMESTAMPTZ NULL,
    observacoes TEXT,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade: uma igreja não pode estar na mesma aliança duas vezes
    UNIQUE(igreja_id, alianca_id)
);



-- ==================== ALIANCA_LIDERES ====================
-- Tabela para controlar participação individual de líderes nas alianças
-- ATUALIZADA: Migração 2025_09_06_024102 - Unificada com igreja_alianca_id
CREATE TABLE IF NOT EXISTS alianca_lideres (
    id BIGSERIAL PRIMARY KEY,
    igreja_alianca_id BIGINT NOT NULL REFERENCES igreja_aliancas(id) ON DELETE CASCADE,
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    cargo_na_alianca VARCHAR(255), -- admin, pastor, ministro, etc.
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_adesao TIMESTAMPTZ DEFAULT now(),
    data_desligamento TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade: um membro não pode estar na mesma participação duas vezes
    UNIQUE(igreja_alianca_id, membro_id)
);


CREATE INDEX IF NOT EXISTS idx_igreja_aliancas_status ON igreja_aliancas(status);
CREATE INDEX IF NOT EXISTS idx_igreja_aliancas_data_adesao ON igreja_aliancas(data_adesao);

-- Índices para alianca_lideres
CREATE INDEX IF NOT EXISTS idx_alianca_lideres_igreja_alianca ON alianca_lideres(igreja_alianca_id);
CREATE INDEX IF NOT EXISTS idx_alianca_lideres_membro ON alianca_lideres(membro_id);
CREATE INDEX IF NOT EXISTS idx_alianca_lideres_ativo ON alianca_lideres(ativo);

-- ==================== ATUALIZAÇÃO ALIANCAS_IGREJAS ====================
-- Adiciona campo limite_membros e remove min_aderentes
ALTER TABLE aliancas_igrejas
DROP COLUMN IF EXISTS min_aderentes,
ADD COLUMN IF NOT EXISTS limite_membros INTEGER;

-- Migração específica para limite_membros (caso não tenha sido executada)
-- 2025_09_06_013620_add_limite_membros_to_aliancas_igrejas_table
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_name = 'aliancas_igrejas' AND column_name = 'limite_membros'
    ) THEN
        ALTER TABLE aliancas_igrejas ADD COLUMN limite_membros INTEGER;
    END IF;
END $$;

-- Atualiza comentários na tabela
COMMENT ON TABLE igreja_aliancas IS 'Relacionamentos muitos-para-muitos entre igrejas e alianças';
COMMENT ON COLUMN igreja_aliancas.status IS 'Status da participação: ativo, inativo, suspenso';
COMMENT ON COLUMN igreja_aliancas.data_adesao IS 'Data em que a igreja aderiu à aliança';
COMMENT ON COLUMN igreja_aliancas.data_desligamento IS 'Data em que a igreja se desligou (NULL = ainda ativa)';

COMMENT ON COLUMN aliancas_igrejas.limite_membros IS 'Limite opcional de membros (NULL = ilimitado)';
COMMENT ON COLUMN aliancas_igrejas.aderentes_count IS 'Contador atual de igrejas aderidas';
COMMENT ON TABLE alianca_lideres IS 'Controle individual de participação de líderes nas alianças';

-- =========================================================
-- SISTEMA DE MENSAGENS DE ALIANÇA
-- Tabelas: alianca_mensagens e alianca_mensagem_leituras
-- =========================================================

-- ==================== ALIANCA_MENSAGENS ====================
CREATE TABLE IF NOT EXISTS alianca_mensagens (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID UNIQUE DEFAULT gen_random_uuid(),
    alianca_id BIGINT NOT NULL REFERENCES aliancas_igrejas(id) ON DELETE CASCADE,
    remetente_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    tipo_mensagem VARCHAR(20) DEFAULT 'texto' CHECK (tipo_mensagem IN ('texto', 'imagem', 'audio', 'video', 'arquivo', 'localizacao')),
    mensagem TEXT NULL, -- Permitir NULL para mensagens com apenas anexos
    anexos JSONB NULL, -- Para arquivos anexados
    lida_em TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Índices para alianca_mensagens
CREATE INDEX IF NOT EXISTS idx_alianca_mensagens_alianca_created ON alianca_mensagens(alianca_id, created_at);
CREATE INDEX IF NOT EXISTS idx_alianca_mensagens_remetente_alianca ON alianca_mensagens(remetente_id, alianca_id);

-- ==================== ALIANCA_MENSAGEM_LEITURAS ====================
CREATE TABLE IF NOT EXISTS alianca_mensagem_leituras (
    id BIGSERIAL PRIMARY KEY,
    mensagem_id BIGINT NOT NULL REFERENCES alianca_mensagens(id) ON DELETE CASCADE,
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    lida_em TIMESTAMPTZ NOT NULL DEFAULT now(),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);



-- Comentários das tabelas
COMMENT ON TABLE alianca_mensagens IS 'Mensagens enviadas nos chats das alianças de igrejas';
COMMENT ON COLUMN alianca_mensagens.uuid IS 'UUID único para identificação externa da mensagem';
COMMENT ON COLUMN alianca_mensagens.anexos IS 'JSON com informações de arquivos anexados (URLs, tipos, etc.)';
COMMENT ON COLUMN alianca_mensagens.lida_em IS 'Timestamp de quando a mensagem foi lida (deprecated - usar alianca_mensagem_leituras)';

-- =========================================================
-- FIM DO SISTEMA DE MENSAGENS DE ALIANÇA
-- =========================================================

-- =========================================================
-- FIM DAS ATUALIZAÇÕES DO SISTEMA DE ALIANÇAS
-- =========================================================

-- =========================================================
-- SISTEMA DE COMUNIDADE GERAL DE MEMBROS DAS ALIANÇAS
-- Tabelas para participação geral de todos os membros nas alianças
-- =========================================================

-- ==================== ALIANCA_MEMBROS_GERAIS ====================
-- Tabela para participação geral de todos os membros nas alianças
CREATE TABLE IF NOT EXISTS alianca_membros_gerais (
    id BIGSERIAL PRIMARY KEY,
    igreja_alianca_id BIGINT NOT NULL REFERENCES igreja_aliancas(id) ON DELETE CASCADE,
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    cargo_na_alianca role_enum DEFAULT 'membro',
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    data_adesao TIMESTAMPTZ DEFAULT now(),
    data_desligamento TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade: um membro não pode estar na mesma aliança duas vezes
    UNIQUE(igreja_alianca_id, membro_id)
);

-- ==================== ALIANCA_COMUNIDADE_MENSAGENS ====================
-- Mensagens da comunidade geral (todos os membros)
CREATE TABLE IF NOT EXISTS alianca_comunidade_mensagens (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID UNIQUE DEFAULT gen_random_uuid(),
    alianca_id BIGINT NOT NULL REFERENCES aliancas_igrejas(id) ON DELETE CASCADE,
    remetente_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    tipo_mensagem VARCHAR(20) DEFAULT 'texto' CHECK (tipo_mensagem IN ('texto', 'imagem', 'audio', 'video', 'arquivo', 'localizacao')),
    mensagem TEXT NULL, -- Permitir NULL para mensagens com apenas anexos
    anexos JSONB NULL, -- Para arquivos anexados
    lida_em TIMESTAMPTZ NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- ==================== ALIANCA_COMUNIDADE_LEITURAS ====================
-- Controle de leitura das mensagens da comunidade geral
CREATE TABLE IF NOT EXISTS alianca_comunidade_leituras (
    id BIGSERIAL PRIMARY KEY,
    mensagem_id BIGINT NOT NULL REFERENCES alianca_comunidade_mensagens(id) ON DELETE CASCADE,
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    lida_em TIMESTAMPTZ NOT NULL DEFAULT now(),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade: um membro só pode ler uma mensagem uma vez
    UNIQUE(mensagem_id, membro_id)
);

-- Índices para alianca_membros_gerais
CREATE INDEX IF NOT EXISTS idx_alianca_membros_gerais_igreja_alianca ON alianca_membros_gerais(igreja_alianca_id);
CREATE INDEX IF NOT EXISTS idx_alianca_membros_gerais_membro ON alianca_membros_gerais(membro_id);
CREATE INDEX IF NOT EXISTS idx_alianca_membros_gerais_ativo ON alianca_membros_gerais(ativo);
CREATE INDEX IF NOT EXISTS idx_alianca_membros_gerais_data_adesao ON alianca_membros_gerais(data_adesao);

-- Índices para alianca_comunidade_mensagens
CREATE INDEX IF NOT EXISTS idx_alianca_comunidade_mensagens_alianca_created ON alianca_comunidade_mensagens(alianca_id, created_at);
CREATE INDEX IF NOT EXISTS idx_alianca_comunidade_mensagens_remetente_alianca ON alianca_comunidade_mensagens(remetente_id, alianca_id);

-- Índices para alianca_comunidade_leituras
CREATE INDEX IF NOT EXISTS idx_alianca_comunidade_leituras_membro_lida ON alianca_comunidade_leituras(membro_id, lida_em);

-- Comentários das tabelas
COMMENT ON TABLE alianca_membros_gerais IS 'Participação geral de todos os membros nas alianças de igrejas';
COMMENT ON COLUMN alianca_membros_gerais.igreja_alianca_id IS 'Referência à participação da igreja na aliança';
COMMENT ON COLUMN alianca_membros_gerais.membro_id IS 'Referência ao membro da igreja';
COMMENT ON COLUMN alianca_membros_gerais.cargo_na_alianca IS 'Cargo do membro na comunidade geral (igual ao cargo na igreja)';

COMMENT ON TABLE alianca_comunidade_mensagens IS 'Mensagens enviadas na comunidade geral das alianças';
COMMENT ON COLUMN alianca_comunidade_mensagens.uuid IS 'UUID único para identificação externa da mensagem';
COMMENT ON COLUMN alianca_comunidade_mensagens.anexos IS 'JSON com informações de arquivos anexados (URLs, tipos, etc.)';

COMMENT ON TABLE alianca_comunidade_leituras IS 'Controle de leitura individual das mensagens da comunidade geral';

-- ==================== FUNÇÕES PARA GERENCIAMENTO ====================

-- Função para adicionar todos os membros de uma igreja à comunidade geral da aliança
CREATE OR REPLACE FUNCTION adicionar_todos_membros_alianca(
    p_igreja_alianca_id BIGINT
) RETURNS VOID AS $$
BEGIN
    -- Adicionar todos os membros ativos da igreja à comunidade geral
    INSERT INTO alianca_membros_gerais (
        igreja_alianca_id, membro_id, cargo_na_alianca
    )
    SELECT
        p_igreja_alianca_id,
        im.id,
        im.cargo
    FROM igreja_membros im
    JOIN igreja_aliancas ia ON ia.igreja_id = im.igreja_id
    WHERE ia.id = p_igreja_alianca_id
      AND im.status = 'ativo'
      AND im.deleted_at IS NULL
    ON CONFLICT (igreja_alianca_id, membro_id) DO NOTHING;
END;
$$ LANGUAGE plpgsql;

-- Função para adicionar um membro específico à comunidade geral
CREATE OR REPLACE FUNCTION adicionar_membro_alianca_comunidade(
    p_igreja_alianca_id BIGINT,
    p_membro_id UUID
) RETURNS VOID AS $$
DECLARE
    v_cargo_membro role_enum;
BEGIN
    -- Buscar o cargo do membro
    SELECT cargo INTO v_cargo_membro
    FROM igreja_membros
    WHERE id = p_membro_id;

    -- Adicionar à comunidade geral
    INSERT INTO alianca_membros_gerais (
        igreja_alianca_id, membro_id, cargo_na_alianca
    ) VALUES (
        p_igreja_alianca_id,
        p_membro_id,
        v_cargo_membro
    ) ON CONFLICT (igreja_alianca_id, membro_id) DO NOTHING;
END;
$$ LANGUAGE plpgsql;

-- Função para remover um membro da comunidade geral
CREATE OR REPLACE FUNCTION remover_membro_alianca_comunidade(
    p_igreja_alianca_id BIGINT,
    p_membro_id UUID
) RETURNS VOID AS $$
BEGIN
    -- Marcar como inativo (soft delete)
    UPDATE alianca_membros_gerais
    SET ativo = FALSE,
        data_desligamento = now(),
        updated_at = now()
    WHERE igreja_alianca_id = p_igreja_alianca_id
      AND membro_id = p_membro_id
      AND ativo = TRUE;
END;
$$ LANGUAGE plpgsql;

-- ==================== TRIGGERS AUTOMÁTICOS ====================

-- Trigger para adicionar membros automaticamente quando igreja entra na aliança
CREATE OR REPLACE FUNCTION trigger_adicionar_membros_alianca()
RETURNS TRIGGER AS $$
BEGIN
    -- Quando uma igreja entra na aliança (status = 'ativo')
    IF NEW.status = 'ativo' AND (OLD.status IS NULL OR OLD.status != 'ativo') THEN
        PERFORM adicionar_todos_membros_alianca(NEW.id);
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger para adicionar novo membro quando ele é adicionado à igreja
CREATE OR REPLACE FUNCTION trigger_adicionar_novo_membro_alianca()
RETURNS TRIGGER AS $$
DECLARE
    v_alianca RECORD;
BEGIN
    -- Para cada aliança ativa da igreja, adicionar o novo membro
    FOR v_alianca IN
        SELECT ia.id as igreja_alianca_id
        FROM igreja_aliancas ia
        WHERE ia.igreja_id = NEW.igreja_id
          AND ia.status = 'ativo'
    LOOP
        PERFORM adicionar_membro_alianca_comunidade(v_alianca.igreja_alianca_id, NEW.id);
    END LOOP;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger para remover membro quando ele sai da igreja ou é desativado
CREATE OR REPLACE FUNCTION trigger_remover_membro_alianca()
RETURNS TRIGGER AS $$
DECLARE
    v_alianca RECORD;
BEGIN
    -- Se o membro foi desativado ou removido
    IF (TG_OP = 'UPDATE' AND OLD.status = 'ativo' AND NEW.status != 'ativo') OR
       (TG_OP = 'UPDATE' AND NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL) THEN

        -- Para cada aliança ativa da igreja, remover o membro
        FOR v_alianca IN
            SELECT ia.id as igreja_alianca_id
            FROM igreja_aliancas ia
            WHERE ia.igreja_id = NEW.igreja_id
              AND ia.status = 'ativo'
        LOOP
            PERFORM remover_membro_alianca_comunidade(v_alianca.igreja_alianca_id, NEW.id);
        END LOOP;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger para remover membros quando igreja sai da aliança
CREATE OR REPLACE FUNCTION trigger_remover_membros_quando_igreja_sai()
RETURNS TRIGGER AS $$
BEGIN
    -- Quando igreja sai da aliança (status muda de 'ativo')
    IF OLD.status = 'ativo' AND NEW.status != 'ativo' THEN
        -- Remover todos os membros da comunidade geral desta aliança
        UPDATE alianca_membros_gerais
        SET ativo = FALSE,
            data_desligamento = now(),
            updated_at = now()
        WHERE igreja_alianca_id = NEW.id
          AND ativo = TRUE;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Criar os triggers
DROP TRIGGER IF EXISTS trg_adicionar_membros_alianca ON igreja_aliancas;
CREATE TRIGGER trg_adicionar_membros_alianca
    AFTER INSERT OR UPDATE ON igreja_aliancas
    FOR EACH ROW
    EXECUTE FUNCTION trigger_adicionar_membros_alianca();

DROP TRIGGER IF EXISTS trg_adicionar_novo_membro_alianca ON igreja_membros;
CREATE TRIGGER trg_adicionar_novo_membro_alianca
    AFTER INSERT ON igreja_membros
    FOR EACH ROW
    EXECUTE FUNCTION trigger_adicionar_novo_membro_alianca();

DROP TRIGGER IF EXISTS trg_remover_membro_alianca ON igreja_membros;
CREATE TRIGGER trg_remover_membro_alianca
    AFTER UPDATE ON igreja_membros
    FOR EACH ROW
    EXECUTE FUNCTION trigger_remover_membro_alianca();

DROP TRIGGER IF EXISTS trg_remover_membros_quando_igreja_sai ON igreja_aliancas;
CREATE TRIGGER trg_remover_membros_quando_igreja_sai
    AFTER UPDATE ON igreja_aliancas
    FOR EACH ROW
    EXECUTE FUNCTION trigger_remover_membros_quando_igreja_sai();

-- =========================================================
-- FIM DO SISTEMA DE COMUNIDADE GERAL DE MEMBROS
-- =========================================================

-- =========================================================
-- GATILHO PARA ATUALIZAÇÃO AUTOMÁTICA DE STATUS DOS AGENDAMENTOS
-- Atualiza automaticamente o status de 'agendado' ou 'confirmado' para 'realizado'
-- quando a data/hora do evento já passou
-- =========================================================

-- Função para atualizar status dos agendamentos
CREATE OR REPLACE FUNCTION atualizar_status_agendamentos()
RETURNS VOID AS $$
BEGIN
    -- Atualizar agendamentos que já terminaram para 'realizado'
    UPDATE agendamentos
    SET status = 'realizado',
        updated_at = now()
    WHERE status IN ('agendado', 'confirmado')
      AND (
          -- Se tem hora_fim, usar ela como referência
          (hora_fim IS NOT NULL AND
           (data_agendamento + hora_fim::interval) < now())
          OR
          -- Se não tem hora_fim, assumir 2 horas após o início
          (hora_fim IS NULL AND
           (data_agendamento + hora_inicio::interval + interval '2 hours') < now())
      )
      AND deleted_at IS NULL;

    -- Log da operação (opcional)
    INSERT INTO auditoria_logs (
        tabela,
        registro_id,
        acao,
        usuario_id,
        valores
    )
    SELECT
        'agendamentos',
        id::text,
        'update',
        NULL, -- Sistema automático
        jsonb_build_object(
            'status_anterior', 'agendado/confirmado',
            'status_novo', 'realizado',
            'motivo', 'Atualização automática - evento finalizado'
        )
    FROM agendamentos
    WHERE status = 'realizado'
      AND updated_at >= now() - interval '1 minute'; -- Apenas os recém atualizados

END;
$$ LANGUAGE plpgsql;

-- Função de gatilho que verifica e atualiza o status de um agendamento específico
CREATE OR REPLACE FUNCTION trigger_verificar_status_agendamento()
RETURNS TRIGGER AS $$
BEGIN
    -- Para INSERT e UPDATE, verificar se o agendamento deveria estar realizado
    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
        -- Verificar se o evento já deveria ter terminado
        IF NEW.status IN ('agendado', 'confirmado') AND (
            -- Se tem hora_fim, usar ela como referência
            (NEW.hora_fim IS NOT NULL AND
             (NEW.data_agendamento + NEW.hora_fim::interval) < now())
            OR
            -- Se não tem hora_fim, assumir 2 horas após o início
            (NEW.hora_fim IS NULL AND
             (NEW.data_agendamento + NEW.hora_inicio::interval + interval '2 hours') < now())
        ) THEN
            NEW.status := 'realizado';
            NEW.updated_at := now();
        END IF;

        RETURN NEW;
    END IF;

    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- Criar o gatilho na tabela agendamentos
-- Este gatilho será executado ANTES de INSERT ou UPDATE
DROP TRIGGER IF EXISTS trg_atualizar_status_agendamentos ON agendamentos;
CREATE TRIGGER trg_atualizar_status_agendamentos
    BEFORE INSERT OR UPDATE ON agendamentos
    FOR EACH ROW
    EXECUTE FUNCTION trigger_verificar_status_agendamento();

-- Função adicional para executar atualização em lote (pode ser chamada manualmente)
CREATE OR REPLACE FUNCTION executar_atualizacao_status_agendamentos()
RETURNS INTEGER AS $$
DECLARE
    registros_atualizados INTEGER;
BEGIN
    -- Executar a atualização de status
    PERFORM atualizar_status_agendamentos();

    -- Retornar quantos registros foram atualizados
    GET DIAGNOSTICS registros_atualizados = ROW_COUNT;

    RETURN registros_atualizados;
END;
$$ LANGUAGE plpgsql;

-- Comentários explicativos
COMMENT ON FUNCTION atualizar_status_agendamentos() IS 'Função que atualiza automaticamente o status dos agendamentos de agendado/confirmado para realizado quando o evento já terminou';
COMMENT ON FUNCTION trigger_verificar_status_agendamento() IS 'Função de gatilho que verifica e atualiza automaticamente o status de agendamentos individuais durante INSERT/UPDATE';
COMMENT ON FUNCTION executar_atualizacao_status_agendamentos() IS 'Função que executa atualização em lote e retorna o número de registros atualizados';


-- Função de gatilho para atualizar status em tempo real
CREATE OR REPLACE FUNCTION trigger_confirmar_status_agendamento()
RETURNS TRIGGER AS $$
BEGIN
    -- Se o status está 'agendado'
    IF NEW.status = 'agendado' THEN
        -- Caso tenha hora_fim definida
        IF NEW.hora_fim IS NOT NULL AND
           (NEW.data_agendamento + NEW.hora_inicio::interval) <= now()
           AND (NEW.data_agendamento + NEW.hora_fim::interval) > now() THEN

           NEW.status := 'confirmado';
           NEW.updated_at := now();
        END IF;

        -- Caso não tenha hora_fim (assumir 2h de duração)
        IF NEW.hora_fim IS NULL AND
           (NEW.data_agendamento + NEW.hora_inicio::interval) <= now()
           AND (NEW.data_agendamento + NEW.hora_inicio::interval + interval '2 hours') > now() THEN

           NEW.status := 'confirmado';
           NEW.updated_at := now();
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Criar o gatilho na tabela agendamentos
DROP TRIGGER IF EXISTS trg_confirmar_status_agendamentos ON agendamentos;
CREATE TRIGGER trg_confirmar_status_agendamentos
    BEFORE INSERT OR UPDATE ON agendamentos
    FOR EACH ROW
    EXECUTE FUNCTION trigger_confirmar_status_agendamento();


-- =========================================================
-- FIM DO SISTEMA DE ATUALIZAÇÃO AUTOMÁTICA DE STATUS
-- =========================================================

-- =========================================================
-- MÓDULO DE CARTÃO DE MEMBRO - VERSÃO SIMPLIFICADA
-- Sistema básico com apenas as tabelas essenciais
-- =========================================================

-- ==================== CARTAO_MEMBRO ====================
-- Tabela para controlar cartões de membros da igreja
CREATE TABLE IF NOT EXISTS cartao_membro (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,

    -- Controle do cartão
    numero_cartao VARCHAR(20) UNIQUE,
    data_emissao DATE NOT NULL DEFAULT CURRENT_DATE,
    data_validade DATE,
    status VARCHAR(20) DEFAULT 'ativo' CHECK (status IN ('ativo', 'inativo', 'perdido', 'danificado', 'renovado', 'cancelado')),

    -- Processo de produção
    solicitado_em TIMESTAMPTZ DEFAULT now(),
    solicitado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    aprovado_em TIMESTAMPTZ,
    aprovado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    impresso_em TIMESTAMPTZ,
    impresso_por UUID REFERENCES users(id) ON DELETE SET NULL,
    entregue_em TIMESTAMPTZ,
    entregue_por UUID REFERENCES users(id) ON DELETE SET NULL,

    -- Dados para impressão
    foto_url TEXT,
    assinatura_digital TEXT,
    qr_code TEXT, -- Código QR único para validação
    template_usado VARCHAR(100), -- Template de design usado

    -- Controle financeiro
    custo_producao NUMERIC(10,2) DEFAULT 0,
    custo_entrega NUMERIC(10,2) DEFAULT 0,

    -- Observações e controle
    observacoes TEXT,
    motivo_inativacao TEXT,
    numero_renovacao INT DEFAULT 0, -- Contador de renovações

    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL,

    -- Unicidade: um membro não pode ter mais de um cartão ativo por igreja
    UNIQUE(membro_id, igreja_id, status) DEFERRABLE INITIALLY DEFERRED
);

-- Índices para cartao_membro
CREATE INDEX IF NOT EXISTS idx_cartao_membro_membro ON cartao_membro(membro_id);
CREATE INDEX IF NOT EXISTS idx_cartao_membro_igreja ON cartao_membro(igreja_id);
CREATE INDEX IF NOT EXISTS idx_cartao_membro_status ON cartao_membro(status);
CREATE INDEX IF NOT EXISTS idx_cartao_membro_numero_cartao ON cartao_membro(numero_cartao);
CREATE INDEX IF NOT EXISTS idx_cartao_membro_data_emissao ON cartao_membro(data_emissao);
CREATE INDEX IF NOT EXISTS idx_cartao_membro_data_validade ON cartao_membro(data_validade);

-- ==================== CARTAO_MEMBRO_HISTORICO ====================
-- Histórico de ações realizadas nos cartões
CREATE TABLE IF NOT EXISTS cartao_membro_historico (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    cartao_id UUID NOT NULL REFERENCES cartao_membro(id) ON DELETE CASCADE,
    acao VARCHAR(50) NOT NULL CHECK (acao IN ('solicitado', 'aprovado', 'impresso', 'entregue', 'renovado', 'cancelado', 'perdido', 'danificado')),
    descricao TEXT,
    realizado_por UUID REFERENCES users(id) ON DELETE SET NULL,
    data_acao TIMESTAMPTZ NOT NULL DEFAULT now(),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);

-- Índices para cartao_membro_historico
CREATE INDEX IF NOT EXISTS idx_cartao_historico_cartao ON cartao_membro_historico(cartao_id);
CREATE INDEX IF NOT EXISTS idx_cartao_historico_acao ON cartao_membro_historico(acao);
CREATE INDEX IF NOT EXISTS idx_cartao_historico_data ON cartao_membro_historico(data_acao);

-- ==================== COMENTÁRIOS DAS TABELAS ====================

COMMENT ON TABLE cartao_membro IS 'Controle de cartões de membros da igreja';
COMMENT ON COLUMN cartao_membro.numero_cartao IS 'Número único do cartão (gerado automaticamente)';
COMMENT ON COLUMN cartao_membro.data_validade IS 'Data de expiração do cartão';
COMMENT ON COLUMN cartao_membro.qr_code IS 'Código QR único para validação digital';
COMMENT ON COLUMN cartao_membro.numero_renovacao IS 'Contador de quantas vezes o cartão foi renovado';

COMMENT ON TABLE cartao_membro_historico IS 'Histórico completo de ações realizadas nos cartões';
COMMENT ON COLUMN cartao_membro_historico.acao IS 'Tipo de ação realizada no cartão';

-- =========================================================
-- FIM DO MÓDULO DE CARTÃO DE MEMBRO - VERSÃO SIMPLIFICADA
-- =========================================================

-- =========================================================
-- TABELA DE CONFIGURAÇÕES DO CARTÃO DE MEMBRO
-- Sistema para armazenar configurações de cores e estilos
-- =========================================================

-- ==================== CARTAO_CONFIG ====================
-- Tabela para armazenar configurações de cores dos cartões
CREATE TABLE IF NOT EXISTS cartao_config (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,

    -- Configurações de cores (em formato hexadecimal)
    cor_fundo_header VARCHAR(7) DEFAULT '#8B5CF6' CHECK (cor_fundo_header ~ '^#[0-9A-Fa-f]{6}$'),
    cor_texto_header VARCHAR(7) DEFAULT '#FFFFFF' CHECK (cor_texto_header ~ '^#[0-9A-Fa-f]{6}$'),
    cor_texto_principal VARCHAR(7) DEFAULT '#1F2937' CHECK (cor_texto_principal ~ '^#[0-9A-Fa-f]{6}$'),
    cor_texto_secundario VARCHAR(7) DEFAULT '#6B7280' CHECK (cor_texto_secundario ~ '^#[0-9A-Fa-f]{6}$'),
    cor_acento VARCHAR(7) DEFAULT '#8B5CF6' CHECK (cor_acento ~ '^#[0-9A-Fa-f]{6}$'),
    cor_status_ativo VARCHAR(7) DEFAULT '#10B981' CHECK (cor_status_ativo ~ '^#[0-9A-Fa-f]{6}$'),
    cor_status_inativo VARCHAR(7) DEFAULT '#DC3545' CHECK (cor_status_inativo ~ '^#[0-9A-Fa-f]{6}$'),
    cor_status_perdido VARCHAR(7) DEFAULT '#FD7E14' CHECK (cor_status_perdido ~ '^#[0-9A-Fa-f]{6}$'),
    cor_status_danificado VARCHAR(7) DEFAULT '#6F42C1' CHECK (cor_status_danificado ~ '^#[0-9A-Fa-f]{6}$'),
    cor_status_renovado VARCHAR(7) DEFAULT '#20C997' CHECK (cor_status_renovado ~ '^#[0-9A-Fa-f]{6}$'),
    cor_status_cancelado VARCHAR(7) DEFAULT '#6C757D' CHECK (cor_status_cancelado ~ '^#[0-9A-Fa-f]{6}$'),

    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Índices para cartao_config
CREATE INDEX IF NOT EXISTS idx_cartao_config_igreja ON cartao_config(igreja_id);

-- ==================== COMENTÁRIOS DA TABELA ====================

COMMENT ON TABLE cartao_config IS 'Configurações de cores dos cartões de membro';
COMMENT ON COLUMN cartao_config.cor_fundo_header IS 'Cor de fundo do cabeçalho do cartão (formato hexadecimal)';
COMMENT ON COLUMN cartao_config.cor_texto_header IS 'Cor do texto no cabeçalho (formato hexadecimal)';
COMMENT ON COLUMN cartao_config.cor_texto_principal IS 'Cor do texto principal (nome, etc.)';
COMMENT ON COLUMN cartao_config.cor_texto_secundario IS 'Cor do texto secundário (detalhes, etc.)';
COMMENT ON COLUMN cartao_config.cor_acento IS 'Cor de destaque/acento usada no design';
COMMENT ON COLUMN cartao_config.cor_status_ativo IS 'Cor usada para status ativo';
COMMENT ON COLUMN cartao_config.cor_status_inativo IS 'Cor usada para status inativo';
COMMENT ON COLUMN cartao_config.cor_status_perdido IS 'Cor usada para status perdido';
COMMENT ON COLUMN cartao_config.cor_status_danificado IS 'Cor usada para status danificado';
COMMENT ON COLUMN cartao_config.cor_status_renovado IS 'Cor usada para status renovado';
COMMENT ON COLUMN cartao_config.cor_status_cancelado IS 'Cor usada para status cancelado';
-- =========================================================
-- FIM DA TABELA DE CONFIGURAÇÕES DO CARTÃO DE MEMBRO
-- =========================================================

-- =========================================================
-- TABELA: RELATORIO_CULTO
-- Tabela para salvar relatórios dos cultos e eventos
-- =========================================================

CREATE TABLE IF NOT EXISTS relatorio_culto (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    evento_id UUID REFERENCES eventos(id) ON DELETE SET NULL,
    culto_padrao_id UUID REFERENCES cultos_padrao(id) ON DELETE SET NULL,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,

    titulo TEXT,
    conteudo TEXT NOT NULL,
    numero_participantes INTEGER,
    valor_oferta NUMERIC(14,2),
    observacoes TEXT,
    status VARCHAR(20) DEFAULT 'rascunho' CHECK (status IN ('rascunho', 'finalizado')),
    data_relatorio DATE DEFAULT CURRENT_DATE,

    -- Estatísticas detalhadas
    numero_visitantes INTEGER DEFAULT 0,
    numero_decisoes INTEGER DEFAULT 0,
    numero_batismos INTEGER DEFAULT 0,
    numero_conversoes INTEGER DEFAULT 0,
    numero_reconciliacoes INTEGER DEFAULT 0,
    numero_casamentos INTEGER DEFAULT 0,
    numero_funeral INTEGER DEFAULT 0,
    numero_outros_eventos INTEGER DEFAULT 0,

    -- Valores financeiros
    valor_dizimos NUMERIC(14,2) DEFAULT 0,
    valor_ofertas NUMERIC(14,2) DEFAULT 0,
    valor_doacoes NUMERIC(14,2) DEFAULT 0,
    valor_outros NUMERIC(14,2) DEFAULT 0,

    -- Informações do culto
    tema_culto TEXT,
    pregador TEXT,
    pregador_convidado TEXT,
    dirigente TEXT,
    texto_base TEXT,
    resumo_mensagem TEXT,
    musica_responsavel TEXT,
    tipo_culto TEXT CHECK (tipo_culto IN ('domingo','sexta','vigilia','especial','outro')) DEFAULT 'outro',
    observacoes_gerais TEXT,

    -- Avaliação administrativa
    avaliado_por TEXT,
    data_avaliacao TIMESTAMPTZ,

    -- Controle do registro
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    deleted_at TIMESTAMPTZ NULL
);


-- Índices principais de relacionamento
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_igreja ON relatorio_culto(igreja_id);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_evento ON relatorio_culto(evento_id);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_culto_padrao ON relatorio_culto(culto_padrao_id);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_created_by ON relatorio_culto(created_by);

-- Índices de uso comum em relatórios
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_data_relatorio ON relatorio_culto(data_relatorio);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_status ON relatorio_culto(status);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_tipo_culto ON relatorio_culto(tipo_culto);

-- Índices de identificação de pessoas envolvidas
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_pregador ON relatorio_culto(pregador);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_pregador_convidado ON relatorio_culto(pregador_convidado);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_dirigente ON relatorio_culto(dirigente);

-- Índices administrativos e de auditoria
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_avaliado_por ON relatorio_culto(avaliado_por);
CREATE INDEX IF NOT EXISTS idx_relatorio_culto_data_avaliacao ON relatorio_culto(data_avaliacao);



-- Comentários da tabela
COMMENT ON TABLE relatorio_culto IS 'Relatórios dos cultos e eventos preenchidos pelos usuários';
COMMENT ON COLUMN relatorio_culto.igreja_id IS 'Referência à igreja relacionada';
COMMENT ON COLUMN relatorio_culto.evento_id IS 'Referência ao evento/culto específico (pode ser nulo)';
COMMENT ON COLUMN relatorio_culto.culto_padrao_id IS 'Referência ao culto padrão semanal';
COMMENT ON COLUMN relatorio_culto.created_by IS 'Usuário que criou/preencheu o relatório';
COMMENT ON COLUMN relatorio_culto.titulo IS 'Título opcional do relatório';
COMMENT ON COLUMN relatorio_culto.conteudo IS 'Conteúdo detalhado do relatório';
COMMENT ON COLUMN relatorio_culto.numero_participantes IS 'Número de participantes no culto/evento';
COMMENT ON COLUMN relatorio_culto.valor_oferta IS 'Valor total das ofertas coletadas';
COMMENT ON COLUMN relatorio_culto.observacoes IS 'Observações adicionais';
COMMENT ON COLUMN relatorio_culto.status IS 'Status do relatório: rascunho ou finalizado';
COMMENT ON COLUMN relatorio_culto.data_relatorio IS 'Data em que o relatório foi preenchido';

-- =========================================================
-- SISTEMA DE GRUPOS WHATSAPP-LIKE - ARQUITETURA CORRIGIDA
-- Funcionalidades implementadas:
-- 1. Qualquer usuário pode criar uma igreja (grupo)
-- 2. Criador do chat se torna automaticamente admin do grupo
-- 3. Admins podem adicionar/remover outros admins
-- 4. Mensagens suportam texto, áudio, arquivos, imagens, localização
-- 5. Tabela igreja_chat_participantes separada para isolamento de responsabilidades
-- =========================================================

COMMENT ON TABLE igreja_chat_participantes IS 'Participantes dos chats/grupos (WhatsApp-like) - separada da tabela igreja_membros';
COMMENT ON COLUMN igreja_chat_participantes.is_admin IS 'Indica se o participante é administrador do grupo (pode adicionar/remover membros)';
COMMENT ON COLUMN igreja_chat_participantes.added_by IS 'Usuário que adicionou este participante ao grupo';
COMMENT ON COLUMN igreja_chat_participantes.data_entrada IS 'Data/hora em que o participante entrou no grupo';
COMMENT ON COLUMN igreja_chat_participantes.status IS 'Status do participante: ativo, removido, saiu';

COMMENT ON TABLE igreja_chat_mensagens IS 'Mensagens do chat da igreja com suporte a múltiplos tipos de mídia';
COMMENT ON COLUMN igreja_chat_mensagens.tipo_mensagem IS 'Tipo da mensagem: texto, imagem, audio, video, arquivo, localizacao';
COMMENT ON COLUMN igreja_chat_mensagens.anexo_url IS 'URL do arquivo anexado no Supabase';
COMMENT ON COLUMN igreja_chat_mensagens.anexo_nome IS 'Nome original do arquivo anexado';
COMMENT ON COLUMN igreja_chat_mensagens.anexo_tamanho IS 'Tamanho do arquivo em bytes';
COMMENT ON COLUMN igreja_chat_mensagens.anexo_tipo IS 'Tipo MIME do arquivo anexado';
COMMENT ON COLUMN igreja_chat_mensagens.duracao_audio IS 'Duração do áudio em segundos';
COMMENT ON COLUMN igreja_chat_mensagens.latitude IS 'Latitude para mensagens de localização';
COMMENT ON COLUMN igreja_chat_mensagens.longitude IS 'Longitude para mensagens de localização';

COMMENT ON TABLE mensagens_privadas IS 'Mensagens privadas entre usuários com suporte a múltiplos tipos de mídia';
COMMENT ON COLUMN mensagens_privadas.tipo_mensagem IS 'Tipo da mensagem: texto, imagem, audio, video, arquivo, localizacao';
COMMENT ON COLUMN mensagens_privadas.anexo_url IS 'URL do arquivo anexado no Supabase';
COMMENT ON COLUMN mensagens_privadas.anexo_nome IS 'Nome original do arquivo anexado';
COMMENT ON COLUMN mensagens_privadas.anexo_tamanho IS 'Tamanho do arquivo em bytes';
COMMENT ON COLUMN mensagens_privadas.anexo_tipo IS 'Tipo MIME do arquivo anexado';
COMMENT ON COLUMN mensagens_privadas.duracao_audio IS 'Duração do áudio em segundos';
COMMENT ON COLUMN mensagens_privadas.latitude IS 'Latitude para mensagens de localização';
COMMENT ON COLUMN mensagens_privadas.longitude IS 'Longitude para mensagens de localização';
COMMENT ON COLUMN mensagens_privadas.lida_por IS 'Array JSON com IDs dos usuários que leram a mensagem';

-- SISTEMA DE PERMISSÕES - RBAC (Role-Based Access Control)
-- Tabelas para controle granular de acesso aos módulos
-- Baseado na documentação fluxo_permissao.md
-- =========================================================

-- ==================== IGREJA_PERMISSOES ====================
-- Permissões específicas por igreja (ex: "gerenciar_membros", "ver_financeiro")
CREATE TABLE IF NOT EXISTS igreja_permissoes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    codigo VARCHAR(100) NOT NULL, -- Ex: "gerenciar_membros", "ver_financeiro"
    nome VARCHAR(255) NOT NULL,   -- Ex: "Gerenciar Membros"
    descricao TEXT,
    modulo VARCHAR(50) NOT NULL,  -- Ex: "membros", "financeiro", "eventos"
    categoria VARCHAR(50),        -- Ex: "admin", "visualizacao", "edicao"
    nivel_hierarquia INT DEFAULT 1 CHECK (nivel_hierarquia BETWEEN 1 AND 10),
    ativo BOOLEAN DEFAULT TRUE,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(igreja_id, codigo)
);

-- ==================== IGREJA_FUNCOES ====================
-- Funções/Roles que podem ser atribuídas aos membros (ex: "Diácono", "Tesoureiro")
CREATE TABLE IF NOT EXISTS igreja_funcoes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    nome VARCHAR(100) NOT NULL,    -- Ex: "Diácono", "Tesoureiro"
    descricao TEXT,
    nivel_hierarquia INT DEFAULT 1 CHECK (nivel_hierarquia BETWEEN 1 AND 10),
    cor_identificacao VARCHAR(7),  -- Hex color para UI
    ativo BOOLEAN DEFAULT TRUE,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(igreja_id, nome)
);

-- ==================== IGREJA_FUNCAO_PERMISSOES ====================
-- Relacionamento muitos-para-muitos entre funções e permissões
CREATE TABLE IF NOT EXISTS igreja_funcao_permissoes (
    funcao_id UUID NOT NULL REFERENCES igreja_funcoes(id) ON DELETE CASCADE,
    permissao_id UUID NOT NULL REFERENCES igreja_permissoes(id) ON DELETE CASCADE,
    concedido_em TIMESTAMPTZ DEFAULT now(),
    concedido_por UUID REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),
    PRIMARY KEY (funcao_id, permissao_id)
);

-- ==================== IGREJA_MEMBRO_FUNCOES ====================
-- Atribuição de funções específicas aos membros da igreja
CREATE TABLE IF NOT EXISTS igreja_membro_funcoes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    membro_id UUID NOT NULL REFERENCES igreja_membros(id) ON DELETE CASCADE,
    funcao_id UUID NOT NULL REFERENCES igreja_funcoes(id) ON DELETE CASCADE,
    igreja_id BIGINT NOT NULL REFERENCES igrejas(id) ON DELETE CASCADE,
    -- Controle de atribuição
    atribuido_por UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    atribuido_em TIMESTAMPTZ DEFAULT now(),
    valido_ate DATE,  -- Opcional: validade da função
    -- Status
    status VARCHAR(20) DEFAULT 'ativo' CHECK (status IN ('ativo', 'suspenso', 'revogado')),
    motivo_atribuicao TEXT,
    observacoes TEXT,
    revogado_por UUID REFERENCES users(id)  ON DELETE SET NULL ,
    revogado_em TIMESTAMPTZ DEFAULT now(),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now(),

    UNIQUE(membro_id, funcao_id, status) DEFERRABLE INITIALLY DEFERRED
);

-- ==================== IGREJA_PERMISSAO_LOGS ====================
-- Auditoria completa de todas as ações relacionadas a permissões
CREATE TABLE IF NOT EXISTS igreja_permissao_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    igreja_id BIGINT REFERENCES igrejas(id) ON DELETE SET NULL,
    membro_id UUID REFERENCES igreja_membros(id) ON DELETE SET NULL,
    funcao_id UUID REFERENCES igreja_funcoes(id) ON DELETE SET NULL,
    permissao_id UUID REFERENCES igreja_permissoes(id) ON DELETE SET NULL,

    acao VARCHAR(50) NOT NULL, -- 'atribuir_funcao', 'revogar_funcao', 'alterar_permissao'
    detalhes JSONB, -- Dados adicionais da ação

    realizado_por UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    realizado_em TIMESTAMPTZ DEFAULT now(),

    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

-- ==================== ÍNDICES PARA PERFORMANCE ====================
-- Índices para igreja_permissoes
CREATE INDEX IF NOT EXISTS idx_igreja_permissoes_igreja_codigo ON igreja_permissoes(igreja_id, codigo);
CREATE INDEX IF NOT EXISTS idx_igreja_permissoes_modulo ON igreja_permissoes(modulo);
CREATE INDEX IF NOT EXISTS idx_igreja_permissoes_ativo ON igreja_permissoes(ativo);

-- Índices para igreja_funcoes
CREATE INDEX IF NOT EXISTS idx_igreja_funcoes_igreja_nome ON igreja_funcoes(igreja_id, nome);
CREATE INDEX IF NOT EXISTS idx_igreja_funcoes_ativo ON igreja_funcoes(ativo);

-- Índices para igreja_membro_funcoes
CREATE INDEX IF NOT EXISTS idx_igreja_membro_funcoes_membro ON igreja_membro_funcoes(membro_id);
CREATE INDEX IF NOT EXISTS idx_igreja_membro_funcoes_funcao ON igreja_membro_funcoes(funcao_id);
CREATE INDEX IF NOT EXISTS idx_igreja_membro_funcoes_status ON igreja_membro_funcoes(status);
CREATE INDEX IF NOT EXISTS idx_igreja_membro_funcoes_atribuido_por ON igreja_membro_funcoes(atribuido_por);

-- Índices para igreja_permissao_logs
CREATE INDEX IF NOT EXISTS idx_igreja_permissao_logs_igreja ON igreja_permissao_logs(igreja_id);
CREATE INDEX IF NOT EXISTS idx_igreja_permissao_logs_acao ON igreja_permissao_logs(acao);
CREATE INDEX IF NOT EXISTS idx_igreja_permissao_logs_realizado_por ON igreja_permissao_logs(realizado_por);
CREATE INDEX IF NOT EXISTS idx_igreja_permissao_logs_realizado_em ON igreja_permissao_logs(realizado_em);

-- ==================== COMENTÁRIOS DAS TABELAS ====================
COMMENT ON TABLE igreja_permissoes IS 'Permissões específicas por igreja para controle granular de acesso';
COMMENT ON COLUMN igreja_permissoes.codigo IS 'Código único da permissão (ex: "gerenciar_membros")';
COMMENT ON COLUMN igreja_permissoes.modulo IS 'Módulo do sistema ao qual a permissão se aplica';
COMMENT ON COLUMN igreja_permissoes.nivel_hierarquia IS 'Nível hierárquico da permissão (1-10)';

COMMENT ON TABLE igreja_funcoes IS 'Funções/Roles que podem ser atribuídas aos membros';
COMMENT ON COLUMN igreja_funcoes.nivel_hierarquia IS 'Nível hierárquico da função (1-10)';
COMMENT ON COLUMN igreja_funcoes.cor_identificacao IS 'Cor hexadecimal para identificação visual da função';

COMMENT ON TABLE igreja_funcao_permissoes IS 'Relacionamento muitos-para-muitos entre funções e permissões';
COMMENT ON COLUMN igreja_funcao_permissoes.concedido_por IS 'Usuário que concedeu a permissão à função';

COMMENT ON TABLE igreja_membro_funcoes IS 'Atribuição de funções específicas aos membros da igreja';
COMMENT ON COLUMN igreja_membro_funcoes.atribuido_por IS 'Usuário que atribuiu a função ao membro';
COMMENT ON COLUMN igreja_membro_funcoes.valido_ate IS 'Data opcional de expiração da função';
COMMENT ON COLUMN igreja_membro_funcoes.motivo_atribuicao IS 'Motivo/justificativa da atribuição da função';

COMMENT ON TABLE igreja_permissao_logs IS 'Auditoria completa de todas as ações relacionadas a permissões';
COMMENT ON COLUMN igreja_permissao_logs.acao IS 'Tipo de ação realizada (atribuir_funcao, revogar_funcao, etc.)';
COMMENT ON COLUMN igreja_permissao_logs.detalhes IS 'Dados adicionais em formato JSON sobre a ação';




-- ==================== TABELA HISTÓRICO DE MIGRAÇÕES DE MEMBROS ====================
-- Tabela específica para rastrear migrações de membros entre igrejas
CREATE TABLE IF NOT EXISTS membro_migracoes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    membro_user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,

    -- Igreja de origem
    igreja_origem_id BIGINT REFERENCES igrejas(id) ON DELETE SET NULL,
    igreja_origem_nome TEXT, -- Nome da igreja no momento da migração
    membro_origem_id UUID REFERENCES igreja_membros(id) ON DELETE SET NULL,
    numero_membro_origem TEXT, -- Número do membro na igreja origem
    cargo_origem TEXT, -- Cargo na igreja origem

    -- Igreja de destino
    igreja_destino_id BIGINT REFERENCES igrejas(id) ON DELETE SET NULL,
    igreja_destino_nome TEXT, -- Nome da igreja destino (pode ser NULL para igrejas do sistema)
    membro_destino_id UUID REFERENCES igreja_membros(id) ON DELETE SET NULL,
    numero_membro_destino TEXT, -- Novo número do membro
    cargo_destino TEXT NOT NULL, -- Cargo na igreja destino

    -- Detalhes da migração
    data_migracao TIMESTAMPTZ NOT NULL DEFAULT now(),
    tipo_migracao TEXT NOT NULL CHECK (tipo_migracao IN ('transferencia', 'reintegracao', 'mudanca_cargo', 'nova_adesao')),
    motivo TEXT, -- Motivo da migração
    observacoes TEXT, -- Observações adicionais

    -- Controle de associações
    manteve_perfil BOOLEAN DEFAULT TRUE,

    -- Controle operacional
    migrado_por UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status TEXT NOT NULL DEFAULT 'concluida' CHECK (status IN ('concluida', 'pendente', 'cancelada')),
    referencia_externa TEXT, -- Para integração com sistemas externos

    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Índices para performance
CREATE INDEX IF NOT EXISTS idx_membro_migracoes_user ON membro_migracoes(membro_user_id);
CREATE INDEX IF NOT EXISTS idx_membro_migracoes_origem ON membro_migracoes(igreja_origem_id);
CREATE INDEX IF NOT EXISTS idx_membro_migracoes_destino ON membro_migracoes(igreja_destino_id);
CREATE INDEX IF NOT EXISTS idx_membro_migracoes_data ON membro_migracoes(data_migracao);
CREATE INDEX IF NOT EXISTS idx_membro_migracoes_tipo ON membro_migracoes(tipo_migracao);
CREATE INDEX IF NOT EXISTS idx_membro_migracoes_migrado_por ON membro_migracoes(migrado_por);


INSERT INTO users (
    id,
    name,
    email,
    email_verified_at,
    password,
    phone,
    photo_url,
    role,
    denomination,
    is_active,
    status,
    remember_token,
    created_by,
    created_at,
    updated_at,
    deleted_at,
    two_factor_secret,
    two_factor_recovery_codes,
    two_factor_confirmed_at
) VALUES (
    '54209238-b4b2-4184-b88a-7a80b313326f'::uuid,
    'Lésio Luis ',
    'luiskediambiko@gmail.com',
    '2025-11-30 05:23:28+00',
    '$2y$12$qNPpGNAtzqizdR1yqShXH.fSFQb0aypwxxjnKwWZwqwQgTCBywhtm',
    '932713172',
    NULL,
    'root',
    'Geral',
    TRUE,
    'ativo',
    NULL,
    NULL,
    '2025-11-30 05:23:54.987188+00',
    '2025-11-30 05:23:54.987188+00',
    NULL,
    NULL,
    NULL,
    NULL
);


CREATE OR REPLACE FUNCTION protect_specific_user()
RETURNS trigger AS $$
BEGIN
    IF OLD.id = '54209238-b4b2-4184-b88a-7a80b313326f'::uuid THEN
        RAISE EXCEPTION 'Operação proibida: este usuário é protegido e não pode ser eliminado.';
    END IF;

    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_protect_specific_user
BEFORE DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION protect_specific_user();

CREATE POLICY hide_specific_user
ON users
FOR SELECT
USING (id <> '54209238-b4b2-4184-b88a-7a80b313326f'::uuid);


-- ==================== FUNÇÃO PARA REGISTRAR MIGRAÇÃO ====================
-- Função auxiliar para registrar migração na tabela específica
CREATE OR REPLACE FUNCTION registrar_migracao_membro(
    p_membro_user_id UUID,
    p_igreja_origem_id BIGINT,
    p_igreja_destino_id BIGINT,
    p_membro_origem_id UUID,
    p_membro_destino_id UUID,
    p_cargo_origem TEXT,
    p_cargo_destino TEXT,
    p_tipo_migracao TEXT,
    p_motivo TEXT,
    p_observacoes TEXT,
    p_migrado_por UUID
) RETURNS UUID AS $$
DECLARE
    v_igreja_origem_nome TEXT;
    v_igreja_destino_nome TEXT;
    v_numero_origem TEXT;
    v_numero_destino TEXT;
    v_migracao_id UUID;
BEGIN
    -- Buscar nomes das igrejas
    SELECT nome INTO v_igreja_origem_nome
    FROM igrejas WHERE id = p_igreja_origem_id;

    SELECT nome INTO v_igreja_destino_nome
    FROM igrejas WHERE id = p_igreja_destino_id;

    -- Buscar números dos membros
    IF p_membro_origem_id IS NOT NULL THEN
        SELECT numero_membro INTO v_numero_origem
        FROM igreja_membros WHERE id = p_membro_origem_id;
    END IF;

    IF p_membro_destino_id IS NOT NULL THEN
        SELECT numero_membro INTO v_numero_destino
        FROM igreja_membros WHERE id = p_membro_destino_id;
    END IF;

    -- Inserir registro de migração
    INSERT INTO membro_migracoes (
        membro_user_id,
        igreja_origem_id, igreja_origem_nome, membro_origem_id, numero_membro_origem, cargo_origem,
        igreja_destino_id, igreja_destino_nome, membro_destino_id, numero_membro_destino, cargo_destino,
        tipo_migracao, motivo, observacoes,
        migrado_por
    ) VALUES (
        p_membro_user_id,
        p_igreja_origem_id, v_igreja_origem_nome, p_membro_origem_id, v_numero_origem, p_cargo_origem,
        p_igreja_destino_id, v_igreja_destino_nome, p_membro_destino_id, v_numero_destino, p_cargo_destino,
        p_tipo_migracao, p_motivo, p_observacoes,
        p_migrado_por
    ) RETURNING id INTO v_migracao_id;

    RETURN v_migracao_id;
EXCEPTION WHEN OTHERS THEN
    -- Em caso de erro, retornar NULL
    RETURN NULL;
END;
$$ LANGUAGE plpgsql;

-- ==================== VIEW PARA HISTÓRICO DETALHADO ====================
-- View completa do histórico de migrações
CREATE OR REPLACE VIEW view_historico_migracoes AS
SELECT
    mm.id as migracao_id,
    mm.data_migracao,
    mm.tipo_migracao,
    mm.motivo,
    mm.observacoes,
    mm.status,

    -- Dados do membro
    u.name as nome_membro,
    u.email as email_membro,

    -- Igreja origem
    mm.igreja_origem_nome,
    mm.numero_membro_origem,
    mm.cargo_origem,

    -- Igreja destino
    mm.igreja_destino_nome,
    mm.numero_membro_destino,
    mm.cargo_destino,

    -- Controle de associações
    mm.manteve_perfil,

    -- Quem migrou
    mu.name as migrado_por_nome,

    -- Formatação para exibição
    CASE mm.tipo_migracao
        WHEN 'transferencia' THEN 'Transferência'
        WHEN 'reintegracao' THEN 'Reintegração'
        WHEN 'mudanca_cargo' THEN 'Mudança de Cargo'
        WHEN 'nova_adesao' THEN 'Nova Adesão'
        ELSE mm.tipo_migracao
    END as tipo_migracao_formatado,

    -- Descrição completa
    CASE
        WHEN mm.igreja_origem_nome IS NOT NULL THEN
            'De ' || mm.igreja_origem_nome || ' (' || mm.cargo_origem || ') para ' || mm.igreja_destino_nome || ' (' || mm.cargo_destino || ')'
        ELSE
            'Nova adesão à ' || mm.igreja_destino_nome || ' como ' || mm.cargo_destino
    END as descricao_completa

FROM membro_migracoes mm
JOIN users u ON u.id = mm.membro_user_id
LEFT JOIN users mu ON mu.id = mm.migrado_por
ORDER BY mm.data_migracao DESC;

-- ==================== VIEW PARA TRAJETÓRIA DO MEMBRO ====================
-- View mostrando toda a trajetória de um membro
CREATE OR REPLACE VIEW view_trajetoria_membro AS
SELECT
    u.id as user_id,
    u.name as nome_membro,
    u.email,

    -- Dados da migração
    mm.data_migracao,
    mm.igreja_origem_nome,
    mm.igreja_destino_nome,
    mm.cargo_origem,
    mm.cargo_destino,
    mm.tipo_migracao,
    mm.motivo,

    -- Sequência temporal
    ROW_NUMBER() OVER (PARTITION BY u.id ORDER BY mm.data_migracao) as sequencia,

    -- Status atual
    CASE
        WHEN mm.data_migracao = (SELECT MAX(data_migracao) FROM membro_migracoes WHERE membro_user_id = u.id)
        THEN 'Atual'
        ELSE 'Histórico'
    END as status_atual

FROM users u
JOIN membro_migracoes mm ON mm.membro_user_id = u.id
ORDER BY u.name, mm.data_migracao;

-- ==================== ATUALIZAR FUNÇÃO PRINCIPAL ====================
-- Modificar a função migrar_membro_igreja para usar a nova tabela
CREATE OR REPLACE FUNCTION migrar_membro_igreja(
    p_membro_id UUID,           -- ID do registro em igreja_membros
    p_igreja_destino BIGINT,    -- ID da igreja destino
    p_usuario_migracao UUID,    -- Quem está fazendo a migração
    p_novo_cargo TEXT DEFAULT NULL, -- Novo cargo (opcional)
    p_motivo TEXT DEFAULT NULL  -- Motivo da migração
) RETURNS JSONB AS $$
DECLARE
    v_membro_atual RECORD;
    v_novo_numero TEXT;
    v_novo_membro_id UUID;
    v_migracao_id UUID;
    v_tipo_migracao TEXT;
    v_resultado JSONB;
BEGIN
    -- Buscar dados atuais do membro
    SELECT im.*, i.nome as igreja_nome INTO v_membro_atual
    FROM igreja_membros im
    JOIN igrejas i ON i.id = im.igreja_id
    WHERE im.id = p_membro_id AND im.deleted_at IS NULL;

    IF NOT FOUND THEN
        RETURN jsonb_build_object('sucesso', false, 'erro', 'Membro não encontrado');
    END IF;

    -- Verificar se membro está ativo
    IF v_membro_atual.status != 'ativo' THEN
        RETURN jsonb_build_object('sucesso', false, 'erro', 'Membro não está ativo');
    END IF;

    -- Verificar limites da igreja destino (comentado temporariamente)
    -- IF NOT verificar_limite_membros(p_igreja_destino) THEN
    --     RETURN jsonb_build_object('sucesso', false, 'erro', 'Limite de membros atingido na igreja destino');
    -- END IF;

    -- Determinar tipo de migração
    SELECT CASE
        WHEN v_membro_atual.igreja_id = p_igreja_destino THEN 'mudanca_cargo'
        WHEN EXISTS(SELECT 1 FROM membro_migracoes WHERE membro_user_id = v_membro_atual.user_id AND igreja_destino_id = v_membro_atual.igreja_id) THEN 'reintegracao'
        ELSE 'transferencia'
    END INTO v_tipo_migracao;

    -- O número do membro será gerado automaticamente pelo trigger trg_gerar_numero_membro
    v_novo_numero := NULL;

    -- Iniciar transação
    BEGIN
        -- 1. Registrar saída na igreja origem (se não for mudança de cargo)
        IF v_tipo_migracao != 'mudanca_cargo' THEN
            INSERT INTO igreja_membros_historico (
                igreja_membro_id, cargo, inicio, fim
            ) VALUES (
                p_membro_id, v_membro_atual.cargo, v_membro_atual.data_entrada, CURRENT_DATE
            );

            -- 2. Atualizar status na igreja origem apenas para migrações externas
            IF v_tipo_migracao = 'transferencia_externa' THEN
                UPDATE igreja_membros
                SET status = 'transferido',
                    updated_at = now()
                WHERE id = p_membro_id;
            END IF;
            -- Para 'transferencia' (igreja existente), mantém status 'ativo'
        END IF;

        -- 3. Atualizar o registro existente do membro para a nova igreja
        UPDATE igreja_membros
        SET igreja_id = p_igreja_destino,
            cargo = CASE WHEN p_novo_cargo IS NOT NULL THEN p_novo_cargo::role_enum ELSE v_membro_atual.cargo END,
            numero_membro = NULL, -- Deixar NULL para o trigger gerar automaticamente
            principal = TRUE, -- Nova igreja se torna principal
            data_entrada = CASE WHEN v_tipo_migracao = 'transferencia' THEN CURRENT_DATE ELSE data_entrada END, -- Data atual para transferências
            updated_at = now()
        WHERE id = p_membro_id
        RETURNING id INTO v_novo_membro_id;

        -- Buscar o número gerado pelo trigger
        SELECT numero_membro INTO v_novo_numero
        FROM igreja_membros
        WHERE id = v_novo_membro_id;

        -- 5. Migrar perfil se não existir na igreja destino
        IF v_tipo_migracao != 'mudanca_cargo' THEN
            INSERT INTO membro_perfis (
                igreja_membro_id, genero, data_nascimento, endereco, observacoes, created_by
            )
            SELECT v_novo_membro_id, genero, data_nascimento, endereco, observacoes, p_usuario_migracao
            FROM membro_perfis
            WHERE igreja_membro_id = p_membro_id
            ON CONFLICT (igreja_membro_id) DO NOTHING;
        END IF;

        -- 6. Registrar migração na tabela específica
        SELECT registrar_migracao_membro(
            v_membro_atual.user_id,
            v_membro_atual.igreja_id,
            p_igreja_destino,
            CASE WHEN v_tipo_migracao = 'mudanca_cargo' THEN NULL ELSE p_membro_id END,
            v_novo_membro_id,
            v_membro_atual.cargo::TEXT,
            CASE WHEN p_novo_cargo IS NOT NULL THEN p_novo_cargo::TEXT ELSE v_membro_atual.cargo::TEXT END,
            v_tipo_migracao,
            p_motivo,
            NULL, -- observacoes
            p_usuario_migracao
        ) INTO v_migracao_id;

        -- 7. Registrar log de auditoria
        INSERT INTO auditoria_logs (
            tabela, registro_id, acao, usuario_id, valores
        ) VALUES (
            'igreja_membros',
            v_novo_membro_id::text,
            'update',
            p_usuario_migracao,
            jsonb_build_object(
                'igreja_origem', v_membro_atual.igreja_id,
                'igreja_destino', p_igreja_destino,
                'tipo_migracao', v_tipo_migracao,
                'motivo', p_motivo,
                'migracao_id', v_migracao_id
            )
        );

        -- Retornar sucesso
        v_resultado := jsonb_build_object(
            'sucesso', true,
            'novo_membro_id', v_novo_membro_id,
            'numero_membro', v_novo_numero,
            'tipo_migracao', v_tipo_migracao,
            'migracao_id', v_migracao_id,
            'mensagem', 'Migração realizada com sucesso'
        );

    EXCEPTION WHEN OTHERS THEN
        -- Em caso de erro, rollback automático
        v_resultado := jsonb_build_object(
            'sucesso', false,
            'erro', SQLERRM
        );
    END;

    RETURN v_resultado;
END;
$$ LANGUAGE plpgsql;




-- ==================== PERMISSÕES PADRÃO ====================
-- Inserção de permissões básicas baseadas nos módulos existentes
-- Estas permissões serão criadas automaticamente para novas igrejas

-- Função para criar permissões padrão para uma igreja
CREATE OR REPLACE FUNCTION criar_permissoes_padrao(igreja_id_param BIGINT)
RETURNS VOID AS $$
BEGIN
    -- Módulo: Gestão Organizacional
    INSERT INTO igreja_permissoes (igreja_id, codigo, nome, descricao, modulo, categoria, nivel_hierarquia) VALUES
    (igreja_id_param, 'gerenciar_corpo_lideranca', 'Gerenciar Corpo de Liderança', 'Gerenciar corpo de liderança da igreja', 'lideranca', 'admin', 8),
    (igreja_id_param, 'gerenciar_igrejas', 'Gerenciar Igrejas', 'Criar, editar e gerenciar igrejas e filiais', 'igrejas', 'admin', 9),
    (igreja_id_param, 'ver_igrejas', 'Visualizar Igrejas', 'Visualizar informações das igrejas', 'igrejas', 'visualizacao', 3),
    (igreja_id_param, 'editar_igrejas', 'Editar Igrejas', 'Editar informações básicas das igrejas', 'igrejas', 'edicao', 7),
    (igreja_id_param, 'gerenciar_membros', 'Gerenciar Membros', 'Cadastrar, editar e remover membros da igreja', 'membros', 'admin', 7),
    (igreja_id_param, 'ver_membros', 'Visualizar Membros', 'Visualizar lista e detalhes dos membros', 'membros', 'visualizacao', 2),
    (igreja_id_param, 'editar_membros', 'Editar Membros', 'Editar informações básicas dos membros', 'membros', 'edicao', 5),
    (igreja_id_param, 'gerenciar_cartoes_membros', 'Gerenciar Cartões Membros', 'Emitir, renovar e cancelar cartões de membro da igreja', 'membros', 'admin', 6),
    (igreja_id_param, 'gerenciar_ministerios', 'Gerenciar Ministérios', 'Criar, editar e remover ministérios da igreja', 'ministerios', 'admin', 7),
    (igreja_id_param, 'ver_ministerios', 'Visualizar Ministérios', 'Visualizar ministérios e membros associados', 'ministerios', 'visualizacao', 2),
    (igreja_id_param, 'gerenciar_membros_ministerios', 'Gerenciar Membros em Ministérios', 'Adicionar/remover membros dos ministérios', 'ministerios', 'edicao', 5),

    -- Módulo: Alianças
    (igreja_id_param, 'gerenciar_aliancas', 'Gerenciar Alianças', 'Criar e gerenciar alianças de igrejas', 'aliancas', 'admin', 8),
    (igreja_id_param, 'ver_aliancas', 'Visualizar Alianças', 'Visualizar alianças e participantes', 'aliancas', 'visualizacao', 2),

    -- Módulo: Eventos e Escalas
    (igreja_id_param, 'gerenciar_eventos', 'Gerenciar Eventos', 'Criar, editar e remover eventos', 'eventos', 'admin', 6),
    (igreja_id_param, 'ver_eventos', 'Visualizar Eventos', 'Visualizar eventos e escalas', 'eventos', 'visualizacao', 2),
    (igreja_id_param, 'gerenciar_escalas', 'Gerenciar Escalas', 'Criar e editar escalas de eventos', 'eventos', 'edicao', 5),
    (igreja_id_param, 'gerenciar_cultos', 'Gerenciar Cultos', 'Gerenciar cultos padrão semanais', 'eventos', 'admin', 7),
    (igreja_id_param, 'gerenciar_mapa_talentos', 'Gerenciar Mapa de Talentos', 'Gerenciar mapa de talentos e habilidades dos membros', 'talentos', 'admin', 7),
    (igreja_id_param, 'visualizar_mapa_talentos', 'Visualizar Mapa de Talentos', 'Acessar mapa de talentos dos membros', 'talentos', 'visualizacao', 3),
    (igreja_id_param, 'gerenciar_relatorios', 'Gerenciar Relatórios', 'Gerenciar e configurar relatórios do sistema', 'relatorios', 'admin', 8),
    (igreja_id_param, 'visualizar_relatorios', 'Visualizar Relatórios', 'Acessar relatórios do sistema', 'relatorios', 'visualizacao', 3),
    (igreja_id_param, 'gerenciar_pedidos_especiais', 'Gerenciar Pedidos Especiais', 'Gerenciar pedidos especiais (batismo, casamento, etc.)', 'pedidos', 'admin', 7),
    (igreja_id_param, 'gerenciar_estatisticas', 'Gerenciar Estatísticas', 'Gerenciar métricas e estatísticas da igreja', 'estatisticas', 'admin', 7),
    (igreja_id_param, 'visualizar_estatisticas', 'Visualizar Estatísticas', 'Acessar estatísticas da igreja', 'estatisticas', 'visualizacao', 3),
    (igreja_id_param, 'gerenciar_calendario', 'Gerenciar Calendário', 'Gerenciar eventos e calendário da igreja', 'calendario', 'admin', 6),
    (igreja_id_param, 'visualizar_calendario', 'Visualizar Calendário', 'Acessar calendário da igreja', 'calendario', 'visualizacao', 2),

    -- Módulo: Financeiro
    (igreja_id_param, 'gerenciar_financeiro', 'Gerenciar Financeiro', 'Acesso completo ao módulo financeiro', 'financeiro', 'admin', 9),
    (igreja_id_param, 'ver_financeiro', 'Visualizar Financeiro', 'Visualizar relatórios e movimentos financeiros', 'financeiro', 'visualizacao', 3),
    (igreja_id_param, 'lancar_movimentos', 'Lançar Movimentos', 'Registrar entradas e saídas financeiras', 'financeiro', 'edicao', 6),
    (igreja_id_param, 'gerenciar_contas', 'Gerenciar Contas', 'Gerenciar contas bancárias e digitais', 'financeiro', 'admin', 8),
    (igreja_id_param, 'aprovar_pagamentos', 'Aprovar Pagamentos', 'Aprovar pagamentos pendentes', 'financeiro', 'edicao', 7),

    -- Módulo: Social e Comunicação
    (igreja_id_param, 'gerenciar_posts', 'Gerenciar Posts', 'Criar, editar e remover posts', 'social', 'edicao', 4),
    (igreja_id_param, 'ver_posts', 'Visualizar Posts', 'Visualizar posts e comentários', 'social', 'visualizacao', 1),
    (igreja_id_param, 'gerenciar_comunicacoes', 'Gerenciar Comunicações', 'Enviar comunicações oficiais', 'social', 'edicao', 5),
    (igreja_id_param, 'gerenciar_chats', 'Gerenciar Chats', 'Gerenciar chats da igreja', 'social', 'admin', 6),
    (igreja_id_param, 'gerenciar_chats_igreja', 'Gerenciar Chats da Igreja', 'Gerenciar chats e grupos da igreja', 'social', 'admin', 6),
    (igreja_id_param, 'gerenciar_mensagens_privadas', 'Gerenciar Mensagens Privadas', 'Gerenciar mensagens privadas', 'social', 'admin', 5),

    -- Módulo: Cursos e Educação
    (igreja_id_param, 'gerenciar_cursos', 'Gerenciar Cursos', 'Criar e gerenciar cursos', 'cursos', 'admin', 7),
    (igreja_id_param, 'ver_cursos', 'Visualizar Cursos', 'Visualizar cursos disponíveis', 'cursos', 'visualizacao', 2),
    (igreja_id_param, 'inscrever_alunos', 'Inscrever Alunos', 'Inscrever membros em cursos', 'cursos', 'edicao', 4),
    (igreja_id_param, 'gerenciar_inscricoes', 'Gerenciar Inscrições', 'Gerenciar inscrições em cursos', 'cursos', 'edicao', 5),
    (igreja_id_param, 'emitir_certificados', 'Emitir Certificados', 'Emitir certificados de conclusão', 'cursos', 'edicao', 5),
    (igreja_id_param, 'visualizar_certificados', 'Visualizar Certificados', 'Visualizar certificados emitidos', 'cursos', 'visualizacao', 3),

    -- Módulo: Recursos e Voluntariado
    (igreja_id_param, 'gerenciar_recursos', 'Gerenciar Recursos', 'Gerenciar recursos da igreja', 'recursos', 'admin', 6),
    (igreja_id_param, 'ver_recursos', 'Visualizar Recursos', 'Visualizar recursos disponíveis', 'recursos', 'visualizacao', 2),
    (igreja_id_param, 'gerenciar_voluntarios', 'Gerenciar Voluntários', 'Gerenciar voluntários e escalas', 'recursos', 'admin', 6),

    -- Módulo: Marketplace
    (igreja_id_param, 'gerenciar_produtos', 'Gerenciar Produtos', 'Gerenciar produtos no marketplace', 'marketplace', 'admin', 6),
    (igreja_id_param, 'ver_pedidos', 'Visualizar Pedidos', 'Visualizar pedidos do marketplace', 'marketplace', 'visualizacao', 3),
    (igreja_id_param, 'processar_pedidos', 'Processar Pedidos', 'Processar e entregar pedidos', 'marketplace', 'edicao', 5),
    (igreja_id_param, 'gerenciar_pedidos', 'Gerenciar Pedidos', 'Gerenciar pedidos do marketplace', 'marketplace', 'admin', 6),
    (igreja_id_param, 'gerenciar_pagamentos', 'Gerenciar Pagamentos', 'Gerenciar pagamentos do marketplace', 'marketplace', 'admin', 7),

    -- Módulo: Relatórios
    (igreja_id_param, 'gerar_relatorios', 'Gerar Relatórios', 'Gerar relatórios diversos do sistema', 'relatorios', 'visualizacao', 4),
    (igreja_id_param, 'ver_estatisticas', 'Ver Estatísticas', 'Acessar estatísticas da igreja', 'relatorios', 'visualizacao', 3),

    -- Módulo: Pedidos Especiais
    (igreja_id_param, 'gerenciar_pedidos', 'Gerenciar Pedidos', 'Gerenciar pedidos especiais (batismo, casamento, etc.)', 'pedidos', 'admin', 7),
    (igreja_id_param, 'ver_pedidos', 'Visualizar Pedidos', 'Visualizar pedidos especiais', 'pedidos', 'visualizacao', 3),
    (igreja_id_param, 'aprovar_pedidos', 'Aprovar Pedidos', 'Aprovar pedidos especiais', 'pedidos', 'edicao', 6),

    -- Módulo: Gamificação e Engajamento
    (igreja_id_param, 'gerenciar_engajamento', 'Gerenciar Engajamento', 'Gerenciar sistema de engajamento', 'engajamento', 'admin', 5),
    (igreja_id_param, 'ver_engajamento', 'Visualizar Engajamento', 'Visualizar métricas de engajamento', 'engajamento', 'visualizacao', 2),
    (igreja_id_param, 'gerenciar_badges', 'Gerenciar Badges', 'Gerenciar badges de engajamento', 'engajamento', 'admin', 6),
    (igreja_id_param, 'gerenciar_pontos', 'Gerenciar Pontos', 'Gerenciar pontos de engajamento', 'engajamento', 'admin', 6),
    (igreja_id_param, 'gerenciar_enquetes', 'Gerenciar Enquetes', 'Gerenciar enquetes de engajamento', 'engajamento', 'admin', 5),

    -- Módulo: Atendimentos Pastorais
    (igreja_id_param, 'gerenciar_atendimentos', 'Gerenciar Atendimentos', 'Registrar e gerenciar atendimentos pastorais', 'pastoral', 'edicao', 5),
    (igreja_id_param, 'ver_atendimentos', 'Visualizar Atendimentos', 'Visualizar atendimentos pastorais', 'pastoral', 'visualizacao', 3),
    (igreja_id_param, 'gerenciar_atendimentos_pastorais', 'Gerenciar Atendimentos Pastorais', 'Gerenciar atendimentos pastorais', 'pastoral', 'admin', 6),

    -- Módulo: Doações
    (igreja_id_param, 'gerenciar_doacoes', 'Gerenciar Doações', 'Gerenciar sistema de doações online', 'doacoes', 'admin', 6),
    (igreja_id_param, 'ver_doacoes', 'Visualizar Doações', 'Visualizar doações recebidas', 'doacoes', 'visualizacao', 3),
    (igreja_id_param, 'gerenciar_doacoes_online', 'Gerenciar Doações Online', 'Gerenciar doações online', 'doacoes', 'admin', 6),

    -- Módulo: Cartão de Membro
    (igreja_id_param, 'gerenciar_cartao_membro', 'Gerenciar Cartão de Membro', 'Gerenciar sistema de cartões de membro', 'cartao_membro', 'admin', 7),
    (igreja_id_param, 'ver_cartao_membro', 'Visualizar Cartão de Membro', 'Visualizar cartões de membro', 'cartao_membro', 'visualizacao', 3),
    (igreja_id_param, 'solicitar_cartao_membro', 'Solicitar Cartão de Membro', 'Solicitar emissão de cartão de membro', 'cartao_membro', 'edicao', 4),
    (igreja_id_param, 'aprovar_cartao_membro', 'Aprovar Cartão de Membro', 'Aprovar solicitações de cartão de membro', 'cartao_membro', 'edicao', 6),
    (igreja_id_param, 'imprimir_cartao_membro', 'Imprimir Cartão de Membro', 'Imprimir cartões de membro', 'cartao_membro', 'edicao', 5),
    (igreja_id_param, 'entregar_cartao_membro', 'Entregar Cartão de Membro', 'Registrar entrega de cartões de membro', 'cartao_membro', 'edicao', 5),

    -- Módulo: Relatório de Culto
    (igreja_id_param, 'gerenciar_relatorio_culto', 'Gerenciar Relatório de Culto', 'Gerenciar relatórios de culto', 'relatorio_culto', 'admin', 7),
    (igreja_id_param, 'ver_relatorio_culto', 'Visualizar Relatório de Culto', 'Visualizar relatórios de culto', 'relatorio_culto', 'visualizacao', 3),
    (igreja_id_param, 'criar_relatorio_culto', 'Criar Relatório de Culto', 'Criar novos relatórios de culto', 'relatorio_culto', 'edicao', 4),
    (igreja_id_param, 'editar_relatorio_culto', 'Editar Relatório de Culto', 'Editar relatórios de culto existentes', 'relatorio_culto', 'edicao', 5),
    (igreja_id_param, 'finalizar_relatorio_culto', 'Finalizar Relatório de Culto', 'Marcar relatórios como finalizados', 'relatorio_culto', 'edicao', 5),
    (igreja_id_param, 'avaliar_relatorio_culto', 'Avaliar Relatório de Culto', 'Avaliar e aprovar relatórios de culto', 'relatorio_culto', 'edicao', 6),

    -- Módulo: Sistema de Follow (Seguir Usuários)
    (igreja_id_param, 'gerenciar_follow', 'Gerenciar Sistema de Follow', 'Gerenciar sistema de seguir usuários', 'follow', 'admin', 6),
    (igreja_id_param, 'seguir_usuarios', 'Seguir Usuários', 'Seguir outros usuários do sistema', 'follow', 'edicao', 2),
    (igreja_id_param, 'ver_seguidores', 'Ver Seguidores', 'Visualizar lista de seguidores', 'follow', 'visualizacao', 2),
    (igreja_id_param, 'ver_seguidos', 'Ver Seguidos', 'Visualizar lista de usuários seguidos', 'follow', 'visualizacao', 2),
    (igreja_id_param, 'gerenciar_notificacoes_follow', 'Gerenciar Notificações de Follow', 'Gerenciar notificações de usuários seguidos', 'follow', 'admin', 5),

    -- Módulo: Migração de Membros
    (igreja_id_param, 'gerenciar_migracao_membros', 'Gerenciar Migração de Membros', 'Gerenciar migrações de membros entre igrejas', 'migracao', 'admin', 8),
    (igreja_id_param, 'ver_historico_migracao', 'Ver Histórico de Migração', 'Visualizar histórico de migrações de membros', 'migracao', 'visualizacao', 4),
    (igreja_id_param, 'migrar_membro', 'Migrar Membro', 'Realizar migração de membros para outras igrejas', 'migracao', 'edicao', 7),
    (igreja_id_param, 'aprovar_migracao', 'Aprovar Migração', 'Aprovar solicitações de migração de membros', 'migracao', 'edicao', 7),

    -- Módulo: Vitrine de Igrejas
    (igreja_id_param, 'gerenciar_vitrine_igrejas', 'Gerenciar Vitrine de Igrejas', 'Gerenciar vitrine e exposição das igrejas', 'vitrine', 'admin', 7),
    (igreja_id_param, 'ver_vitrine_igrejas', 'Ver Vitrine de Igrejas', 'Visualizar vitrine das igrejas', 'vitrine', 'visualizacao', 2),

    -- Módulo: Sistema Geral
    (igreja_id_param, 'gerenciar_assinaturas', 'Gerenciar Assinaturas', 'Gerenciar assinaturas do sistema', 'sistema', 'admin', 9),
    (igreja_id_param, 'gerenciar_definicoes', 'Gerenciar Definições', 'Gerenciar configurações e definições do sistema', 'sistema', 'admin', 8),
    (igreja_id_param, 'acessar_definicoes', 'Acessar Definições', 'Acessar configurações do sistema', 'sistema', 'visualizacao', 4),
    (igreja_id_param, 'gerenciar_sms', 'Gerenciar SMS', 'Gerenciar sistema de mensagens SMS administrativas', 'sms', 'admin', 8),

    -- Módulo: Controle de Acesso
    (igreja_id_param, 'gerenciar_controle_acesso', 'Gerenciar Controle de Acesso', 'Gerenciar sistema de controle de acesso e permissões', 'sistema', 'admin', 9)
    ON CONFLICT (igreja_id, codigo) DO NOTHING;

END;
$$ LANGUAGE plpgsql;


-- ================================================================
-- Função: garantir_permissoes_padrao
-- Verifica se a igreja já possui permissões e cria se necessário
-- ================================================================
CREATE OR REPLACE FUNCTION garantir_permissoes_padrao(p_igreja_id BIGINT)
RETURNS BOOLEAN AS $$
DECLARE
    v_total_permissoes INT;
BEGIN
    -- Conta quantas permissões já existem
    SELECT COUNT(*) INTO v_total_permissoes
    FROM igreja_permissoes
    WHERE igreja_id = p_igreja_id;

    IF v_total_permissoes = 0 THEN
        -- Nenhuma permissão? Cria todas as padrão
        PERFORM criar_permissoes_padrao(p_igreja_id);
        RAISE NOTICE 'Permissões padrão criadas para igreja_id=%', p_igreja_id;
        RETURN TRUE;
    ELSE
        -- Já possui permissões, não faz nada
        RAISE NOTICE 'Igreja % já possui % permissões registradas', p_igreja_id, v_total_permissoes;
        RETURN FALSE;
    END IF;
END;
$$ LANGUAGE plpgsql;


-- =========================================================
-- FIM DO SISTEMA DE PERMISSÕES - RBAC
-- =========================================================

-- =========================================================
-- SISTEMA DE SEGUIR/USUÁRIOS (FOLLOW SYSTEM)
-- Sistema similar ao Facebook/Instagram para seguir usuários
-- e receber notificações de suas atividades
-- =========================================================

-- ==================== USER_FOLLOWS ====================
-- Tabela para relacionamentos de seguimento entre usuários
CREATE TABLE IF NOT EXISTS user_follows (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    follower_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    followed_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status VARCHAR(20) DEFAULT 'ativo' CHECK (status IN ('ativo', 'bloqueado')),
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),

    -- Unicidade: um usuário não pode seguir o mesmo usuário duas vezes
    UNIQUE(follower_id, followed_id),

    -- Validação: usuário não pode seguir a si mesmo
    CONSTRAINT check_not_self_follow CHECK (follower_id != followed_id)
);

-- Índices para performance
CREATE INDEX IF NOT EXISTS idx_user_follows_follower ON user_follows(follower_id);
CREATE INDEX IF NOT EXISTS idx_user_follows_followed ON user_follows(followed_id);
CREATE INDEX IF NOT EXISTS idx_user_follows_status ON user_follows(status);
CREATE INDEX IF NOT EXISTS idx_user_follows_created_at ON user_follows(created_at);

-- ==================== USER_FOLLOW_ACTIVITIES ====================
-- Tabela para registrar atividades dos usuários seguidos
-- (opcional, para notificações mais detalhadas)
CREATE TABLE IF NOT EXISTS user_follow_activities (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- quem fez a atividade
    activity_type VARCHAR(50) NOT NULL CHECK (activity_type IN ('post_created', 'post_liked', 'comment_created', 'event_created', 'message_sent')),
    reference_id UUID, -- ID do post, comentário, evento, etc.
    reference_type VARCHAR(50), -- 'post', 'comment', 'event', 'message'
    description TEXT, -- descrição da atividade
    metadata JSONB DEFAULT '{}'::jsonb, -- dados adicionais
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Índices para user_follow_activities
CREATE INDEX IF NOT EXISTS idx_user_follow_activities_user_type ON user_follow_activities(user_id, activity_type);
CREATE INDEX IF NOT EXISTS idx_user_follow_activities_created_at ON user_follow_activities(created_at);
CREATE INDEX IF NOT EXISTS idx_user_follow_activities_reference ON user_follow_activities(reference_id, reference_type);

-- ==================== USER_FOLLOW_NOTIFICATIONS ====================
-- Tabela para notificações específicas de usuários seguidos
CREATE TABLE IF NOT EXISTS user_follow_notifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    follower_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- quem recebe a notificação
    followed_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE, -- quem fez a atividade
    activity_id UUID REFERENCES user_follow_activities(id) ON DELETE CASCADE, -- referência à atividade
    notification_type VARCHAR(50) NOT NULL, -- tipo da notificação
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSONB DEFAULT '{}'::jsonb, -- dados adicionais
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

-- Índices para user_follow_notifications
CREATE INDEX IF NOT EXISTS idx_user_follow_notifications_follower ON user_follow_notifications(follower_id);
CREATE INDEX IF NOT EXISTS idx_user_follow_notifications_followed ON user_follow_notifications(followed_id);
CREATE INDEX IF NOT EXISTS idx_user_follow_notifications_is_read ON user_follow_notifications(is_read);
CREATE INDEX IF NOT EXISTS idx_user_follow_notifications_created_at ON user_follow_notifications(created_at);

-- ==================== COMENTÁRIOS DAS TABELAS ====================

COMMENT ON TABLE user_follows IS 'Relacionamentos de seguimento entre usuários do sistema';
COMMENT ON COLUMN user_follows.follower_id IS 'ID do usuário que está seguindo';
COMMENT ON COLUMN user_follows.followed_id IS 'ID do usuário que está sendo seguido';
COMMENT ON COLUMN user_follows.status IS 'Status do seguimento: ativo ou bloqueado';

COMMENT ON TABLE user_follow_activities IS 'Registro de atividades dos usuários para notificações de seguidores';
COMMENT ON COLUMN user_follow_activities.activity_type IS 'Tipo da atividade realizada';
COMMENT ON COLUMN user_follow_activities.reference_id IS 'ID do objeto relacionado (post, comentário, etc.)';
COMMENT ON COLUMN user_follow_activities.reference_type IS 'Tipo do objeto relacionado';

COMMENT ON TABLE user_follow_notifications IS 'Notificações específicas enviadas aos seguidores sobre atividades';
COMMENT ON COLUMN user_follow_notifications.follower_id IS 'Usuário que recebe a notificação';
COMMENT ON COLUMN user_follow_notifications.followed_id IS 'Usuário que realizou a atividade';
COMMENT ON COLUMN user_follow_notifications.activity_id IS 'Referência à atividade que gerou a notificação';

-- ==================== FUNÇÕES ÚTEIS ====================

-- Função para seguir um usuário
CREATE OR REPLACE FUNCTION seguir_usuario(
    p_follower_id UUID,
    p_followed_id UUID
) RETURNS BOOLEAN AS $$
DECLARE
    v_exists BOOLEAN;
BEGIN
    -- Verificar se já existe o seguimento
    SELECT EXISTS(
        SELECT 1 FROM user_follows
        WHERE follower_id = p_follower_id
          AND followed_id = p_followed_id
          AND status = 'ativo'
    ) INTO v_exists;

    IF v_exists THEN
        -- Já está seguindo
        RETURN FALSE;
    END IF;

    -- Verificar se não está tentando seguir a si mesmo
    IF p_follower_id = p_followed_id THEN
        RETURN FALSE;
    END IF;

    -- Inserir novo seguimento
    INSERT INTO user_follows (follower_id, followed_id, status)
    VALUES (p_follower_id, p_followed_id, 'ativo');

    RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

-- Função para deixar de seguir um usuário
CREATE OR REPLACE FUNCTION deixar_seguir_usuario(
    p_follower_id UUID,
    p_followed_id UUID
) RETURNS BOOLEAN AS $$
DECLARE
    v_affected INTEGER;
BEGIN
    -- Remover o seguimento (soft delete via status)
    UPDATE user_follows
    SET status = 'bloqueado',
        updated_at = now()
    WHERE follower_id = p_follower_id
      AND followed_id = p_followed_id
      AND status = 'ativo';

    GET DIAGNOSTICS v_affected = ROW_COUNT;

    RETURN v_affected > 0;
END;
$$ LANGUAGE plpgsql;

-- Função para verificar se um usuário está seguindo outro
CREATE OR REPLACE FUNCTION esta_seguindo(
    p_follower_id UUID,
    p_followed_id UUID
) RETURNS BOOLEAN AS $$
BEGIN
    RETURN EXISTS(
        SELECT 1 FROM user_follows
        WHERE follower_id = p_follower_id
          AND followed_id = p_followed_id
          AND status = 'ativo'
    );
END;
$$ LANGUAGE plpgsql;

-- Função para contar seguidores de um usuário
CREATE OR REPLACE FUNCTION contar_seguidores(p_user_id UUID) RETURNS INTEGER AS $$
BEGIN
    RETURN (
        SELECT COUNT(*)::INTEGER
        FROM user_follows
        WHERE followed_id = p_user_id
          AND status = 'ativo'
    );
END;
$$ LANGUAGE plpgsql;

-- Função para contar usuários seguidos por um usuário
CREATE OR REPLACE FUNCTION contar_seguidos(p_user_id UUID) RETURNS INTEGER AS $$
BEGIN
    RETURN (
        SELECT COUNT(*)::INTEGER
        FROM user_follows
        WHERE follower_id = p_user_id
          AND status = 'ativo'
    );
END;
$$ LANGUAGE plpgsql;

-- Função para registrar atividade de usuário seguido
CREATE OR REPLACE FUNCTION registrar_atividade_seguidor(
    p_user_id UUID,
    p_activity_type VARCHAR(50),
    p_reference_id UUID DEFAULT NULL,
    p_reference_type VARCHAR(50) DEFAULT NULL,
    p_description TEXT DEFAULT NULL,
    p_metadata JSONB DEFAULT '{}'::jsonb
) RETURNS UUID AS $$
DECLARE
    v_activity_id UUID;
BEGIN
    -- Inserir atividade
    INSERT INTO user_follow_activities (
        user_id, activity_type, reference_id, reference_type,
        description, metadata
    ) VALUES (
        p_user_id, p_activity_type, p_reference_id, p_reference_type,
        p_description, p_metadata
    ) RETURNING id INTO v_activity_id;

    -- Notificar seguidores (se houver)
    INSERT INTO user_follow_notifications (
        follower_id, followed_id, activity_id,
        notification_type, title, message, data
    )
    SELECT
        uf.follower_id,
        p_user_id,
        v_activity_id,
        p_activity_type,
        CASE
            WHEN p_activity_type = 'post_created' THEN 'Novo post'
            WHEN p_activity_type = 'post_liked' THEN 'Post curtido'
            WHEN p_activity_type = 'comment_created' THEN 'Novo comentário'
            WHEN p_activity_type = 'event_created' THEN 'Novo evento'
            ELSE 'Nova atividade'
        END,
        COALESCE(p_description, 'Uma pessoa que você segue teve uma nova atividade'),
        jsonb_build_object(
            'activity_type', p_activity_type,
            'reference_id', p_reference_id,
            'reference_type', p_reference_type,
            'user_id', p_user_id
        )
    FROM user_follows uf
    WHERE uf.followed_id = p_user_id
      AND uf.status = 'ativo';

    RETURN v_activity_id;
END;
$$ LANGUAGE plpgsql;


SET TIMEZONE = 'Africa/Luanda';


-- =========================================================
-- ÍNDICES ADICIONAIS PARA OTIMIZAÇÃO DE PERFORMANCE
-- Adicionados em 2025-12-23 para melhorar consultas frequentes
-- =========================================================

-- Índices para tabela users
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at);
CREATE INDEX IF NOT EXISTS idx_users_is_active ON users(is_active);

-- Índices para tabela igreja_membros
CREATE INDEX IF NOT EXISTS idx_igreja_membros_status ON igreja_membros(status);
CREATE INDEX IF NOT EXISTS idx_igreja_membros_data_entrada ON igreja_membros(data_entrada);
CREATE INDEX IF NOT EXISTS idx_igreja_membros_cargo ON igreja_membros(cargo);

-- Índices para tabela financeiro_movimentos
CREATE INDEX IF NOT EXISTS idx_financeiro_movimentos_tipo ON financeiro_movimentos(tipo);
CREATE INDEX IF NOT EXISTS idx_financeiro_movimentos_categoria_id ON financeiro_movimentos(categoria_id);
CREATE INDEX IF NOT EXISTS idx_financeiro_movimentos_responsavel_id ON financeiro_movimentos(responsavel_id);

-- Índices para tabela agendamentos
CREATE INDEX IF NOT EXISTS idx_agendamentos_responsavel ON agendamentos(responsavel_id);
CREATE INDEX IF NOT EXISTS idx_agendamentos_convidado ON agendamentos(convidado_id);

-- Índices para tabela eventos
CREATE INDEX IF NOT EXISTS idx_eventos_igreja_data ON eventos(igreja_id, data_evento);
CREATE INDEX IF NOT EXISTS idx_eventos_tipo ON eventos(tipo);
CREATE INDEX IF NOT EXISTS idx_eventos_status ON eventos(status);

-- Índices para tabela pedidos_especiais
CREATE INDEX IF NOT EXISTS idx_pedidos_especiais_status ON pedidos_especiais(status);
CREATE INDEX IF NOT EXISTS idx_pedidos_especiais_data_pedido ON pedidos_especiais(data_pedido);
CREATE INDEX IF NOT EXISTS idx_pedidos_especiais_responsavel_id ON pedidos_especiais(responsavel_id);

-- Índices para tabela notificacoes
CREATE INDEX IF NOT EXISTS idx_notificacoes_user_lida ON notificacoes(user_id, lida);
CREATE INDEX IF NOT EXISTS idx_notificacoes_tipo ON notificacoes(tipo);

-- Índices para tabela posts
CREATE INDEX IF NOT EXISTS idx_posts_created_at ON posts(created_at);
CREATE INDEX IF NOT EXISTS idx_posts_author ON posts(author_id);

-- Índices para tabela comunicacoes
CREATE INDEX IF NOT EXISTS idx_comunicacoes_igreja_tipo ON comunicacoes(igreja_id, tipo);
CREATE INDEX IF NOT EXISTS idx_comunicacoes_data_envio ON comunicacoes(data_envio);

-- Índices para tabela doacoes_online
CREATE INDEX IF NOT EXISTS idx_doacoes_online_igreja_status ON doacoes_online(igreja_id, status);
CREATE INDEX IF NOT EXISTS idx_doacoes_online_user ON doacoes_online(user_id);

-- Índices para tabela engajamento_pontos
CREATE INDEX IF NOT EXISTS idx_engajamento_pontos_user_igreja ON engajamento_pontos(user_id, igreja_id);
CREATE INDEX IF NOT EXISTS idx_engajamento_pontos_data ON engajamento_pontos(data);

-- Índices para tabela atendimentos_pastorais
CREATE INDEX IF NOT EXISTS idx_atendimentos_pastorais_igreja ON atendimentos_pastorais(igreja_id);
CREATE INDEX IF NOT EXISTS idx_atendimentos_pastorais_pastor ON atendimentos_pastorais(pastor_id);
CREATE INDEX IF NOT EXISTS idx_atendimentos_pastorais_membro ON atendimentos_pastorais(membro_id);

-- Índices para tabela pedidos_oracao
CREATE INDEX IF NOT EXISTS idx_pedidos_oracao_igreja ON pedidos_oracao(igreja_id);
CREATE INDEX IF NOT EXISTS idx_pedidos_oracao_membro ON pedidos_oracao(membro_id);

-- Índices para tabela voluntarios
CREATE INDEX IF NOT EXISTS idx_voluntarios_membro ON voluntarios(membro_id);

-- Índices para tabela escala_auto
CREATE INDEX IF NOT EXISTS idx_escala_auto_voluntario ON escala_auto(voluntario_id);
CREATE INDEX IF NOT EXISTS idx_escala_auto_data ON escala_auto(data);

-- Índices para tabela recursos
CREATE INDEX IF NOT EXISTS idx_recursos_igreja ON recursos(igreja_id);
CREATE INDEX IF NOT EXISTS idx_recursos_tipo ON recursos(tipo);

-- Índices para tabela agendamentos_recursos
CREATE INDEX IF NOT EXISTS idx_agendamentos_recursos_recurso ON agendamentos_recursos(recurso_id);
CREATE INDEX IF NOT EXISTS idx_agendamentos_recursos_reservado_por ON agendamentos_recursos(reservado_por);

-- Índices para tabela relatorios_cache
CREATE INDEX IF NOT EXISTS idx_relatorios_cache_igreja ON relatorios_cache(igreja_id);
CREATE INDEX IF NOT EXISTS idx_relatorios_cache_tipo ON relatorios_cache(tipo);

-- Índices para tabela auditoria_logs
CREATE INDEX IF NOT EXISTS idx_auditoria_logs_tabela ON auditoria_logs(tabela);
CREATE INDEX IF NOT EXISTS idx_auditoria_logs_usuario ON auditoria_logs(usuario_id);
CREATE INDEX IF NOT EXISTS idx_auditoria_logs_data ON auditoria_logs(data_acao);

-- Índices para tabela comentarios
CREATE INDEX IF NOT EXISTS idx_comentarios_post ON comentarios(post_id);
CREATE INDEX IF NOT EXISTS idx_comentarios_evento ON comentarios(evento_id);
CREATE INDEX IF NOT EXISTS idx_comentarios_user ON comentarios(user_id);

-- Índices para tabela habilidades_membros
CREATE INDEX IF NOT EXISTS idx_habilidades_membros_membro ON habilidades_membros(membro_id);

-- Índices para tabela marketplace_pedidos
CREATE INDEX IF NOT EXISTS idx_marketplace_pedidos_comprador ON marketplace_pedidos(comprador_id);
CREATE INDEX IF NOT EXISTS idx_marketplace_pedidos_status ON marketplace_pedidos(status);

-- Índices para tabela marketplace_pagamentos
CREATE INDEX IF NOT EXISTS idx_marketplace_pagamentos_pedido ON marketplace_pagamentos(pedido_id);
CREATE INDEX IF NOT EXISTS idx_marketplace_pagamentos_status ON marketplace_pagamentos(status);

-- Índices para tabela cultos_padrao
CREATE INDEX IF NOT EXISTS idx_cultos_padrao_igreja ON cultos_padrao(igreja_id);
CREATE INDEX IF NOT EXISTS idx_cultos_padrao_dia_semana ON cultos_padrao(dia_semana);

-- Índices para tabela enquete_denuncias
CREATE INDEX IF NOT EXISTS idx_enquete_denuncias_igreja ON enquete_denuncias(igreja_id);
CREATE INDEX IF NOT EXISTS idx_enquete_denuncias_data ON enquete_denuncias(data);

-- Índices para tabela financeiro_auditoria
CREATE INDEX IF NOT EXISTS idx_financeiro_auditoria_movimento ON financeiro_auditoria(movimento_id);
CREATE INDEX IF NOT EXISTS idx_financeiro_auditoria_alterado_por ON financeiro_auditoria(alterado_por);

-- Índices para tabela agenda
CREATE INDEX IF NOT EXISTS idx_agenda_user ON agenda(user_id);
CREATE INDEX IF NOT EXISTS idx_agenda_evento ON agenda(evento_id);

-- Índices para tabela igreja_aliancas
CREATE INDEX IF NOT EXISTS idx_igreja_aliancas_igreja ON igreja_aliancas(igreja_id);
CREATE INDEX IF NOT EXISTS idx_igreja_aliancas_alianca ON igreja_aliancas(alianca_id);

-- Índices para tabela alianca_lideres
CREATE INDEX IF NOT EXISTS idx_alianca_lideres_membro ON alianca_lideres(membro_id);

-- Índices para tabela alianca_mensagem_leituras
CREATE INDEX IF NOT EXISTS idx_alianca_mensagem_leituras_membro ON alianca_mensagem_leituras(membro_id);

-- Índices para tabela alianca_comunidade_leituras
CREATE INDEX IF NOT EXISTS idx_alianca_comunidade_leituras_membro ON alianca_comunidade_leituras(membro_id);

-- =========================================================
-- FIM DOS ÍNDICES ADICIONAIS
-- =========================================================
