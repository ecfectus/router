<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 16:09
 */

namespace Ecfectus\Router;


/**
 * Class Route
 * @package Ecfectus\Router
 */
class Route implements RouteInterface
{
    /**
     * @var array
     */
    protected $allowedMethods = [
        'OPTIONS',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var
     */
    protected $regex = '';

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var mixed
     */
    protected $handler;

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var string
     */
    protected $domainRegex = '(?:([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}|localhost)?';

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @inheritDoc
     */
    public static function __set_state(array $atts = []) : RouteInterface
    {
        return new self($atts);
    }

    /**
     * @param array $atts
     */
    public function __construct(array $atts = [])
    {
        foreach($atts as $key => $value){
            $this->{$key} = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function setPath(string $path) : RouteInterface
    {
        $this->path = $this->slashPath($path);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name) : RouteInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setRegex(string $path) : RouteInterface
    {
        $this->regex = $this->slashPath($path);

        $this->parseValues();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRegex() : string
    {
        return $this->regex;
    }

    /**
     * @inheritDoc
     */
    public function setMethods(array $methods) : RouteInterface
    {
        foreach($methods as $method){
            if(!in_array($method, $this->allowedMethods)){
                throw new \InvalidArgumentException('The method should be one of: [' . implode(',', $this->allowedMethods) . ']');
            }
        }
        $this->methods = $methods;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMethods() : array
    {
        return $this->methods;
    }

    /**
     * @inheritDoc
     */
    public function setHandler($handler) : RouteInterface
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function setDomain(string $domain) : RouteInterface
    {
        $this->domain = rtrim($domain, '/');

        $this->parseValues();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @inheritDoc
     */
    public function setDomainRegex(string $regex = '') : RouteInterface
    {
        if($regex == ''){
            $regex = '(?:([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}|localhost)?';
        }

        $this->domainRegex = $regex;

        $this->parseValues();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDomainRegex(): string
    {
        return $this->domainRegex;
    }

    /**
     * @inheritDoc
     */
    public function setValues(array $values) : RouteInterface
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @inheritDoc
     */
    public function setMiddleware(array $middleware) : RouteInterface
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMiddleware() : array
    {
        return $this->middleware;
    }

    /**
     * Set the routes values array to a keyed list of the arguments ready for population.
     */
    protected function parseValues()
    {

        $this->setValues([]);

        $matches = [];

        preg_match_all("/\<(.+?)\>/", $this->getDomainRegex() . '/' . $this->getRegex(), $matches);

        if(!empty($matches[1])){
            foreach($matches[1] as $value){
                $this->values[$value] = '';
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function mergeParams(array $params) : RouteInterface
    {
        $this->mergePath($params['path'] ?? '');
        $this->mergeName($params['name'] ?? '');
        $this->mergeDomain($params['domain'] ?? '');
        $this->mergeMiddleware($params['middleware'] ?? []);
        return $this;
    }

    /**
     * Merge group path with route path.
     *
     * @param string $path
     * @return RouteInterface
     */
    private function mergePath(string $path = '') : RouteInterface
    {
        $this->path = $this->slashPath(implode('/', [$path, $this->path]));
        return $this;
    }

    /**
     * Merge group name with route name, glued by dots.
     *
     * @param string $name
     * @return RouteInterface
     */
    private function mergeName(string $name = '') : RouteInterface
    {
        $this->name = trim(implode('.', [$name, $this->name]), '.');
        return $this;
    }

    /**
     * If the group has a domain AND the route doesnt, add that as the domain.
     *
     * @param string $domain
     * @return RouteInterface
     */
    private function mergeDomain(string $domain = '') : RouteInterface
    {
        if($this->domain == '' && $domain != ''){
            $this->setDomain($domain);
        }
        return $this;
    }

    /**
     * If the group has middleware, merge with the route middleware, map through array values to reset keys.
     *
     * @param array $middleware
     * @return RouteInterface
     */
    private function mergeMiddleware(array $middleware) : RouteInterface
    {
        $this->middleware = array_values(array_unique(array_merge(array_values($this->middleware), array_values($middleware))));
        return $this;
    }

    /**
     * Performs a string manipulation to ensure there is no leading or trailing slash.
     *
     * @param string $path
     * @return string
     */
    private function slashPath(string $path) : string
    {
        return trim($path, '/');
    }

    /**
     * @inheritDoc
     */
    public function matches(string $path): bool
    {
        $matches = [];

        $path = $this->slashPath($path);

        $regex = $this->getDomainRegex() . '/' . $this->getRegex();

        $passes = preg_match('~^' . $this->slashPath($regex) . '$~', $path, $matches);

        foreach($matches as $k => $v) {
            if(!is_int($k)) {
                $this->values[$k] = $matches[$k];
            }
        }

        if($passes === 1 && $matches[0] == $path){
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function isAllowedMethod(string $method) : bool
    {
        return in_array($method, $this->getMethods());
    }

    /**
     * @inheritDoc
     */
    public function url(array $args = []) : string
    {
        $url = $this->getDomain();

        $url .= '/' . $this->getPath();

        $matches = [];

        preg_match_all("~{([^/]+)}~", $url, $matches);

        if(!empty($args) && empty($matches[0]) && empty($matches[1])){
            throw new \InvalidArgumentException('You cannot provide arguments to a route without them.');
        }

        $values = [];
        $replacements = [];

        foreach($matches[1] as $key => $value){
            $arg = rtrim(explode(':', $value)[0], '?');
            //if its not optional, validate input args
            if(substr($value, -1) != '?'){
                if(!isset($args[$arg])){
                    throw new \InvalidArgumentException('You must provide all required arguments, [{' . $arg . '}] is missing.');
                }
            }
            $values[] = $matches[0][$key];
            $replacements[] = $args[$arg] ?? '';
        }

        $url = str_replace($values, $replacements, $url);

        //make domain requests schemeless
        if($this->getDomain() != ''){
            $url = '//' . $url;
        }

        //rtrim removes multiple slashes if it needs to.
        return rtrim($url, '/');
    }


}