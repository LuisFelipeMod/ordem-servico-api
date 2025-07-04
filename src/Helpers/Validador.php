<?php

namespace App\Helpers;

class Validador
{
  public static function cpf($cpf)
  {
    // Remove caracteres que não são numeros
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Verifica se tem 11 dígitos e se não é repetido
    if (strlen($cpf) != 11 || preg_match('/^(\d)\1{10}$/', preg_replace('/\D/', '', $cpf))) {
      return false;
    }
    
    // Verifica se o cpf é valido não um numero aleatorio
    for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
        $d += $cpf[$c] * (($t + 1) - $c);
      }
      $d = ((10 * $d) % 11) % 10;
      if ($cpf[$c] != $d) return false;
    }

    return true;
  }
}
