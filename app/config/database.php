<?php

namespace App\config;

use PDO;
use PDOException;

abstract class Database
{
    private static $host = "localhost";
    private static $db_name = "blog";
    private static $username = "root";
    private static $password = "";
    public static $conn;

    public static function getConnection()
    {
        self::$conn = null;
        try {
            self::$conn = new PDO("mysql:host=".self::$host.";dbname=".self::$db_name, self::$username, self::$password);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$conn;
        } catch (PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }
    }
    public static function closeConnection(&$conn)
    {
        $conn = null;
    }

}