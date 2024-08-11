<?php
session_start();
require __DIR__.'/vendor/autoload.php';

use App\Controller\Home;
use App\Controller\PostController;
use App\Controller\ErrorController;
use App\Controller\CommentController;
use App\Controller\UserController;
use App\core\Core;

ob_start();
if($_SERVER['REQUEST_METHOD'] == 'GET'){
  Core::init($_GET);  
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $formData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    if(!empty($formData['btnComentar'])){
        CommentController::create($formData);
    }
    
    if(!empty($formData['btnEntrar'])){
        Core::userPostHandler($formData);
    }
    
    if(!empty($formData['newPost'])){
        Core::postPostHandler($formData, $_FILES['post_picture']);
    }   
    if(!empty($formData['updatePost'])){
        Core::postUpdateHandler($formData, $_FILES['post_picture']);
    }
}
$outPut = ob_get_contents();
ob_end_clean();
renderLayout($outPut);

function renderLayout($content)
{
    $layout_path = "./app/view/layout.html";
    $layout = file_get_contents($layout_path);
    $updatedLayout = str_replace(["{{pageTitle}}","{{pageContent}}"], ["Home Page",$content], $layout);
    echo $updatedLayout;
}