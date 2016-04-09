<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 08/04/16
 * Time: 17:02
 */

namespace Ecfectus\Router;


use Underscore\Types\Strings;

class Route
{

    use RouteConditionTrait;

    /**
     * @var string|callable
     */
    protected $callable;

    /**
     * @var \Ecfectus\Router\RouteGroup
     */
    protected $group;

    /**
     * @var string[]
     */
    protected $methods = [];

    /**
     * @var string
     */
    protected $path;

    protected $attributes = [
        'middleware' => []
    ];

    /**
     * Get the callable.
     *
     * @return string|callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Set the callable.
     *
     * @param string|callable $callable
     *
     * @return \League\Route\Route
     */
    public function setCallable($callable)
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * Get the parent group.
     *
     * @return \League\Route\RouteGroup
     */
    public function getParentGroup()
    {
        return $this->group;
    }

    /**
     * Set the parent group.
     *
     * @param \League\Route\RouteGroup $group
     *
     * @return \League\Route\Route
     */
    public function setParentGroup(RouteGroup $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path.
     *
     * @param string $path
     *
     * @return \League\Route\Route
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get the methods.
     *
     * @return string[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get the methods.
     *
     * @param string[] $methods
     *
     * @return \League\Route\Route
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;

        return $this;
    }

    public function __call($method, $arguments){
        if(substr($method, 0, 3) === 'set'){
            $attribute = Strings::toSnakeCase(lcfirst(substr($method, 3)));
            $this->attributes[$attribute] = $arguments[0];
        }

        if(substr($method, 0, 3) === 'get'){
            $attribute = Strings::toSnakeCase(lcfirst(substr($method, 3)));
            return $this->attributes[$attribute];
        }

    }
}