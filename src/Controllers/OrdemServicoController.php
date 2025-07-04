<?php

namespace App\Controllers;

use App\Core\Database;
use App\Helpers\Validador;
use PDO;
use App\Helpers\Sanitizer;


class OrdemServicoController
{
  public function criar($dados)
  {
    $numero_ordem = $dados['numero_ordem'] ?? '';
    $data_abertura = $dados['data_abertura'] ?? '';
    $nome_consumidor = $dados['nome_consumidor'] ?? '';
    $cpf_consumidor = $dados['cpf_consumidor'] ?? '';
    $produto_id = $dados['produto_id'] ?? null;

    if (!$numero_ordem || !$data_abertura || !$nome_consumidor || !$cpf_consumidor || !$produto_id) {
      http_response_code(400);
      echo json_encode(['erro' => 'Dados incompletos']);
      return;
    }

    if (!Validador::cpf($cpf_consumidor)) {
      http_response_code(400);
      echo json_encode(['erro' => 'CPF inválido']);
      return;
    }

    $pdo = Database::connect();

    $stmt = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);

    if (!$stmt->fetch()) {
      http_response_code(400);
      echo json_encode(['erro' => 'Produto não encontrado']);
      return;
    }

    $stmt = $pdo->prepare("SELECT id FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf_consumidor]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
      $stmt = $pdo->prepare("INSERT INTO clientes (nome, cpf, endereco) VALUES (?, ?, ?)");
      $stmt->execute([$nome_consumidor, $cpf_consumidor, '']);
      $cliente_id = $pdo->lastInsertId();
    } else {
      $cliente_id = $cliente['id'];
    }

    $stmt = $pdo->prepare("INSERT INTO ordens_servico 
      (numero_ordem, data_abertura, nome_consumidor, cpf_consumidor, produto_id, cliente_id) 
      VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
      $numero_ordem,
      $data_abertura,
      $nome_consumidor,
      $cpf_consumidor,
      $produto_id,
      $cliente_id
    ]);

    $ordem_id = $pdo->lastInsertId();

    // Log de criação
    $log = $pdo->prepare("INSERT INTO logs_ordens_servico (ordem_id, acao, detalhes) VALUES (?, 'criado', ?::jsonb)");
    $log->execute([$ordem_id, json_encode($dados)]);

    echo json_encode(['msg' => 'Ordem de serviço cadastrada com sucesso']);
  }

  public function listar()
  {
    $pdo = Database::connect();

    $stmt = $pdo->query("
      SELECT o.id, o.numero_ordem, o.data_abertura, o.nome_consumidor, o.cpf_consumidor,
             p.codigo AS produto_codigo, p.descricao AS produto_descricao,
             c.nome AS cliente_nome
      FROM ordens_servico o
      JOIN produtos p ON o.produto_id = p.id
      JOIN clientes c ON o.cliente_id = c.id
      ORDER BY o.data_abertura DESC
    ");

    $ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ordens);
  }
  public function atualizar($dados)
  {
    $id = $dados['id'] ?? null;
    $numero_ordem = $dados['numero_ordem'] ?? '';
    $data_abertura = $dados['data_abertura'] ?? '';
    $nome_consumidor = $dados['nome_consumidor'] ?? '';
    $cpf_consumidor = $dados['cpf_consumidor'] ?? '';
    $produto_id = $dados['produto_id'] ?? null;

    if (!$id || !$numero_ordem || !$data_abertura || !$nome_consumidor || !$cpf_consumidor || !$produto_id) {
      http_response_code(400);
      echo json_encode(['erro' => 'Dados incompletos']);
      return;
    }

    if (!Validador::cpf($cpf_consumidor)) {
      http_response_code(400);
      echo json_encode(['erro' => 'CPF inválido']);
      return;
    }

    $pdo = Database::connect();

    $stmt = $pdo->prepare("SELECT * FROM ordens_servico WHERE id = ?");
    $stmt->execute([$id]);
    $ordem_antiga = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ordem_antiga) {
      http_response_code(404);
      echo json_encode(['erro' => 'Ordem de serviço não encontrada']);
      return;
    }

    $stmt = $pdo->prepare("SELECT id FROM produtos WHERE id = ?");
    $stmt->execute([$produto_id]);
    if (!$stmt->fetch()) {
      http_response_code(400);
      echo json_encode(['erro' => 'Produto não encontrado']);
      return;
    }

    $stmt = $pdo->prepare("UPDATE ordens_servico 
      SET numero_ordem = ?, data_abertura = ?, nome_consumidor = ?, cpf_consumidor = ?, produto_id = ?
      WHERE id = ?");
    $stmt->execute([
      $numero_ordem,
      $data_abertura,
      $nome_consumidor,
      $cpf_consumidor,
      $produto_id,
      $id
    ]);

    // Log de atualização
    $log = $pdo->prepare("INSERT INTO logs_ordens_servico (ordem_id, acao, detalhes) VALUES (?, 'atualizado', ?::jsonb)");
    $log->execute([$id, json_encode(['antes' => $ordem_antiga, 'depois' => $dados])]);

    echo json_encode(['msg' => 'Ordem de serviço atualizada com sucesso']);
  }

  public function excluir($dados)
  {
    $id = $dados['id'] ?? null;

    if (!$id) {
      http_response_code(400);
      echo json_encode(['erro' => 'ID da ordem é obrigatório']);
      return;
    }

    $pdo = Database::connect();

    $stmt = $pdo->prepare("SELECT * FROM ordens_servico WHERE id = ?");
    $stmt->execute([$id]);
    $ordem = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ordem) {
      http_response_code(404);
      echo json_encode(['erro' => 'Ordem de serviço não encontrada']);
      return;
    }

    $stmt = $pdo->prepare("DELETE FROM ordens_servico WHERE id = ?");
    $stmt->execute([$id]);

    $ordem_json = json_encode($ordem ?: []);

    try {
      $log = $pdo->prepare("INSERT INTO logs_ordens_servico (ordem_id, acao, detalhes) VALUES (?, 'excluido', ?)");
      $log->execute([$id, $ordem_json]);
    } catch (\PDOException $e) {
      error_log('Erro ao registrar log de exclusão: ' . $e->getMessage());
    }


    echo json_encode(['msg' => 'Ordem de serviço excluída com sucesso']);
  }
  public function logs()
  {
    $id = $_GET['id'] ?? null;

    if (!$id || !is_numeric($id)) {
      error_log("Entrou no bloco de ID inválido: id = " . var_export($id, true));

      http_response_code(400);
      echo json_encode(['erro' => 'ID inválido']);
      return;
    }

    $pdo = Database::connect();

    $stmt = $pdo->prepare("SELECT id FROM ordens_servico WHERE id = ?");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
      http_response_code(404);
      echo json_encode(['erro' => 'Ordem de serviço não encontrada']);
      return;
    }

    $stmt = $pdo->prepare("
    SELECT acao, data_alteracao, alterado_por, detalhes
    FROM logs_ordens_servico
    WHERE ordem_id = ?
    ORDER BY data_alteracao DESC
  ");
    $stmt->execute([$id]);

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formatados = array_map(function ($log) {
      return [
        'mensagem' => strtoupper($log['acao']),
        'data' => date('d/m/Y H:i', strtotime($log['data_alteracao'])),
        'detalhes' => $log['detalhes'],
        'alterado_por' => $log['alterado_por'] ?? 'Sistema'
      ];
    }, $logs);

    echo json_encode($formatados);
  }
}
