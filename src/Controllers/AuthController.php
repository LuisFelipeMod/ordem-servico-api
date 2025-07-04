<?php
namespace App\Controllers;

use App\Core\Database;
use App\Helpers\Auth;
use App\Helpers\Sanitizer;
use PDO;

class AuthController
{
  public function login($dados)
  {
    $email = Sanitizer::sanitizeInput($dados['email'] ?? '');
    $senha = Sanitizer::sanitizeInput($dados['senha'] ?? '');

    if (!$email || !$senha) {
      http_response_code(400);
      echo json_encode(['erro' => 'Email e senha são obrigatórios']);
      return;
    }

    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
      http_response_code(401);
      echo json_encode(['erro' => 'Credenciais inválidas']);
      return;
    }

    $token = Auth::gerarToken([
      'id' => $usuario['id'],
      'role' => $usuario['role'],
      'email' => $usuario['email']
    ]);

    echo json_encode(['token' => $token]);
  }
}
