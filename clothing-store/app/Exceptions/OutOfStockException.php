<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\ProductVariant;
use RuntimeException;

class OutOfStockException extends RuntimeException
{
    public function __construct(
        ProductVariant $variant,
        int $requestedQty,
    ) {
        $available = $variant->stock;
        parent::__construct(
            "Insufficient stock for variant {$variant->id} ({$variant->product->name}). Available: {$available}, requested: {$requestedQty}.",
            0,
        );
        $this->variant = $variant;
        $this->requestedQty = $requestedQty;
        $this->availableStock = $available;
    }

    public ProductVariant $variant;

    public int $requestedQty;

    public int $availableStock;
}
