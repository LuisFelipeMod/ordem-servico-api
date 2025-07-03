<?php

namespace App\Core;

use App\Controllers\ClienteController;

class Router
{
    public function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        $cliente_controller = new ClienteController();

        header('Content-Type: application/json');

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
            $body = file_get_contents('php://input');
            $dados = json_decode($body, true);
            $cliente_controller->excluir($dados);
            return;
        }

        http_response_code(400);
        echo json_encode(['erro' => 'Rota nao encontrada']);
    }
}
