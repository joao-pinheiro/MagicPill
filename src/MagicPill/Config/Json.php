<?php

namespace MagicPill\Config;

use MagicPill\Collection\Container;

class Json extends Container
{
    /**
     * Constructor
     * @param string $jsonString
     */
    public function __construct($jsonString)
    {
        parent::__construct(json_decode($jsonString, true));
    }
}