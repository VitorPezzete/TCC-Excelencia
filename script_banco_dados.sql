CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE categorias (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE produtos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao LONGTEXT NOT NULL,
    preco DECIMAL(8, 2) NOT NULL,
    imagem VARCHAR(255),
    destaque BOOLEAN DEFAULT FALSE,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_produtos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE enderecos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    nome VARCHAR(255) NOT NULL,
    cep VARCHAR(9) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    complemento VARCHAR(255),
    padrao BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_enderecos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cupons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(255) NOT NULL UNIQUE,
    tipo ENUM('fixo', 'porcentagem') NOT NULL,
    valor DECIMAL(8, 2) NOT NULL,
    valor_minimo_pedido DECIMAL(8, 2) DEFAULT 0,
    maximo_usos INT,
    contagem_usos INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    expira_em DATE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE pedidos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    endereco_id BIGINT UNSIGNED NOT NULL,
    cupom_id BIGINT UNSIGNED,
    status ENUM('pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado') DEFAULT 'pendente',
    subtotal DECIMAL(8, 2) NOT NULL,
    desconto DECIMAL(8, 2) DEFAULT 0,
    taxa_entrega DECIMAL(8, 2) DEFAULT 0,
    total DECIMAL(8, 2) NOT NULL,
    observacoes LONGTEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_pedidos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_pedidos_endereco FOREIGN KEY (endereco_id) REFERENCES enderecos(id) ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_cupom FOREIGN KEY (cupom_id) REFERENCES cupons(id) ON DELETE SET NULL
);

CREATE TABLE historico_status_pedido (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pendente', 'confirmado', 'preparando', 'saiu_para_entrega', 'entregue', 'cancelado') NOT NULL,
    observacao VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_historico_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
);

CREATE TABLE itens_pedido (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    produto_id BIGINT UNSIGNED NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(8, 2) NOT NULL,
    preco_total DECIMAL(8, 2) NOT NULL,
    observacoes LONGTEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_itens_pedido_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    CONSTRAINT fk_itens_pedido_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT
);

CREATE TABLE itens_carrinho (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    produto_id BIGINT UNSIGNED NOT NULL,
    quantidade INT DEFAULT 1,
    observacoes LONGTEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_itens_carrinho_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_itens_carrinho_produto FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

CREATE TABLE pagamentos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id BIGINT UNSIGNED NOT NULL,
    metodo ENUM('cartao_credito', 'cartao_debito', 'pix', 'dinheiro') DEFAULT 'pix',
    troco_para DECIMAL(8, 2),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_pagamentos_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE
);

CREATE INDEX idx_produtos_categoria ON produtos(categoria_id);
CREATE INDEX idx_enderecos_user ON enderecos(user_id);
CREATE INDEX idx_pedidos_user ON pedidos(user_id);
CREATE INDEX idx_pedidos_endereco ON pedidos(endereco_id);
CREATE INDEX idx_pedidos_cupom ON pedidos(cupom_id);
CREATE INDEX idx_historico_pedido ON historico_status_pedido(pedido_id);
CREATE INDEX idx_itens_pedido_pedido ON itens_pedido(pedido_id);
CREATE INDEX idx_itens_pedido_produto ON itens_pedido(produto_id);
CREATE INDEX idx_itens_carrinho_user ON itens_carrinho(user_id);
CREATE INDEX idx_itens_carrinho_produto ON itens_carrinho(produto_id);
CREATE INDEX idx_pagamentos_pedido ON pagamentos(pedido_id);
