<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocation;

use Eloquent\Phony\Test\TestClassA;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;

class WrappedMethodTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->method = new ReflectionMethod('Eloquent\Phony\Test\TestClassA::testClassAMethodE');
        $this->instance = new TestClassA();
        $this->subject = new WrappedMethod($this->method, $this->instance);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('ReflectionMethod', $this->subject->method());
        $this->assertSame(
            $this->method->getDeclaringClass()->getName(),
            $this->subject->method()->getDeclaringClass()->getName()
        );
        $this->assertSame($this->method->getName(), $this->subject->method()->getName());
        $this->assertSame($this->instance, $this->subject->instance());
        $this->assertFalse($this->subject->isAnonymous());
        $this->assertSame(array($this->instance, 'testClassAMethodE'), $this->subject->callback());
        $this->assertNull($this->subject->id());
    }

    public function testConstructorWithStatic()
    {
        $this->method = new ReflectionMethod('Eloquent\Phony\Test\TestClassA::testClassAStaticMethodE');
        $this->instance = new TestClassA();
        $this->name = 'name';
        $this->subject = new WrappedMethod($this->method, $this->instance, $this->name);

        $this->assertInstanceOf('ReflectionMethod', $this->subject->method());
        $this->assertSame(
            $this->method->getDeclaringClass()->getName(),
            $this->subject->method()->getDeclaringClass()->getName()
        );
        $this->assertSame($this->method->getName(), $this->subject->method()->getName());
        $this->assertSame($this->instance, $this->subject->instance());
        $this->assertFalse($this->subject->isAnonymous());
        $this->assertSame(
            array('Eloquent\Phony\Test\TestClassA', 'testClassAStaticMethodE'),
            $this->subject->callback()
        );
        $this->assertNull($this->subject->id());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new WrappedMethod($this->method);

        $this->assertNull($this->subject->instance());
        $this->assertSame(array(null, $this->method->getName()), $this->subject->callback());
    }

    public function testInvokeMethodsNonStatic()
    {
        $subject = $this->subject;

        $this->assertSame('private ab', $subject('a', 'b'));
        $this->assertSame('private ab', $subject->invoke('a', 'b'));
        $this->assertSame('private ab', $subject->invokeWith(array('a', 'b')));
        $this->assertSame('private ', $subject->invokeWith());
    }

    public function testInvokeMethodsStatic()
    {
        $subject =
            new WrappedMethod(new ReflectionMethod('Eloquent\Phony\Test\TestClassA::testClassAStaticMethodE'));

        $this->assertSame('private ab', $subject('a', 'b'));
        $this->assertSame('private ab', $subject->invoke('a', 'b'));
        $this->assertSame('private ab', $subject->invokeWith(array('a', 'b')));
        $this->assertSame('private ', $subject->invokeWith());
    }

    public function testInvokeWithReferenceParameters()
    {
        $this->method = new ReflectionMethod('Eloquent\Phony\Test\TestClassA::testClassAMethodD');
        $this->subject = new WrappedMethod($this->method, $this->instance);
        $first = null;
        $second = null;
        $this->subject->invokeWith(array(&$first, &$second));

        $this->assertSame('first', $first);
        $this->assertSame('second', $second);
    }
}
