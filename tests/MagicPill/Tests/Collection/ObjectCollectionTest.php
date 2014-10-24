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

namespace MagicPill\Test\Collection;

use MagicPill\Collection\ObjectCollection;

class ObjectCollectionTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'MagicPill\Collection\Collection';
    
    /**
     * Test Constructor
     */
    public function testConstructor()
    {   
        // test empty object collection
        $collection = new ObjectCollection();
        $this->assertTrue($collection->isEmpty());
        $this->assertFalse($collection->isReadOnly());        
        $this->assertEquals(0, $collection->count());
        $this->assertEmpty($collection->toArray());

        // test with array, no type
        $data = $this->getData();
        $collection = new ObjectCollection($data);
        $this->assertFalse($collection->isEmpty());
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());

        // test with array, specific type
        $data = $this->getData();
        $collection = new ObjectCollection($data, 'DateTime');
        $this->assertFalse($collection->isEmpty());
        $this->assertFalse($collection->isReadOnly());
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());
    }
    
    /**
     * Test ObjectCollection::add()
     */
    public function testAdd()
    {
        $data = $this->getData();
        $collection = new ObjectCollection($data, 'DateTime');
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());

        // add different object type, should be ignored
        $testObject = new ObjectCollection();
        $collection->add($testObject);
        $this->assertEquals(count($data), $collection->count());
        
        //no specific type
        $collection = new ObjectCollection($data);
        $collection->add('432');
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());
        
    }
        
    /**
     * Test ObjectCollection:isValidType()
     */
    public function testIsValidType()
    {
        $collection = new ObjectCollection(array(), 'DateTime');
        $this->assertTrue($collection->isValidType(new \DateTime()));
        $this->assertFalse($collection->isValidType(new \StdClass()));
    }
    
    /**
     * Test ObjectCollection::getObjectType()
     */
    public function testGetObjectType()
    {
        $collection = new ObjectCollection(array(), 'DateTime');
        $this->assertEquals('DateTime', $collection->getObjectType());
        
        $collection = new ObjectCollection();
        $this->assertEquals(null, $collection->getObjectType());
    }
    
    /**
     * Test ObjectCollection::appendFrom()
     */
    public function testAppendFrom()
    {
        $data = $this->getData();
        $collection = new ObjectCollection($data);
        $collection2 = new ObjectCollection($data);
        $this->assertEquals(count($data), $collection->count());
        $this->assertEquals($data, $collection->toArray());

        $collection->appendFrom($collection2);
        $this->assertEquals(count($data) * 2, $collection->count());                
    }
    
    /**
     * Test ObjectCollection::equals()
     */
    public function testEquals()
    {
        $data = $this->getData();
        $col1 = new ObjectCollection();
        $col2 = new ObjectCollection();
        $this->assertTrue($col1->equals($col2));
        $col1->fromArray($data);
        $this->assertFalse($col1->equals($col2));
        $col2->fromArray($data);
        $this->assertTrue($col1->equals($col2));
        $this->assertTrue($col2->equals($col1));
        $col1->add(new \DateTime());
        $this->assertFalse($col1->equals($col2));
        $this->assertFalse($col2->equals($col1));        
    }
    
    /**
     * Generates test data
     * @return array
     */
    protected function getData()
    {
        $items = 10;
        $result = array();
        while($items > 0) {
            $items--;
            $result[] = new \DateTime();
        }
        return $result;
    }
}