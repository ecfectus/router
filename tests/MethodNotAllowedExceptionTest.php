<?php
/**
 * Created by PhpStorm.
 * User: leemason
 * Date: 01/10/16
 * Time: 14:26
 */

namespace Ecfectus\Router\Tests;


use Ecfectus\Router\MethodNotAllowedException;
use PHPUnit\Framework\TestCase;

class MethodNotAllowedExceptionTest extends TestCase
{
    public function testCanGetMethods(){

        $e = new MethodNotAllowedException('', []);

        $this->assertSame([], $e->getMethods());

        $e = new MethodNotAllowedException('', ['GET', 'POST']);

        $this->assertSame(['GET', 'POST'], $e->getMethods());
    }

    public function testCanSetMethods(){

        $e = new MethodNotAllowedException('', []);

        $this->assertSame([], $e->getMethods());

        $e->setMethods(['GET', 'POST']);

        $this->assertSame(['GET', 'POST'], $e->getMethods());
    }

}