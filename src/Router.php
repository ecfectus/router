<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 08/04/16
 * Time: 16:46
 */

namespace Ecfectus\Router;

class Router implements RouterInterface
{

    /**
     * @var array
     */
    protected $methods = [
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
    protected $patternMatchers = [
        '/\/\{([a-zA-Z:]+)\?\}/' => '(?:/{$1})?',
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

    public static function __set_state(array $atts = []) : RouterInterface
    {
        return new self($atts);
    }

    public function __construct(array $atts = [])
    {
        $this->routes = $atts['routes'] ?? [];
        $this->patternMatchers = $atts['patternMatchers'] ?? $this->patternMatchers;
    }

    private function addRoute(RouteInterface $route) : RouteInterface
    {
        if(!empty($this->groupParams)){
            $route->mergeParams($this->groupParams);
        }

        $this->routes[] = $route;

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

    private function addGroupParams(array $params)
    {
        $this->groupParams[] = $params;
    }

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
    public function match(string $path, string $method = 'GET') : RouteInterface
    {

        foreach($this->routes as $route){

            if($route->matches($path)){

                if(!$route->isAllowedMethod($method)){
                    throw new MethodNotallowedException('A Route Matched the provided path: [' . $path . '], but it is not allowed for the supplied method [' . $method . '].');
                }

                return $route;
            }
        }
        throw new NotFoundException('No Route Matches the provided path: [' . $path . '].');
    }

    /**
     * @inheritDoc
     */
    public function addPatternMatcher(string $alias, string $regex) : RouterInterface
    {
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

    public function export(){
        return "<?php\nreturn " . var_export($this, true) . ';';
    }
}