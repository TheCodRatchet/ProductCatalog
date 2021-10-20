<?php

namespace App\Repositories\Tags;

use App\Models\Collections\TagsCollection;
use App\Models\Tag;
use PDO;
use PDOException;

class MysqlTagsRepository implements TagsRepository
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

    public function getAll(array $filters = []): TagsCollection
    {
        $sql = "SELECT * FROM tags";
        $params = [];

        $statement = $this->connection->prepare($sql);
        $statement->execute($params);

        $tags = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new TagsCollection();

        foreach ($tags as $tag) {
            $collection->add(new Tag(
                $tag['id'],
                $tag['name']
            ));
        }
        return $collection;
    }

    public function getOne(string $id): ?Tag
    {
        $sql = "SELECT * FROM tags WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
        $tag = $statement->fetch();

        return new Tag(
            $tag['id'],
            $tag['name'],
        );
    }

    public function save(Tag $tag): void
    {
        $sql = "INSERT INTO tags (id, name) VALUES (?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $tag->getId(),
            $tag->getName()
        ]);
    }

    public function delete(Tag $tag): void
    {
        var_dump("i was here");
        $sql = "DELETE FROM tags WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId()]);
    }

    public function edit(Tag $tag): void
    {
        $postName = $_POST['name'];
        $sql = "UPDATE tags SET name='$postName' WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId()]);
    }
}
