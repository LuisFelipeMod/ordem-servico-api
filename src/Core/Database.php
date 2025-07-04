<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
  private static $instance;

  public static function connect()
  {
    if (!self::$instance) {
      $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . './../../');
      $dotenv->load();

      $host = $_ENV['DB_HOST'];
      $db = $_ENV['DB_NAME'];
      $user = $_ENV['DB_USER'];
      $pass = $_ENV['DB_PASS'];

      try {
        self::$instance = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
        self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
        die('Erro ao conectar com o banco: ' . $e->getMessage());
      }
    }

    return self::$instance;
  }
}
