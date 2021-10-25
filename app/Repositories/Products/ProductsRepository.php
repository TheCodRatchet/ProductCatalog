<?php

namespace App\Repositories\Products;

use App\Models\Product;
use App\Models\Collections\ProductsCollection;

interface ProductsRepository
{
    public function getAll(array $filters = []): ProductsCollection;

    public function getOne(string $id): ?Product;

    public function save(Product $product, array $tags): void;

    public function delete(Product $product): void;

    public function edit(Product $product, array $tags, string $name, string $category, int $amount): void;
}