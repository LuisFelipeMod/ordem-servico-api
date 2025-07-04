<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\ProdutosController;
use App\Core\Database;


class ProdutosControllerTest extends TestCase
{
    public function testCriarProdutoComDadosValidos()
    {
        $controller = new ProdutosController();

        $pdo = Database::connect();
        $pdo->exec("DELETE FROM produtos");


        ob_start();
        $controller->criar([
            'codigo' => 'P001',
            'descricao' => 'Produto de teste',
            'status' => 'ativo',
            'tempo_garantia' => 12
        ]);
        $output = ob_get_clean();

        $this->assertStringContainsString('sucesso', $output);
    }
}
