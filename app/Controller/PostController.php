<?php

namespace App\Controller;

use App\Models\Post;
use App\Models\Comment;
use Exception;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

final class PostController
{
    public static function index()
    {
        try {
            $loader = new FilesystemLoader('app/view');
            $twig = new Environment($loader);

            $view = $twig->load("posts.html");
            $post = new Post();

            $posts = $post->getAllPosts();

            $viewData = [
                'posts' => $posts
            ];
            $renderedContent = $view->render($viewData);
            echo $renderedContent;

        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
    }
    public static function viewSingle($id)
    {

        $loader = new FilesystemLoader('app/view');
        $twig = new Environment($loader);

        $view = $twig->load("viewSingle.html");
        $post = new Post();
        $comment = new Comment();

        $actualPost = $post->getPostById($id);
        $viewData = [
           'post' => $actualPost,
           'comments' => array(),
        ];
        $comments = $comment->getCommentsById($id);
        if($comments) {
            $viewData['comments'] = $comments;
        }

        $content = $view->render($viewData);

        echo $content;
    }
    public static function createPost($data, $files)
    {
        extract($files);
        $post = new Post();
        $newFileName = str_replace(' ', '', $name);
        $isPostUpdated = $post->create($data, $newFileName);

        if($isPostUpdated) {
            if (isset($name) && !empty($name)) {
                $lastId = $isPostUpdated;
                // criando um directorio para as imagens
                $usrImagesDir = "public/img/$lastId/";
                // director para imagem de cada usuario
                mkdir($usrImagesDir, 0755);
                $fileName = $newFileName;
                move_uploaded_file($tmp_name, $usrImagesDir.$fileName);
            }
            try {
                $loader = new FilesystemLoader('app/view');
                $twig = new Environment($loader);
                $renderedData = array();

                $view = $twig->load("admin.html");
                $post = new Post();
                $posts = $post->getAllPosts();

                $data =  (object) array(
                    "user_pic" => $_SESSION['profile_pic'],
                    "username" => $_SESSION['username'],
                );
                $renderedData = [
                    'user' => $data,
                    "posts" => $posts
                ];
                $renderedContent = $view->render($renderedData);
                echo $renderedContent;

            } catch(Exception $ex) {
                echo $ex->getMessage();
            }

        }
    }
    public static function edit($id)
    {
        try {
            $loader = new FilesystemLoader('app/view');
            $twig = new Environment($loader);
            $renderedData = array();

            $view = $twig->load("edit.html");
            $post = new Post();
            $posts = $post->getPostById($id);

            $data =  (object) array(
                "user_pic" => $_SESSION['profile_pic'],
                "username" => $_SESSION['username'],
            );
            $renderedData = [
                'user' => $data,
                "posts" => $posts
            ];
            $renderedContent = $view->render($renderedData);
            echo $renderedContent;

        } catch(Exception $ex) {
            echo $ex->getMessage();
        }

    }
    public function delete($id)
    {
        $post = new Post();

        if (!$id) {
            die("Id invalido");
        }

        if(CommentController::delete($id)) {
            $postExist = $post->getPostById($id);

            if($postExist) {
                $post->deletePost($id);
                $usrImagesDir = "public/img/$id";
                if(file_exists($usrImagesDir)) {
                    $this->rrmdir($usrImagesDir);
                }
                header('Location: index.php?page=user');
            } else {
                throw new Exception("invalid post id");
            }
        }
    }
    public function updatePost($data, $file)
    {
        extract($file);
        $post = new Post();
        $newFileName = str_replace(' ', '', $name);
        $isPostUpdated = $post->update($data, $newFileName);

        if($isPostUpdated) {

            if (isset($name) && !empty($name)) {
                $lastId = $isPostUpdated;

                $usrImagesDir = "public/img/$lastId/";
                if(file_exists($usrImagesDir)) {
                    $this->rrmdir($usrImagesDir);
                }

                mkdir($usrImagesDir, 0755);
                $fileName = $newFileName;
                move_uploaded_file($tmp_name, $usrImagesDir.$fileName);
            }
            try {

                $loader = new FilesystemLoader('app/view');
                $twig = new Environment($loader);
                $renderedData = array();

                $view = $twig->load("admin.html");
                $post = new Post();
                $posts = $post->getAllPosts();

                $data =  (object) array(
                    "user_pic" => $_SESSION['profile_pic'],
                    "username" => $_SESSION['username'],
                );
                $renderedData = [
                    'user' => $data,
                    "posts" => $posts
                ];
                $renderedContent = $view->render($renderedData);
                echo $renderedContent;
            } catch(Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            throw new Exception("Error on update");
        }
    }
    public function rrmdir($src)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src.'/'.$file;
                if (is_dir($full)) {
                    $this->rrmdir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }
}