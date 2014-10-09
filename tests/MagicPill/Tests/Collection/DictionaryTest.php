<?php

namespace MagicPill\Test\Dictionary;

use MagicPill\Collection\Dictionary;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dictionary */
    private $dictionary;

    public function setup()
    {
        $this->dictionary = new Dictionary();
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testArrayConversion($array)
    {
        $this->dictionary->fromArray($array);

        $this->assertSame($array, $this->dictionary->toArray());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAdd($array)
    {
        foreach($array as $key => $value) {
            $this->dictionary->add($key, $value);
        }

        $this->assertCount(count($array), $this->dictionary);

        foreach($array as $key => $value) {
            $this->assertSame($value, $this->dictionary->get($key));
        }
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testContainsValue($array)
    {
        $this->dictionary->fromArray($array);

        $this->assertTrue($this->dictionary->containsValue('banana'));
        $this->assertFalse($this->dictionary->containsValue('orange'));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testClear($array)
    {
        $this->dictionary->fromArray($array);
        $this->dictionary->clear();

        $this->assertTrue($this->dictionary->isEmpty());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testProtection($array)
    {
        $this->dictionary->fromArray($array);
        $this->assertFalse($this->dictionary->isReadOnly());

        $this->dictionary->protect();
        $this->dictionary->add('key', "won't be added");

        $this->assertTrue($this->dictionary->isReadOnly());
        $this->assertCount(3, $this->dictionary);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testUnusedPointer($array)
    {
        $this->dictionary->fromArray($array);

        $key = $this->dictionary->key();
        $current = $this->dictionary->current();
        $this->assertSame('one', $key);
        $this->assertSame(1, $current);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPointerPosition($array)
    {
        $this->dictionary->fromArray($array);

        $this->dictionary->seek('two');
        $key = $this->dictionary->key();
        $current = $this->dictionary->current();
        $this->assertSame('two', $key);
        $this->assertSame(42, $current);

        $this->dictionary->next();
        $key = $this->dictionary->key();
        $current = $this->dictionary->current();
        $this->assertSame(3, $key);
        $this->assertSame('banana', $current);

        $this->dictionary->rewind();
        $key = $this->dictionary->key();
        $current = $this->dictionary->current();
        $this->assertSame('one', $key);
        $this->assertSame(1, $current);
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testPointerValidity($array)
    {
        $this->dictionary->fromArray($array);

        $this->assertTrue($this->dictionary->valid());

        $this->dictionary->seek('unexisting');
        $this->assertFalse($this->dictionary->valid());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAppendFrom($array)
    {
        $this->dictionary->fromArray($array);
        $appendDictionary = new Dictionary(
            array('four' => 'orange', 'five' => 'apple', 6 => 'pineapple')
        );

        $this->dictionary->appendFrom($appendDictionary);

        $this->assertCount(6, $this->dictionary);
        $this->assertSame(
            array('one' => 1, 'two' => 42, 3 => 'banana', 'four' => 'orange', 'five' => 'apple', 6 => 'pineapple'),
            $this->dictionary->toArray()
        );
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testEquality($array)
    {
        $this->dictionary->fromArray($array);
        $equalDictionary = new Dictionary($array);
        $differentDictionary = new Dictionary(
            array('four' => 'orange', 'five' => 'apple', 6 => 'pineapple')
        );

        $this->assertTrue($this->dictionary->equals($equalDictionary));
        $this->assertFalse($this->dictionary->equals($differentDictionary));
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testKeys($array)
    {
        $this->dictionary->fromArray($array);
        $keys = array('one', 'two', 3);

        $this->assertSame($keys, $this->dictionary->keys());
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testValues($array)
    {
        $this->dictionary->fromArray($array);
        $keys = array(1, 42, 'banana');

        $this->assertSame($keys, $this->dictionary->values());
    }

    public function dataProvider()
    {
        return array(
            array(
                array('one' => 1, 'two' => 42, 3 => 'banana')
            )
        );
    }
}