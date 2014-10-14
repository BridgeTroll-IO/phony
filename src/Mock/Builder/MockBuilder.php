<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder;

use Closure;
use Eloquent\Phony\Mock\Builder\Exception\FinalClassException;
use Eloquent\Phony\Mock\Builder\Exception\FinalizedMockException;
use Eloquent\Phony\Mock\Builder\Exception\InvalidClassNameException;
use Eloquent\Phony\Mock\Builder\Exception\InvalidTypeException;
use Eloquent\Phony\Mock\Builder\Exception\MultipleInheritanceException;
use ReflectionClass;
use ReflectionException;

/**
 * Builds mock classes.
 *
 * @internal
 */
class MockBuilder implements MockBuilderInterface
{
    /**
     * The regular expression used to validate class names.
     */
    const CLASS_NAME_PATTERN = '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/S';

    /**
     * Construct a new mock builder.
     *
     * @param array<string|object>|string|object|null $types     Types to add.
     * @param string|null                             $className The class name.
     * @param integer|null                            $id        The identifier.
     *
     * @throws MockBuilderExceptionInterface If invalid input is supplied.
     */
    public function __construct(
        $types = null,
        $className = null,
        $id = null
    ) {
        $this->types = array();
        $this->reflectors = array();
        $this->methods = array();
        $this->staticMethods = array();
        $this->properties = array();
        $this->staticProperties = array();
        $this->id = $id;
        $this->isFinalized = false;

        $reflectorReflector = new ReflectionClass('ReflectionClass');
        $this->isTraitSupported = $reflectorReflector->hasMethod('isTrait');

        if (null === $types) {
            $this->normalize();
        } else {
            $this->like($types);
        }

        $this->named($className);
    }

    /**
     * Add classes, interfaces, or traits.
     *
     * @param string|object|array<string|object> $type      A type, or types to add.
     * @param string|object|array<string|object> $types,... Additional types to add.
     *
     * @return MockBuilderInterface          This builder.
     * @throws MockBuilderExceptionInterface If invalid input is supplied, or this builder is already finalized.
     */
    public function like($type)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        $types = array();

        foreach (func_get_args() as $type) {
            if (is_array($type)) {
                $types = array_merge($types, $type);
            } else {
                $types[] = $type;
            }
        }

        $toAdd = array();

        foreach ($types as $type) {
            if (is_object($type)) {
                if ($type instanceof MockBuilderInterface) {
                    $toAdd = array_merge($toAdd, $type->types());
                } else {
                    $toAdd[] = get_class($type);
                }
            } elseif (is_string($type)) {
                $toAdd[] = $type;
            } else {
                throw new InvalidTypeException($type);
            }
        }

        $this->normalize($toAdd);

        return $this;
    }

    /**
     * Add custom methods and properties via a prototype definition.
     *
     * @param array|object $definition The definition.
     *
     * @return MockBuilderInterface This builder.
     */
    public function prototype($definition)
    {
        if (is_object($definition)) {
            $definition = get_object_vars($definition);
        }

        foreach ($definition as $name => $value) {
            $nameParts = explode(' ', $name);
            $name = array_pop($nameParts);
            $isStatic = in_array('static', $nameParts);

            if ($value instanceof Closure) {
                if ($isStatic) {
                    $this->addStaticMethod($name, $value);
                } else {
                    $this->addMethod($name, $value);
                }
            } else {
                if ($isStatic) {
                    $this->addStaticProperty($name, $value);
                } else {
                    $this->addProperty($name, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Add a custom method.
     *
     * @param string        $name     The name.
     * @param callable|null $callback The callback.
     *
     * @return MockBuilderInterface This builder.
     */
    public function addMethod($name, $callback = null)
    {
        $this->methods[$name] = $callback;

        return $this;
    }

    /**
     * Add a custom static method.
     *
     * @param string        $name     The name.
     * @param callable|null $callback The callback.
     *
     * @return MockBuilderInterface This builder.
     */
    public function addStaticMethod($name, $callback = null)
    {
        $this->staticMethods[$name] = $callback;

        return $this;
    }

    /**
     * Add a custom property.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return MockBuilderInterface This builder.
     */
    public function addProperty($name, $value = null)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Add a custom static property.
     *
     * @param string $name  The name.
     * @param mixed  $value The value.
     *
     * @return MockBuilderInterface This builder.
     */
    public function addStaticProperty($name, $value = null)
    {
        $this->staticProperties[$name] = $value;

        return $this;
    }

    /**
     * Set the class name.
     *
     * @param string $className|null The class name, or null to use a generated name.
     *
     * @return MockBuilderInterface          This builder.
     * @throws MockBuilderExceptionInterface If this builder is already finalized.
     */
    public function named($className)
    {
        if ($this->isFinalized) {
            throw new FinalizedMockException();
        }

        if (null !== $className) {
            if (!preg_match(static::CLASS_NAME_PATTERN, $className)) {
                throw new InvalidClassNameException($className);
            }
        }

        $this->className = $className;

        return $this;
    }

    /**
     * Finalize the mock builder, generate the mock class, and return a new
     * instance.
     *
     * @param boolean|null $createNew True if a new instance should be created.
     *
     * @return object The newly created mock instance.
     */
    public function get($createNew = null)
    {
        if (null === $createNew) {
            $createNew = false;
        }

        $this->build();

        return (object) array();
    }

    /**
     * Finalize the mock builder, and generate the mock class.
     *
     * If the mock class is already built, this method does nothing.
     *
     * @return MockBuilderInterface This builder.
     */
    public function build()
    {
        return $this->finalize();
    }

    /**
     * Finalize the mock builder.
     *
     * @return MockBuilderInterface This builder.
     */
    public function finalize()
    {
        $this->isFinalized = true;

        return $this;
    }

    /**
     * Get the identifier.
     *
     * @return integer|null The identifier.
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the class name.
     *
     * @return string The class name.
     */
    public function className()
    {
        if (null !== $this->className) {
            return $this->className;
        }

        return $this->generatedClassName;
    }

    /**
     * Get the parent class name.
     *
     * @return string|null The parent class name, or null if the mock will not extend a class.
     */
    public function parentClassName()
    {
        return $this->parentClassName;
    }

    /**
     * Get the interface names.
     *
     * @return array<string> The interface names.
     */
    public function interfaceNames()
    {
        return $this->interfaceNames;
    }

    /**
     * Get the trait names.
     *
     * @return array<string> The trait names.
     */
    public function traitNames()
    {
        return $this->traitNames;
    }

    /**
     * Get the types.
     *
     * @return array<string> The types.
     */
    public function types()
    {
        return $this->types;
    }

    /**
     * Get the custom methods.
     *
     * @return array<string,callable|null> The custom methods.
     */
    public function methods()
    {
        return $this->methods;
    }

    /**
     * Get the custom static methods.
     *
     * @return array<string,callable|null> The custom static methods.
     */
    public function staticMethods()
    {
        return $this->staticMethods;
    }

    /**
     * Get the custom properties.
     *
     * @return array<string,mixed> The custom properties.
     */
    public function properties()
    {
        return $this->properties;
    }

    /**
     * Get the custom static properties.
     *
     * @return array<string,mixed> The custom static properties.
     */
    public function staticProperties()
    {
        return $this->staticProperties;
    }

    /**
     * Returns true if this builder is finalized.
     *
     * @return boolean True if finalized.
     */
    public function isFinalized()
    {
        return $this->isFinalized;
    }

    /**
     * Normalize the specified build parameters.
     *
     * @throws MockBuilderExceptionInterface If invalid input is supplied.
     */
    protected function normalize(array $toAdd = null)
    {
        if (null === $toAdd) {
            $toAdd = array();
        } else {
            $toAdd = array_unique($toAdd);

            foreach ($toAdd as $index => $type) {
                if (in_array($type, $this->types, true)) {
                    unset($toAdd[$index]);
                }
            }
        }

        $reflectors = $this->reflectors;

        foreach ($toAdd as $type) {
            try {
                $reflectors[] = $reflector = new ReflectionClass($type);
            } catch (ReflectionException $e) {
                throw new InvalidTypeException($type, $e);
            }

            if ($reflector->isFinal()) {
                throw new FinalClassException($type);
            }
        }

        $parentClassCount = 0;
        $parentClassNames = array();
        $parentClassName = null;
        $interfaceNames = array();
        $traitNames = array();

        foreach ($reflectors as $reflector) {
            $className = $reflector->getName();

            if ($reflector->isInterface()) {
                $interfaceNames[] = $className;
            } elseif ($this->isTraitSupported && $reflector->isTrait()) {
                $traitNames[] = $className;
            } else {
                $parentClassNames[] = $className;
                $parentClassCount++;

                if (null === $parentClassName) {
                    $parentClassName = $className;
                }
            }
        }

        if ($parentClassCount > 1) {
            throw new MultipleInheritanceException($parentClassNames);
        }

        $this->types = array_merge($this->types, $toAdd);
        $this->reflectors = $reflectors;
        $this->parentClassName = $parentClassName;
        $this->interfaceNames = $interfaceNames;
        $this->traitNames = $traitNames;

        if (null === $this->id) {
            $this->generatedClassName =
                sprintf('PhonyMock_%s', substr(md5(uniqid()), 0, 6));
        } else {
            $this->generatedClassName = sprintf('PhonyMock_%d', $this->id);
        }
    }

    private $types;
    private $reflectors;
    private $methods;
    private $staticMethods;
    private $properties;
    private $staticProperties;
    private $className;
    private $generatedClassName;
    private $parentClassName;
    private $interfaceNames;
    private $traitNames;
    private $isFinalized;
    private $isTraitSupported;
}