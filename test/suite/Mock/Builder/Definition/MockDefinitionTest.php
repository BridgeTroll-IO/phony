<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder\Definition;

use Eloquent\Phony\Feature\FeatureDetector;
use Eloquent\Phony\Mock\Builder\Definition\Method\CustomMethodDefinition;
use Eloquent\Phony\Mock\Builder\Definition\Method\MethodDefinitionCollection;
use Eloquent\Phony\Mock\Builder\Definition\Method\RealMethodDefinition;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionMethod;

class MockDefinitionTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->featureDetector = new FeatureDetector();

        $this->typeNames = array(
            'Eloquent\Phony\Test\TestClassB',
            'Eloquent\Phony\Test\TestInterfaceA',
            'Iterator',
            'Countable',
        );
        $this->typeNamesTraits = array(
            'Eloquent\Phony\Test\TestClassB',
            'Eloquent\Phony\Test\TestInterfaceA',
            'Iterator',
            'Countable',
            'Eloquent\Phony\Test\TestTraitA',
            'Eloquent\Phony\Test\TestTraitB',
        );
        $this->parentClassName = 'Eloquent\Phony\Test\TestClassB';
        $this->interfaceNames = array('Eloquent\Phony\Test\TestInterfaceA', 'Iterator', 'Countable');
        $this->traitNames = array(
            'Eloquent\Phony\Test\TestTraitA',
            'Eloquent\Phony\Test\TestTraitB',
        );

        $this->callbackA = function () {};
        $this->callbackB = function () {};
        $this->callbackC = function () {};
        $this->callbackD = function () {};
    }

    protected function setUpWith($typeNames)
    {
        $this->types = array();

        foreach ($typeNames as $typeName) {
            $this->types[$typeName] = new ReflectionClass($typeName);
        }

        $this->customMethods = array(
            'methodA' => $this->callbackA,
            'methodB' => $this->callbackB,
        );
        $this->customProperties = array('a' => 'b', 'c' => 'd');
        $this->customStaticMethods = array(
            'methodC' => $this->callbackC,
            'methodD' => $this->callbackD,
        );
        $this->customStaticProperties = array('e' => 'f', 'g' => 'h');
        $this->customConstants = array('i' => 'j', 'k' => 'l');
        $this->className = 'className';
        $this->id = 111;
        $this->subject = new MockDefinition(
            $this->types,
            $this->customMethods,
            $this->customProperties,
            $this->customStaticMethods,
            $this->customStaticProperties,
            $this->customConstants,
            $this->className,
            $this->id,
            $this->featureDetector
        );
    }

    public function testConstructor()
    {
        $this->setUpWith($this->typeNames);

        $this->assertSame($this->types, $this->subject->types());
        $this->assertSame($this->customMethods, $this->subject->customMethods());
        $this->assertSame($this->customProperties, $this->subject->customProperties());
        $this->assertSame($this->customStaticMethods, $this->subject->customStaticMethods());
        $this->assertSame($this->customStaticProperties, $this->subject->customStaticProperties());
        $this->assertSame($this->customConstants, $this->subject->customConstants());
        $this->assertSame($this->className, $this->subject->className());
        $this->assertSame($this->id, $this->subject->id());
        $this->assertSame($this->featureDetector, $this->subject->featureDetector());
        $this->assertSame($this->typeNames, $this->subject->typeNames());
        $this->assertSame($this->parentClassName, $this->subject->parentClassName());
        $this->assertSame($this->interfaceNames, $this->subject->interfaceNames());
        $this->assertSame(array(), $this->subject->traitNames());
    }

    public function testConstructorWithTraits()
    {
        if (!$this->featureDetector->isSupported('trait')) {
            $this->markTestSkipped('Requires traits.');
        }

        $this->setUpWith($this->typeNamesTraits);

        $this->assertSame($this->types, $this->subject->types());
        $this->assertSame($this->className, $this->subject->className());
        $this->assertSame($this->typeNamesTraits, $this->subject->typeNames());
        $this->assertSame($this->parentClassName, $this->subject->parentClassName());
        $this->assertSame($this->interfaceNames, $this->subject->interfaceNames());
        $this->assertSame($this->traitNames, $this->subject->traitNames());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new MockDefinition();

        $this->assertSame(array(), $this->subject->types());
        $this->assertSame(array(), $this->subject->customMethods());
        $this->assertSame(array(), $this->subject->customProperties());
        $this->assertSame(array(), $this->subject->customStaticMethods());
        $this->assertSame(array(), $this->subject->customStaticProperties());
        $this->assertSame(array(), $this->subject->customConstants());
        $this->assertRegExp('/^PhonyMock_[[:xdigit:]]{6}$/', $this->subject->className());
        $this->assertNull($this->subject->id());
        $this->assertSame(FeatureDetector::instance(), $this->subject->featureDetector());
        $this->assertSame(array(), $this->subject->typeNames());
        $this->assertNull($this->subject->parentClassName());
        $this->assertSame(array(), $this->subject->interfaceNames());
        $this->assertSame(array(), $this->subject->traitNames());
        $this->assertEquals(new MethodDefinitionCollection(), $this->subject->methods());
    }

    public function classNameData()
    {
        //                                      types                                    expected
        return array(
            'Anonymous'                => array(array(),                                 'PhonyMock_111'),
            'Extends class'            => array(array('stdClass'),                       'PhonyMock_stdClass_111'),
            'Extends namespaced class' => array(array('Eloquent\Phony\Test\TestClassB'), 'PhonyMock_TestClassB_111'),
            'Inherits interface'       => array(array('Iterator', 'Countable'),          'PhonyMock_Iterator_111'),
        );
    }

    /**
     * @dataProvider classNameData
     */
    public function testClassName($types, $expected)
    {
        $types = array_map(function ($type) { return new ReflectionClass($type); }, $types);
        $this->subject = new MockDefinition($types, null, null, null, null, null, null, 111);

        $this->assertSame($expected, $this->subject->className());
    }

    public function testClassNameWithTraits()
    {
        if (!$this->featureDetector->isSupported('trait')) {
            $this->markTestSkipped('Requires traits.');
        }

        $this->types = array(
            new ReflectionClass('Eloquent\Phony\Test\TestTraitA'),
            new ReflectionClass('Eloquent\Phony\Test\TestTraitB'),
        );
        $this->subject = new MockDefinition($this->types, null, null, null, null, null, null, 111);

        $this->assertSame('PhonyMock_TestTraitA_111', $this->subject->className());
    }

    public function testMethods()
    {
        $this->setUpWith($this->typeNames);

        $expected = new MethodDefinitionCollection(
            array(
                'count' => new RealMethodDefinition(new ReflectionMethod('Countable::count')),
                'current' => new RealMethodDefinition(new ReflectionMethod('Iterator::current')),
                'key' => new RealMethodDefinition(new ReflectionMethod('Iterator::key')),
                'methodA' => new CustomMethodDefinition(false, 'methodA', $this->callbackA),
                'methodB' => new CustomMethodDefinition(false, 'methodB', $this->callbackB),
                'methodC' => new CustomMethodDefinition(true, 'methodC', $this->callbackC),
                'methodD' => new CustomMethodDefinition(true, 'methodD', $this->callbackD),
                'next' => new RealMethodDefinition(new ReflectionMethod('Iterator::next')),
                'rewind' => new RealMethodDefinition(new ReflectionMethod('Iterator::rewind')),
                'testClassAMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodA')
                ),
                'testClassAMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodB')
                ),
                'testClassAMethodC' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodC')
                ),
                'testClassAMethodD' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodD')
                ),
                'testClassAStaticMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodA')
                ),
                'testClassAStaticMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodB')
                ),
                'testClassAStaticMethodC' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodC')
                ),
                'testClassAStaticMethodD' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodD')
                ),
                'testClassBMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBMethodA')
                ),
                'testClassBMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBMethodB')
                ),
                'testClassBStaticMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBStaticMethodA')
                ),
                'testClassBStaticMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBStaticMethodB')
                ),
                'valid' => new RealMethodDefinition(new ReflectionMethod('Iterator::valid')),
                '__call' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::__call')
                ),
                '__callStatic' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::__callStatic')
                ),
            )
        );
        $actual = $this->subject->methods();

        $this->assertEquals($expected, $actual);
        $this->assertSame($actual, $this->subject->methods());
    }

    public function testMethodsWithTraits()
    {
        if (!$this->featureDetector->isSupported('trait')) {
            $this->markTestSkipped('Requires traits.');
        }

        $this->setUpWith($this->typeNamesTraits);

        $expected = new MethodDefinitionCollection(
            array(
                'count' => new RealMethodDefinition(new ReflectionMethod('Countable::count')),
                'current' => new RealMethodDefinition(new ReflectionMethod('Iterator::current')),
                'key' => new RealMethodDefinition(new ReflectionMethod('Iterator::key')),
                'methodA' => new CustomMethodDefinition(false, 'methodA', $this->callbackA),
                'methodB' => new CustomMethodDefinition(false, 'methodB', $this->callbackB),
                'methodC' => new CustomMethodDefinition(true, 'methodC', $this->callbackC),
                'methodD' => new CustomMethodDefinition(true, 'methodD', $this->callbackD),
                'next' => new RealMethodDefinition(new ReflectionMethod('Iterator::next')),
                'rewind' => new RealMethodDefinition(new ReflectionMethod('Iterator::rewind')),
                'testClassAMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodA')
                ),
                'testClassAMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestTraitB::testClassAMethodB')
                ),
                'testClassAMethodC' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodC')
                ),
                'testClassAMethodD' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAMethodD')
                ),
                'testClassAStaticMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestTraitA::testClassAStaticMethodA')
                ),
                'testClassAStaticMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodB')
                ),
                'testClassAStaticMethodC' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodC')
                ),
                'testClassAStaticMethodD' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassAStaticMethodD')
                ),
                'testClassBMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBMethodA')
                ),
                'testClassBMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBMethodB')
                ),
                'testClassBStaticMethodA' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBStaticMethodA')
                ),
                'testClassBStaticMethodB' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::testClassBStaticMethodB')
                ),
                'valid' => new RealMethodDefinition(new ReflectionMethod('Iterator::valid')),
                '__call' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::__call')
                ),
                '__callStatic' => new RealMethodDefinition(
                    new ReflectionMethod('Eloquent\Phony\Test\TestClassB::__callStatic')
                ),
            )
        );
        $actual = $this->subject->methods();

        $this->assertEquals($expected, $actual);
        $this->assertSame($actual, $this->subject->methods());
    }
}