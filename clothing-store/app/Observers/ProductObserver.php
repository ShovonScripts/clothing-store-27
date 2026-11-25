<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Services\HtmlSanitizer;

class ProductObserver
{
    protected HtmlSanitizer $sanitizer;

    public function __construct(HtmlSanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function saving(Product $product): void
    {
        if ($product->isDirty('description')) {
            $product->description = $this->sanitizer->cleanProductDescription($product->description);
        }

        if ($product->isDirty('short_description')) {
            $product->short_description = $this->sanitizer->clean($product->short_description);
        }

        if ($product->isDirty('care')) {
            $product->care = $this->sanitizer->clean($product->care);
        }

        if ($product->isDirty('fabric')) {
            $product->fabric = $this->sanitizer->clean($product->fabric);
        }
    }
}
