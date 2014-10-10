<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call\Event;

use Eloquent\Phony\Test\TestCallFactory;
use Exception;
use PHPUnit_Framework_TestCase;

class SentExceptionEventTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->sequenceNumber = 111;
        $this->time = 1.11;
        $this->exception = new Exception();
        $this->subject = new SentExceptionEvent($this->sequenceNumber, $this->time, $this->exception);

        $this->callFactory = new TestCallFactory();
    }

    public function testConstructor()
    {
        $this->assertSame($this->sequenceNumber, $this->subject->sequenceNumber());
        $this->assertSame($this->time, $this->subject->time());
        $this->assertSame($this->exception, $this->subject->exception());
        $this->assertNull($this->subject->call());
        $this->assertTrue($this->subject->hasEvents());
        $this->assertSame(array($this->subject), $this->subject->events());
        $this->assertSame($this->subject, $this->subject->firstEvent());
        $this->assertSame($this->subject, $this->subject->lastEvent());
    }

    public function testConstructorDefaults()
    {
        $this->subject = new SentExceptionEvent($this->sequenceNumber, $this->time);

        $this->assertEquals($this->exception, $this->subject->exception());
    }

    public function testSetCall()
    {
        $call = $this->callFactory->create();
        $this->subject->setCall($call);

        $this->assertSame($call, $this->subject->call());
    }
}
