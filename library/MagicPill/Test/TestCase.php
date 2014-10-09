<?php

namespace MagicPill\Test;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     * @param type $name
     * @param array $data
     * @param type $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->backupGlobals = false;
        $this->backupStaticAttributes = false;
    }
}
