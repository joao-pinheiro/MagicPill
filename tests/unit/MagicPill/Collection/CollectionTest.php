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

use MagicPill\Collection\Collection;

class CollectionTest extends \MagicPill\Test\TestCase
{
    const CLASS_NAME = 'MagicPill\Collection\Collection';
    
    /**
     * Test Constructor
     */
    public function testConstructor()
    {   
        // test empty collection
        $collection = new Collection();
        $this->assertTrue($collection->isEmpty());
        $this->assertFalse($collection->isReadOnly());        
        $this->assertEquals(0, $collection->count());
        $this->assertEmpty($collection->toArray());
        
        // test with array
        $data = array('item1', 'item2', 'item3');
        $collection = new Collection($data);
        $this->assertFalse($collection->isEmpty());
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());
        
        // test with constructon initialization
        $otherCollection = new Collection($data);
        $collection = new Collection($otherCollection);
        $this->assertFalse($collection->isEmpty());
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());
    }
    
    /**
     * Test Collection::fromArray(), Collection::toArray()
     */
    public function testFromArrayToArray()
    {
        $limit = 100;        
        $data = array();
        for($i = 0; $i < $limit; $i++) {
            $data[] = md5($i);
        }
        
        // empty collection
        $collection = new Collection();
        $this->assertEquals(0, $collection->count());
        
        // fromArray collection
        $result = $collection->fromArray($data);
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        
        // check every entry
        for($i = 0; $i < $limit; $i++) {
            $this->assertEquals($data[$i], $collection[$i]);
        }
        
        // test Collection::toArray()
        $arrayCollection = $collection->toArray();
        $this->assertTrue(is_array($arrayCollection));
        $this->assertEquals(count($data), count($arrayCollection));
        $this->assertEquals($data, $arrayCollection);
        
        $collection->clear();
        $this->assertEquals(array(), $collection->toArray());
    }
    
    /**
     * Test Collection::add()
     */
    public function testAdd()
    {
        $limit = 100;
        $data = array();        
        $collection = new Collection();

        for ($i = 0; $i < $limit; $i++) {
            $item = md5($i);
            $data[] = $item;
            $result = $collection->add($item);
            $this->assertEquals(self::CLASS_NAME, get_class($result));
        }
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());
        
        for ($i = 0; $i < $limit; $i++) {
            $this->assertEquals($data[$i], $collection[$i]);
        }
    }

    /**
     * Test Collection::Clear()
     */
    public function testClear()
    {
        $limit = 100;
        $collection = new Collection();
        
        $this->assertEquals(0, $collection->count());
        $this->assertFalse($collection->isReadOnly());
        
        for ($i = 0; $i < $limit; $i++) {
            $result = $collection->add($i);
        }
        
        // test count
        $this->assertEquals($limit, $collection->count());
        $collection->clear();
        $this->assertEquals(0, $collection->count());
        
        // test read only
        $result = $collection->protect();
        $this->assertTrue($collection->isReadOnly());
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        
        $result = $collection->clear();
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(self::CLASS_NAME, get_class($result));
    }
   
    /**
     * Test Collection::containsValue()
     */
    public function testContainsValue()
    {
        $limit = 10;
        $data = array();
        $collection = new Collection();
        
        $this->assertFalse($collection->containsValue(null));
        $this->assertFalse($collection->containsValue(1));
        $this->assertFalse($collection->containsValue('item'));
        
        for ($i = 0; $i < $limit; $i++) {
            $collection->add(md5($i));
        }
        
        $this->assertTrue($collection->containsValue(md5(1)));
        $this->assertTrue($collection->containsValue(md5($limit -1)));
        
        // checks random values against the list of $limit items
        for($i = 0; $i < ($limit * 10); $i++) {
            $rnd = rand(0, $limit * 10);
            $this->assertEquals($rnd < $limit, $collection->containsValue(md5($rnd)));
        }
    }
    
    /**
     * Test Collection::getHashCode()
     */
    public function testGetHashCode()
    {
        $collection = new Collection();
        $hash = $collection->getHashCode();
        
        $this->assertNotNull($hash);
        $this->assertNotEmpty($hash);
        $this->assertTrue(strlen($hash) > 8);
        $this->assertEquals($hash, $collection->getHashCode());
    }
    
    /**
     * Test Collection::isReadOnly()
     */
    public function testIsReadOnly()
    {
        $collection = new Collection();
        $collection->add('item1');
        
        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->isReadOnly());
        
        $result = $collection->protect();
        $this->assertTrue($collection->isReadOnly());
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $collection->add('item2');
        $this->assertEquals(1, $collection->count());
        
        $result = $collection->unprotect();
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $collection->add('item2');
        $this->assertEquals(2, $collection->count());
        
        $collection->protect();
        $collection->clear();
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(0, $collection->count());
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        
    }

    /**
     * Test Collection::isEmpty()
     */
    public function testIsEmpty()
    {
        $collection = new Collection();
        $this->assertTrue($collection->isEmpty());
        $collection->add('item');
        $this->assertFalse($collection->isEmpty());
        $collection->clear();
        $this->assertTrue($collection->isEmpty());        
    }
    
    /**
     * Tests Collection::seek(), Collection::current(), Collection::key(),
     * Collection::valid(), Collection::next(), Collection::rewind(), 
     * Collection::get(), Collection::offsetGet(), Collection::offsetExists()
     */
    public function testArrayContent()
    {
        $limit = 10;
        $collection = new Collection();
        $this->assertTrue($collection->isEmpty());

        for ($i = 0; $i < $limit; $i++) {
            $collection->add(md5($i));
        }
        // test Collection::seek(), Collection::current(), Collection::key()
        $this->assertFalse($collection->isEmpty());         
        for ($j = 0; $j < ($limit * 2); $j++) {
            $i = rand(0, $limit - 1);
            $result = $collection->seek($i);
            $this->assertEquals(self::CLASS_NAME, get_class($result));
            $this->assertEquals(md5($i), $collection->current());
            $this->assertEquals($i, $collection->key());
        }       
        
        // test upper and lower boundaries
        $collection->seek($limit - 1);
        $collection->seek($limit);
        $this->assertEquals($limit - 1, $collection->key());
        $this->assertEquals(md5($limit - 1), $collection->current());
        $collection->seek(0);
        $collection->seek(-15);        
        $this->assertEquals(0, $collection->key());
        $this->assertEquals(md5(0), $collection->current());
        
        // test Collection::valid(), Collection::key()
        $collection->seek($limit - 1);
        $this->assertTrue($collection->valid());
        $this->assertEquals($limit - 1, $collection->key());
        $collection->pop(); // remove last item, invalidate offset
        $this->assertFalse($collection->valid());
        $this->assertNull($collection->key());
        $collection->add(md5($limit - 1)); // add a new last item
        $this->assertTrue($collection->valid());
        $this->assertEquals($limit - 1, $collection->key());
        $this->assertEquals(md5($limit - 1), $collection[$limit - 1]);
        
        // test Collection::rewind(), Collection::next()
        $collection->seek($limit - 1);
        $result = $collection->rewind();
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertEquals(0, $collection->key());
        $result = $collection->next();
        $this->assertEquals(self::CLASS_NAME, get_class($result));        
        $this->assertEquals(1, $collection->key());
        $this->assertEquals(md5(1), $collection->current());
        
        $i = $limit * 2; // upper bound
        while ($collection->valid() && ($i > 0)) {
            $i--;
            $collection->next();
        }
        $this->assertFalse($collection->valid());
        $this->assertTrue($i > 0);
        $this->assertTrue($i == ($limit + 1));
        $collection->rewind();
        $this->assertEquals(0, $collection->key());
        
        
        // test Collection::get(), Collection::offsetGet()
        for($i = 0; $i < $limit; $i++) {
            $item = md5($i);
            $this->assertEquals($item, $collection->get($i));
            $this->assertEquals($item, $collection[$i]);
        }
        $this->assertNull($collection->get($limit));
        $this->assertNull($collection[$limit]);
        $this->assertNull($collection->get(-5));
        $this->assertNull($collection[-5]);
        
        // test Collection::offsetExists()
        $this->assertTrue($collection->offsetExists(0));
        $this->assertTrue($collection->offsetExists($limit - 1));
        $this->assertFalse($collection->offsetExists($limit));
        $this->assertFalse($collection->offsetExists(-3));
        $collection->pop();
        $this->assertFalse($collection->offsetExists($limit - 1));

        // clear collection
        $collection->clear();
        $this->assertEquals(0, $collection->count());
    }

    /**
     * Test Collection::OffsetSet(), Collection::OffsetUnset(), Collection::OffsetExists()
     */
    public function testOffsetSetUnset()
    {
        $limit = 10;
        $collection = new Collection();
        $this->assertEquals(0, $collection->count());
        
        for ($i = 0; $i < $limit; $i++) {
            $item = 'item_' . $i;
            $collection->offsetSet($i, $item);
            $this->assertEquals($i, $collection->count());
            $this->assertNull($collection->offsetGet($i));
            $this->assertNull($collection[$i]);
            
            // add item, creating valid offset
            $collection->add($item);
            $collection->offsetSet($i, md5($item));
            $this->assertEquals(md5($item), $collection->offsetGet($i));
            $this->assertEquals(md5($item), $collection[$i]);
        }
        
        $count = $collection->count();
        for ($j = 0; $j < $limit; $j++) {
            $i = rand(0, $limit - 1);
            $exists = $collection->offsetExists($i);
            if ($exists) {
                $count--;                
                $collection->offsetUnset($i);
                $this->assertEquals($count, $collection->count());
            }
        }
        
        $collection->clear();
        $collection->fromArray(array('item1', 'item2', 'item3'));
        $this->assertEquals(3, $collection->count());
        $collection->offsetUnset(1);
        $this->assertEquals('item3', $collection->offsetGet(1));
        $this->assertEquals('item3', $collection[1]);
        $this->assertTrue($collection->offsetExists(1));
        
        $collection->offsetUnset(1);
        $this->assertFalse($collection->offsetExists(1));
        $this->assertNull($collection->offsetGet(1));
    }
    
    /**
     * Test Collection::count()
     */
    public function testCount()
    {
        $collection = new Collection();
        $this->assertEquals(0, $collection->count());
        $collection->add('item1');
        $this->assertEquals(1, $collection->count());
        $collection->add('item2');        
        $this->assertEquals(2, $collection->count());
        
        $collection->pop();
        $this->assertEquals(1, $collection->count());
        $collection->add('item3');
        $this->assertEquals(2, $collection->count());        
        $collection->offsetUnset(0);
        $this->assertEquals(1, $collection->count());
        $collection->clear();
        $this->assertEquals(0, $collection->count());
    }
    
    
    /**
     * Test Collection::pop(), Collection::shift(), Collection::unshift(), Collection::push()
     */
    public function testShiftPop()
    {
        $data = array('item1', 'item2', 'item3', 'item4');
        $collection = new Collection();
        $this->assertNull($collection->pop());
        $this->assertNull($collection->shift());
        
        $collection->fromArray($data);
        $this->assertEquals(count($data), $collection->count());
        
        // test Collection::pop(), Collection::shift()
        $item = $collection->pop();
        $this->assertEquals(count($data) - 1, $collection->count());
        $this->assertEquals($data[count($data) - 1], $item);
        $item = $collection->shift();
        $this->assertEquals(count($data) - 2, $collection->count());
        $this->assertEquals($data[0], $item);
        
        // test read only operations on pop, shift, unshift, push
        $fixedCount = $collection->count();
        $collection->protect();
        $this->assertNull($collection->pop());
        $this->assertNull($collection->shift());
        $this->assertEquals($fixedCount, $collection->count());
        $collection->unshift('test1');
        $this->assertEquals($fixedCount, $collection->count());
        $collection->push('test2');
        $this->assertEquals($fixedCount, $collection->count());
        
        // test Collection::unshift()
        $collection->unprotect();
        $result = $collection->unshift('test');
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertEquals($fixedCount + 1, $collection->count());
        $this->assertTrue($collection->offsetExists(0));
        $this->assertEquals('test', $collection[0]);
        $this->assertEquals($data[1], $collection[1]);

        // test Collection::push()
        $fixedCount = $collection->count();
        $result = $collection->push('push_test');
        $this->assertEquals(self::CLASS_NAME, get_class($result));
        $this->assertEquals($fixedCount + 1, $collection->count());
        $item = $collection->pop();
        $this->assertEquals('push_test', $item);
    }

    /**
     * Test Collection::AppendFrom()
     */
    public function testAppendFrom()
    {
        $set1 = array('orange', 'banana', 'pear');
        $set2 = array('duck', 'pigeon', 'falcon');
        
        $col1 = new Collection();
        $col2 = new Collection();
        $this->assertEquals(0, $col1->count());
        $col1->fromArray($set1);
        $this->assertEquals(count($set1), $col1->count());
        $col1->appendFrom($col2); // empty collection
        $this->assertEquals(count($set1), $col1->count());
        $col2->fromArray($set2);
        $col1->appendFrom($col2);
        $this->assertEquals(count($set1) + count($set2), $col1->count());
        
        $merge = array_merge($set1, $set2);
        $this->assertEquals($merge, $col1->toArray());
    }

    /**
     * Tests Collection::equals()
     */
    public function testEquals()
    {
        $set1 = array('orange', 'banana', 'pear');
        $set2 = array('duck', 'pigeon', 'falcon');
        
        $col1 = new Collection();
        $col2 = new Collection();
        $this->assertTrue($col1->equals($col2));
        $this->assertTrue($col2->equals($col1));
        
        $col1->fromArray($set1);
        $this->assertFalse($col1->equals($col2));
        $this->assertFalse($col2->equals($col1));
        $col2->fromArray($set2);
        $this->assertFalse($col1->equals($col2));
        $this->assertFalse($col2->equals($col1));
        
        $col1->clear();
        $col1->fromArray($set2);
        $this->assertTrue($col1->equals($col2));
        $this->assertTrue($col2->equals($col1));        
    }
}
