<?php

namespace App\controller;

use App\Controller\PostController;
use App\Models\Comment;
use App\Models\Post;
use App\models\User;
use Exception;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class UserController
{
    public static function index()
    {
        try {
            $loader = new FilesystemLoader('app/view');
            $twig = new Environment($loader);
            $renderedData = array();

            if (isset($_SESSION['id'])) {
                $view = $twig->load("admin.html");
                $post = new Post();
                $posts = $post->getAllPosts();

                $data = (object) array(
                    "user_pic" => $_SESSION['profile_pic'],
                    "username" => $_SESSION['username'],
                );
                $renderedData = [
                    'user' => $data,
                    "posts" => $posts,
                ];
                $renderedContent = $view->render($renderedData);
            } else {
                $view = $twig->load("login.html");
            }

            $renderedContent = $view->render($renderedData);
            echo $renderedContent;

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    public static function login($formData)
    {
        echo ("Yes");
        new User();
        extract($formData);

        $userExist = User::getUserByEmail($email);
        if (!$userExist) {
            echo $userExist['error'];
            exit;
        }

        if ($senhaInput == $userExist->senha) {
            $_SESSION['id'] = $userExist->id;
            $_SESSION['username'] = $userExist->nome_usuario;
            $_SESSION['profile_pic'] = $userExist->profile_pic;

            try {
                $loader = new FilesystemLoader('app/view');
                $twig = new Environment($loader);

                $view = $twig->load("admin.html");
                $post = new Post();
                $comment = new Comment();
                $posts = $post->getAllPosts();

                $data = (object) array(
                    "user_pic" => $userExist->profile_pic,
                    "username" => $userExist->nome_usuario,
                );

                $renderedData = [
                    'user' => $data,
                    "posts" => $posts,
                ];

                print_r($renderedData);
                exit;
                $renderedContent = $view->render($renderedData);

                echo $renderedContent;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            echo "Usuario Invalido";
        }
    }
    public static function logout()
    {
        session_destroy();
        PostController::index();
    }
}
