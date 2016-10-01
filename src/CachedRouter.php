<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 01/10/16
 * Time: 00:29
 */

namespace Ecfectus\Router;


/**
 * Class CachedRouter
 * @package Ecfectus\Router
 */
class CachedRouter extends Router implements RouterInterface, CachedRouterInterface
{

    /**
     * @var string
     */
    protected $cachePath = '';

    /**
     * @inheritDoc
     */
    public static function __set_state(array $atts = []) : RouterInterface
    {
        return new self($atts);
    }

    /**
     * @param array $atts
     */
    public function __construct(array $atts = [])
    {
        parent::__construct($atts);

        $this->cachePath = $atts['cachePath'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function setCachePath(string $path = '') : RouterInterface
    {
        $this->cachePath = $path;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $path = '') : CachedRouterInterface
    {
        if(is_file($path)){
            return require($path);
        }

        return (new self())->setCachePath($path);
    }

    /**
      * @inheritDoc
      */
    public function export()
    {
        $this->compileRegex();
        file_put_contents($this->cachePath, "<?php\nreturn " . var_export($this, true) . ';');
    }

    /**
     * @inheritDoc
     */
    public function isCached() : bool
    {
        return is_file($this->cachePath);
    }

}