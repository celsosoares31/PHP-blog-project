<?php

namespace App\core;

use App\controller\UserController;
use App\controller\PostController;

final class Core
{
    public static function init($urlGet)
    {
        $namespace = "App\Controller";

        if (!isset($urlGet['page'])) {
            $controller = "PostController";
        } else {
            $controller = ucfirst($urlGet['page'].'Controller');
        }

        $class_name = $namespace."\\".$controller;

        if(!isset($urlGet['action'])) {
            $action = 'index';
        } else {
            $action = $urlGet['action'];
        }
        $params = array();
        if(isset($urlGet['id'])) {
            $id = $urlGet['id'];
            $params[] = $id;
        }

        if (!class_exists($class_name)) {
            $class_name = $namespace."\\".'ErrorController';
            echo "Controller not found ".$class_name;
        }
        call_user_func_array(array(new $class_name(), $action), $params);
    }
    public static function userPostHandler($postData)
    {
        $isLoged = UserController::login($postData);
    }
    public static function postUpdateHandler($postData, $files)
    {
        $isPostUpdated = new PostController();
        $isPostUpdated->updatePost($postData, $files);
        return $isPostUpdated;
    }
    public static function postPostHandler($postData, $files)
    {
        PostController::createPost($postData, $files);
    }
}