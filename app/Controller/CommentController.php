<?php

namespace App\Controller;

use App\Models\Comment;
use App\Controller\PostController;

final class CommentController
{
    public static function create($data)
    {
        $comment = new Comment();
        $commentCreated = $comment->insert($data);

        if ($commentCreated) {
            PostController::viewSingle($data['postId']);
            return true;
        }
    }
    public static function delete($id)
    {
        // Lógica para excluir uma tarefa pelo ID
        $post = new Comment();

        if ($post->deleteComment($id)) {
            return true;
        }
    }
}