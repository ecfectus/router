<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 16:34
 */

namespace Ecfectus\Router;


interface RouteInterface
{

    public function setPath(string $path) : RouteInterface;

    public function getPath() : string;

    public function setName(string $name) : RouteInterface;

    public function getName() : string;

    public function setRegex(string $path) : RouteInterface;

    public function getRegex() : string;

    public function setMethods(array $methods) : RouteInterface;

    public function getMethods() : array;

    public function setHandler($handler) : RouteInterface;

    public function getHandler();

    public function setDomain(string $domain) : RouteInterface;

    public function getDomain() : string;

    public function setDomainRegex(string $domain) : RouteInterface;

    public function getDomainRegex() : string;

    public function setValues(array $values) : RouteInterface;

    public function getValues();

    public function mergeParams(array $params) : RouteInterface;

    public function matches(string $path) : bool;

    public function isAllowedMethod(string $method) : bool;

    /**
     * Return the route to a regex ready state from cached routes.
     *
     * @param array $atts
     * @return RouteInterface
     */
    public static function __set_state(array $atts = []) : RouteInterface;

}