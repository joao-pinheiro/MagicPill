<?php

namespace MagicPill\Test\Collection;

use MagicPill\Collection\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container */
    private $container;

    /**
     * setUp tests
     */
    public function setup()
    {
        $this->container = new Container();
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testArrayConversion($array)
    {
        $this->container->fromArray($array);
        $this->assertSame($array, $this->container->toArray());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAdd($array)
    {
        foreach ($array as $key => $value) {
            $this->container->add($key, $value);
        }

        $this->assertCount(count($array), $this->container);

        foreach ($array as $key => $value) {
            $storedValue = $this->container->get($key);
            if ($storedValue instanceof Container) {
                $this->assertSame($value, $storedValue->toArray());
            } else {
                $this->assertSame($value, $this->container->get($key));
            }
        }
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testContainsKey($array)
    {
        $this->container->fromArray($array);

        $this->assertTrue($this->container->containsKey('one'));
        $this->assertFalse($this->container->containsKey('three'));
        $this->assertTrue($this->container->etc->containsKey('option_1'));
        $this->assertFalse($this->container->etc->containsKey('option_3'));
        $this->assertTrue($this->container->etc2->container->containsKey('container_option_1'));
        $this->assertFalse($this->container->etc2->container->containsKey('container_option_3'));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testContainsValue($array)
    {
        $this->container->fromArray($array);

        $this->assertTrue($this->container->containsValue('banana'));
        $this->assertFalse($this->container->containsValue('orange'));
        $this->assertTrue($this->container->etc->containsValue('1'));
        $this->assertTrue($this->container->etc->containsValue('value2'));
        $this->assertFalse($this->container->etc->containsValue('44'));
        $this->assertTrue($this->container->etc2->container->containsValue('value_container_option_1'));
        $this->assertFalse($this->container->etc2->container->containsValue('value_container_option_3'));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testClear($array)
    {
        $this->container->fromArray($array);
        $this->container->clear();

        $this->assertTrue($this->container->isEmpty());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testProtection($array)
    {
        $this->container->fromArray($array);
        $this->assertFalse($this->container->isReadOnly());

        $this->container->protect();
        $this->container->add('key', "won't be added");

        $this->assertTrue($this->container->isReadOnly());
        $this->assertCount(count($array), $this->container);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPointerPosition($array)
    {
        $this->container->fromArray($array);

        $this->container->seek('two');
        $key = $this->container->key();
        $current = $this->container->current();
        $this->assertSame('two', $key);
        $this->assertSame(42, $current);

        $this->container->next();
        $key = $this->container->key();
        $current = $this->container->current();
        $this->assertSame(3, $key);
        $this->assertSame('banana', $current);

        $this->container->rewind();
        $key = $this->container->key();
        $current = $this->container->current();
        $this->assertSame('one', $key);
        $this->assertSame(1, $current);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPointerValidity($array)
    {
        $this->container->fromArray($array);

        $this->assertTrue($this->container->valid());

        $this->container->seek('unexisting');
        $this->assertFalse($this->container->valid());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAppendFrom($array)
    {
        $this->container->fromArray($array);
        $append = ['four' => 'orange', 'five' => 'apple', 6 => 'pineapple'];

        $appendContainer = new Container($append);
        $this->container->appendFrom($appendContainer);

        $array = array_merge_recursive($array, $append);

        $this->assertCount(count($array), $this->container);
        $result = $this->container->toArray();
        $this->assertSame(ksort($array), ksort($result));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testEquality($array)
    {
        $this->container->fromArray($array);
        $equalContainer = new Container($array);
        array_shift($array);
        $differentContainer = new Container($array);

        $this->assertTrue($this->container->equals($equalContainer));
        $this->assertFalse($this->container->equals($differentContainer));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testKeys($array)
    {
        $this->container->fromArray($array);
        $this->assertSame(array_keys($array), $this->container->keys());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testValues($array)
    {
        $this->container->fromArray($array);
        $this->assertSame(array_values($array['etc']), $this->container->etc->values());
    }

    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            [
                [
                    'one'  => 1,
                    'two'  => 42,
                    3      => 'banana',
                    'etc'  => [
                        'option_1' => 1,
                        'option_2' => 'value2'
                    ],
                    'etc2' => [
                        'option_1'  => 'value1',
                        'option_2'  => 'value2',
                        'container' => [
                            'container_option_1' => 'value_container_option_1',
                            'container_option_2' => 'value_container_option_2',
                        ]
                    ]
                ]
            ]
            ];
    }
}