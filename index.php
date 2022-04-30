<?php
//framework
require_once('./small-php/small/small.php');

//fichier src
require_once('./src/user.php');
require_once('./src/auth.php');


$small = new Small();

$small->get('/', function($request, $response) {

    $response->setData(['message'=>'pong']);
    
    return $response;
});

$small->post('/login', function($request, $response) {

    $data=login($request->params['mail'], md5($request->params['password']));
    if($data==false) {
        $response->setData(['error'=>"Erreur d'identification"]);
        $response->setResponseCode(403); 
    }else {
        $response->setCookie('mail', $data['mail']);
        $response->setCookie('password', $data['password']);
    }
    
    return $response;
});

$small->get('/me', function($request, $response) {

    $data=login($request->cookies['mail'], $request->cookies['password']);
    if($data==false) {
        $response->setData(['error'=>"Utilisateur non reconnu"]);
        $response->setResponseCode(403); 
    }else {
        $response->setData($data);
    }
    
    return $response;
});

$small->get('/user', function($request, $response) {

    $data=listUser();
    $response->setData($data);
    
    return $response;
});

$small->post('/user', function($request, $response) {

    $data=verifMail($request->params['mail']);
    $response->setData($data);

    if($data==false){
        $password = md5($request->params['password']);
        $data = addUser($request->params['name'], $request->params['mail'], $password);
        $response->setData($data);
    }else{
        $response->setData(['error'=>'Un utilisateur est déjà enregistré avec ce mail']);
        $response->setResponseCode(404); 
    }
    return $response;

});

$small->get('user/{id}', function($request, $response) {
    
    $data = getUser($request->resource['id']);
    $response->setData($data);
    
    //Verification si l'utilisateur avec cet ID existe
    if($data==false){
        $response->setData(['error'=>"L'utilisateur n'existe pas"]);
        $response->setResponseCode(404);    
    }

    return $response;
});

$small->req('user/{id}', 'delete', function($request, $response) {

    //Verification si l'utilisateur avec cet ID existe
    $data = getUser($request->resource['id']);
    $response->setData($data);
    
    if($data==false){
        $response->setData(['error'=>"L'utilisateur n'existe pas"]);
        $response->setResponseCode(404);    
    }else{
        $data = deleteUser($request->resource['id']);
        $response->setData($data);
    }

    return $response;
});