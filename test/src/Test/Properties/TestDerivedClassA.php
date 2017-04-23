<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2017 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Test\Properties;

class TestDerivedClassA extends TestBaseClass
{
    public function __construct(
        $publicValue = null,
        $privateValue = null,
        $protectedValue = null
    ) {
        if (null !== $publicValue) {
            $this->derivedPublic = $publicValue;
        }
        if (null !== $privateValue) {
            $this->derivedPrivate = $privateValue;
        }
        if (null !== $protectedValue) {
            $this->derivedProtected = $protectedValue;
        }
    }

    public $derivedPublic = '<derived-public>';
    private $derivedPrivate = '<derived-private>';
    private $basePrivate = '<derived-base-private>';
    protected $derivedProtected = '<derived-protected>';
}
