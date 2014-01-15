# MagicPill
### What is it?
MagicPill is a work-in-progress PHP Meta Framework based on Zend Framework 1 and licensed under a 2-clause BSD License, with a strong focus on performance.

### Why?
Most feature-complete PHP frameworks tend to be over-engineered and too focused on long-term maintainability of code. MagicPill does not intend to be a generic framework, but a base platform for a specific kind of sites. That said, at this point the project is mostly my personal playground, and a way of sharing some implementation concepts.

### Is it suitable for me?
At this point, probably not - It is still missing a huge amount of work.

### What is available?

#### Core Object
\MagicPill\Core\Object
Implements a base class with some boilerplate funcionality

#### Collections
\MagicPill\Collection\ListCollection
  Implements a List
  
\MagicPill\Collection\Dictionary
  Implements a dictionary

\MagicPill\Collections\HashTable
  Implements a HashTable
  
\MagicPill\Collections\HashDictionary
  Implements a HashTable that mantains key/value lists per branch (Dictionaries)
  
#### Resource Management
\MagicPill\Resource\Load
Implements loading of resource objects
\MagicPill\Resource\Manager
Implements registration management of resource objects

#### Application
\MagicPill\Application
Implements application bootstrapping with lazy loading of dependencies

\MagicPill\Application\ResourceManager
Implements the Resource Manager used by \MagicPill\Application 

\MagicPill\Application\Resource\Config
Resource class for configuration files

\MagicPill\Application\Resource\Log
Resource class for logging infrastructure

\MagicPill\Application\Resource\PhpSettings
Resource class for PHP settings override via config file

\MagicPill\Application\Resource\Registry
Resource class for an application registry

### Examples
Simple usage example for \MagicPill\Application
```php
$app = new \MagicPill\Application(array(
    'environment' => getenv('APPLICATION_ENV'),
    'configFile' => APPLICATION_PATH . '/config/application.ini',
    'resourceNamespace' => array(
        '\Namespace\One',
        '\Namespace\Two'
        ),
    'developmentEnvironment', in_array(APPLICATION_ENV, array('development', 'testing', 'staging'))
));

//executing resource class as methods
$app->runMvc();

// lazy loading of Log Resource
$log = $app->getLog();
```
