<?php

namespace App\Services;

final class RandomString
{
    /**
     * Gera uma string criptograficamente aleatória a partir de um alfabeto.
     */
    public static function from(string $alphabet, int $length): string
    {
        $max = strlen($alphabet) - 1;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $alphabet[random_int(0, $max)];
        }

        return $result;
    }
}
