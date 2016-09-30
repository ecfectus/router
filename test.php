<?php

require __DIR__ . '/vendor/autoload.php';


$router = \Ecfectus\Router\CachedRouter::create('data.php');

if(!$router->isCached()){
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

        $router->export();
    }catch( Exception $e){

    }
}

try{

    print_r($router);


    print_r($router->match('http://hello.leemason.co.uk/test'));
    print_r($router->match('http://leemason.co.uk/testing/hello'));
    print_r($router->match('/testing/123/name'));
    print_r($router->match('/testing/123/name/group'));
    print_r($router->match('/testing/123/name/group/test'));

}catch( Exception $e){

}