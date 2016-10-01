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
    protected $domainRegex = '(?:([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6})?';

    /**
     * @var array
     */
    protected $values = [];

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
            $regex = '(?:([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6})?';
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
        $params = array_reverse($params);
        foreach($params as $p){
            $this->mergePath($p['path'] ?? '');
            $this->mergeName($p['name'] ?? '');
            $this->mergeDomain($p['domain'] ?? '');
        }
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

        $passes = preg_match('~^' . $this->getDomainRegex() . '/' .$this->getRegex() . '$~', rtrim($path, '/'), $matches);

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

}