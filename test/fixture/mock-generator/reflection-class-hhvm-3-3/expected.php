<?php

/**
 * A mock class generated by Phony.
 *
 * @uses \ReflectionClass
 *
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with the Phony source code.
 *
 * @link https://github.com/eloquent/phony
 */
class MockGeneratorReflectionClassHhvm3_3
extends \ReflectionClass
implements \Eloquent\Phony\Mock\MockInterface
{
    /**
     * Set the static stubs.
     *
     * @param array<string,\Eloquent\Phony\Stub\StubInterface>|null $staticStubs The stubs to use.
     */
    public static function _setStaticStubs(array $staticStubs)
    {
        self::$_staticStubs = $staticStubs;
    }

    /**
     * Inherited static method 'export'.
     *
     * @uses \ReflectionClass::export()
     *
     * @param mixed $a0 Originally named 'name'.
     * @param mixed $a1 Originally named 'ret'.
     */
    public static function export(
        $a0,
        $a1 = false
    ) {
        if (isset(self::$_staticStubs[__FUNCTION__])) {
            return call_user_func_array(
                self::$_staticStubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Construct a mock.
     *
     * @param array<string,\Eloquent\Phony\Stub\StubInterface>|null $stubs The stubs to use.
     */
    public function __construct(
        array $stubs = null
    ) {
        if (null === $stubs) {
            $stubs = array();
        }

        $this->_stubs = $stubs;
    }

    /**
     * Call the parent constructor.
     */
    public function _constructParent(
        $a0
    ) {
        call_user_func_array(
            array($this, 'parent::__construct'),
            func_get_args()
        );
    }

    /**
     * Inherited method '__toString'.
     *
     * @uses \ReflectionClass::__toString()
     */
    public function __toString()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getAttribute'.
     *
     * @uses \ReflectionClass::getAttribute()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function getAttribute(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getAttributeRecursive'.
     *
     * @uses \ReflectionClass::getAttributeRecursive()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function getAttributeRecursive(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getAttributes'.
     *
     * @uses \ReflectionClass::getAttributes()
     */
    public function getAttributes()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getAttributesRecursive'.
     *
     * @uses \ReflectionClass::getAttributesRecursive()
     */
    public function getAttributesRecursive()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getConstant'.
     *
     * @uses \ReflectionClass::getConstant()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function getConstant(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getConstants'.
     *
     * @uses \ReflectionClass::getConstants()
     */
    public function getConstants()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getConstructor'.
     *
     * @uses \ReflectionClass::getConstructor()
     */
    public function getConstructor()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getDefaultProperties'.
     *
     * @uses \ReflectionClass::getDefaultProperties()
     */
    public function getDefaultProperties()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getDocComment'.
     *
     * @uses \ReflectionClass::getDocComment()
     */
    public function getDocComment()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getEndLine'.
     *
     * @uses \ReflectionClass::getEndLine()
     */
    public function getEndLine()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getExtension'.
     *
     * @uses \ReflectionClass::getExtension()
     */
    public function getExtension()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getExtensionName'.
     *
     * @uses \ReflectionClass::getExtensionName()
     */
    public function getExtensionName()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getFileName'.
     *
     * @uses \ReflectionClass::getFileName()
     */
    public function getFileName()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getInterfaceNames'.
     *
     * @uses \ReflectionClass::getInterfaceNames()
     */
    public function getInterfaceNames()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getInterfaces'.
     *
     * @uses \ReflectionClass::getInterfaces()
     */
    public function getInterfaces()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getMethod'.
     *
     * @uses \ReflectionClass::getMethod()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function getMethod(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getMethods'.
     *
     * @uses \ReflectionClass::getMethods()
     *
     * @param mixed $a0 Originally named 'filter'.
     */
    public function getMethods(
        $a0 = null
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getModifiers'.
     *
     * @uses \ReflectionClass::getModifiers()
     */
    public function getModifiers()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getName'.
     *
     * @uses \ReflectionClass::getName()
     */
    public function getName()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getNamespaceName'.
     *
     * @uses \ReflectionClass::getNamespaceName()
     */
    public function getNamespaceName()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getParentClass'.
     *
     * @uses \ReflectionClass::getParentClass()
     */
    public function getParentClass()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getProperties'.
     *
     * @uses \ReflectionClass::getProperties()
     *
     * @param mixed $a0 Originally named 'filter'.
     */
    public function getProperties(
        $a0 = 65535
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getProperty'.
     *
     * @uses \ReflectionClass::getProperty()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function getProperty(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getRequirementNames'.
     *
     * @uses \ReflectionClass::getRequirementNames()
     */
    public function getRequirementNames()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getRequirements'.
     *
     * @uses \ReflectionClass::getRequirements()
     */
    public function getRequirements()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getShortName'.
     *
     * @uses \ReflectionClass::getShortName()
     */
    public function getShortName()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getStartLine'.
     *
     * @uses \ReflectionClass::getStartLine()
     */
    public function getStartLine()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getStaticProperties'.
     *
     * @uses \ReflectionClass::getStaticProperties()
     */
    public function getStaticProperties()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getStaticPropertyValue'.
     *
     * @uses \ReflectionClass::getStaticPropertyValue()
     *
     * @param mixed $a0 Originally named 'name'.
     * @param mixed $a1 Originally named 'default'.
     */
    public function getStaticPropertyValue(
        $a0,
        $a1 = null
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getTraitAliases'.
     *
     * @uses \ReflectionClass::getTraitAliases()
     */
    public function getTraitAliases()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getTraitNames'.
     *
     * @uses \ReflectionClass::getTraitNames()
     */
    public function getTraitNames()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'getTraits'.
     *
     * @uses \ReflectionClass::getTraits()
     */
    public function getTraits()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'hasConstant'.
     *
     * @uses \ReflectionClass::hasConstant()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function hasConstant(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'hasMethod'.
     *
     * @uses \ReflectionClass::hasMethod()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function hasMethod(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'hasProperty'.
     *
     * @uses \ReflectionClass::hasProperty()
     *
     * @param mixed $a0 Originally named 'name'.
     */
    public function hasProperty(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'implementsInterface'.
     *
     * @uses \ReflectionClass::implementsInterface()
     *
     * @param mixed $a0 Originally named 'cls'.
     */
    public function implementsInterface(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'inNamespace'.
     *
     * @uses \ReflectionClass::inNamespace()
     */
    public function inNamespace()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isAbstract'.
     *
     * @uses \ReflectionClass::isAbstract()
     */
    public function isAbstract()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isCloneable'.
     *
     * @uses \ReflectionClass::isCloneable()
     */
    public function isCloneable()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isFinal'.
     *
     * @uses \ReflectionClass::isFinal()
     */
    public function isFinal()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isInstance'.
     *
     * @uses \ReflectionClass::isInstance()
     *
     * @param mixed $a0 Originally named 'obj'.
     */
    public function isInstance(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isInstantiable'.
     *
     * @uses \ReflectionClass::isInstantiable()
     */
    public function isInstantiable()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isInterface'.
     *
     * @uses \ReflectionClass::isInterface()
     */
    public function isInterface()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isInternal'.
     *
     * @uses \ReflectionClass::isInternal()
     */
    public function isInternal()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isIterateable'.
     *
     * @uses \ReflectionClass::isIterateable()
     */
    public function isIterateable()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isSubclassOf'.
     *
     * @uses \ReflectionClass::isSubclassOf()
     *
     * @param mixed $a0 Originally named 'cls'.
     */
    public function isSubclassOf(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isTrait'.
     *
     * @uses \ReflectionClass::isTrait()
     */
    public function isTrait()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'isUserDefined'.
     *
     * @uses \ReflectionClass::isUserDefined()
     */
    public function isUserDefined()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'newInstance'.
     *
     * @uses \ReflectionClass::newInstance()
     *
     * @param mixed $a0 Originally named 'args'.
     */
    public function newInstance(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'newInstanceArgs'.
     *
     * @uses \ReflectionClass::newInstanceArgs()
     *
     * @param mixed $a0 Originally named 'args'.
     */
    public function newInstanceArgs(
        $a0 = array()
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'newInstanceWithoutConstructor'.
     *
     * @uses \ReflectionClass::newInstanceWithoutConstructor()
     */
    public function newInstanceWithoutConstructor()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'serialize'.
     *
     * @uses \ReflectionClass::serialize()
     */
    public function serialize()
    {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'setStaticPropertyValue'.
     *
     * @uses \ReflectionClass::setStaticPropertyValue()
     *
     * @param mixed $a0 Originally named 'name'.
     * @param mixed $a1 Originally named 'value'.
     */
    public function setStaticPropertyValue(
        $a0,
        $a1
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    /**
     * Inherited method 'unserialize'.
     *
     * @uses \ReflectionClass::unserialize()
     *
     * @param mixed $a0 Originally named 'string'.
     */
    public function unserialize(
        $a0
    ) {
        if (isset($this->_stubs[__FUNCTION__])) {
            return call_user_func_array(
                $this->_stubs[__FUNCTION__],
                func_get_args()
            );
        }
    }

    private static $_staticStubs = array();
    private $_stubs;
}
