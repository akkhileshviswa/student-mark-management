<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * This class connects to the database and returns the object when called.
 */
class Database
{
    /** @var PDO */
    private $connection;

    /** @var Database */
    private static $instance;

    /**
     * This method connects to the database for the provided details.
     */
    private function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=" . $_ENV['DB_HOST'] .
                ";dbname=" . $_ENV['DB_NAME'], $_SERVER['DB_USER'], $_SERVER['DB_PASS']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Failed to connect to database: ". $e->getMessage());
        }
    }

    /**
     * This method returns the instance of the current class.
     * @return $instance
     */
    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * This method returns the object of the connection.
     * @return $connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
