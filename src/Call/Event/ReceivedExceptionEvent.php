<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2017 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Call\Event;

use Throwable;

/**
 * Represents an exception received by a generator.
 */
class ReceivedExceptionEvent extends AbstractCallEvent implements IterableEvent
{
    /**
     * Construct a 'received exception' event.
     *
     * @param int       $sequenceNumber The sequence number.
     * @param float     $time           The time at which the event occurred, in seconds since the Unix epoch.
     * @param Throwable $exception      The received exception.
     */
    public function __construct($sequenceNumber, $time, Throwable $exception)
    {
        parent::__construct($sequenceNumber, $time);

        $this->exception = $exception;
    }

    /**
     * Get the received exception.
     *
     * @return Throwable The received exception.
     */
    public function exception()
    {
        return $this->exception;
    }

    private $exception;
}
