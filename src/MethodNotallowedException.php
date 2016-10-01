<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 30/09/16
 * Time: 21:57
 */

namespace Ecfectus\Router;


/**
 * Class MethodNotAllowedException
 * @package Ecfectus\Router
 */
class MethodNotAllowedException extends \Exception
{
    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @param string $message
     * @param array $methods
     */
    public function __construct(string $message = '', array $methods = []){
        parent::__construct($message);

        $this->setMethods($methods);
    }

    /**
     * @return array
     */
    public function getMethods() : array
    {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }



}