<?php
/**
 * MagicPill
 *
 * Copyright (c) 2014, Joao Pinheiro
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MagicPill
 * @package    Collection
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace Test\Collection;

use MagicPill\Collection\Collection;
use MagicPill\Collection\HashTable;

class HashTableTest extends \MagicPill\Test\TestCase
{
    const CLASS_NAME = 'MagicPill\Collection\HashTable';
    const CLASS_NAME_COLLECTION = 'MagicPill\Collection\Collection';    
    /**
     * Tests HashTable::add()
     */
    public function testAdd()
    {
        $data = $this->generateData();
        $hash = new HashTable();
        $this->assertTrue($hash->isEmpty());
        
        $count = 0;
        foreach($data as $key => $value) {
            $count++;
            $result = $hash->add($key, $value);
            $this->assertEquals(self::CLASS_NAME, get_class($result));
            $this->assertEquals($count, $hash->count());
        }
        
        $hash->rewind();
        $keys = $hash->keys();
        $values = $hash->values();
        $firstKey = array_shift($keys);
        $firstValue = array_shift($values);
        
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($firstValue));
        $this->assertTrue($hash->offsetExists($firstKey));
        $this->assertEquals($data, $hash->toArray());
        
        // test automatic collection conversion
        $hash = new HashTable();
        $hash->add('hash_1', array(1, 2, 3, 4));
        $item = $hash->get('hash_1');
        $this->assertTrue(is_object($item));
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($item));
        $this->assertEquals(4, $item->count());
        
        $hash->add('hash_2', new Collection(array(1, 2, 3, 4)));
        $item = $hash->get('hash_2');
        $this->assertTrue(is_object($item));
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($item));
        $this->assertEquals(4, $item->count()); 
        
        // test read only mode
        $count = $hash->count();
        $this->assertFalse($hash->isReadOnly());
        $result = $hash->protect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertTrue($hash->isReadOnly());
        $hash->add('test', array());
        $this->assertEquals($count, $hash->count());
        $result = $hash->unprotect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertFalse($hash->isReadOnly());        
    }

    /**
     * Tests HashTable::addScalar()
     */
    public function testAddScalar()
    {
        $data = $this->generateData();
        $hash = new HashTable();
        $this->assertTrue($hash->isEmpty());
        
        $count = 0;
        foreach($data as $key => $value) {
            $count++;
            foreach($value as $item) {
                $result = $hash->addScalar($key, $item);
                $this->assertEquals(self::CLASS_NAME, get_class($result));
            }
            $this->assertEquals($count, $hash->count());
        }
        
        $hash->rewind();
        $keys = $hash->keys();
        $values = $hash->values();
        $firstKey = array_shift($keys);
        $firstValue = array_shift($values);
        
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($firstValue));
        $this->assertTrue($hash->offsetExists($firstKey));
        $this->assertEquals($data, $hash->toArray());
        
        // test (lack of) automatic collection conversion
        // arrays and collections should be stored without conversion
        $hash = new HashTable();
        $hash->addScalar('hash_1', array(1, 2, 3, 4));
        $item = $hash->get('hash_1');
        $this->assertTrue(is_object($item));
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($item));
        $this->assertEquals(1, $item->count());
        
        $hash->addScalar('hash_2', new Collection(array(1, 2, 3, 4)));
        $item = $hash->get('hash_2');
        $this->assertTrue(is_object($item));
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($item));
        $this->assertEquals(1, $item->count());        
        $item->rewind();
        $subItem = $item->current();
        $this->assertEquals(self::CLASS_NAME_COLLECTION, get_class($subItem));
        $this->assertEquals(4, $subItem->count());
        
        // test read only mode
        $count = $hash->count();
        $this->assertFalse($hash->isReadOnly());
        $result = $hash->protect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertTrue($hash->isReadOnly());
        $hash->addScalar('test', array());
        $this->assertEquals($count, $hash->count());
        $result = $hash->unprotect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertFalse($hash->isReadOnly());        
    }
    
    /**
     * Test HashTable::containsValue()
     */
    public function testContainsValue()
    {
        $hash = new HashTable();
        $this->assertTrue($hash->isEmpty());
        $this->assertFalse($hash->containsValue('element_0'));
        $hash->fromArray($this->generateData());
        $this->assertFalse($hash->isEmpty());
        $this->assertTrue($hash->containsValue('element_0'));        
        $hash->clear();
        $this->assertFalse($hash->containsValue('element_0'));
    }
    
    /**
     * Test HashTable::toArray()
     */
    public function testToArray()
    {
        $data = $this->generateData();
        $hash = new HashTable($data);
        $this->assertFalse($hash->isEmpty());
        $this->assertEquals($data, $hash->toArray());
        $this->assertEquals($data, $hash->toArray());
    }
    
    /**
     * Tests HashTable::equals()
     */
    public function testEquals()
    {
        $data = $this->generateData();
        $hash1 = new HashTable();
        $hash2 = new HashTable();
        
        $this->assertTrue($hash1->equals($hash2));
        $this->assertTrue($hash2->equals($hash1));
        $hash1->fromArray($data);
        $this->assertFalse($hash1->equals($hash2));
        $this->assertFalse($hash2->equals($hash1));
        $hash2->fromArray($data);
        $this->assertTrue($hash1->equals($hash2));
        $this->assertTrue($hash2->equals($hash1));
        
        // remove a leaf
        $hash1->get('hash_0')->pop();
        $this->assertFalse($hash1->equals($hash2));
        $this->assertFalse($hash2->equals($hash1));
        $hash2->get('hash_0')->pop();        
        $this->assertTrue($hash1->equals($hash2));
        $this->assertTrue($hash2->equals($hash1));

        // change a hash collection
        $hash1->add('test', array());
        $this->assertFalse($hash1->equals($hash2));
        $this->assertFalse($hash2->equals($hash1));
        $hash1->remove('test');
        $this->assertTrue($hash1->equals($hash2));
        $this->assertTrue($hash2->equals($hash1));        
    }
    
    /**
     * Test HashTable::AppendFrom()
     */
    public function testAppendFrom()
    {
        $data = $this->generateData();
        $hash1 = new HashTable();
        $hash2 = new HashTable($data);
        $this->assertTrue($hash1->isEmpty());
        
        $hash1->appendFrom($hash2);
        $this->assertFalse($hash1->isEmpty());
        $this->assertEquals($data, $hash1->toArray());
        $this->assertTrue($hash1->equals($hash2));
    }
    
    /**
     * Generate test data
     * @return array
     */
    protected function generateData()
    {
        $result = array();
        $hashLimit = 10;
        $elementLimit = 10;
        
        for($i = 0; $i < $hashLimit; $i++) {
            $item = array();
            for($j = 0; $j < $elementLimit; $j++) {
                $item[] = 'element_' . $j;
            }
            $result['hash_' . $i] = $item;
        }
        return $result;
    }
}
