<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth
{
  private static $secret = '';

  private static function initSecret()
  {
    if (!self::$secret) {
      self::$secret = $_ENV['JWT_SECRET'] ?? 'chave_insegura_padrao';
    }
  }

  public static function gerarToken($dados)
  {
    self::initSecret();

    $payload = [
      'iss' => 'ordem-servico-api',
      'iat' => time(),
      'exp' => time() + (60 * 60),
      'dados' => $dados
    ];

    return JWT::encode($payload, self::$secret, 'HS256');
  }

  public static function verificarToken($token)
  {
    self::initSecret();

    try {
      return JWT::decode($token, new Key(self::$secret, 'HS256'));
    } catch (\Exception $e) {
      return false;
    }
  }
}
