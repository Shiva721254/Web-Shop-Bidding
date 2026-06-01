<?php

namespace App\Framework;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host     = $_ENV['DB_HOST']     ?? 'mysql';
            $dbname   = $_ENV['DB_NAME']     ?? 'developmentdb';
            $user     = $_ENV['DB_USER']     ?? 'developer';
            $password = $_ENV['DB_PASSWORD'] ?? 'secret123';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            self::$connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$connection;
    }
}
