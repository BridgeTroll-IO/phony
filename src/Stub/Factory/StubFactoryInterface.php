<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Stub\Factory;

use Eloquent\Phony\Stub\StubInterface;

/**
 * The interface implemented by stub factories.
 */
interface StubFactoryInterface
{
    /**
     * Create a new stub.
     *
     * @param callable|null $callback  The callback, or null to create an unbound stub.
     * @param object|null   $thisValue The $this value.
     *
     * @return StubInterface The newly created stub.
     */
    public function create($callback = null, $thisValue = null);
}
