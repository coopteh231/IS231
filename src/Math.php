<?php
namespace App;

use \InvalidArgumentException;

class Math {
    public static function divide(int $a, int $b): float {
        if ($b === 0) {
            throw new InvalidArgumentException("Делить на ноль нельзя!");
        }
        return $a / $b;
    }
}