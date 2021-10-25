<?php

namespace App\Repositories\Tags;

use App\Connection;
use App\Models\Collections\TagsCollection;
use App\Models\Tag;
use PDO;

class MysqlTagsRepository implements TagsRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::configure();
    }

    public function getAll(): TagsCollection
    {
        $sql = "SELECT * FROM tags";

        $statement = $this->connection->prepare($sql);
        $statement->execute();

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
        $sql = "DELETE FROM tags WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId()]);

        $sql = "DELETE FROM products_tags WHERE tag_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId()]);
    }

    public function edit(Tag $tag, string $name): void
    {
        $sql = "DELETE FROM products_tags WHERE tag_id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId()]);

        $sql = "UPDATE tags SET name='$name' WHERE id = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$tag->getId()]);
    }
}
