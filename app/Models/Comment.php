<?php

namespace App\Models;

use App\config\database;

final class Comment
{
    private $conn;
    private $table_name = "comments";

    public $id;
    public $comment;
    public $post_id;
    public $commented_by;
    public $profile_pic;
    public $created_at;

    public function __construct()
    {
        $this->conn = Database::getConnection();

    }
    //Cria um novo comentário
    public function insert($formData)
    {
        $query = "INSERT INTO " . $this->table_name . "(post_id, comment, commented_by, profile_pic, created_at) VALUES (:post_id, :comment, :commented_by, :profile_pic, :created_at)";
        $stmt = $this->conn->prepare($query);

        $comennter = isset($_SESSION['id']) ? $_SESSION['username'] : (!empty($formData['commented_by']) ? $formData['commented_by'] : "visitante");
        $profilePic = isset($_SESSION['id']) ? $_SESSION['profile_pic'] : "default.png";

        $this->post_id = htmlspecialchars(strip_tags($formData['postId']));
        $this->commented_by = htmlspecialchars(strip_tags($comennter, ""));
        $this->profile_pic = $profilePic;
        $this->comment = htmlspecialchars(strip_tags($formData['comment']));
        $this->created_at = date('Y-m-d H:i:s');

        $stmt->bindParam(":post_id", $this->post_id);
        $stmt->bindParam(":comment", $this->comment);
        $stmt->bindParam(":commented_by", $this->commented_by);
        $stmt->bindParam(':profile_pic', $this->profile_pic);
        $stmt->bindParam("created_at", $this->created_at);

        if ($stmt->execute()) {
            Database::closeConnection($this->conn);
            return true;
        }
        Database::closeConnection($this->conn);
        return false;
    }
    // Buscar todos comentarios
    public function getCommentsById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE post_id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        $resul = array();
        while ($row = $stmt->fetchObject(__CLASS__)) {
            $resul[] = $row;
        }
        Database::closeConnection($this->conn);
        return $resul;
    }
    public function deleteComment($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE post_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            Database::closeConnection($this->conn);
            return true;
        }
        Database::closeConnection($this->conn);
        return false;
    }
}
