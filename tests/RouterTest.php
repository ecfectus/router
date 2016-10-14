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

    public function testAddRouteIsCalled(){
        $router = new Router();

        //$route = (new Route())->setPath('path');

        $mock = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->setMethods(['mergeParams'])
            ->getMock();

        $mock->expects($this->any())
            ->method('mergeParams')
            ->with([]);

        $router->addRoute($mock);
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

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testInvalidPatternMatcherAlias(){
        $router = new Router();

        $router->addPatternMatcher('', 'regex');
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testInvalidPatternMatcherPattern(){
        $router = new Router();

        $router->addPatternMatcher('alias', '');
    }

    public function testCanAddGroup(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->group(['name' => 'name', 'domain' => 'domain', 'path' => 'path'], function($r){
            $params = $this->readAttribute($r, 'groupParams');
            $this->assertSame([['name' => 'name', 'domain' => 'domain', 'path' => 'path']], $params);

            $r->group(['name' => 'name'], function($subR){
                $params = $this->readAttribute($subR, 'groupParams');
                $this->assertSame([['name' => 'name', 'domain' => 'domain', 'path' => 'path'], ['name' => 'name']], $params);
            });
        });
    }

    public function testCanAddRouteToGroup(){
        $router = new Router();

        $this->assertSame([], $router->getRoutes());

        $router->group(['name' => 'name', 'domain' => 'domain', 'path' => 'path'], function($r){

            $r->get('path')->setName('route');

            $r->group(['name' => 'name', 'path' => 'sub'], function($rSub){

                $rSub->get('path')->setName('route');
            });
        });

        $route = $router->getRoute('name.route');

        $route2 = $router->getRoute('name.name.route');

        $this->assertSame([$route, $route2], $router->getRoutes());

        $this->assertEquals('path/sub/path', $route2->getPath());
    }

    /**
     * @expectedException     \Ecfectus\Router\NotFoundException
     */
    public function testThrows404Error(){
        $router = new Router();

        $router->get('path');

        $router->match('domain.com/somepath');
    }

    /**
     * @expectedException     \Ecfectus\Router\MethodNotAllowedException
     */
    public function testThrowsMethodNotAllowedError(){
        $router = new Router();

        $router->get('path');

        $router->compileRegex();

        $router->match('domain.com/path', 'POST');
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testThrowsInvalidArgumentError(){
        $router = new Router();

        $router->get('path');

        $router->compileRegex();

        $router->match('domain.com/path', 'POSTING');
    }

    public function testMatchesRoute(){
        $router = new Router();

        $route = $router->get('path');

        $router->compileRegex();

        $result = $router->match('domain.com/path', 'GET');

        $this->assertSame($route, $result);
    }

    public function testMatchesRouteLocalhost(){
        $router = new Router();

        $route = $router->get('path')->setDomain('localhost');

        $router->prepare();

        $result = $router->match('localhost/path', 'GET');

        $this->assertSame($route, $result);
    }

    public function testMatchesRouteLocalhostWithoutSetDomain(){
        $router = new Router();

        $route = $router->get('path');

        $router->prepare();

        $result = $router->match('localhost/path', 'GET');

        $this->assertSame($route, $result);
    }

    public function testMatchesRouteWithArgs(){
        $router = new Router();

        $route = $router->get('path/{with}/{args}');

        $router->compileRegex();

        $result = $router->match('domain.com/path/with/args', 'GET');

        $this->assertSame($route, $result);

        $this->assertSame(['with' => 'with', 'args' => 'args'], $result->getValues());
    }

    public function testMatchesRouteWithOptionalArgs(){
        $router = new Router();

        $route = $router->get('path/{with}/{args?}');

        $router->compileRegex();

        $result = $router->match('domain.com/path/with', 'GET');

        $this->assertSame($route, $result);

        $this->assertSame(['with' => 'with', 'args' => ''], $result->getValues());

        $result = $router->match('domain.com/path/with/args', 'GET');

        $this->assertSame($route, $result);

        $this->assertSame(['with' => 'with', 'args' => 'args'], $result->getValues());
    }

    public function testGetNamedRoute(){
        $router = new Router();

        $route = $router->get('path')->setName('test');

        $result = $router->getRoute('test');

        $this->assertSame($result, $route);
    }

}