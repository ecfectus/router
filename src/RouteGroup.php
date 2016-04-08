<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 08/04/16
 * Time: 17:04
 */

namespace Ecfectus\Router;

class RouteGroup implements RouteCollectionInterface
{
    use RouteCollectionMapTrait;
    use RouteConditionTrait;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var \Ecfectus\Router\RouteCollectionInterface
     */
    protected $collection;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var []
     */
    protected $groups = [];

    /**
     * Constructor.
     *
     */
    public function __construct($prefix, callable $callback, RouteCollectionInterface $collection)
    {
        $this->callback   = $callback;
        $this->collection = $collection;
        $this->prefix     = sprintf('/%s', ltrim($prefix, '/'));
    }

    /**
     * Process the group and ensure routes are added to the collection.
     *
     * @return void
     */
    public function __invoke()
    {
        call_user_func_array($this->callback, [$this]);

        $this->processGroups();
    }

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler)
    {
        $path  = ($path === '/') ? $this->prefix : $this->prefix . sprintf('/%s', ltrim($path, '/'));
        $route = $this->collection->map($method, $path, $handler);

        $route->setParentGroup($this);

        if ($host = $this->getHost()) {
            $route->setHost($host);
        }

        if ($scheme = $this->getScheme()) {
            $route->setScheme($scheme);
        }

        return $route;
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
}