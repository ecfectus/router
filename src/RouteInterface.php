<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 16:34
 */

namespace Ecfectus\Router;


/**
 * Interface RouteInterface
 * @package Ecfectus\Router
 */
interface RouteInterface
{

    /**
     * @param string $path
     * @return RouteInterface
     */
    public function setPath(string $path) : RouteInterface;

    /**
     * @return string
     */
    public function getPath() : string;

    /**
     * @param string $name
     * @return RouteInterface
     */
    public function setName(string $name) : RouteInterface;

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param string $path
     * @return RouteInterface
     */
    public function setRegex(string $path) : RouteInterface;

    /**
     * @return string
     */
    public function getRegex() : string;

    /**
     * @param array $methods
     * @return RouteInterface
     */
    public function setMethods(array $methods) : RouteInterface;

    /**
     * @return mixed
     */
    public function getMethods() : array;

    /**
     * @param $handler
     * @return RouteInterface
     */
    public function setHandler($handler) : RouteInterface;

    /**
     * @return mixed
     */
    public function getHandler();

    /**
     * @param string $domain
     * @return RouteInterface
     */
    public function setDomain(string $domain) : RouteInterface;

    /**
     * @return string
     */
    public function getDomain() : string;

    /**
     * @param string $domain
     * @return RouteInterface
     */
    public function setDomainRegex(string $domain) : RouteInterface;

    /**
     * @return string
     */
    public function getDomainRegex() : string;

    /**
     * @param array $values
     * @return RouteInterface
     */
    public function setValues(array $values) : RouteInterface;

    /**
     * @return array
     */
    public function getValues() : array;

    /**
     * @param array $params
     * @return RouteInterface
     */
    public function mergeParams(array $params) : RouteInterface;

    /**
     * @param string $path
     * @return bool
     */
    public function matches(string $path) : bool;

    /**
     * @param string $method
     * @return bool
     */
    public function isAllowedMethod(string $method) : bool;

    /**
     * Return the route to a regex ready state from cached routes.
     *
     * @param array $atts
     * @return RouteInterface
     */
    public static function __set_state(array $atts = []) : RouteInterface;

}