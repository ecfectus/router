<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 08/04/16
 * Time: 16:46
 */

namespace Ecfectus\Router;

/**
 * Class Router
 * @package Ecfectus\Router
 */
class Router implements RouterInterface
{

    /**
     * @var array
     */
    protected $methods = [
        'OPTIONS',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $methodRoutes = [
        'OPTIONS' => [],
        'HEAD' => [],
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => []
    ];

    /**
     * @var array
     */
    protected $patternMatchers = [
        '/\/\{([a-zA-Z:]+)\?\}/' => '(?:/{$1})?',//run this first to replace optional params with a regex to suit
        '/{([a-zA-Z]+)}/'          => '(?<$1>[^/]+)',
        '/{(.+?):number}/'        => '(?<$1>[0-9]+)',
        '/{(.+?):word}/'          => '(?<$1>[a-zA-Z]+)',
        '/{(.+?):alphanumdash}/' => '(?<$1>[a-zA-Z0-9-_]+)',
        '/{(.+?):slug}/'          => '(?<$1>[a-z0-9-]+)',
        '/{(.+?):uuid}/'          => '(?<$1>[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+)'
    ];

    /**
     * @var array
     */
    protected $groupParams = [];

    /**
     * @param array $atts
     */
    public function __construct(array $atts = [])
    {
        $this->routes = $atts['routes'] ?? [];
        $this->patternMatchers = $atts['patternMatchers'] ?? $this->patternMatchers;
        $this->methodRoutes = $atts['methodRoutes'] ?? $this->methodRoutes;
    }

    /**
     * @inheritDoc
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }

    /**
     * @inheritDoc
     */
    public function setRoutes(array $routes = []) : RouterInterface
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addRoute(RouteInterface $route) : RouteInterface
    {
        if(!empty($this->groupParams)){
            $route->mergeParams($this->groupParams);
        }

        $this->routes[] = $route;

        end($this->routes);
        $key = key($this->routes);

        foreach($route->getMethods() as $method){
            $this->methodRoutes[$method][] = $key;
        }

        return $route;
    }

    /**
     * @inheritDoc
     */
    public function any(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods($this->methods);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function options(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['OPTIONS']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function head(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['HEAD']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function get(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['HEAD', 'GET']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function post(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['POST']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function put(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['PUT']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function patch(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['PATCH']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path) : RouteInterface
    {
        $route = (new Route())->setPath($path)->setMethods(['DELETE']);
        return $this->addRoute($route);
    }

    /**
     * @inheritDoc
     */
    public function group(array $params, callable $callback) : RouterInterface
    {
        $this->addGroupParams($params);
        $callback($this);
        $this->popGroupParams();
        return $this;
    }

    /**
     * Add group params to the stack.
     *
     * @param array $params
     */
    private function addGroupParams(array $params)
    {
        $this->groupParams[] = $params;
    }

    /**
     * Remove the last group params from the stack.
     *
     */
    private function popGroupParams()
    {
        array_pop($this->groupParams);
    }

    /**
     * @inheritDoc
     */
    public function compileRegex()
    {

        foreach($this->routes as $route) {

            //add pattern matcher regex
            $route->setDomainRegex($this->parseRoutePath($route->getDomain()));
            $route->setRegex($this->parseRoutePath($route->getPath()));

        }
    }

    /**
     * @inheritDoc
     */
    public function match(string $path = '', string $method = 'GET') : RouteInterface
    {

        $method = strtoupper($method);

        if(!in_array($method, $this->methods)){
            throw new \InvalidArgumentException('The method should be one of: [' . implode(',', $this->methods) . ']');
        }

        // for performance loop only the same method routes for smaller sample.
        if(isset($this->methodRoutes[$method])){
            foreach($this->methodRoutes[$method] as $routeIndex){

                $route = $this->routes[$routeIndex];

                if($route->matches($path)){

                    return $route;

                }
            }
        }

        // if we get here its possibly a method not allowed route, slower but used much less often.
        $allowedMethods = [];
        foreach($this->routes as $route){
            if($route->matches($path)){
                $allowedMethods = array_merge($allowedMethods, $route->getMethods());
            }
        }

        if(!empty($allowedMethods)){
            throw new MethodNotAllowedException('A Route Matched the provided path: [' . $path . '], but it is not allowed for the supplied method [' . $method . '].', $allowedMethods);
        }

        //finally throw a 404
        throw new NotFoundException('No Route Matches the provided path: [' . $path . '].');
    }

    /**
     * @inheritDoc
     */
    public function addPatternMatcher(string $alias = '', string $regex = '') : RouterInterface
    {
        if($alias == '' || $regex == ''){
            throw new \InvalidArgumentException('$alias and $regex should not be empty.');
        }

        $pattern = '/{(.+?):' . $alias . '}/';
        $regex   = '{$1:' . $regex . '}';

        $this->patternMatchers[$pattern] = $regex;

        return $this;
    }

    /**
     * Convenience method to convert pre-defined key words in to regex strings.
     *
     * @param string $path
     * @return string
     */
    protected function parseRoutePath(string $path) : string
    {
        return preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $path);
    }

}