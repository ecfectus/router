# Ecfectus Router

PHP 7 Router implementation.

The ecfectus router is a lean, fast and cachable PHP router with no dependencies.

The router has been made lean by not needing any knowledge of a request, it is quite simply a pattern matcher, which matches a path pattern against a route.

## Usage

```php
$router = new \Ecfectus\Router\Router();

/**
 * Basic Requests
 */

//get requests
$router->get('/url')
    ->setHandler('Class@method');

//post requests
$router->post('/url')
    ->setHandler('Class@method');

//put requests
$router->put('/url')
    ->setHandler('Class@method');

//patch requests
$router->patch('/url')
    ->setHandler('Class@method');

//delete requests
$router->delete('/url')
    ->setHandler('Class@method');

/**
 * Route Params
 */

//args and optional args
$router->get('/url/{arg}/{optionalarg?}')
    ->setHandler('Class@method');

//only matches number
$router->get('/url/{arg:number}')
    ->setHandler('Class@method');

//only matches words
$router->get('/url/{arg:word}')
    ->setHandler('Class@method');

//only matches alpha numeric + dashs
$router->get('/url/{arg:alphanumdash}')
    ->setHandler('Class@method');

//only matches slugs
$router->get('/url/{arg:slug}')
    ->setHandler('Class@method');

//only matches uuids
$router->get('/url/{arg:uuid}')
    ->setHandler('Class@method');

/**
 * Named Routes
 */

$router->get('/url')
    ->setName('routename')
    ->setHandler('Class@method');

/**
 * Domain Routes
 */

$router->get('/url')
    ->setDomain('domain.com')
    ->setHandler('Class@method');

// domain placeholders
$router->get('/url')
    ->setDomain('{subdomain}.domain.com')
    ->setHandler('Class@method');

/**
 * Grouped routes
 */

$router->group([
        'path' => '/api',
        'name' => 'api',
        'domain' => 'domain.com'
    ], function($r){

        $r->get('/users') // becomes domain.com/api/users
            ->setName('users') // becomes api.users
            ->setHandler('Class@method');

        //groups can be nested
    });

/**
 * Compile Routes before matching
 */
try{
    $router->compileRegex();
}catch( Exception $e){

}

/**
 * Match routes
 */
try{

    $route = $router->match('hello.domain.com/url', 'GET'); // $path, $method = 'GET|POST|PUT|PATCH|DELETE'

    $values = $route->getValues(); // ['subdomain' => 'hello']

}catch( \Ecfectus\Router\NotFoundException $e){
    //no route matched
}catch( \Ecfectus\Router\MethodNotAllowedException $e){
    //route matched but method not allowed
}catch( \Exception $e){

}
```
