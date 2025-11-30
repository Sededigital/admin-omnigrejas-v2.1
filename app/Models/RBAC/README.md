# 📋 Models RBAC - Sistema de Permissões

## 📁 Estrutura das Models

Esta pasta contém as 5 models principais do sistema de permissões baseado em RBAC (Role-Based Access Control):

### 1. **`IgrejaPermissao.php`**
**Tabela:** `igreja_permissoes`
- Gerencia permissões específicas por igreja
- Campos: `codigo`, `nome`, `modulo`, `categoria`, `nivel_hierarquia`
- Relacionamentos: `igreja`, `funcoes` (many-to-many)

### 2. **`IgrejaFuncao.php`**
**Tabela:** `igreja_funcoes`
- Gerencia funções/roles atribuíveis aos membros
- Campos: `nome`, `descricao`, `nivel_hierarquia`, `cor_identificacao`
- Relacionamentos: `igreja`, `permissoes` (many-to-many), `membroFuncoes`

### 3. **`IgrejaFuncaoPermissao.php`**
**Tabela:** `igreja_funcao_permissoes`
- Tabela pivot entre funções e permissões
- Campos: `funcao_id`, `permissao_id`, `concedido_por`, `concedido_em`
- Relacionamentos: `funcao`, `permissao`, `concedidoPor`

### 4. **`IgrejaMembroFuncao.php`**
**Tabela:** `igreja_membro_funcoes`
- Atribuição de funções aos membros da igreja
- Campos: `membro_id`, `funcao_id`, `status`, `valido_ate`, `motivo_atribuicao`
- Relacionamentos: `membro`, `funcao`, `igreja`, `atribuidoPor`

### 5. **`IgrejaPermissaoLog.php`**
**Tabela:** `igreja_permissao_logs`
- Auditoria completa de todas as ações relacionadas a permissões
- Campos: `acao`, `detalhes` (JSON), `realizado_por`, `realizado_em`
- Relacionamentos: `igreja`, `membro`, `funcao`, `permissao`, `realizadoPor`

---

## 🔗 Relacionamentos

```
Igreja (1) ──── (N) IgrejaPermissao
    │                   │
    │                   │
    └─── (N) IgrejaFuncao ────┐
              │              │
              │              │
              └── (N) IgrejaMembroFuncao
                     │
                     │
              (N) IgrejaPermissaoLog
```

---

## 🎯 Uso Básico

### Verificar se um membro tem uma permissão específica:

```php
use App\Models\RBAC\IgrejaMembroFuncao;
use App\Models\RBAC\IgrejaPermissao;

// Verificar se membro tem permissão específica
$temPermissao = IgrejaMembroFuncao::where('membro_id', $membroId)
    ->where('status', 'ativo')
    ->whereHas('funcao.permissoes', function($q) {
        $q->where('codigo', 'gerenciar_membros');
    })
    ->exists();
```

### Atribuir função a um membro:

```php
use App\Models\RBAC\IgrejaMembroFuncao;

IgrejaMembroFuncao::create([
    'membro_id' => $membroId,
    'funcao_id' => $funcaoId,
    'igreja_id' => $igrejaId,
    'atribuido_por' => auth()->id(),
    'motivo_atribuicao' => 'Auxílio na gestão de membros',
    'status' => 'ativo'
]);
```

### Registrar log de auditoria:

```php
use App\Models\RBAC\IgrejaPermissaoLog;

IgrejaPermissaoLog::logAtribuicaoFuncao(
    $membro,
    $funcao,
    auth()->user(),
    ['motivo' => 'Substituição temporária']
);
```

---

## 🔒 Regras de Segurança

### Acesso Hierárquico:
- **Admins e Pastores**: Acesso total automático (não precisam de registros)
- **Membros com função**: Acesso limitado às permissões da função
- **Membros sem função**: Sem acesso ao sistema administrativo

### Validações:
- Funções só podem ser atribuídas por admins da mesma igreja
- Permissões são isoladas por igreja
- Auditoria completa de todas as ações

---

## 📊 Principais Métodos

### IgrejaPermissao:
- `isAtiva()`, `isInativa()`
- `getModuloLabel()`, `getCategoriaLabel()`
- `permissoesPorModulo($igrejaId)`
- `buscarPorCodigo($igrejaId, $codigo)`

### IgrejaFuncao:
- `temMembros()`, `contarMembrosAtivos()`
- `adicionarPermissao()`, `removerPermissao()`
- `temPermissao($codigo)`
- `funcoesPorIgreja($igrejaId)`

### IgrejaMembroFuncao:
- `estaValido()`, `estaExpirado()`
- `ativar()`, `suspender()`, `revogar()`
- `podeSerEditadoPor(User $user)`
- `funcoesAtivasDoMembro($membroId)`

### IgrejaPermissaoLog:
- `logAtribuicaoFuncao()`, `logRevogacaoFuncao()`
- `estatisticasPorIgreja($igrejaId)`
- `atividadesRecentes($igrejaId)`

---

## 🚀 Próximos Passos

1. **Criar Middleware** para verificação de permissões
2. **Desenvolver Interface Admin** para gestão
3. **Implementar Gates/Policies** no Laravel
4. **Criar Seeders** para dados iniciais
5. **Testes** de segurança e validações

---

*Models criadas seguindo o padrão do projeto OmniIgrejas* 🎯
