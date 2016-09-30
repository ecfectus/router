<?php

require __DIR__ . '/vendor/autoload.php';

//$router = require('data.php');


$router = new \Ecfectus\Router\Router();

$router->get('/test')->setDomain('http://{test}.leemason.co.uk');

$router->group([
    'path' => '/testing'
], function($r){
   $r->any('/hello')->setDomain('http://leemason.co.uk');
    $r->group([
        'path' => '/123'
    ], function($r){
        //$r->post('/{name}');
        //$r->post('/{name}(?:/{group})?(?:/{test})?');
        $r->get('/{name}/{group:alphanumdash?}/{test?}');
    });
});


try{
    $router->compileRegex();

    print_r($router);


    print_r($router->match('http://hello.leemason.co.uk/test'));
    print_r($router->match('http://leemason.co.uk/testing/hello'));
    print_r($router->match('/testing/123/name'));
    print_r($router->match('/testing/123/name/group'));
    print_r($router->match('/testing/123/name/group/test'));
}catch( Exception $e){

}


//file_put_contents('data.php', $router->export());