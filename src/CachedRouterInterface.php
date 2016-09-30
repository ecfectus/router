<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 16:04
 */

namespace Ecfectus\Router;


interface CachedRouterInterface extends RouterInterface
{
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
    public function export();

    /**
     * Returns true/false to determine if the router is being served from a cache.
     *
     * @return bool
     */
    public function isCached() : bool;

    /**
     * Sets the cache path location.
     *
     * @param string $path
     * @return RouterInterface
     */
    public function setCachePath(string $path = '') : RouterInterface;

    /**
     * Return a new or cached instance of the router.
     *
     * @return CachedRouterInterface
     */
    public static function create() : CachedRouterInterface;
}