<?php

namespace App\Models;

class Product
{
    private string $id;
    private string $name;
    private string $category;
    private int $amount;
    private string $createdAt;
    private string $editedAt;

    public function __construct(string $id, string $name, string $category, int $amount, string $createdAt, string $editedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->amount = $amount;
        $this->createdAt = $createdAt;
        $this->editedAt = $editedAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getEditedAt(): string
    {
        return $this->editedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'category' => $this->getCategory(),
            'amount' => $this->getAmount(),
            'createdAt' => $this->getCreatedAt(),
            'editedAt' => $this->getEditedAt()
        ];
    }
}