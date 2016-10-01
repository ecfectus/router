<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 16:04
 */

namespace Ecfectus\Router;


/**
 * Interface RouterInterface
 * @package Ecfectus\Router
 */
interface RouterInterface
{

    /**
     * Get router Routes
     *
     * @return array
     */
    public function getRoutes() : array;

    /**
     * Set Router Routes.
     *
     * @param array $routes
     * @return RouterInterface
     */
    public function setRoutes(array $routes = []) : RouterInterface;

    /**
     * Return a named Route.
     *
     * @param string $name
     * @return RouteInterface
     */
    public function getRoute(string $name = '') : RouteInterface;

    /**
     * Add a created route to the stack.
     *
     * @param RouteInterface $router
     * @return RouteInterface
     */
    public function addRoute(RouteInterface $router) : RouteInterface;

    /**
     * Adds a route to the collection for all methods at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function any(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the OPTIONS method at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function options(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the HEAD method at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function head(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the GET and HEAD methods at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function get(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the POST method at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function post(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the PUT method at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function put(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the PATCH method at the specified path.
     *
     * @param string $path
     * @return Route
     */
    public function patch(string $path) : RouteInterface;

    /**
     * Adds a route to the collection for the DELETE method at the specified path.
     *
     * @param string $path
     * @return Route
     */
    public function delete(string $path) : RouteInterface;

    /**
     * Allows you to define a callback which will add routes with the included params.
     *
     * @param array $params
     * @param callable $callback
     * @return RouterInterface
     */
    public function group(array $params, callable $callback) : RouterInterface;

    /**
     * Compile regex strings across all routes.
     */
    public function compileRegex();

    /**
     * Prepare the router for matching urls.
     */
    public function prepare();

    /**
     * Match a route against the provided path.
     *
     * @param string $path
     * @param string $method
     * @return RouteInterface
     */
    public function match(string $path = '', string $method = 'GET') : RouteInterface;

    /**
     * Add a convenient pattern matcher to the internal array for use with all routes.
     *
     * @param string $alias
     * @param string $regex
     *
     * @return void
     */
    public function addPatternMatcher(string $alias = '', string $regex = '') : RouterInterface;
}