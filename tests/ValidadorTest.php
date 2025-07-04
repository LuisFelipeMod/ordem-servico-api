<?php

use PHPUnit\Framework\TestCase;
use App\Helpers\Validador;

class ValidadorTest extends TestCase
{
    public function testCpfValido()
    {
        $this->assertTrue(Validador::cpf('792.630.310-02'));
    }

    public function testCpfInvalido()
    {
        $this->assertFalse(Validador::cpf('000.000.000-00'));
    }
}
