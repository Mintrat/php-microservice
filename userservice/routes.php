<?php

use MicroService\controllers\UserController;

$app->get('/', function($request, $response, $args){


    echo <<<HTML
<form action="/user/" method="post">
имя<input type="text" name="name"><br>
пароль<input type="text" name="password"><br>
<input type="submit">
</form>

HTML;

});
$app->post('/user/', function ($request, $response, $args) {
    $user = new \MicroService\controllers\UserController();
    $user->createUser($_POST['name'], $_POST['password']);
});

