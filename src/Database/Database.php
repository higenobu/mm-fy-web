<?php

namespace App\Database;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Get the database connection.
     * This ensures there is only one connection (Singleton).
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                // PostgreSQL connection settings
                $host = '127.0.0.1'; // Change this if hosting elsewhere
                $port = '5432'; // Default PostgreSQL port
                $dbname = 'fydb';
                $user = 'ubuntu';
                $password = 'u123';

                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                self::$connection = new PDO($dsn, $user, $password);

                // Set error mode to exceptions
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }
}

