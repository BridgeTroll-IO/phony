<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Invocable;

use Exception;

/**
 * An abstract base class for implementing wrapped invocables.
 *
 * @internal
 */
abstract class AbstractWrappedInvocable implements WrappedInvocableInterface
{
    /**
     * Construct a new wrapped invocable.
     *
     * @param callable|null $callback The callback.
     */
    public function __construct($callback = null)
    {
        if (null === $callback) {
            $callback = function () {};
        }

        $this->callback = $callback;
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
     * Invoke this object.
     *
     * @param mixed $arguments,... The arguments.
     *
     * @return mixed     The result of invocation.
     * @throws Exception If an error occurs.
     */
    public function invoke()
    {
        return $this->invokeWith(func_get_args());
    }

    /**
     * Invoke this object.
     *
     * @param mixed $arguments,... The arguments.
     *
     * @return mixed     The result of invocation.
     * @throws Exception If an error occurs.
     */
    public function __invoke()
    {
        return $this->invokeWith(func_get_args());
    }

    protected $callback;
}
