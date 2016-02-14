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

namespace MagicPill\Test\Collection;

use MagicPill\Collection\Collection;
use MagicPill\Collection\HashDictionary;


class HashDictionaryTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME = 'MagicPill\Collection\HashDictionary';
    const CLASS_NAME_DICTIONARY = 'MagicPill\Collection\Dictionary';    
    /**
     * Tests HashDictionary::add()
     */
    public function testAdd()
    {
        $data = $this->generateData();
        $hash = new HashDictionary();
        $this->assertTrue($hash->isEmpty());
        $hash->fromArray($data);
        $this->assertEquals(count($data), $hash->count());
        $this->assertEquals($data, $hash->toArray());

        $hash = new HashDictionary($data);
        $this->assertEquals(count($data), $hash->count());
        $this->assertEquals($data, $hash->toArray());

        $hash = new HashDictionary();
        $hash->add('top', 'key1', 'value1');
        $hash->add('top', 'key2', 'value2');
        $hash->add('othertop', 'otherkey', 'value');
        $this->assertEquals(2, $hash->count());
        $element = $hash->get('top');
        $this->assertEquals(self::CLASS_NAME_DICTIONARY, get_class($element));
        $this->assertEquals(array('key1' => 'value1', 'key2' => 'value2'), $element->toArray());
        $element = $hash->get('othertop');
        $this->assertEquals(array('otherkey' => 'value'), $element->toArray());
    }

    /**
     * Test HashDictionary::fromArray()
     */
    public function testFromArray()
    {
        $data = $this->generateData();
        $hash = new HashDictionary();
        $hash->fromArray($data);
        $this->assertEquals(count($data), $hash->count());
        $this->assertEquals($data, $hash->toArray());
    }
    
    /**
     * Test HashDictionary::equals()
     */
    public function testEquals()
    {
        $data = $this->generateData();
        $col1 = new HashDictionary();
        $col2 = new HashDictionary();
        $this->assertTrue($col1->equals($col2));
        $col1->fromArray($data);
        $this->assertFalse($col1->equals($col2));
        $col2->fromArray($data);
        $this->assertTrue($col1->equals($col2));
        $this->assertTrue($col2->equals($col1));
        $col1->add('top','akey', 'avalue');
        $this->assertFalse($col1->equals($col2));
        $this->assertFalse($col2->equals($col1));        
    }
    
    /**
     * Test HashDictionary::appendFrom() 
     */
    public function testAppendFrom()
    {
        $set1 = array(
            'top1' => array('key1' => 'value1', 'key2' => 'value2')
        );
        $set2 = array(
            'top2' => array('key1' => 'value1', 'key2' => 'value2')
        );
        $hash1 = new HashDictionary($set1);
        $hash2 = new HashDictionary($set2);
        $merge = array_merge($set1, $set2);
        $hash1->appendFrom($hash2);
        $this->assertEquals($merge, $hash1->toArray());
        // repeat append, duplicate data should be ignored
        $hash1->appendFrom($hash2);
        $this->assertEquals($merge, $hash1->toArray());
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
        
        $leaf = array();
        while ($elementLimit > 0) {
            $leaf['element_' . $elementLimit] = md5($elementLimit);
            $elementLimit--;
        }
        
        for($i = 0; $i < $hashLimit; $i++) {
            $result['hash_' . $i] = $leaf;
        }
        return $result;
    }
}