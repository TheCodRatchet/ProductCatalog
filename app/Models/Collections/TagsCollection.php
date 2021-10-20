<?php

namespace App\Models\Collections;

use App\Models\Tag;

class TagsCollection
{
    private array $tags = [];

    public function __construct(array $tags = [])
    {
        foreach ($tags as $tag) {
            $this->add($tag);
        }
    }

    public function add(Tag $tag): void
    {
        $this->tags[$tag->getId()] = $tag;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}