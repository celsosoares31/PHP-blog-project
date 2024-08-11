<?php

namespace App\Models;

use App\config\Database;
use Exception;
use PDO;

final class Post
{
    private $conn;
    private $table_name = "posts";

    public $id;
    public $post_title;
    public $post_content;
    public $post_picture;
    public $updated_at;
    public $created_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }
    public function create($data, $fileName)
    {
        $this->conn = Database::getConnection();
        extract($data);
        $query = "INSERT INTO " . $this->table_name . " (post_title, post_content, post_picture, created_at) VALUES(:post_title, :post_content, :post_picture, :created_at)";
        $stmt = $this->conn->prepare($query);

        $this->post_title = htmlspecialchars(strip_tags($post_title));
        $this->post_content = htmlspecialchars(strip_tags($post_content));
        $this->post_picture = $fileName;
        $this->created_at = date('Y-m-d H:i:s');

        $stmt->bindParam(":post_title", $this->post_title);
        $stmt->bindParam(":post_content", $this->post_content);
        $fileName ? $stmt->bindParam(":post_picture", $this->post_picture) : "";
        $stmt->bindParam(":created_at", $this->created_at);

        if ($stmt->execute()) {
            $lastId = $this->conn->lastInsertId();
            Database::closeConnection($this->conn);
            return $lastId;
        }

        Database::closeConnection($this->conn);
        return false;
    }
    public function getPostById($id)
    {
        $this->conn = Database::getConnection();
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $row = $stmt->fetchObject(__CLASS__);

        if (!$row) {
            Database::closeConnection($this->conn);
            return false;
        }

        Database::closeConnection($this->conn);
        return $row;
    }
    public function update($data, $fileName)
    {
        extract($data);
        $query = "UPDATE " . $this->table_name . "
            SET post_title = :post_title, post_content = :post_content, post_picture = :post_picture, updated_at = :updated_at WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($postId));
        $this->post_title = htmlspecialchars(strip_tags($post_title));
        $this->post_content = htmlspecialchars(strip_tags($post_content));
        $this->post_picture = $fileName;
        $this->updated_at = date('Y-m-d H:i:s');

        $actualPost = $this->getPostById($this->id);
        $actualPostPicture = $fileName ? $this->post_picture : $actualPost->post_picture;

        $stmt->bindParam(":post_title", $this->post_title);
        $stmt->bindParam(":post_content", $this->post_content);
        $stmt->bindParam(":post_picture", $actualPostPicture);
        $stmt->bindParam(":updated_at", $this->updated_at);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            Database::closeConnection($this->conn);
            return $this->id;
        }
        Database::closeConnection($this->conn);
        return false;
    }
    public function deletePost($id)
    {
        $this->conn = Database::getConnection();
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            Database::closeConnection($this->conn);
            return true;
        }

        Database::closeConnection($this->conn);
        return false;
    }
    public function getAllPosts()
    {
        $this->conn = Database::getConnection();
        $query = "SELECT * FROM ".$this->table_name." ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $resul = array();

        if($stmt->execute()) {
            while ($row = $stmt->fetchObject(__CLASS__)) {
                $resul[] = $row;
            }
        }

        Database::closeConnection($this->conn);
        return $resul;
    }
}