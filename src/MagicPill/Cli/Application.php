<?php

namespace MagicPill\Cli;

use MagicPill\Application\ApplicationAbstract;

class Application extends ApplicationAbstract
{
    /**
     * Default application resource namespaces
     * @var array
     */
    protected $resourceNamespaces = [
        '\MagicPill\Cli\Resource',
        '\MagicPill\Application\Resource'
    ];

    public function start()
    {

    }

    public function run()
    {

    }
}