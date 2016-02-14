<?php

namespace MagicPill\Mvc\View\Helper;

use \MagicPill\Core\Object,
    \MagicPill\Mvc\ViewModel;
use MagicPill\Mixin\Inherit;

abstract class HelperAbstract extends Object
{
    use Inherit;

    /**
     * @var \MagicPill\Mvc\ViewModel
     */
    protected $view = null;

    /**
     * Constructor
     * @param View $viewModel
     */
    public function __construct(View $viewModel = null)
    {
        $this->setView($viewModel);
        $this->setParent($viewModel);
    }

    /**
     * Set ViewModel Object
     * @param ViewModel $view
     */
    public function setView(ViewModel $view)
    {
        $this->view = $view;
    }

    /**
     * Retrieve ViewModel Object
     * @return ViewModel
     */
    public function getView()
    {
        return $this->view;
    }
}