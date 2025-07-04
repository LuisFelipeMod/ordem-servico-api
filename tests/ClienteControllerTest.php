<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\ClienteController;
use App\Core\Database;


class ClienteControllerTest extends TestCase
{
    public function testCriarClienteComDadosValidos()
    {
        $controller = new ClienteController();

        $pdo = Database::connect();
        $pdo->exec("DELETE FROM clientes");

        ob_start();

        $controller->criar([
            'nome' => 'João da Silva',
            'cpf' => '21759716006',
            'endereco' => 'Rua A, 123'
        ]);
        $output = ob_get_clean();

        $this->assertStringContainsString('sucesso', $output);
    }
}
