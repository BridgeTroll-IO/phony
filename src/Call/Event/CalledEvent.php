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

use Eloquent\Phony\Call\Exception\UndefinedArgumentException;

/**
 * Represents the start of a call.
 *
 * @internal
 */
class CalledEvent extends AbstractCallEvent implements CalledEventInterface
{
    /**
     * Construct a new 'called' event.
     *
     * @param integer                   $sequenceNumber The sequence number.
     * @param float                     $time           The time at which the event occurred, in seconds since the Unix epoch.
     * @param callable|null             $callback       The callback.
     * @param array<integer,mixed>|null $arguments      The arguments.
     */
    public function __construct(
        $sequenceNumber,
        $time,
        $callback = null,
        array $arguments = null
    ) {
        if (null === $callback) {
            $callback = function () {};
        }
        if (null === $arguments) {
            $arguments = array();
        }

        parent::__construct($sequenceNumber, $time);

        $this->callback = $callback;
        $this->arguments = $arguments;
    }

    /**
     * Get the callback.
     *
     * @return callable The callback.
     */
    public function callback()
    {
        return $this->callback;
    }

    /**
     * Get the received arguments.
     *
     * @return array<integer,mixed> The received arguments.
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * Get an argument by index.
     *
     * @param integer|null $index The index, or null for the first argument.
     *
     * @return mixed                      The argument.
     * @throws UndefinedArgumentException If the requested argument is undefined.
     */
    public function argument($index = null)
    {
        if (null === $index) {
            $index = 0;
        }

        if (array_key_exists($index, $this->arguments)) {
            return $this->arguments[$index];
        }

        throw new UndefinedArgumentException($index);
    }

    private $callback;
    private $arguments;
}
