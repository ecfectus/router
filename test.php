<?php

require __DIR__ . '/vendor/autoload.php';


$router = \Ecfectus\Router\CachedRouter::create('routes.php');

if(!$router->isCached()){
    $router->get('/test')->setDomain('{test}.leemason.co.uk');

    $router->group([
        'path' => '/testing'
    ], function($r){
        $r->any('/hello')->setDomain('leemason.co.uk');
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


    print_r($router->match('hello.leemason.co.uk/test', 'OPTIONS'));
    print_r($router->match('leemason.co.uk/testing/hello'));
    print_r($router->match('domain.com/testing/123/name'));
    print_r($router->match('/testing/123/name/group'));
    print_r($router->match('/testing/123/name/group/test'));

}catch( Exception $e){
    print_r($e);
}