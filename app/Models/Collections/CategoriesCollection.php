<?php

namespace App\Models\Collections;

use App\Models\Category;

class CategoriesCollection
{
    private array $categories;

    public function __construct(array $categories = [])
    {
        foreach ($categories as $category) {
            $this->categories[] = $category;
        }
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}