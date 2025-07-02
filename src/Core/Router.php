<?php
namespace App\Core;

class Router
{
    public function handleRequest()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        header('Content-Type: application/json');

        if ($uri === '/api/clientes' && $method === 'GET') {
            echo json_encode(['msg' => 'Listando clientes']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Rota não encontrada']);
        }
    }
}
