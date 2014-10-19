<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Factory;

use Eloquent\Phony\Mock\Builder\MockBuilder;
use Eloquent\Phony\Mock\Generator\MockGenerator;
use Eloquent\Phony\Mock\Proxy\Factory\MockProxyFactory;
use Eloquent\Phony\Sequencer\Sequencer;
use Eloquent\Phony\Spy\Factory\SpyFactory;
use Eloquent\Phony\Stub\Factory\StubFactory;
use Eloquent\Phony\Test\TestMockGenerator;
use Mockery\Generator\Generator;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class MockFactoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->idSequencer = new Sequencer();
        $this->generator = new MockGenerator();
        $this->stubFactory = new StubFactory();
        $this->spyFactory = new SpyFactory();
        $this->subject = new MockFactory($this->idSequencer, $this->generator, $this->stubFactory, $this->spyFactory);

        $this->proxyFactory = new MockProxyFactory();
    }

    public function testConstructor()
    {
        $this->assertSame($this->idSequencer, $this->subject->idSequencer());
        $this->assertSame($this->generator, $this->subject->generator());
        $this->assertSame($this->stubFactory, $this->subject->stubFactory());
        $this->assertSame($this->spyFactory, $this->subject->spyFactory());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new MockFactory();

        $this->assertSame(Sequencer::sequence('mock-id'), $this->subject->idSequencer());
        $this->assertSame(MockGenerator::instance(), $this->subject->generator());
        $this->assertSame(StubFactory::instance(), $this->subject->stubFactory());
        $this->assertSame(SpyFactory::instance(), $this->subject->spyFactory());
    }

    public function testCreateMockClass()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassB',
            array(
                'static methodA' => function () {
                    return 'static custom ' . implode(func_get_args());
                },
                'methodB' => function () {
                    return 'custom ' . implode(func_get_args());
                },
            ),
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockClass'
        );
        $actual = $this->subject->createMockClass($builder);
        $protectedMethod = $actual->getMethod('testClassAStaticMethodC');
        $protectedMethod->setAccessible(true);

        $this->assertInstanceOf('ReflectionClass', $actual);
        $this->assertTrue($actual->implementsInterface('Eloquent\Phony\Mock\MockInterface'));
        $this->assertTrue($actual->isSubclassOf('Eloquent\Phony\Test\TestClassB'));
        $this->assertSame('ab', PhonyMockFactoryTestCreateMockClass::testClassAStaticMethodA('a', 'b'));
        $this->assertSame('protected ab', $protectedMethod->invoke(null, 'a', 'b'));
        $this->assertSame('static custom ab', PhonyMockFactoryTestCreateMockClass::methodA('a', 'b'));
    }

    public function testCreateMockClassFailureExists()
    {
        $builderA = new MockBuilder();
        $builderB = new MockBuilder(null, null, $builderA->build()->getName());

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\ClassExistsException');
        $this->subject->createMockClass($builderB);
    }

    public function testCreateMockClassFailureSyntax()
    {
        $this->subject = new MockFactory($this->idSequencer, new TestMockGenerator('{'));
        $builder = new MockBuilder();

        $this->setExpectedException('Eloquent\Phony\Mock\Exception\MockGenerationFailedException');
        $this->subject->createMockClass($builder);
    }

    public function testCreateMock()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassB',
            array(
                'static methodA' => function () {
                    return 'static custom ' . implode(func_get_args());
                },
                'methodB' => function () {
                    return 'custom ' . implode(func_get_args());
                },
            ),
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMock'
        );
        $actual = $this->subject->createMock($builder);
        $class = new ReflectionClass($actual);
        $idProperty = $class->getProperty('_mockId');
        $idProperty->setAccessible(true);
        $protectedMethod = $class->getMethod('testClassAMethodC');
        $protectedMethod->setAccessible(true);
        $protectedStaticMethod = $class->getMethod('testClassAStaticMethodC');
        $protectedStaticMethod->setAccessible(true);

        $this->assertInstanceOf('Eloquent\Phony\Mock\MockInterface', $actual);
        $this->assertInstanceOf('Eloquent\Phony\Test\TestClassB', $actual);
        $this->assertSame('0', $idProperty->getValue($actual));
        $this->assertSame('ab', $actual->testClassAMethodA('a', 'b'));
        $this->assertSame('protected ab', $protectedMethod->invoke($actual, 'a', 'b'));
        $this->assertSame('custom ab', $actual->methodB('a', 'b'));
        $this->assertSame('ab', PhonyMockFactoryTestCreateMock::testClassAStaticMethodA('a', 'b'));
        $this->assertSame('protected ab', $protectedStaticMethod->invoke(null, 'a', 'b'));
        $this->assertSame('static custom ab', PhonyMockFactoryTestCreateMock::methodA('a', 'b'));
    }

    public function testCreateMockWithConstructorArgumentsAndId()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassB',
            null,
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockWithConstructorArguments'
        );
        $actual = $this->subject->createMock($builder, array('a', 'b'), 'id');
        $class = new ReflectionClass($actual);
        $idProperty = $class->getProperty('_mockId');
        $idProperty->setAccessible(true);

        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
        $this->assertSame('id', $idProperty->getValue($actual));
    }

    public function testCreateMockWithConstructorArgumentsWithReferences()
    {
        $builder = new MockBuilder(
            'Eloquent\Phony\Test\TestClassA',
            null,
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockWithConstructorArgumentsWithReferences'
        );
        $a = 'a';
        $b = 'b';
        $actual = $this->subject->createMock($builder, array(&$a, &$b));

        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
        $this->assertSame('first', $a);
        $this->assertSame('second', $b);
    }

    public function testCreateMockWithOldConstructor()
    {
        $builder = new MockBuilder(
            'TestClassOldConstructor',
            null,
            __NAMESPACE__ . '\PhonyMockFactoryTestCreateMockWithOldConstructor'
        );
        $actual = $this->subject->createMock($builder, array('a', 'b'));

        $this->assertSame(array('a', 'b'), $actual->constructorArguments);
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
