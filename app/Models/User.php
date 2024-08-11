<?php

namespace App\models;

use App\config\Database;

final class User
{
    private static $conn;
    private static $table_name = "usuarios";

    public $id;
    public $username;
    public $email;
    public $senha;
    public $profile_pic;
    public $created_at;

    public function __construct()
    {
        self::$conn = Database::getConnection();
    }

    // // Ler um usuário pelo ID
    public static function getUserByEmail($email)
    {
        $query = "SELECT * FROM " . self::$table_name . " WHERE email = :email";
        $stmt = self::$conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetchObject(__CLASS__);

        if (!$row) {
            Database::closeConnection(self::$conn);
            return false;
        }
        Database::closeConnection(self::$conn);
        return $row;

    }
}