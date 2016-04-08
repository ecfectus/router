<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 08/04/16
 * Time: 16:46
 */

namespace Ecfectus\Router;

use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use Psr\Http\Message\RequestInterface;

class Router extends RouteCollector implements RouteCollectionInterface
{

    use RouteCollectionMapTrait;

    /**
     * @var []
     */
    protected $routes = [];

    /**
     * @var []
     */
    protected $namedRoutes = [];

    /**
     * @var []
     */
    protected $groups = [];

    /**
     * @var array
     */
    protected $patternMatchers = [
        '/{(.+?):number}/'        => '{$1:[0-9]+}',
        '/{(.+?):word}/'          => '{$1:[a-zA-Z]+}',
        '/{(.+?):alphanum_dash}/' => '{$1:[a-zA-Z0-9-_]+}',
        '/{(.+?):slug}/'          => '{$1:[a-z0-9-]+}',
        '/{(.+?):uuid}/'          => '{$1:[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}+}'
    ];

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler)
    {
        $path = sprintf('/%s', ltrim($path, '/'));

        $route = (new Route)->setMethods((array) $method)->setPath($path)->setCallable($handler);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * Add a group of routes to the collection.
     *
     * @param string   $prefix
     * @param callable $group
     */
    public function group($prefix, callable $group)
    {
        $group = new RouteGroup($prefix, $group, $this);

        $this->groups[] = $group;

        return $group;
    }

    public function matchRequest(RequestInterface $request){
        return $this->match($request->getMethod(), $request->getUri()->getScheme(), $request->getUri()->getHost(), $request->getUri()->getPath());
    }

    /**
     * Dispatch the route based on the request.
     */
    public function match($method = 'GET', $scheme = '', $host, $uri)
    {
        $dispatcher = $this->getDispatcher($method, $scheme, $host, $uri);

        return $dispatcher->dispatch($method, $uri);
    }

    /**
     * Return a fully configured dispatcher.
     */
    public function getDispatcher($method = 'GET', $scheme = '', $host, $uri)
    {

        $this->prepRoutes($method, $scheme, $host, $uri);

        return (new GroupCountBased($this->getData()));
    }

    /**
     * Prepare all routes, build name index and filter out none matching
     * routes before being passed off to the parser.
     *
     *
     * @return void
     */
    protected function prepRoutes($method = 'GET', $scheme = '', $host, $uri)
    {
        $this->buildNameIndex();

        $routes = array_merge(array_values($this->routes), array_values($this->namedRoutes));

        foreach ($routes as $key => $route) {
            // check for scheme condition
            if (! is_null($route->getScheme()) && $route->getScheme() !== $scheme) {
                continue;
            }

            // check for domain condition
            if (! is_null($route->getHost()) && $route->getHost() !== $host) {
                continue;
            }

            $this->addRoute(
                $route->getMethods(),
                $this->parseRoutePath($route->getPath()),
                $route
            );
        }
    }

    /**
     * Build an index of named routes.
     *
     * @return void
     */
    protected function buildNameIndex()
    {
        $this->processGroups();

        foreach ($this->routes as $key => $route) {
            if (! is_null($route->getName())) {
                unset($this->routes[$key]);
                $this->namedRoutes[$route->getName()] = $route;
            }
        }
    }

    /**
     * Process all groups.
     *
     * @return void
     */
    protected function processGroups()
    {
        foreach ($this->groups as $key => $group) {
            unset($this->groups[$key]);
            $group();
        }
    }

    /**
     * Get named route.
     *
     * @param string $name
     */
    public function getNamedRoute($name)
    {
        $this->buildNameIndex();

        if (array_key_exists($name, $this->namedRoutes)) {
            return $this->namedRoutes[$name];
        }

        throw new \InvalidArgumentException(sprintf('No route of the name (%s) exists', $name));
    }

    /**
     * Add a convenient pattern matcher to the internal array for use with all routes.
     *
     * @param string $alias
     * @param string $regex
     *
     * @return void
     */
    public function addPatternMatcher($alias, $regex)
    {
        $pattern = '/{(.+?):' . $alias . '}/';
        $regex   = '{$1:' . $regex . '}';

        $this->patternMatchers[$pattern] = $regex;
    }

    /**
     * Convenience method to convert pre-defined key words in to regex strings.
     *
     * @param string $path
     *
     * @return string
     */
    protected function parseRoutePath($path)
    {
        return preg_replace(array_keys($this->patternMatchers), array_values($this->patternMatchers), $path);
    }
}