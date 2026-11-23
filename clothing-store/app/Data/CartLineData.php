<?php

declare(strict_types=1);

namespace App\Data;

readonly class CartLineData
{
    public function __construct(
        public int $variantId,
        public int $qty,
    ) {}
}
