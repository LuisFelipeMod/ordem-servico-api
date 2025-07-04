<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use App\Core\Database;

class AuthApiTest extends TestCase
{
  private $client;

  protected function setUp(): void
  {
    $this->client = new Client(['base_uri' => 'http://localhost:8000/api/']);
  }

  public function testLoginComSucesso()
  {
    $pdo = Database::connect();

    $senhaHash = password_hash('senha123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Administrador', 'admin@email.com', $senhaHash, 'admin']);


    $response = $this->client->post('/api/login', [
      'json' => [
        'email' => 'admin@email.com',
        'senha' => 'senha123'
      ]
    ]);
    $this->assertEquals(200, $response->getStatusCode());
    $body = json_decode($response->getBody(), true);
    $this->assertArrayHasKey('token', $body);
  }
}
