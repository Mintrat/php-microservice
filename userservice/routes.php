<?php

use MicroService\controllers\UserController;

$app->get('/', function($request, $response, $args){
    echo 'Main page';
});

$app->post('/user/', function ($request, $response, $args) {
    $user = new \MicroService\controllers\UserController();
    $user->createUser($_POST['name'], $_POST['password']);
});

$app->post('/check/', function ($request, $response, $args) {
    $user = new \MicroService\controllers\UserController();
    echo $user->checkUser($_POST['name'], $_POST['password']);
});

