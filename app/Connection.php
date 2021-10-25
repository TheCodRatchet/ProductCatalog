<?php

namespace App;

use PDO;
use PDOException;

class Connection
{
    public static function configure(): PDO
    {
        $config = require "config.php";

        $host = $config['host'];
        $db = $config['database'];
        $user = $config['username'];
        $pass = $config['password'];

        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        try {
            $connection = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }

        return $connection;
    }
}