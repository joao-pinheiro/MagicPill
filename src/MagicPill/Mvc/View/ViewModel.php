<?php

namespace MagicPill\Mvc\View;

use MagicPill\Collection\Dictionary;
use \Weduc\Core\Object;

class ViewModel extends Object
{
    /**
     * @var \MagicPill\Collection\Dictionary
     */
    protected $children = null;

    /**
     * @var \MagicPill\Collection\Dictionary
     */
    protected $viewMap = null;

    /**
     * @var \MagicPill\Mvc\View\Object
     */
    protected $layout = null;

    /**
     * Defines the view scripts map
     * @param array $map
     * @return $this
     */
    public function setViewScriptMap(array $map)
    {
        $this->getViewScriptMap()->fromArray($map);
        return $this;
    }

    /**
     * Appends a view script map
     * @param string $name
     * @param string $viewFile
     * @return $this
     */
    public function addViewScriptMap($name, $viewFile)
    {
        $this->getViewScriptMap()->add($name, $viewFile);
        return $this;
    }

    /**
     * Retrieve view script map
     * @return Dictionary
     */
    public function getViewScriptMap()
    {
        if (null == $this->viewMap) {
            $this->viewMap = new Dictionary();
        }
        return $this->viewMap;
    }

    /**
     * Adds a children view
     * @param string $viewName
     * @param ViewModel $model
     * @return $this
     */
    public function addChild($viewName, ViewModel $model)
    {
        $this->getChildren()->add($viewName, $model);
        return $this;
    }

    /**
     * Retrieve children view collection
     * @return Dictionary
     */
    public function getChildren()
    {
        if (null == $this->children) {
            $this->children = new Dictionary();
        }
        return $this->children;
    }
}
