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
 */

namespace Test\Collection;

use \MagicPill\Collection\Dictionary;

class DictionaryTest extends \MagicPill\Test\TestCase
{
    const CLASS_NAME = 'MagicPill\Collection\Dictionary';

    /**
     * Test Dictionary constructor
     */
    public function testConstruct()
    {
        $dict = new Dictionary();
        $this->assertTrue($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());        
        $this->assertEquals(0, $dict->count());
        $this->assertFalse($dict->valid());
        
        // data from array
        $data = $this->generateData();
        $dict = new Dictionary($data);
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(count($data), $dict->count());
        $this->assertEquals($data, $dict->toArray());
        
        // data from dictionary
        $otherDict = new Dictionary($dict);
        $this->assertFalse($otherDict->isEmpty());
        $this->assertEquals(count($data), $otherDict->count());
        $this->assertEquals($data, $otherDict->toArray());
    }    
    
    /**
     * Test Dictionary::Add()
     */
    public function testAdd()
    {
        // simple add
        $dict = new Dictionary();
        $data = $this->generateData();
        foreach($data as $key => $value) {
            $result = $dict->add($key, $value);
            $this->assertEquals(self::CLASS_NAME, get_class($result));
        }
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());        
        $this->assertEquals(count($data), $dict->count());
        $this->assertEquals($data, $dict->toArray());

        // test null corner cases
        $dict = new Dictionary();
        $dict->add(null, 'test');
        $this->assertTrue($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(0, $dict->count());        

        $dict->add(null, null);
        $this->assertTrue($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(0, $dict->count());  
        
        $dict->add('key', null);
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(1, $dict->count()); 
        $this->assertEquals(null, $dict->get('key'));
        
        // test different data types
        $dict = new Dictionary();
        $dict->add('integer', 1);
        $dict->add('object', new \DateTime());
        $dict->add('bool', true);
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(3, $dict->count()); 
        $this->assertTrue(is_integer($dict->get('integer')));
        $this->assertEquals(1, $dict->get('integer'));
        $this->assertEquals(1, $dict['integer']);
        $this->assertEquals('DateTime', get_class($dict->get('object')));
        $this->assertEquals('DateTime', get_class($dict['object']));
        $this->assertTrue($dict->get('bool'));
        $this->assertTrue($dict['bool']);
        
        // test read only mode
        $dict = new Dictionary();
        $dict->protect();
        $this->assertTrue($dict->isEmpty());
        $this->assertTrue($dict->isReadOnly());
        $this->assertEquals(0, $dict->count()); 
        $dict->add('key', 'value');
        $this->assertTrue($dict->isEmpty());
        $this->assertTrue($dict->isReadOnly());
        $this->assertEquals(0, $dict->count());
        // test read-only with non-empty collection
        $dict->unprotect();
        $dict->add('key', 'value');
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(1, $dict->count()); 
        $dict->protect();
        $dict->add('otherkey', 'othervalue');
        $this->assertFalse($dict->isEmpty());
        $this->assertTrue($dict->isReadOnly());
        $this->assertEquals(1, $dict->count());         
        
        // test add rewrite
        $dict = new Dictionary();
        $dict->add('key', 'value');
        $dict->add('key', 'other value'); // should rewrite previous key
        $this->assertFalse($dict->isEmpty());
        $this->assertEquals(1, $dict->count());
        $this->assertEquals('other value', $dict->get('key'));
    }
    
    /**
     * Test Dictionary::fromArray(), Dictionary::toArray()
     */
    public function testFromToArray()
    {
        $data = $this->generateData();
        $dict = new Dictionary();
        $result = $dict->fromArray($data);
        $this->assertEquals(count($data), $dict->count());
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());        
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertTrue(is_array($dict->toArray()));
        $this->assertEquals($data, $dict->toArray());
        
        //test add rewrite
        $keys = array_keys($data);
        $key = array_shift($keys);
        $data[$key] = 'some new value';
        $dict->fromArray($data); 
        $this->assertEquals(count($data), $dict->count());
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());        
        $this->assertEquals($data, $dict->toArray());        
        $this->assertEquals($data[$key], $dict->get($key));
    }
    
    /**
     * Test Dictionary::clear()
     */
    public function testClear()
    {
        $data = $this->generateData();
        $dict = new Dictionary($data);
        $this->assertFalse($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());        
        $this->assertEquals(count($data), $dict->count());
        
        $result = $dict->clear();
        $this->assertEquals(self::CLASS_NAME, get_class($result));        
        $this->assertTrue($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
        $this->assertEquals(0, $dict->count()); 
        
        $dict = new Dictionary($data);
        $dict->protect();
        $this->assertFalse($dict->isEmpty());
        $this->assertTrue($dict->isReadOnly());        
        $dict->clear();
        $this->assertTrue($dict->isEmpty());
        $this->assertFalse($dict->isReadOnly());
    }
    
    /**
     * Test Dictionary::getHashCode()
     */
    public function testGetHashCode()
    {
        $dict = new Dictionary();
        $hash = $dict->getHashCode();
        
        $this->assertNotNull($hash);
        $this->assertNotEmpty($hash);
        $this->assertTrue(strlen($hash) > 8);
        $this->assertEquals($hash, $dict->getHashCode());
    }    
    
    /**
     * Test Dictionary::isReadOnly(), Dictionary::protect(), Dictionary::unprotect()
     */
    public function testIsReadOnly()
    {
        $dict = new Dictionary();
        $this->assertFalse($dict->isReadOnly());
        
        $result = $dict->protect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertTrue($dict->isReadOnly());
        $dict->add('test', 'item1');
        $this->assertTrue($dict->isEmpty());
        $this->assertEquals(0, $dict->count());
        
        $result = $dict->unprotect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertFalse($dict->isReadOnly());
        $dict->add('test', 'item1');
        $this->assertFalse($dict->isEmpty());
        
        $result = $dict->protect();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $result = $dict->clear();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertFalse($dict->isReadOnly());        
    }
    
    /**
     * Test Dictionary::isEmpty()
     */
    public function testIsEmpty()
    {
        $dict = new Dictionary();
        $this->assertTrue($dict->isEmpty());
        $dict->fromArray($this->generateData());
        $this->assertFalse($dict->isEmpty());
        $dict->clear();
        $this->assertTrue($dict->isEmpty());        
    }
    
    /**
     * Tests Dictionary::seek(), Dictionary::current(), Dictionary::key(),
     * Dictionary::valid(), Dictionary::next(), Dictionary::rewind(), 
     * Dictionary::get(), Dictionary::offsetGet(), Dictionary::offsetExists()
     */
    public function testArrayContent()
    {
        $data = $this->generateData();
        
        $dict = new Dictionary();
        $this->assertTrue($dict->isEmpty());
        $dict->fromArray($data);
        $this->assertFalse($dict->isEmpty());
        
        // test Dictionary::seek(), Dictionary::current(), Dictionary::key() 
        // Dictionary::next(), Dictionary::valid()
        reset($dict);
        foreach($data as $key => $value) {
            $tmpKey = $dict->key();
            $this->assertEquals($key, $tmpKey);
            $tmpValue = $dict->current();
            $this->assertEquals($value, $tmpValue);            
            $this->assertTrue($dict->valid());
            $dict->next();
        }
        $this->assertFalse($dict->valid());
        $this->assertNull($dict->key());
        $this->assertNull($dict->current());
        $dict->next();
        $this->assertFalse($dict->valid());
        $this->assertNull($dict->key());
        $this->assertNull($dict->current());        
        $dict->rewind();
        $this->assertTrue($dict->valid());
        $this->assertFalse(is_null($dict->key()));
        $this->assertFalse(is_null($dict->current()));
 
        foreach($data as $key => $value) {
            $dict->seek($key);
            $tmpKey = $dict->key();
            $this->assertEquals($key, $tmpKey);
            $tmpValue = $dict->current();
            $this->assertEquals($value, $tmpValue);            
            $this->assertTrue($dict->valid());
        }
        
        $dict->seek(null);
        $this->assertFalse($dict->valid());
        $this->assertNull($dict->key());
        $this->assertNull($dict->current());
        $dict->rewind();
        reset($data);
        $this->assertTrue($dict->valid());
        $this->assertEquals(key($data), $dict->key());
        $this->assertEquals($data[$dict->key()], $dict->current());

        $dict->rewind();
        $key = $dict->key();
        $this->assertTrue($dict->offsetExists($key));
        $this->assertFalse($dict->offsetExists($data[$key]));
        $this->assertEquals($data[$key], $dict->offsetGet($key));
        $this->assertNull($dict->offsetGet(null));
        
        $result = $dict->offsetSet($key, 'myTestValue');
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertEquals('myTestValue', $dict->offsetGet($key));
        
        $result = $dict->offsetSet('test', 'otherValue');
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertEquals('otherValue', $dict->offsetGet('test'));
        $this->assertTrue($dict->containsKey('test'));
    }    
    
    /**
     * Test Dictionary;containsKey(), Dictionary;containsValue()
     */
    public function testContainsKeyValue()
    {
        $data = $this->generateData();
        $keys = array_keys($data);
        $values = array_values($data);
        $firstKey = array_shift($keys);
        $firstValue = array_pop($values);
        
        $dict = new Dictionary();
        $this->assertFalse($dict->containsKey($firstKey));
        $this->assertFalse($dict->containsValue($firstValue));
        
        $dict->fromArray($data);
        $this->assertTrue($dict->containsKey($firstKey));
        $this->assertTrue($dict->containsValue($firstValue));
        $this->assertFalse($dict->containsKey($firstValue));
        $this->assertFalse($dict->containsValue($firstKey));

        $dict->clear();
        $this->assertFalse($dict->containsKey($firstKey));
        $this->assertFalse($dict->containsValue($firstValue));
        
    }
    
    /**
     * Test Dictionary::keys(), Dictionary::values()
     */
    public function testKeysValues()
    {
        $data = $this->generateData();
        $dict = new Dictionary();
        $keys = $dict->keys();
        $values = $dict->values();
        
        $this->assertTrue(is_array($keys));
        $this->assertTrue(is_array($values));
        $this->assertEmpty($keys);
        $this->assertEmpty($values);
        
        $dict->fromArray($data);
        $keys = $dict->keys();
        $values = $dict->values();
        $this->assertTrue(is_array($keys));
        $this->assertTrue(is_array($values));
        $this->assertEquals($keys, array_keys($data));
        $this->assertEquals($values, array_values($data));
    }
    
    /**
     * Test Dictionary::remove()
     */
    public function testRemove()
    {
        $data = $this->generateData();
        $dict = new Dictionary($data);
        
        $this->assertEquals(count($data), $dict->count());
        $count = $dict->count();
        foreach($data as $key => $value) {
            $count--;
            $result = $dict->remove($key);
            $this->assertEquals(self::CLASS_NAME, get_class($result));
            $this->assertEquals($count, $dict->count());
        }
        $this->assertTrue($dict->isEmpty());
        $result = $dict->remove('non_existing_key');
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertTrue($dict->isEmpty());
        
        // test remove with protection
        $dict = new Dictionary($data);
        $dict->protect();
        $this->assertTrue($dict->isReadOnly());
        foreach($data as $key => $value) {
            $result = $dict->remove($key);
            $this->assertEquals(self::CLASS_NAME, get_class($result));
            $this->assertEquals(count($data), $dict->count());
        }
        $dict->unprotect();
        $this->assertFalse($dict->isReadOnly());
    }
    
    /**
     * Test Dictionary::appendFrom()
     */
    public function testAppendFrom()
    {
        $data = $this->generateData();
        $dict = new Dictionary();
        $dict->fromArray($data);
        $this->assertFalse($dict->isEmpty());
        $this->assertEquals(count($data), $dict->count());
        unset($dict);
        
        $segments = array_chunk($data, count($data) / 2, true);
        $dict1 = new Dictionary($segments[0]);
        $dict2 = new Dictionary($segments[1]);
        
        // append from dictionary
        $dict1->appendFrom($dict2);
        $this->assertEquals(count($data), $dict1->count());
        $this->assertEquals($data, $dict1->toArray());

        // append from array
        $dict2->fromArray($dict1->toArray());
        $this->assertEquals(count($data), $dict2->count());
        
        // append existing keys
        $dict1 = new Dictionary($segments[0]);
        $dict1->appendFrom($segments[0]);
        $this->assertEquals(count($segments[0]), $dict1->count());
        $this->assertEquals($segments[0], $dict1->toArray());
        
        foreach (array_keys($segments[0]) as $key) {
            $segments[0][$key] = md5($key);
        }
        $dict1->appendFrom($segments[0]);
        $this->assertEquals(count($segments[0]), $dict1->count());
        $this->assertEquals($segments[0], $dict1->toArray());
    }
    
    /**
     * Test Dictionary::equals()
     */
    public function testEquals()
    {
        $data = $this->generateData();
        $dict1 = new Dictionary();
        $dict2 = new Dictionary();
        
        $this->assertTrue($dict1->isEmpty());
        $this->assertTrue($dict2->isEmpty());
        $this->assertTrue($dict1->equals($dict2));
        $this->assertTrue($dict2->equals($dict1));
        
        $dict1->fromArray($data);
        $this->assertFalse($dict1->isEmpty());
        $this->assertFalse($dict1->equals($dict2));
        $this->assertFalse($dict2->equals($dict1));
        
        $dict2->fromArray($data);
        $this->assertTrue($dict1->equals($dict2));
        $this->assertTrue($dict2->equals($dict1));

        $dict1->add('test');
        $this->assertFalse($dict1->equals($dict2));
        $this->assertFalse($dict2->equals($dict1));        
    }
    
    /**
     * Generate test data
     * @return array
     */
    protected function generateData()
    {
        $limit = 10;
        $data = array();
        for($i = 0; $i < $limit; $i++) {
            $data['key_' . $i] = 'value_' . $i;
        }
        return $data;
    }
}
