CREATE TABLE
  IF NOT EXISTS clientes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    endereco TEXT NOT NULL
  );

CREATE TABLE
  IF NOT EXISTS produtos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT NOT NULL,
    status VARCHAR(20) NOT NULL,
    tempo_garantia INT NOT NULL
  );

CREATE TABLE
  IF NOT EXISTS ordens_servico (
    id SERIAL PRIMARY KEY,
    numero_ordem VARCHAR(20) NOT NULL UNIQUE,
    data_abertura DATE NOT NULL,
    nome_consumidor VARCHAR(100) NOT NULL,
    cpf_consumidor VARCHAR(14) NOT NULL,
    produto_id INTEGER NOT NULL REFERENCES produtos (id) ON DELETE RESTRICT,
    cliente_id INTEGER NOT NULL REFERENCES clientes (id) ON DELETE CASCADE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );

CREATE TABLE
  IF NOT EXISTS logs_ordens_servico (
    id SERIAL PRIMARY KEY,
    ordem_id INTEGER NOT NULL REFERENCES ordens_servico (id) ON DELETE CASCADE,
    acao VARCHAR(20) NOT NULL,
    alterado_por VARCHAR(100),
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detalhes JSONB
  );

CREATE TABLE
  usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    role VARCHAR(20) NOT NULL CHECK (role IN ('admin', 'usuario'))
  );

INSERT INTO
  usuarios (nome, email, senha, role)
VALUES
  (
    'Usuário Teste',
    'teste@email.com',
    '$2y$10$sH0zy9EmZbSXd8Zwl.qkQuMFbrXWslvv0d1h44cB7XpFLFuJLxfrq', -- senha: 123456
    'admin'
  );