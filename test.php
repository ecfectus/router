<?php

require __DIR__ . '/vendor/autoload.php';


$router = new \Ecfectus\Router\Router(new \FastRoute\RouteParser\Std(), new \FastRoute\DataGenerator\GroupCountBased());

$router->get('/test', [function(){
    return 'hello';
}])->setName('test')->setMiddleware('hello');

$router->group('/admin', function($group){

   $group->get('/{something}', function(){

   })->setName('index');

    $group->group('/users', function($g){
        $g->get('/', function(){})->setName('index');
        $g->post('/', function(){})->setName('create');
    })->setName('users.')->setHost('testing.com');

})->setName('admin.');


print_r($router->match('GET', '', 'localhost', '/admin/hello'));

//$router->all();
//print_r($router->getData());