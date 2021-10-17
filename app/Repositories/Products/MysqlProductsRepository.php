<?php

namespace App\Repositories\Products;

use App\Models\Collections\ProductsCollection;
use App\Models\Product;
use PDO;
use PDOException;

class MysqlProductsRepository implements ProductsRepository
{
    private PDO $connection;

    public function __construct()
    {
        $host = '127.0.0.1';
        $db = 'products_catalog_app';
        $user = 'root';
        $pass = 'Ratchet140298';

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try {
            $this->connection = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getAll(): ProductsCollection
    {
        $statement = $this->connection->query("SELECT * FROM products");
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

    public function save(Product $product): void
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
    }

    public function delete(Product $product): void
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);
    }

    public function edit(Product $product): void
    {
        $post = $_POST['name'];
        $sql = "UPDATE products SET name='$post' WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$product->getId()]);

    }
}
