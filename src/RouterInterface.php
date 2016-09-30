<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 16:04
 */

namespace Ecfectus\Router;


interface RouterInterface
{

    /**
     * Adds a route to the collection for all methods at the specified path.
     *
     * @param string $path
     * @param string $path
     * @return Route
     */
    public function any(string $path) : RouteInterface;

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
     * Match a route against the provided path.
     *
     * @param string $path
     * @param string $method
     * @return Route
     */
    public function match(string $path, string $method = 'GET') : RouteInterface;

    /**
     * Add a convenient pattern matcher to the internal array for use with all routes.
     *
     * @param string $alias
     * @param string $regex
     *
     * @return void
     */
    public function addPatternMatcher(string $alias, string $regex) : RouterInterface;

    /**
     * Return the router to a regex ready state from cached routes.
     *
     * @param array $atts
     * @return RouterInterface
     */
    public static function __set_state(array $atts = []) : RouterInterface;

    /**
     * Return a writable/executable string to repopulate the router.
     *
     * @return string
     */
    public function export() : string;
}