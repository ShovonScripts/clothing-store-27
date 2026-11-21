<?php

declare(strict_types=1);

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $variantsData = $data['variants'] ?? [];
        unset($data['variants']);

        $product = Product::create($data);

        foreach ($variantsData as $variantData) {
            if (! empty($variantData['size']) && ! empty($variantData['color_id'])) {
                $color = Color::find($variantData['color_id']);
                $colorCode = $color ? strtoupper(substr($color->name, 0, 3)) : 'COL';
                $sku = $product->sku.'-'.$colorCode.'-'.$variantData['size'];

                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variantData['size'],
                    'color_id' => $variantData['color_id'],
                    'stock' => $variantData['stock'] ?? 0,
                    'price' => $variantData['price'] ?? null,
                    'sku' => $sku,
                    'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                    'is_active' => $variantData['is_active'] ?? true,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                ]);
            }
        }

        return $product;
    }
}
