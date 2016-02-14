<?php

namespace MagicPill\Mixin;

Trait Inherit
{
    /**
     * parent object
     * @var object
     */
    protected $parent = null;

    /**
     * Retrieves the parent object
     * @return object
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Defines the parent object
     * @param object|null $parent
     * @return \MagicPill\Resource\Resource
     */
    public function setParent($parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Check if current object is a child of $object
     * @param object $object
     * @return bool
     */
    public function isChildOf($object)
    {
        if (is_object($object)) {
            return $object === $this->parent;
        }
        return false;
    }

    /**
     * Check if current object is parent of $object
     * @param object $object
     * @return bool
     */
    public function isParentOf($object)
    {
        if (is_object($object) && ($object instanceof Inherit)) {
            return $this == $object->getParent();
        }
        return false;
    }
}