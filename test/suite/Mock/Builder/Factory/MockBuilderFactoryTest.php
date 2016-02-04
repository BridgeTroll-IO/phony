<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder\Factory;

use Eloquent\Phony\Call\Argument\Arguments;
use Eloquent\Phony\Mock\Builder\MockBuilder;
use Eloquent\Phony\Mock\Factory\MockFactory;
use Eloquent\Phony\Mock\Handle\Factory\HandleFactory;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class MockBuilderFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->mockFactory = new MockFactory();
        $this->handleFactory = new HandleFactory();
        $this->subject = new MockBuilderFactory($this->mockFactory, $this->handleFactory);
    }

    public function testConstructor()
    {
        $this->assertSame($this->mockFactory, $this->subject->mockFactory());
        $this->assertSame($this->handleFactory, $this->subject->handleFactory());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new MockBuilderFactory();

        $this->assertSame(MockFactory::instance(), $this->subject->mockFactory());
        $this->assertSame(HandleFactory::instance(), $this->subject->handleFactory());
    }

    public function testCreate()
    {
        $types = array('Eloquent\Phony\Test\TestInterfaceA', 'Eloquent\Phony\Test\TestInterfaceB');
        $actual = $this->subject->create($types);
        $expected = new MockBuilder($types, $this->mockFactory, $this->handleFactory);

        $this->assertEquals($expected, $actual);
        $this->assertSame($this->mockFactory, $actual->factory());
        $this->assertSame($this->handleFactory, $actual->handleFactory());
    }

    public function testCreatePartialMock()
    {
        $types = array('Eloquent\Phony\Test\TestClassB', 'Countable');
        $arguments = new Arguments(array('a', 'b'));
        $actual = $this->subject->createPartialMock($types, $arguments);

        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Test\TestClassB', $actual);
        $this->assertInstanceOf('Countable', $actual);
        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
        $this->assertSame('ab', $actual->testClassAMethodA('a', 'b'));
    }

    public function testCreatePartialMockWithNullArguments()
    {
        $types = array('Eloquent\Phony\Test\TestClassB', 'Countable');
        $arguments = null;
        $actual = $this->subject->createPartialMock($types, $arguments);

        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Test\TestClassB', $actual);
        $this->assertInstanceOf('Countable', $actual);
        $this->assertNull($actual->constructorArguments);
        $this->assertSame('ab', $actual->testClassAMethodA('a', 'b'));
    }

    public function testCreatePartialMockWithOmittedArguments()
    {
        $types = array('Eloquent\Phony\Test\TestClassB', 'Countable');
        $actual = $this->subject->createPartialMock($types);

        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Test\TestClassB', $actual);
        $this->assertInstanceOf('Countable', $actual);
        $this->assertEquals(array(), $actual->constructorArguments);
        $this->assertSame('ab', $actual->testClassAMethodA('a', 'b'));
    }

    public function testCreatePartialMockDefaults()
    {
        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $this->subject->createPartialMock());
    }

    public function testCreateFullMock()
    {
        $types = array('Eloquent\Phony\Test\TestClassB', 'Countable');
        $actual = $this->subject->createFullMock($types);

        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Test\TestClassB', $actual);
        $this->assertInstanceOf('Countable', $actual);
        $this->assertNull($actual->constructorArguments);
        $this->assertNull($actual->testClassAMethodA('a', 'b'));
    }

    public function testInstance()
    {
        $class = get_class($this->subject);
        $reflector = new ReflectionClass($class);
        $property = $reflector->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
        $instance = $class::instance();

        $this->assertInstanceOf($class, $instance);
        $this->assertSame($instance, $class::instance());
    }
}
