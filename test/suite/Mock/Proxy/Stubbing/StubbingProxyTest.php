<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Proxy\Stubbing;

use Eloquent\Phony\Matcher\WildcardMatcher;
use Eloquent\Phony\Mock\Builder\MockBuilder;
use Eloquent\Phony\Mock\Factory\MockFactory;
use Eloquent\Phony\Stub\Factory\StubVerifierFactory;
use PHPUnit_Framework_TestCase;

class StubbingProxyTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');
    }

    protected function setUpWith($className)
    {
        $this->mockBuilder = new MockBuilder($className);
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->get();
        $this->className = $this->class->getName();
        $stubsProperty = $this->class->getProperty('_stubs');
        $stubsProperty->setAccessible(true);
        $this->stubs = $this->expectedStubs($stubsProperty->getValue($this->mock));
        $this->isFullMockProperty = $this->class->getProperty('_isFullMock');
        $this->isFullMockProperty->setAccessible(true);
        if ($this->class->hasMethod('__call')) {
            $this->magicStubsProperty = $this->class->getProperty('_magicStubs');
            $this->magicStubsProperty->setAccessible(true);
        } else {
            $this->magicStubsProperty = null;
        }
        $this->mockFactory = new MockFactory();
        $this->stubVerifierFactory = new StubVerifierFactory();
        $this->wildcardMatcher = new WildcardMatcher();
        $this->subject = new StubbingProxy(
            $this->mock,
            $this->class,
            $this->stubs,
            $this->isFullMockProperty,
            $this->magicStubsProperty,
            $this->mockFactory,
            $this->stubVerifierFactory,
            $this->wildcardMatcher
        );
    }

    public function testConstructor()
    {
        $this->assertSame($this->mock, $this->subject->mock());
        $this->assertSame($this->class, $this->subject->reflector());
        $this->assertSame($this->className, $this->subject->className());
        $this->assertSame($this->stubs, $this->subject->stubs());
        $this->assertSame($this->isFullMockProperty, $this->subject->isFullMockProperty());
        $this->assertSame($this->magicStubsProperty, $this->subject->magicStubsProperty());
        $this->assertSame($this->mockFactory, $this->subject->mockFactory());
        $this->assertSame($this->stubVerifierFactory, $this->subject->stubVerifierFactory());
        $this->assertSame($this->wildcardMatcher, $this->subject->wildcardMatcher());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new StubbingProxy($this->mock, $this->class, $this->stubs, $this->isFullMockProperty);

        $this->assertNull($this->subject->magicStubsProperty());
        $this->assertSame(MockFactory::instance(), $this->subject->mockFactory());
        $this->assertSame(StubVerifierFactory::instance(), $this->subject->stubVerifierFactory());
        $this->assertSame(WildcardMatcher::instance(), $this->subject->wildcardMatcher());
    }

    public function testFull()
    {
        $className = $this->className;

        $this->assertSame($this->subject, $this->subject->full());
        $this->assertNull($this->mock->testClassAMethodA());
        $this->assertNull($this->mock->testClassAMethodB('a', 'b'));
        $this->assertTrue($this->isFullMockProperty->getValue($this->mock));
    }

    public function testMagicStubs()
    {
        $this->subject->nonexistentA;
        $this->subject->nonexistentB;

        $this->assertSame(array('nonexistentA', 'nonexistentB'), array_keys($this->subject->magicStubs()));
    }

    public function testStubMethods()
    {
        $this->assertSame($this->stubs['testClassAMethodA'], $this->subject->stub('testClassAMethodA')->spy());
        $this->assertSame($this->stubs['testClassAMethodA'], $this->subject->testClassAMethodA->spy());
        $this->assertSame(
            $this->stubs['testClassAMethodA'],
            $this->subject->testClassAMethodA('a', 'b')->returns('x')->spy()
        );
        $this->assertSame('x', $this->mock->testClassAMethodA('a', 'b'));
        $this->assertSame('cd', $this->mock->testClassAMethodA('c', 'd'));
    }

    public function testStubFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\UndefinedMethodStubException');
        $this->subject->stub('nonexistent');
    }

    public function testMagicPropertyFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->setExpectedException('Eloquent\Phony\Mock\Proxy\Exception\UndefinedPropertyException');
        $this->subject->nonexistent;
    }

    public function testMagicCallFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->setExpectedException('Eloquent\Phony\Mock\Proxy\Exception\UndefinedMethodException');
        $this->subject->nonexistent();
    }

    protected function expectedStubs(array $stubs)
    {
        foreach ($stubs as $name => $stub) {
            $stubs[$name] = StubVerifierFactory::instance()->create($stub->callback(), $stub);
        }

        return $stubs;
    }
}
