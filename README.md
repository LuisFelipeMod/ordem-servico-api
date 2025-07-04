# Ordem de Serviço API

API para gerenciamento de clientes, produtos e ordens de serviço com autenticação JWT.

## Requisitos

- PHP >= 8.x
- Extensão PDO e pdo_pgsql
- Composer (para dependências, ex: firebase/php-jwt)

## Instalação

1. Clone o repositório:

```bash
git clone https://github.com/LuisFelipeMod/ordem-servico-api
cd ordem-servico-api
```

2. Instale dependências usando composer:

```bash
composer install
```

3. Configure o arquivo .env com as variáveis do banco e JWT_SECRET.

### Passos para criar o .env

1. Crie um arquivo chamado .env na raiz do projeto.

2. Dentro do arquivo .env, adicione as variáveis necessárias para sua aplicação, por exemplo:

```bash
  DB_HOST=localhost
  DB_PORT=5432
  DB_DATABASE=nome_do_banco
  DB_USERNAME=usuario
  DB_PASSWORD=senha

  JWT_SECRET=chave_secreta_aqui
```

### Como gerar o valor para JWT_SECRET

1. A variável JWT_SECRET é uma chave secreta usada para assinar e verificar os tokens JWT, garantindo que eles sejam válidos e não possam ser falsificados.

2. Você pode gerar um valor seguro para essa chave de diferentes formas:

3. Execute o comando abaixo para gerar uma string aleatória segura de 32 bytes em base64:

```bash
  openssl rand -base64 32
```

4. Crie o banco de dados:

```bash
  psql -U seu_usuario -c "CREATE DATABASE nome_do_banco;"
```

5. Execute o Schema SQL:

```bash
  psql -U seu_usuario -d nome_do_banco -f diretorio/schema.sql
```

6. Execute o servidor PHP embutido para testes:

```bash
php -S localhost:8000 -t public
```

## Rotas principais

POST /api/login - autenticação e geração do token JWT

GET /api/clientes - listar clientes (requer token)

POST /api/clientes - criar cliente (requer token)

PUT /api/clientes - atualizar cliente (requer token)

DELETE /api/clientes - deletar cliente (requer token)

(Similar para /produtos e /ordens_servico)

## Documentação

A documentação da API está disponível no arquivo swagger.yaml. Use o [Swagger Editor](https://editor.swagger.io/) para visualizar.

## Segurança

- Todas as rotas protegidas com JWT

- Validação e sanitização de entradas

- Prevenção contra SQL Injection via prepared statements

## Contato

Desenvolvido por Luis Modesto - luisgmodesto12@gmail.com
