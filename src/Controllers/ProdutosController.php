<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class ProdutosController
{
  public function criar($dados)
  {
    $codigo = $dados['codigo'] ?? '';
    $descricao = $dados['descricao'] ?? '';
    $status = $dados['status'] ?? '';
    $tempo_garantia = $dados['tempo_garantia'] ?? null;

    if (!$codigo || !$descricao || !$status || !is_numeric($tempo_garantia)) {
      http_response_code(400);
      echo json_encode(['erro' => 'Dados inválidos ou incompletos']);
      return;
    }

    $pdo = Database::connect();

    $stmt = $pdo->prepare("SELECT id FROM produtos WHERE codigo = ?");
    $stmt->execute([$codigo]);

    if ($stmt->fetch()) {
      http_response_code(400);
      echo json_encode(['erro' => 'Código já cadastrado']);
      return;
    }

    $stmt = $pdo->prepare("INSERT INTO produtos (codigo, descricao, status, tempo_garantia) VALUES (?, ?, ?, ?)");
    $stmt->execute([$codigo, $descricao, $status, $tempo_garantia]);

    echo json_encode(['msg' => 'Produto Cadastrado com sucesso']);
  }
  public function listar()
  {
    $pdo = Database::connect();

    $status = $_GET['status'] ?? '';
    $tempo_garantia = $_GET['tempo_garantia'] ?? '';

    $query = "SELECT * FROM produtos WHERE 1=1";
    $params = [];

    if ($status) {
      $query .= " AND status = ?";
      $params[] = $status;
    }

    if ($tempo_garantia !== '') {
      $query .= " AND tempo_garantia = ?";
      $params[] = $tempo_garantia;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($result);
  }

  public function atualizar($dados)
  {
    $pdo = Database::connect();
    $codigo = $dados['codigo'] ?? '';
    $novo_codigo = $dados['novo_codigo'] ?? '';
    $descricao = $dados['descricao'] ?? '';
    $status = $dados['status'] ?? '';
    $tempo_garantia = $dados['tempo_garantia'] ?? null;

    if (!$codigo) {
      echo error_log('Código inválido ou ausente');
      return;
    }

    $stmt = $pdo->prepare("SELECT id FROM produtos WHERE codigo = ?");
    $stmt->execute([$codigo]);

    if (!$stmt->fetch()) {
      http_response_code(404);
      echo json_encode(['erro' => 'Produto não encontrado']);
      return;
    }

    if ($novo_codigo) {
      $stmt = $pdo->prepare("UPDATE produtos SET codigo = ?, descricao = ?, status = ?, tempo_garantia = ? WHERE codigo = ?");
      $stmt->execute([$novo_codigo, $descricao, $status, $tempo_garantia, $codigo]);
      echo json_encode(['msg' => 'Produto atualizado com sucesso']);

      return;
    }

    $stmt = $pdo->prepare("UPDATE produtos SET descricao = ?, status = ?, tempo_garantia = ? WHERE codigo = ?");
    $stmt->execute([$descricao, $status, $tempo_garantia, $codigo]);

    echo json_encode(['msg' => 'Produto atualizado com sucesso']);
  }
  public function excluir($dados)
  {
    $pdo = Database::connect();
    $id = $dados['id'] ?? '';

    if (!$id || !is_numeric($id)) {
      http_response_code(400);
      echo json_encode(['erro' => 'ID inválido ou ausente']);
      return;
    }

    $stmt = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
      http_response_code(404);
      echo json_encode(['msg' => 'Produto não encontrado']);
      return;
    }

    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['msg' => 'Produto deletado com sucesso']);
  }
}
