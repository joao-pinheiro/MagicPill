<?php

namespace MagicPill\Test\Dictionary;

use MagicPill\Collection\Collection;
use MagicPill\Collection\HashTable;

class HashTableTest extends \PHPUnit_Framework_TestCase
{
    /** @var HashTable */
    private $hashTable;

    public function setup()
    {
        $this->hashTable = new HashTable();
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAdd($array)
    {
        foreach($array as $key => $value) {
            $this->hashTable->add($key, $value);
        }

        $this->assertCount(count($array), $this->hashTable);

        foreach($array as $key => $value) {
            $collectionValue = new Collection(array($value));
            $this->assertTrue(
                $collectionValue->equals($this->hashTable->get($key))
            );
        }
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testContainsValue($array)
    {
        $this->hashTable->fromArray($array);

        $this->assertTrue(
            $this->hashTable->containsValue('banana')
        );
        $this->assertFalse(
            $this->hashTable->containsValue('orange')
        );
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testAppendFrom($array)
    {
        $this->hashTable->fromArray($array);
        $appendDictionary = new HashTable(
            array('four' => 'orange', 'five' => 'apple', 6 => 'pineapple')
        );

        $this->hashTable->appendFrom($appendDictionary);
        $resultHashTable = new HashTable(
            array(
                'one' => 1,
                'two' => 42,
                3 => 'banana',
                'four' => 'orange',
                'five' => 'apple',
                6 => 'pineapple'
            )
        );

        $this->assertCount(6, $this->hashTable);
        $this->assertTrue(
            $this->hashTable->equals($resultHashTable)
        );
    }

    /**
     * @dataProvider dataProvider
     * @param array $array
     */
    public function testEquality($array)
    {
        $this->hashTable->fromArray($array);
        $equalHashTable = new HashTable($array);
        $differentHashTable = new HashTable(
            array('four' => 'orange', 'five' => 'apple', 6 => 'pineapple')
        );

        $this->assertTrue($this->hashTable->equals($equalHashTable));
        $this->assertFalse($this->hashTable->equals($differentHashTable));
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