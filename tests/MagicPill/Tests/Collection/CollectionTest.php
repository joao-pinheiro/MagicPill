<?php

namespace MagicPill\Test\Collection;

use MagicPill\Collection\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection */
    private $collection;

    public function setup()
    {
        $this->collection = new Collection();
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testArrayConversion($array)
    {
        $this->collection->fromArray($array);

        $this->assertSame($array, $this->collection->toArray());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAdd($array)
    {
        foreach($array as $value) {
            $this->collection->add($value);
        }

        $this->assertCount(count($array), $this->collection);

        foreach(array_values($array) as $key => $value) {
            $this->assertSame($value, $this->collection->get($key));
        }
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testContainsValue($array)
    {
        $this->collection->fromArray($array);

        $this->assertTrue($this->collection->containsValue('banana'));
        $this->assertFalse($this->collection->containsValue('orange'));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPop($array)
    {
        $this->collection->fromArray($array);
        $value = $this->collection->pop();

        $this->assertCount(2, $this->collection);
        $this->assertSame('banana', $value);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testShift($array)
    {
        $this->collection->fromArray($array);
        $value = $this->collection->shift();

        $this->assertCount(2, $this->collection);
        $this->assertSame(1, $value);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testUnshift($array)
    {
        $this->collection->fromArray($array);
        $this->collection->unshift('orange');

        $this->assertCount(4, $this->collection);
        $this->assertSame('orange', $this->collection->get(0));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testClear($array)
    {
        $this->collection->fromArray($array);
        $this->collection->clear();

        $this->assertTrue($this->collection->isEmpty());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testProtection($array)
    {
        $this->collection->fromArray($array);
        $this->assertFalse($this->collection->isReadOnly());

        $this->collection->protect();
        $this->collection->add("won't be added");

        $this->assertTrue($this->collection->isReadOnly());
        $this->assertCount(3, $this->collection);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testUnusedPointer($array)
    {
        $this->collection->fromArray($array);

        $key = $this->collection->key();
        $current = $this->collection->current();
        $this->assertSame(null, $key);
        $this->assertSame(null, $current);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPointerPosition($array)
    {
        $this->collection->fromArray($array);

        $this->collection->seek(1);
        $key = $this->collection->key();
        $current = $this->collection->current();
        $this->assertSame(1, $key);
        $this->assertSame(42, $current);

        $this->collection->next();
        $key = $this->collection->key();
        $current = $this->collection->current();
        $this->assertSame(2, $key);
        $this->assertSame('banana', $current);

        $this->collection->rewind();
        $key = $this->collection->key();
        $current = $this->collection->current();
        $this->assertSame(0, $key);
        $this->assertSame(1, $current);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPointerValidity($array)
    {
        $this->collection->fromArray($array);

        $this->assertFalse($this->collection->valid());

        $this->collection->seek(1);
        $this->assertTrue($this->collection->valid());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAppendFrom($array)
    {
        $this->collection->fromArray($array);
        $appendCollection = new Collection(
            array('orange', 'apple', 'pineapple')
        );

        $this->collection->appendFrom($appendCollection);

        $this->assertCount(6, $this->collection);
        $this->assertSame(
            array(1, 42, 'banana', 'orange', 'apple', 'pineapple'),
            $this->collection->toArray()
        );
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testEquality($array)
    {
        $this->collection->fromArray($array);
        $equalCollection = new Collection($array);
        $differentCollection = new Collection(
            array('orange', 'apple', 'pineapple')
        );

        $this->assertTrue($this->collection->equals($equalCollection));
        $this->assertFalse($this->collection->equals($differentCollection));
    }

    public function dataProvider()
    {
        return array(
            array(
                array(1, 42, 'banana')
            )
        );
    }
}