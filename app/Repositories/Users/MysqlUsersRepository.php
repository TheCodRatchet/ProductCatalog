<?php


namespace App\Repositories\Users;

use App\Connection;
use App\Models\Collections\UsersCollection;
use App\Models\User;
use PDO;

class MysqlUsersRepository implements UsersRepository
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Connection::configure();
    }

    public function getAll(): UsersCollection
    {
        $statement = $this->connection->query("SELECT * FROM users");
        $users = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new UsersCollection();

        foreach ($users as $user) {
            $collection->add(new User(
                $user['id'],
                $user['name'],
                $user['email'],
                $user['password']
            ));
        }

        return $collection;
    }

    public function save(User $user): void
    {
        $sql = "INSERT INTO users (id, name, email, password) VALUES (?, ?, ?, ?)";
        $statement = $this->connection->prepare($sql);
        $statement->execute([
            $user->getId(),
            $user->getName(),
            $user->getEmail(),
            $user->getPassword(),
        ]);
    }

    public function getByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$email]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($user)) return null;

        return new User(
            $user['id'],
            $user['name'],
            $user['email'],
            $user['password']
        );
    }
}