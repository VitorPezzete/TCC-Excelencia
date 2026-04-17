# Capítulo 4: Modelagem do Sistema

Este capítulo apresenta o modelo relacional do sistema, ou seja, como os dados estão organizados em tabelas no banco de dados.

## Visão Geral

A modelagem do sistema foi estruturada em formato relacional, representando os dados por meio de tabelas, colunas e chaves, permitindo sua implementação em um banco de dados relacional (MySQL).

## Estrutura das Tabelas

Seguem as entidades principais do sistema com seus campos e tipos de dados:

### Tabela: users
**Descrição:** Armazena informações dos usuários do sistema.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | NOT NULL, UNIQUE |
| email_verified_at | TIMESTAMP | NULL |
| password | VARCHAR(255) | NOT NULL |
| phone | VARCHAR(255) | NOT NULL |
| remember_token | VARCHAR(100) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: categorias
**Descrição:** Define as categorias de produtos disponíveis no cardápio.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| nome | VARCHAR(255) | NOT NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: produtos
**Descrição:** Armazena os produtos disponíveis no cardápio, organizados por categorias.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| categoria_id | BIGINT UNSIGNED | FK (categorias), NOT NULL |
| nome | VARCHAR(255) | NOT NULL |
| descricao | LONGTEXT | NOT NULL |
| preco | DECIMAL(8,2) | NOT NULL |
| imagem | VARCHAR(255) | NULL |
| destaque | BOOLEAN | DEFAULT FALSE |
| ativo | BOOLEAN | DEFAULT TRUE |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: enderecos
**Descrição:** Armazena os endereços de entrega dos usuários.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FK (users), NOT NULL, CASCADE |
| nome | VARCHAR(255) | NOT NULL |
| cep | VARCHAR(9) | NOT NULL |
| numero | VARCHAR(20) | NOT NULL |
| complemento | VARCHAR(255) | NULL |
| padrao | BOOLEAN | DEFAULT FALSE |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: cupons
**Descrição:** Gerencia os cupons de desconto disponíveis no sistema.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| codigo | VARCHAR(255) | NOT NULL, UNIQUE |
| tipo | ENUM('fixo', 'porcentagem') | NOT NULL |
| valor | DECIMAL(8,2) | NOT NULL |
| valor_minimo_pedido | DECIMAL(8,2) | DEFAULT 0 |
| maximo_usos | INT | NULL |
| contagem_usos | INT | DEFAULT 0 |
| ativo | BOOLEAN | DEFAULT TRUE |
| expira_em | DATE | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: pedidos
**Descrição:** Registra todos os pedidos realizados no sistema.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FK (users), NOT NULL, CASCADE |
| endereco_id | BIGINT UNSIGNED | FK (enderecos), NOT NULL, RESTRICT |
| cupom_id | BIGINT UNSIGNED | FK (cupons), NULL, SET NULL |
| status | ENUM | DEFAULT 'pendente' |
| subtotal | DECIMAL(8,2) | NOT NULL |
| desconto | DECIMAL(8,2) | DEFAULT 0 |
| taxa_entrega | DECIMAL(8,2) | DEFAULT 0 |
| total | DECIMAL(8,2) | NOT NULL |
| observacoes | LONGTEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Status possíveis:** pendente, confirmado, preparando, saiu_para_entrega, entregue, cancelado

### Tabela: historico_status_pedido
**Descrição:** Mantém o histórico de mudanças de status de cada pedido.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| pedido_id | BIGINT UNSIGNED | FK (pedidos), NOT NULL, CASCADE |
| status | ENUM | NOT NULL |
| observacao | VARCHAR(255) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: itens_pedido
**Descrição:** Armazena os itens (produtos) de cada pedido realizado.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| pedido_id | BIGINT UNSIGNED | FK (pedidos), NOT NULL, CASCADE |
| produto_id | BIGINT UNSIGNED | FK (produtos), NOT NULL, RESTRICT |
| quantidade | INT | NOT NULL |
| preco_unitario | DECIMAL(8,2) | NOT NULL |
| preco_total | DECIMAL(8,2) | NOT NULL |
| observacoes | LONGTEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: itens_carrinho
**Descrição:** Armazena os produtos no carrinho de compras dos usuários.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| user_id | BIGINT UNSIGNED | FK (users), NOT NULL, CASCADE |
| produto_id | BIGINT UNSIGNED | FK (produtos), NOT NULL, CASCADE |
| quantidade | INT | DEFAULT 1 |
| observacoes | LONGTEXT | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

### Tabela: pagamentos
**Descrição:** Registra as informações de pagamento de cada pedido.

| Campo | Tipo | Restrições |
|-------|------|-----------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| pedido_id | BIGINT UNSIGNED | FK (pedidos), NOT NULL, CASCADE |
| metodo | ENUM | DEFAULT 'pix' |
| troco_para | DECIMAL(8,2) | NULL |
| created_at | TIMESTAMP | NULL |
| updated_at | TIMESTAMP | NULL |

**Métodos de pagamento:** cartao_credito, cartao_debito, pix, dinheiro

## Relacionamentos no Banco de Dados

Os relacionamentos entre as tabelas são definidos por meio de chaves estrangeiras, garantindo integridade referencial:

- **users → enderecos:** Um usuário pode ter múltiplos endereços (1:N)
- **users → itens_carrinho:** Um usuário pode ter múltiplos itens no carrinho (1:N)
- **categorias → produtos:** Uma categoria pode conter múltiplos produtos (1:N)
- **produtos → itens_pedido:** Um produto pode estar em múltiplos pedidos (1:N)
- **produtos → itens_carrinho:** Um produto pode estar no carrinho de múltiplos usuários (1:N)
- **users → pedidos:** Um usuário pode fazer múltiplos pedidos (1:N)
- **enderecos → pedidos:** Um endereço pode ser usado em múltiplos pedidos (1:N)
- **cupons → pedidos:** Um cupom pode ser aplicado a múltiplos pedidos (1:N)
- **pedidos → itens_pedido:** Um pedido contém múltiplos itens (1:N)
- **pedidos → pagamentos:** Um pedido tem um pagamento associado (1:1)
- **pedidos → historico_status_pedido:** Um pedido tem múltiplos registros de histórico (1:N)

## Regras de Negócio Implementadas

Foram aplicadas as seguintes regras para garantir a consistência e integridade dos dados:

### Campos Obrigatórios (NOT NULL)
- Todas as informações essenciais como nome, email, senha, preço e descrição são obrigatórias
- Isso impede registros incompletos no sistema

### Campos Únicos (UNIQUE)
- Email do usuário: garante que não haja duplicação de contas
- Código do cupom: cada cupom tem um código único

### Integridade Referencial (FOREIGN KEY)
- **ON DELETE CASCADE:** Ao deletar um usuário, seus endereços, pedidos e itens de carrinho são automaticamente removidos
- **ON DELETE RESTRICT:** Ao tentar deletar um produto ou endereço vinculado a um pedido, o sistema impede a exclusão
- **ON DELETE SET NULL:** Ao deletar um cupom, pedidos que o referenciavam têm o campo cupom_id zerado

### Valores Padrão
- **status de pedidos:** Inicia como 'pendente'
- **método de pagamento:** Padrão 'pix'
- **quantidade de itens no carrinho:** Padrão 1
- **ativo de produtos:** Padrão true (ativo)
- **destaque de produtos:** Padrão false (não destaque)

### Índices para Otimização
Foram criados índices nas colunas de chaves estrangeiras para otimizar consultas e relacionamentos:
- idx_produtos_categoria
- idx_enderecos_user
- idx_pedidos_user
- idx_pedidos_endereco
- idx_pedidos_cupom
- idx_historico_pedido
- idx_itens_pedido_pedido
- idx_itens_pedido_produto
- idx_itens_carrinho_user
- idx_itens_carrinho_produto
- idx_pagamentos_pedido

## Script SQL

O modelo relacional foi implementado utilizando o MySQL. O script completo de criação das tabelas encontra-se no arquivo `script_banco_dados.sql` anexado ao projeto, contendo todas as definições de tabelas, campos, relacionamentos e índices.

## Considerações Finais

O modelo relacional definido estrutura o banco de dados de forma organizada, garantindo:
- **Integridade dos dados:** Através de constraints de chave primária, chave estrangeira e campos únicos
- **Consistência das informações:** Aplicando regras de negócio e validações em nível de banco
- **Eficiência nas consultas:** Através de índices nas colunas críticas
- **Escalabilidade:** Permitindo expansão do sistema conforme novas necessidades

A estrutura foi projetada para suportar as operações principais do sistema de gerenciamento de pedidos, bem como futuras evoluções e funcionalidades adicionais.
