<?php

namespace App\Core;

use App\Controllers\ClienteController;
use App\Controllers\OrdemServicoController;
use App\Controllers\ProdutosController;
use App\Controllers\AuthController;

class Router
{
    public function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        // Limpa query string da URI para facilitar comparação
        $uri_path = parse_url($uri, PHP_URL_PATH);

        $cliente_controller = new ClienteController();
        $produtos_controller = new ProdutosController();
        $ordens_servico_controller = new OrdemServicoController();
        $auth_controller = new AuthController();

        header('Content-Type: application/json');

        if ($uri_path === '/api/login' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $auth_controller->login($dados);
            return;
        }

        $usuario = Middleware::proteger();

        if ($uri_path === '/api/clientes' && $method === 'GET') {
            $cliente_controller->listar();
            return;
        }
        if ($uri_path === '/api/clientes' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $cliente_controller->criar($dados);
            return;
        }
        if ($uri_path === '/api/clientes' && $method === 'PUT') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $cliente_controller->atualizar($dados);
            return;
        }
        if ($uri_path === '/api/clientes' && $method === 'DELETE') {
            $cpf = $_GET['cpf'] ?? null;
            $cliente_controller->excluir(['cpf' => $cpf]);
            return;
        }
        if ($uri_path === '/api/produtos' && $method === 'GET') {
            $produtos_controller->listar();
            return;
        }
        if ($uri_path === '/api/produtos' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $produtos_controller->criar($dados);
            return;
        }
        if ($uri_path === '/api/produtos' && $method === 'PUT') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $produtos_controller->atualizar($dados);
            return;
        }
        if ($uri_path === '/api/produtos' && $method === 'DELETE') {
            $codigo = $_GET['codigo'] ?? null;
            $produtos_controller->excluir(['codigo' => $codigo]);
            return;
        }
        if ($uri_path === '/api/ordens_servico' && $method === 'GET') {
            $ordens_servico_controller->listar();
            return;
        }
        if ($uri_path === '/api/ordens_servico' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $ordens_servico_controller->criar($dados);
            return;
        }
        if ($uri_path === '/api/ordens_servico' && $method === 'PUT') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $ordens_servico_controller->atualizar($dados);
            return;
        }
        if ($uri_path === '/api/ordens_servico' && $method === 'DELETE') {
            $id = $_GET['id'] ?? null;
            $ordens_servico_controller->excluir(['id' => $id]);
            return;
        }

        http_response_code(400);
        echo json_encode(['erro' => 'Rota nao encontrada']);
    }
}
