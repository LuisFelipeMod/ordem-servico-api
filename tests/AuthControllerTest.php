<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;

class AuthControllerTest extends TestCase
{
  public function testLoginComCredenciaisInvalidas()
  {
    $controller = new AuthController();
    $dados = ['email' => 'errado', 'senha' => 'invalido'];

    ob_start();
    $controller->login($dados);
    $output = ob_get_clean();

$this->assertStringContainsString('Credenciais inválidas', json_decode($output, true)['erro']);
  }
}
