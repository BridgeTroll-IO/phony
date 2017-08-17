<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2017 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Handle;

use Eloquent\Phony\Assertion\AssertionRenderer;
use Eloquent\Phony\Assertion\ExceptionAssertionRecorder;
use Eloquent\Phony\Call\CallVerifierFactory;
use Eloquent\Phony\Difference\DifferenceEngine;
use Eloquent\Phony\Event\EventSequence;
use Eloquent\Phony\Exporter\InlineExporter;
use Eloquent\Phony\Hook\FunctionHookManager;
use Eloquent\Phony\Invocation\InvocableInspector;
use Eloquent\Phony\Invocation\Invoker;
use Eloquent\Phony\Matcher\MatcherFactory;
use Eloquent\Phony\Matcher\MatcherVerifier;
use Eloquent\Phony\Mock\Builder\MockBuilderFactory;
use Eloquent\Phony\Reflection\FeatureDetector;
use Eloquent\Phony\Sequencer\Sequencer;
use Eloquent\Phony\Spy\SpyFactory;
use Eloquent\Phony\Stub\Answer\Builder\GeneratorAnswerBuilderFactory;
use Eloquent\Phony\Stub\EmptyValueFactory;
use Eloquent\Phony\Stub\StubFactory;
use Eloquent\Phony\Stub\StubVerifierFactory;
use Eloquent\Phony\Test\TestClassH;
use Eloquent\Phony\Verification\GeneratorVerifierFactory;
use Eloquent\Phony\Verification\IterableVerifierFactory;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class InstanceHandleTest extends TestCase
{
    protected function setUp()
    {
        $this->state = (object) [
            'stubs' => (object) [],
            'defaultAnswerCallback' => 'Eloquent\Phony\Stub\StubData::returnsEmptyAnswerCallback',
            'isRecording' => true,
            'label' => 'label',
        ];
        $this->stubFactory = StubFactory::instance();
        $this->objectSequencer = new Sequencer();
        $this->invocableInspector = new InvocableInspector();
        $this->exporter = new InlineExporter(1, $this->objectSequencer, $this->invocableInspector);
        $this->matcherVerifier = new MatcherVerifier();
        $this->featureDetector = FeatureDetector::instance();
        $this->differenceEngine = new DifferenceEngine($this->featureDetector);
        $this->differenceEngine->setUseColor(false);
        $this->assertionRenderer = new AssertionRenderer(
            $this->matcherVerifier,
            $this->exporter,
            $this->differenceEngine,
            $this->featureDetector
        );
        $this->assertionRenderer->setUseColor(false);
        $this->callVerifierFactory = CallVerifierFactory::instance();
        $this->assertionRecorder = ExceptionAssertionRecorder::instance();
        $this->assertionRecorder->setCallVerifierFactory($this->callVerifierFactory);
        $this->stubVerifierFactory = new StubVerifierFactory(
            $this->stubFactory,
            SpyFactory::instance(),
            MatcherFactory::instance(),
            $this->matcherVerifier,
            GeneratorVerifierFactory::instance(),
            IterableVerifierFactory::instance(),
            $this->callVerifierFactory,
            $this->assertionRecorder,
            $this->assertionRenderer,
            GeneratorAnswerBuilderFactory::instance(),
            FunctionHookManager::instance()
        );
        $this->emptyValueFactory = EmptyValueFactory::instance();
        $this->mockBuilderFactory = MockBuilderFactory::instance();
        $this->emptyValueFactory->setStubVerifierFactory($this->stubVerifierFactory);
        $this->emptyValueFactory->setMockBuilderFactory($this->mockBuilderFactory);
        $this->invoker = new Invoker();

        $this->featureDetector = FeatureDetector::instance();
    }

    protected function setUpWith($className, $mockClassName = null)
    {
        $this->mockBuilder = $this->mockBuilderFactory->create($className);
        $this->mockBuilder->named($mockClassName);
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partial();
        $this->subject = new InstanceHandle(
            $this->mock,
            $this->state,
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->emptyValueFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );

        $this->className = $this->class->getName();

        $handleProperty = $this->class->getProperty('_handle');
        $handleProperty->setAccessible(true);
        $handleProperty->setValue($this->mock, $this->subject);
    }

    public function testSetLabel()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertSame($this->subject, $this->subject->setLabel(null));
        $this->assertNull($this->subject->label());
        $this->assertSame($this->subject, $this->subject->setLabel($this->state->label));
        $this->assertSame($this->state->label, $this->subject->label());
    }

    public function testFull()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');

        $this->assertSame($this->subject, $this->subject->full());
        $this->assertNull($this->mock->testClassAMethodA());
        $this->assertNull($this->mock->testClassAMethodB('a', 'b'));
    }

    public function testPartial()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');

        $this->assertSame($this->subject, $this->subject->partial());
        $this->assertSame('', $this->mock->testClassAMethodA());
        $this->assertSame('ab', $this->mock->testClassAMethodB('a', 'b'));
    }

    public function testProxy()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $protectedMethod = new ReflectionMethod($this->mock, 'testClassAMethodC');
        $protectedMethod->setAccessible(true);
        $privateMethod = new ReflectionMethod($this->mock, 'testClassAMethodE');
        $privateMethod->setAccessible(true);

        $this->assertSame($this->subject, $this->subject->proxy(new TestClassH()));
        $this->assertSame('final ab', $this->mock->testClassAMethodB('a', 'b'));
        $this->assertSame('final protected ab', $protectedMethod->invoke($this->mock, 'a', 'b'));
        $this->assertSame('private ab', $privateMethod->invoke($this->mock, 'a', 'b'));
    }

    public function testSetDefaultAnswerCallback()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $callbackA = function () {};
        $callbackB = function () {};

        $this->assertSame($this->subject, $this->subject->setDefaultAnswerCallback($callbackA));
        $this->assertSame($callbackA, $this->subject->defaultAnswerCallback());
        $this->assertSame($this->subject, $this->subject->setDefaultAnswerCallback($callbackB));
        $this->assertSame($callbackB, $this->subject->defaultAnswerCallback());
    }

    public function testStub()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $actual = $this->subject->stub('testClassAMethodA');

        $this->assertInstanceOf('Eloquent\Phony\Stub\StubVerifier', $actual);
        $this->assertSame($actual, $this->subject->stub('testClassAMethodA'));
        $this->assertSame($actual, $this->subject->state()->stubs->testclassamethoda);
    }

    public function testStubWithMagic()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');
        $actual = $this->subject->stub('nonexistent');

        $this->assertInstanceOf('Eloquent\Phony\Stub\StubVerifier', $actual);
        $this->assertSame($actual, $this->subject->stub('nonexistent'));
        $this->assertSame($actual, $this->subject->state()->stubs->nonexistent);
    }

    public function testStubFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->expectException('Eloquent\Phony\Mock\Exception\UndefinedMethodStubException');
        $this->subject->stub('nonexistent');
    }

    public function testMagicProperty()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $actual = $this->subject->testClassAMethodA;

        $this->assertInstanceOf('Eloquent\Phony\Stub\StubVerifier', $actual);
        $this->assertSame($actual, $this->subject->testClassAMethodA);
        $this->assertSame($actual, $this->subject->state()->stubs->testclassamethoda);
    }

    public function testMagicPropertyFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->expectException('Eloquent\Phony\Mock\Exception\UndefinedMethodStubException');
        $this->subject->nonexistent;
    }

    public function testSpy()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $actual = $this->subject->spy('testClassAMethodA');

        $this->assertInstanceOf('Eloquent\Phony\Spy\SpyData', $actual);
        $this->assertSame($actual, $this->subject->spy('testClassAMethodA'));
        $this->assertSame($actual, $this->subject->state()->stubs->testclassamethoda->spy());
    }

    public function testCheckNoInteraction()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertTrue((bool) $this->subject->checkNoInteraction());

        $this->mock->testClassAMethodA();

        $this->assertFalse((bool) $this->subject->checkNoInteraction());
    }

    public function testNoInteraction()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');

        $this->assertEquals(new EventSequence([], $this->callVerifierFactory), $this->subject->noInteraction());
    }

    public function testNoInteractionFailure()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA', 'PhonyMockStubbingNoInteraction');
        $this->mock->testClassAMethodA('a', 'b');
        $this->mock->testClassAMethodB('c', 'd');
        $this->mock->testClassAMethodA('e', 'f');

        $this->expectException('Eloquent\Phony\Assertion\Exception\AssertionException');
        $this->subject->noInteraction();
    }

    public function testConstruct()
    {
        $this->mockBuilder = $this->mockBuilderFactory->create('Eloquent\Phony\Test\TestClassB');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partialWith(null);
        $this->subject = new InstanceHandle(
            $this->mock,
            $this->state,
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->emptyValueFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );

        $this->assertNull($this->mock->constructorArguments);
        $this->assertSame($this->subject, $this->subject->construct('a', 'b'));
        $this->assertSame(['a', 'b'], $this->mock->constructorArguments);
    }

    public function testConstructWith()
    {
        $this->mockBuilder = $this->mockBuilderFactory->create('Eloquent\Phony\Test\TestClassB');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partialWith(null);
        $this->subject = new InstanceHandle(
            $this->mock,
            $this->state,
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->emptyValueFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );

        $this->assertNull($this->mock->constructorArguments);
        $this->assertSame($this->subject, $this->subject->constructWith(['a', 'b']));
        $this->assertSame(['a', 'b'], $this->mock->constructorArguments);
    }

    public function testConstructWithWithReferenceParameters()
    {
        $this->mockBuilder = $this->mockBuilderFactory->create('Eloquent\Phony\Test\TestClassA');
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partialWith(null);
        $this->subject = new InstanceHandle(
            $this->mock,
            $this->state,
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->emptyValueFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );
        $a = 'a';
        $b = 'b';

        $this->assertNull($this->mock->constructorArguments);
        $this->assertSame($this->subject, $this->subject->constructWith([&$a, &$b]));
        $this->assertSame('first', $a);
        $this->assertSame('second', $b);
    }

    public function testStubbingWithParentMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassA');
        $this->subject->partial();
        $this->subject->testClassAMethodA->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->testClassAMethodA('a', 'b'));
        $this->assertSame('cd', $this->mock->testClassAMethodA('c', 'd'));
    }

    public function testStubbingWithTraitMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestTraitA');
        $this->subject->partial();
        $this->subject->testClassAMethodB->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->testClassAMethodB('a', 'b'));
        $this->assertSame('cd', $this->mock->testClassAMethodB('c', 'd'));
    }

    public function testStubbingWithMagicMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassB');
        $this->subject->partial();
        $this->subject->nonexistent->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->nonexistent('a', 'b'));
        $this->assertSame('magic nonexistent cd', $this->mock->nonexistent('c', 'd'));
    }

    public function testStubbingWithNoParentMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceA');
        $this->subject->partial();
        $this->subject->testClassAMethodA->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->testClassAMethodA('a', 'b'));
        $this->assertNull($this->mock->testClassAMethodA('c', 'd'));
    }

    public function testStubbingFailureWithFinalMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestClassF');
        $this->subject->partial();

        $this->expectException('Eloquent\Phony\Mock\Exception\FinalMethodStubException');
        $this->subject->testClassFMethodA;
    }

    public function testStubbingWithTraitFinalMethod()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestTraitG');
        $this->subject->partial();
        $this->subject->testTraitGMethodA->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->testTraitGMethodA('a', 'b'));
        $this->assertSame('cd', $this->mock->testTraitGMethodA('c', 'd'));
    }

    public function testStubbingWithCustomMethod()
    {
        $this->mockBuilder = $this->mockBuilderFactory->create(
            [
                'methodA' => function () {
                    return implode(func_get_args());
                },
            ]
        );
        $this->class = $this->mockBuilder->build(true);
        $this->mock = $this->mockBuilder->partial();
        $this->subject = new InstanceHandle(
            $this->mock,
            $this->state,
            $this->stubFactory,
            $this->stubVerifierFactory,
            $this->emptyValueFactory,
            $this->assertionRenderer,
            $this->assertionRecorder,
            $this->invoker
        );
        $handleProperty = $this->class->getProperty('_handle');
        $handleProperty->setAccessible(true);
        $handleProperty->setValue($this->mock, $this->subject);
        $this->subject->partial();
        $this->subject->methodA->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->methodA('a', 'b'));
        $this->assertSame('cd', $this->mock->methodA('c', 'd'));
    }

    public function testStubbingWithUncallableMethodWithReturnType()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceWithReturnType');
        $this->subject->partial();
        $this->subject->scalarType->with('a', 'b')->returns(111);

        $this->assertSame(111, $this->mock->scalarType('a', 'b'));
        $this->assertSame(0, $this->mock->scalarType('c', 'd'));
    }

    public function testStubbingWithUncallableMagicMethodWithReturnType()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceWithReturnType');
        $this->subject->partial();
        $this->subject->nonexistent->with('a', 'b')->returns('x');

        $this->assertSame('x', $this->mock->nonexistent('a', 'b'));
        $this->assertSame('', $this->mock->nonexistent('c', 'd'));
    }

    public function testStopRecording()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceA');

        $this->mock->testClassAMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->stopRecording());

        $this->mock->testClassAMethodB('a', 'b');

        $this->subject->testClassAMethodA->called();
        $this->subject->testClassAMethodB->never()->called();
    }

    public function testStartRecording()
    {
        $this->setUpWith('Eloquent\Phony\Test\TestInterfaceA');

        $this->mock->testClassAMethodA('a', 'b');

        $this->assertSame($this->subject, $this->subject->stopRecording());
        $this->assertSame($this->subject, $this->subject->startRecording());

        $this->mock->testClassAMethodB('a', 'b');

        $this->subject->testClassAMethodA->called();
        $this->subject->testClassAMethodB->called();
    }
}
