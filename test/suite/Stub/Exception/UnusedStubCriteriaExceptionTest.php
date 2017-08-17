<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2017 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Exception;

use Eloquent\Phony\Matcher\MatcherFactory;
use PHPUnit\Framework\TestCase;

class UnusedStubCriteriaExceptionTest extends TestCase
{
    public function testException()
    {
        $matcherFactory = MatcherFactory::instance();
        $criteria = [$matcherFactory->equalTo('a'), $matcherFactory->equalTo('b')];
        $exception = new UnusedStubCriteriaException($criteria);

        $this->assertSame($criteria, $exception->criteria());
        $this->assertSame(
            'Stub criteria \'"a", "b"\' were never used. Check for incomplete stub rules.',
            $exception->getMessage()
        );
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
