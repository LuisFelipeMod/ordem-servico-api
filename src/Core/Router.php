<?php

namespace App\Core;

use App\Controllers\ClienteController;
use App\Controllers\OrdemServicoController;
use App\Controllers\ProdutosController;
use App\Controllers\AuthController;
use App\Middleware\Middleware;

class Router
{
    public function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        $cliente_controller = new ClienteController();
        $produtos_controller = new ProdutosController();
        $ordens_servico_controller = new OrdemServicoController();
        $auth_controller = new AuthController();

        header('Content-Type: application/json');

        if ($uri === '/api/login' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $auth_controller->login($dados);
            return;
        }

        $usuario = Middleware::proteger();

        if ($uri === '/api/clientes' && $method === 'GET') {
            $cliente_controller->listar();
            return;
        }
        if ($uri === '/api/clientes' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $cliente_controller->criar($dados);
            return;
        }
        if ($uri === '/api/clientes' && $method === 'PUT') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $cliente_controller->atualizar($dados);
            return;
        }
        if ($uri === '/api/clientes' && $method === 'DELETE') {
            parse_str(parse_url($uri, PHP_URL_QUERY) ?: '', $query);
            $cliente_controller->excluir($query);
            return;
        }

        if ($uri === '/api/produtos' && $method === 'GET') {
            $produtos_controller->listar();
            return;
        }
        if ($uri === '/api/produtos' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $produtos_controller->criar($dados);
            return;
        }
        if ($uri === '/api/produtos' && $method === 'PUT') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $produtos_controller->atualizar($dados);
            return;
        }
        if ($uri === '/api/produtos' && $method === 'DELETE') {
            parse_str(parse_url($uri, PHP_URL_QUERY) ?: '', $query);
            $produtos_controller->excluir($query);
            return;
        }

        if ($uri === '/api/ordens_servico' && $method === 'GET') {
            $ordens_servico_controller->listar();
            return;
        }
        if ($uri === '/api/ordens_servico' && $method === 'POST') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $ordens_servico_controller->criar($dados);
            return;
        }
        if ($uri === '/api/ordens_servico' && $method === 'PUT') {
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $ordens_servico_controller->atualizar($dados);
            return;
        }
        if ($uri === '/api/ordens_servico' && $method === 'DELETE') {
            parse_str(parse_url($uri, PHP_URL_QUERY) ?: '', $query);
            $ordens_servico_controller->excluir($query);
            return;
        }

        http_response_code(400);
        echo json_encode(['erro' => 'Rota não encontrada']);
    }
}
