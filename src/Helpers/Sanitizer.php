<?php
namespace App\Helpers;


// Previne contra XSS
class Sanitizer
{
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}
