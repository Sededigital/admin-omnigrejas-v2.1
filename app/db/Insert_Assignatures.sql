-- =========================================================
-- OMNIGREJAS • INSERTS PARA PACOTES DE ASSINATURA
-- Inserts para módulos, pacotes, permissões, recursos e níveis
-- Data: 2025-12-23
-- Arquivo: Insert_Assignatures.sql
-- =========================================================

-- Extensões necessárias (se não estiverem habilitadas)
-- CREATE EXTENSION IF NOT EXISTS pgcrypto;

SET TIMEZONE = 'Africa/Luanda';

-- ==================== INSERIR MÓDULOS ====================
-- Inserindo módulos baseados em modulos_existentes.md
INSERT INTO modulos (nome, descricao, created_at, updated_at) VALUES
('Gestão Organizacional', 'Módulos para gestão de igrejas, membros, ministérios e alianças', now(), now()),
('Eventos e Escalas', 'Gerenciamento de eventos, escalas e talentos', now(), now()),
('Educação Cristã', 'Sistema de cursos e certificados', now(), now()),
('Financeiro', 'Controle financeiro, doações e pagamentos', now(), now()),
('Comunicação Social', 'Posts, comunicações e chats', now(), now()),
('Voluntariado e Recursos', 'Gerenciamento de voluntários e recursos', now(), now()),
('Atendimentos Pastorais', 'Registro de atendimentos pastorais', now(), now()),
('Marketplace', 'Sistema de produtos e pedidos', now(), now()),
('Engajamento e Gamificação', 'Sistema de engajamento e badges', now(), now()),
('Sistema Geral', 'Assinaturas, definições e SMS', now(), now())
ON CONFLICT (nome) DO NOTHING;

-- ==================== INSERIR PACOTES ====================
-- 4 tipos de pacotes: Básico, Intermediário, Premium, Empresarial
-- Pacotes mensais (duracao_meses = 1), sistema calcula anual automaticamente
INSERT INTO pacote (nome, descricao, preco, preco_vitalicio, duracao_meses, trial_dias, created_at, updated_at) VALUES
('Básico', 'Pacote básico para igrejas pequenas - até 50 membros (mensal)', 16667.00, 500000.00, 1, 30, now(), now()),
('Intermediário', 'Pacote intermediário para igrejas médias - até 200 membros (mensal)', 50000.00, 1000000.00, 1, 30, now(), now()),
('Premium', 'Pacote premium para igrejas grandes - até 1000 membros (mensal)', 100000.00, 2000000.00, 1, 30, now(), now()),
('Empresarial', 'Pacote empresarial para igrejas muito grandes - membros ilimitados (mensal)', 200000.00, 5000000.00, 1, 30, now(), now())
ON CONFLICT (nome) DO NOTHING;

-- ==================== INSERIR PERMISSÕES DOS PACOTES ====================
-- Associar módulos aos pacotes (pacote_permissao)
-- Primeiro, obter IDs dos pacotes e módulos inseridos
INSERT INTO pacote_permissao (pacote_id, modulo_id, permissao, created_at, updated_at)
SELECT p.id, m.id, 'leitura', now(), now()
FROM pacote p
CROSS JOIN modulos m
WHERE p.nome = 'Básico'
  AND m.nome IN ('Gestão Organizacional', 'Eventos e Escalas', 'Sistema Geral')
ON CONFLICT (pacote_id, modulo_id) DO NOTHING;

-- Pacotes Intermediário, Premium e Empresarial têm mais módulos
INSERT INTO pacote_permissao (pacote_id, modulo_id, permissao, created_at, updated_at)
SELECT p.id, m.id, 'escrita', now(), now()
FROM pacote p
CROSS JOIN modulos m
WHERE p.nome IN ('Intermediário', 'Premium', 'Empresarial')
  AND m.nome IN ('Educação Cristã', 'Financeiro', 'Comunicação Social')
ON CONFLICT (pacote_id, modulo_id) DO NOTHING;

-- Pacotes Premium e Empresarial têm todos os módulos
INSERT INTO pacote_permissao (pacote_id, modulo_id, permissao, created_at, updated_at)
SELECT p.id, m.id, 'escrita', now(), now()
FROM pacote p
CROSS JOIN modulos m
WHERE p.nome IN ('Premium', 'Empresarial')
  AND m.nome IN ('Voluntariado e Recursos', 'Atendimentos Pastorais', 'Marketplace', 'Engajamento e Gamificação')
ON CONFLICT (pacote_id, modulo_id) DO NOTHING;

-- ==================== INSERIR RECURSOS DOS PACOTES ====================
-- Recursos: permissões específicas baseadas em igreja_permissoes (pacote_recursos)
-- Usando os códigos das permissões como recurso_tipo, com limite_valor = 1 (habilitado) ou NULL (desabilitado)
INSERT INTO pacote_recursos (pacote_id, recurso_tipo, limite_valor, unidade, ativo, created_at, updated_at) VALUES
-- Básico: permissões básicas
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_eventos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_posts', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_cursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'ver_recursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Básico'), 'gerenciar_assinaturas', 1, 'habilitado', TRUE, now(), now()),

-- Intermediário: permissões intermediárias
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'editar_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'editar_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'gerenciar_membros_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_eventos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'gerenciar_escalas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_posts', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'gerenciar_posts', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_cursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'inscrever_alunos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_recursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'ver_financeiro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'gerenciar_assinaturas', 1, 'habilitado', TRUE, now(), now()),

-- Premium: permissões avançadas
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_cartoes_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_eventos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_cultos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_mapa_talentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_relatorios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_comunicacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_chats', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_cursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_inscricoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'emitir_certificados', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_recursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_voluntarios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_produtos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_pagamentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_engajamento', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_atendimentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_doacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_financeiro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_assinaturas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'gerenciar_definicoes', 1, 'habilitado', TRUE, now(), now()),

-- Empresarial: todas as permissões habilitadas
((SELECT id FROM pacote WHERE nome = 'Empresarial'), 'gerenciar_corpo_lideranca', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_cartoes_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_eventos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_cultos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_mapa_talentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_relatorios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_comunicacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_chats', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_cursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_inscricoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'emitir_certificados', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_recursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_voluntarios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_produtos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_pagamentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_engajamento', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_atendimentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_doacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_financeiro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_assinaturas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Premium%'), 'gerenciar_definicoes', 1, 'habilitado', TRUE, now(), now()),

-- Empresarial: todas as permissões habilitadas
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_corpo_lideranca', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'editar_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'editar_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_cartoes_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_membros_ministerios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_aliancas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_aliancas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_eventos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_eventos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_escalas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_cultos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_mapa_talentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'visualizar_mapa_talentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_relatorios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'visualizar_relatorios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerar_relatorios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_pedidos_especiais', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'aprovar_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_estatisticas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'visualizar_estatisticas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_estatisticas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_calendario', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'visualizar_calendario', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_financeiro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_financeiro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'lancar_movimentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_contas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'aprovar_pagamentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_doacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_doacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_doacoes_online', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_posts', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_posts', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_comunicacoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_chats', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_chats_igreja', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_mensagens_privadas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_cursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_cursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'inscrever_alunos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_inscricoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'emitir_certificados', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'visualizar_certificados', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_voluntarios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_recursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_recursos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_atendimentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_atendimentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_atendimentos_pastorais', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_produtos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'processar_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_pedidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_pagamentos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_engajamento', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_engajamento', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_badges', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_pontos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_enquetes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_assinaturas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_definicoes', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'acessar_definicoes', 1, 'habilitado', TRUE, now(), now()),
-- Empresarial: todas as permissões habilitadas (continuação)
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_migracao_membros', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_historico_migracao', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'migrar_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'aprovar_migracao', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_vitrine_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_vitrine_igrejas', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_cartao_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_cartao_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'solicitar_cartao_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'aprovar_cartao_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'imprimir_cartao_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'entregar_cartao_membro', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_relatorio_culto', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_relatorio_culto', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'criar_relatorio_culto', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'editar_relatorio_culto', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'finalizar_relatorio_culto', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'avaliar_relatorio_culto', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_follow', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'seguir_usuarios', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_seguidores', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'ver_seguidos', 1, 'habilitado', TRUE, now(), now()),
((SELECT id FROM pacote WHERE nome LIKE 'Empresarial%'), 'gerenciar_notificacoes_follow', 1, 'habilitado', TRUE, now(), now())
ON CONFLICT (pacote_id, recurso_tipo) DO NOTHING;

-- ==================== INSERIR NÍVEIS DOS PACOTES ====================
-- Níveis hierárquicos (pacote_niveis)
INSERT INTO pacote_niveis (pacote_id, nivel, prioridade, recursos_extras, created_at, updated_at) VALUES
((SELECT id FROM pacote WHERE nome = 'Básico'), 'Básico', 1, '{"suporte": "email", "backup": "diario"}', now(), now()),
((SELECT id FROM pacote WHERE nome = 'Intermediário'), 'Intermediário', 2, '{"suporte": "chat", "backup": "diario", "relatorios": "avancados"}', now(), now()),
((SELECT id FROM pacote WHERE nome = 'Premium'), 'Premium', 3, '{"suporte": "telefone", "backup": "hora", "relatorios": "avancados", "api": "basica"}', now(), now()),
((SELECT id FROM pacote WHERE nome = 'Empresarial'), 'Empresarial', 4, '{"suporte": "dedicado", "backup": "tempo_real", "relatorios": "personalizados", "api": "completa", "integracoes": "ilimitadas"}', now(), now())
ON CONFLICT (pacote_id, nivel) DO NOTHING;

-- ==================== INSERIR CUPONS DE DESCONTO ====================
-- Cupons para promoções e descontos
INSERT INTO assinatura_cupons (codigo, descricao, desconto_percentual, desconto_valor, valido_de, valido_ate, uso_max, ativo, created_at, updated_at) VALUES
('OMNI10', 'Desconto de 10% para novos usuários', 10, NULL, '2025-01-01', '2025-12-31', 100, TRUE, now(), now()),
('ANUAL20', 'Desconto de 20% para assinaturas anuais', 20, NULL, '2025-01-01', '2025-12-31', 50, TRUE, now(), now()),
('EMPRESARIAL', 'Desconto especial para pacotes empresariais', NULL, 50000.00, '2025-01-01', '2025-12-31', 10, TRUE, now(), now())
ON CONFLICT (codigo) DO NOTHING;
