<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2017 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Method;

use Eloquent\Phony\Invocation\AbstractWrappedInvocable;
use Eloquent\Phony\Mock\Handle\Handle;
use Eloquent\Phony\Mock\Handle\StaticHandle;
use Eloquent\Phony\Mock\Mock;
use ReflectionMethod;

/**
 * An abstract base class for implementing wrapped methods.
 */
abstract class AbstractWrappedMethod extends AbstractWrappedInvocable implements
    WrappedMethod
{
    /**
     * Construct a new wrapped method.
     *
     * @param ReflectionMethod $method The method.
     * @param Handle           $handle The handle.
     */
    public function __construct(ReflectionMethod $method, Handle $handle)
    {
        $this->method = $method;
        $this->handle = $handle;
        $this->name = $method->getName();

        if ($handle instanceof StaticHandle) {
            $this->mock = null;
            $callback = [
                $method->getDeclaringClass()->getName(),
                $this->name,
            ];
        } else {
            $this->mock = $handle->get();
            $callback = [$this->mock, $this->name];
        }

        parent::__construct($callback, '');
    }

    /**
     * Get the method.
     *
     * @return ReflectionMethod The method.
     */
    public function method(): ReflectionMethod
    {
        return $this->method;
    }

    /**
     * Get the name.
     *
     * @return string The name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Get the handle.
     *
     * @return Handle The handle.
     */
    public function handle(): Handle
    {
        return $this->handle;
    }

    /**
     * Get the mock.
     *
     * @return Mock|null The mock.
     */
    public function mock()
    {
        return $this->mock;
    }

    protected $method;
    protected $handle;
    protected $mock;
    protected $name;
}
