<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 01/10/16
 * Time: 14:38
 */

namespace Ecfectus\Router\Tests;


use Ecfectus\Router\Route;
use Ecfectus\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    public function testCanAddRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $route = (new Route())->setPath('path');

        $router->addRoute($route);

        $this->assertSame([$route], $router->getRoutes());
    }

    public function testCanSetRoutes(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $route = (new Route())->setPath('path');

        $router->setRoutes([$route]);

        $this->assertSame([$route], $router->getRoutes());
    }

    public function testCanAddOptionsRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->options('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddHeadRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->head('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddGetRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->get('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddPostRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->post('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddPutRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->put('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddPatchRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->patch('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddDeleteRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->delete('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddAnyRoute(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->any('path');

        $this->assertCount(1, $router->getRoutes());
    }

    public function testCanAddPatternMatcher(){
        $router = new Router();

        $patterns = $this->readAttribute($router, 'patternMatchers');

        $router->addPatternMatcher('alias', 'regex');

        $this->assertCount(count($patterns) + 1, $this->readAttribute($router, 'patternMatchers'));
    }

}