<?php
namespace App\Middleware;

use App\Helpers\Auth;

class Middleware
{
  public static function proteger()
  {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      http_response_code(401);
      echo json_encode(['erro' => 'Token não fornecido']);
      exit;
    }

    $token = str_replace('Bearer ', '', $authHeader);
    $dados = Auth::verificarToken($token);

    if (!$dados) {
      http_response_code(401);
      echo json_encode(['erro' => 'Token inválido']);
      exit;
    }

    return $dados->dados;
  }
}
