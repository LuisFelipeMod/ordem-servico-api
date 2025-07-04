<?php

namespace App\Controllers;

use App\Core\Database;
use App\Helpers\Validador;
use PDO;
use App\Helpers\Sanitizer;

class ClienteController
{
  public function criar($dados)
  {
    $nome =  Sanitizer::sanitizeInput($dados['nome'] ?? '');
    $cpf =  Sanitizer::sanitizeInput($dados['cpf'] ?? '');
    $endereco =  Sanitizer::sanitizeInput($dados['endereco'] ?? '');

    if (empty($cpf)) {
      http_response_code(400);
      echo json_encode(['erro' => 'CPF ausente']);
      return;
    }

    if (!Validador::cpf($cpf)) {
      http_response_code(400);
      echo json_encode(['erro' => 'CPF inválido']);
      return;
    }

    $pdo = Database::connect();

    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);

    if ($stmt->fetch()) {
      http_response_code(400);
      echo json_encode(['erro' => 'CPF já cadastrado']);
      return;
    }

    $stmt = $pdo->prepare("INSERT INTO clientes (nome,cpf,endereco) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $cpf, $endereco]);

    echo json_encode(['msg' => 'Cliente cadastrado com sucesso']);
  }

  public function listar()
  {
    $pdo = Database::connect();

    $stmt = $pdo->query("SELECT * FROM clientes");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  }
  public function atualizar($dados)
  {
    $pdo = Database::connect();
    $cpf = $dados['cpf'] ?? '';
    $nome = $dados['nome'] ?? '';
    $endereco  = $dados['endereco'] ?? '';

    if (!$cpf || !Validador::cpf($cpf)) {
      http_response_code(400);
      echo json_encode(['erro' => 'CPF Inválido ou ausente']);
      return;
    }

    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);

    if (!$stmt->fetch()) {
      http_response_code(404);
      echo json_encode(['erro' => 'Cliente não encontrado']);
      return;
    }

    $stmt = $pdo->prepare("UPDATE clientes SET nome = ?, endereco = ? WHERE cpf = ?");
    $stmt->execute([$nome, $endereco, $cpf]);

    echo json_encode(['msg' => 'Cliente atualizado com sucesso']);
  }
  public function excluir($dados){
    $pdo = Database::connect();
    $cpf = $dados['cpf'] ?? '';

    if(!$cpf || !Validador::cpf($cpf)){
      http_response_code(400);
      echo json_encode(['erro' => 'CPF inválido ou ausente']);
      return;
    }

    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);

    if(!$stmt->fetch()) {
      http_response_code(404);
      echo json_encode(['msg' => "Cliente não encontrado"]);
      return;
    }

    $stmt = $pdo->prepare("DELETE FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);

    echo json_encode(['msg' => 'Cliente deletado com sucesso']);
  }
}
