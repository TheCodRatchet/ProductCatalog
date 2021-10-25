<?php

namespace App\Repositories\Tags;

use App\Models\Tag;
use App\Models\Collections\TagsCollection;

interface TagsRepository
{
    public function getAll(): TagsCollection;

    public function getOne(string $id): ?Tag;

    public function save(Tag $tag): void;

    public function delete(Tag $tag): void;

    public function edit(Tag $tag, string $name): void;
}