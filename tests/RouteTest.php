<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 01/10/16
 * Time: 13:08
 */

namespace Ecfectus\Router\Tests;


use Ecfectus\Router\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{

    public function testCanSetPath()
    {
        $route = new Route();

        $this->assertEquals('', $route->getPath());

        $route->setPath('path');

        $this->assertEquals('path', $route->getPath());

        $route->setPath('/path');

        $this->assertEquals('path', $route->getPath());

        $route->setPath('/path/');

        $this->assertEquals('path', $route->getPath());
    }

    public function testCanSetRegex()
    {
        $route = new Route();

        $this->assertEquals('', $route->getRegex());

        $route->setRegex('regex');

        $this->assertEquals('regex', $route->getRegex());

        $route->setRegex('/regex');

        $this->assertEquals('regex', $route->getRegex());

        $route->setRegex('/regex/');

        $this->assertEquals('regex', $route->getRegex());
    }

    public function testCanSetName()
    {
        $route = new Route();

        $this->assertEquals('', $route->getName());

        $route->setName('name');

        $this->assertEquals('name', $route->getName());
    }

    public function testCanSetDomain()
    {
        $route = new Route();

        $this->assertEquals('', $route->getDomain());

        $route->setDomain('domain');

        $this->assertEquals('domain', $route->getDomain());
    }

    public function testCanSetDomainRegex()
    {
        $route = new Route();

        $this->assertEquals('(?:([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}|localhost)?', $route->getDomainRegex());

        $route->setDomainRegex('domainregex');

        $this->assertEquals('domainregex', $route->getDomainRegex());

        $route->setDomainRegex('');

        $this->assertEquals('(?:([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}|localhost)?', $route->getDomainRegex());
    }

    public function testCanSetHandler()
    {
        $route = new Route();

        $this->assertEquals('', $route->getHandler());

        $route->setHandler('handler');

        $this->assertEquals('handler', $route->getHandler());
    }

    public function testCanSetMethods()
    {
        $route = new Route();

        $this->assertEmpty($route->getMethods());

        $route->setMethods(['GET', 'OPTIONS']);

        $this->assertSame(['GET', 'OPTIONS'], $route->getMethods());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testCannotSetInvalidMethods()
    {
        $route = new Route();

        $route->setMethods(['GET', 'OPTIONS', 'SOMETHING']);
    }

    public function testCanSetValues()
    {
        $route = new Route();

        $this->assertEquals([], $route->getValues());

        $route->setValues(['test' => '']);

        $this->assertSame(['test' => ''], $route->getValues());
    }

    public function testIsAllowedMethods()
    {
        $route = new Route();
        $route->setMethods(['GET', 'POST']);

        $this->assertFalse($route->isAllowedMethod('OPTIONS'));
        $this->assertFalse($route->isAllowedMethod('HEAD'));
        $this->assertTrue($route->isAllowedMethod('GET'));
        $this->assertTrue($route->isAllowedMethod('POST'));
        $this->assertFalse($route->isAllowedMethod('PUT'));
        $this->assertFalse($route->isAllowedMethod('PATCH'));
        $this->assertFalse($route->isAllowedMethod('DELETE'));
    }

    public function testSetStateFromExport()
    {
        $route = new Route();
        $route->setDomain('domain')
            ->setName('route')
            ->setPath('path');

        $route2 = Route::__set_state([
            'domain' => 'domain',
            'name' => 'route',
            'path' => 'path'
        ]);

        $this->assertSame($route->getPath(), $route2->getPath());
        $this->assertSame($route->getName(), $route2->getName());
        $this->assertSame($route->getDomain(), $route2->getDomain());
    }

    public function testMatchesMethod()
    {
        $route = new Route();
        $route->setPath('/path')
            ->setRegex('/path');

        $this->assertTrue($route->matches('localhost/path'));
        $this->assertTrue($route->matches('domain.com/path'));

        $route->setDomain('leemason.co.uk')
            ->setDomainRegex('leemason.co.uk');

        $this->assertFalse($route->matches('domain.com/path'));
        $this->assertTrue($route->matches('leemason.co.uk/path'));
    }

    public function testMergeParams()
    {
        $route = new Route();
        $route->setPath('/path')
            ->setName('name');

        $route->mergeParams([
            'name' => 'first',
            'path' => 'firstpath',
            'domain' => 'domain.com'
        ]);

        $this->assertEquals('firstpath/path', $route->getPath());
        $this->assertEquals('first.name', $route->getName());
        $this->assertEquals('domain.com', $route->getDomain());
    }

    public function testParseValues()
    {
        $route = new Route();
        $route->setPath('path/{with}/{args?}');

        $this->assertSame([], $route->getValues());

        $route->setRegex('path/(?<with>[^/]+)(?:/(?<args>[^/]+))?');

        $this->assertSame([
            'with' => '',
            'args' => ''
        ], $route->getValues());

        $route->setDomainRegex('(?<subdomain>[^/]+).domain.com');

        $this->assertSame([
            'subdomain' => '',
            'with' => '',
            'args' => ''
        ], $route->getValues());


    }

    public function testUrlGeneration()
    {
        $route = new Route();

        $route->setPath('thepath');

        $this->assertEquals('/thepath', $route->url());

        $route->setDomain('domain.com');

        $this->assertEquals('//domain.com/thepath', $route->url());
    }

    public function testUrlGenerationWithArguments()
    {
        $route = new Route();

        $route->setPath('thepath/{arg:word}/{arg2}');

        $this->assertEquals('/thepath/arg/arg2', $route->url(['arg' => 'arg', 'arg2' => 'arg2']));

        $route->setDomain('domain.com');

        $this->assertEquals('//domain.com/thepath/arg/arg2', $route->url(['arg' => 'arg', 'arg2' => 'arg2']));
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testUrlGenerationWithArgumentsFails()
    {
        $route = new Route();

        $route->setPath('thepath/{arg}/{arg2}');

        $this->assertEquals('/thepath/arg/arg2', $route->url());
    }

    public function testUrlGenerationWithOptionalArguments()
    {
        $route = new Route();

        $route->setPath('thepath/{arg}/{arg2?}');

        $this->assertEquals('/thepath/arg', $route->url(['arg' => 'arg']));

    }

    public function testUrlGenerationWithOptionalArgumentsFilled()
    {
        $route = new Route();

        $route->setPath('thepath/{arg}/{arg2?}');

        $this->assertEquals('/thepath/arg/arg2', $route->url(['arg' => 'arg', 'arg2' => 'arg2']));

    }

    public function testUrlGenerationWithMultipleOptionalArguments()
    {
        $route = new Route();

        $route->setPath('thepath/{arg?}/{arg2?}/{arg3?}');

        $this->assertEquals('/thepath', $route->url());

        $this->assertEquals('/thepath/arg', $route->url(['arg' => 'arg']));

        $this->assertEquals('/thepath/arg/arg2', $route->url(['arg' => 'arg', 'arg2' => 'arg2']));

        $this->assertEquals('/thepath/arg/arg2/arg3', $route->url(['arg' => 'arg', 'arg2' => 'arg2', 'arg3' => 'arg3']));

    }

    public function testUrlGenerationWithOptionalArgumentsAndDomain()
    {
        $route = new Route();

        $route->setPath('thepath/{arg}/{arg2?}');

        $route->setDomain('domain.com');

        $this->assertEquals('//domain.com/thepath/arg', $route->url(['arg' => 'arg']));

    }

    public function testUrlGenerationWithOptionalArgumentsFilledAndDomain()
    {
        $route = new Route();

        $route->setPath('thepath/{arg}/{arg2?}');

        $route->setDomain('domain.com');

        $this->assertEquals('//domain.com/thepath/arg/arg2', $route->url(['arg' =>'arg', 'arg2' => 'arg2']));
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testUrlGenerationWithOptionalArgumentsFails(){
        $route = new Route();

        $route->setPath('thepath/{arg}/{arg2?}');

        $this->assertEquals('/thepath/arg/arg2', $route->url());
    }

    /**
     * @expectedException     InvalidArgumentException
     */
    public function testUrlGenerationWithWrongArgumentsFails(){
        $route = new Route();

        $route->setPath('thepath');

        $this->assertEquals('/thepath', $route->url(['arg' =>'arg', 'arg2' => 'arg2']));
    }
}