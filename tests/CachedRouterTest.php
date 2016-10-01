<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 01/10/16
 * Time: 14:30
 */

namespace Ecfectus\Router\Tests;


use Ecfectus\Router\CachedRouter;
use PHPUnit\Framework\TestCase;

class CachedRouterTest extends TestCase
{

    public function testIsCachedMethod(){
        @unlink('../testroutes.php');
        $router = CachedRouter::create('../testroutes.php');

        $this->assertFalse($router->isCached());

        $router->get('path');

        $router->compileRegex();
        $router->export();

        $this->assertTrue($router->isCached());

        $this->assertSame(serialize($router), serialize(CachedRouter::create('../testroutes.php')));

        unlink('../testroutes.php');

    }

}