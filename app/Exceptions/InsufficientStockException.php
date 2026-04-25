<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a stock deduction would push stock below zero.
 *
 * Usage:
 *   throw new InsufficientStockException($product->name, $requested, $available);
 */
class InsufficientStockException extends RuntimeException
{
    public function __construct(
        public readonly string $productName,
        public readonly int    $requested,
        public readonly int    $available,
    ) {
        parent::__construct(
            "Stok tidak mencukupi untuk produk \"{$productName}\". " .
            "Dibutuhkan: {$requested}, Tersedia: {$available}."
        );
    }
}
