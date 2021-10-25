<?php

namespace App\Repositories\Products;

use App\Connection;
use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use Carbon\Carbon;
use PDO;

class MysqlProductsRepository implements ProductsRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::configure();
    }

    public function getAll(array $filters = []): ProductsCollection
    {
        $sql = "SELECT * FROM products";
        $params = [];

        if (isset($filters['category'])) {
            $sql .= " WHERE category = ?";
            $params[] = $filters['category'];
        }

        if (isset($filters['tag'])) {

            $params[] = $filters['tag'];

            $sqlTag = "SELECT product_id FROM products_tags WHERE tag_id = ?";
            $statement = $this->connection->prepare($sqlTag);
            $statement->execute($params);

            $products = $statement->fetchAll(PDO::FETCH_ASSOC);

            $sql .= " WHERE id = ?";

            foreach ($products as $product) {
                $params[] = $product['product_id'];
            }
        }

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        $collection = new ProductsCollection();

        foreach ($products as $product) {
            $collection->add(new Product(
                $product['id'],
                $product['name'],
                $product['category'],
                $product['amount'],
                $product['createdAt'],
                $product['editedAt']
            ));
        }
        return $collection;
    }

    public function getOne(string $id): ?Product
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        $product = $statement->fetch();

        return new Product(
            $product['id'],
            $product['name'],
            $product['category'],
            $product['amount'],
            $product['createdAt'],
            $product['editedAt']
        );
    }

    public function save(Product $product, array $tags): void
    {
        $sql = "INSERT INTO products (id, name, category, amount, createdAt, editedAt) VALUES (?, ?, ?, ?, ?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $product->getId(),
            $product->getName(),
            $product->getCategory(),
            $product->getAmount(),
            $product->getCreatedAt(),
            $product->getEditedAt()
        ]);

        foreach ($tags as $tag) {
            $sql = "INSERT INTO products_tags (product_id, tag_id) VALUES (?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $product->getId(),
                $tag
            ]);
        }
    }

    public function delete(Product $product): void
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);

        $sql = "DELETE FROM products_tags WHERE product_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);
    }

    public function edit(Product $product, array $tags, string $name, string $category, int $amount): void
    {
        $sql = "DELETE FROM products_tags WHERE product_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);

        $editedAt = Carbon::now();
        $sql = "UPDATE products SET name='$name', category='$category', amount='$amount', editedAt='$editedAt' WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);

        foreach ($tags as $tag) {
            $sql = "INSERT INTO products_tags (product_id, tag_id) VALUES (?, ?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $product->getId(),
                $tag
            ]);
        }
    }
}
