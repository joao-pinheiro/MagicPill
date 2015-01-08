<?php
/**
 * MagicPill
 *
 * Copyright (c) 2014, 2015 Joao Pinheiro
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
 * @package    Util\Debug
 * @copyright  Copyright (c) 2015 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Util\Debug;

class Profiler extends MagicPill\Core\Object
{
    protected $profileEventStack = array();
    
    /**
     * Starts profiling a specific event
     * @param string $eventName
     * @return \Weduc\Debug\Profiler
     */
    public function startEvent($eventName)
    {
        if (!isset($this->profileEventStack[$eventName])) {
            $this->profileEventStack[$eventName] = array();
        }
        $this->profileEventStack[$eventName][] = array(
            'start' => microtime(true),
            'end' => null,
            'elapsedTime' => null,
            'payload' => ''
        );
        return $this;
    }
    
    /**
     * Stops profiling a previously started event
     * 
     * @param string $eventName
     * @param string $payload
     * @return \Weduc\Debug\Profiler
     */
    public function stopEvent($eventName, $payload = '')
    {
        if (isset($this->profileEventStack[$eventName])) {
            $event = array_pop($this->profileEventStack[$eventName]);
            $event['end'] = microtime(true);
            $event['payload'] = $payload;
            $event['elapsedTime'] = $event['end'] - $event['start'];
            $this->profileEventStack[$eventName][] = $event;
        }
        return $this;
    }
    
    /**
     * Returns a stack of results for a given event
     * 
     * @param string $eventName
     * @return array
     */
    public function getResults($eventName)
    {
        return isset($this->profileEventStack[$eventName]) 
            ? $this->profileEventStack[$eventName] 
            : array();
        
    }
    
    /**
     * Removes an event from the stack
     * 
     * @param string $eventName
     * @return \Weduc\Debug\Profiler
     */
    public function remove($eventName)
    {
        if (isset($this->profileEventStack[$eventName])) {
            unset($this->profileEventStack[$eventName]);
        }
        return $this;
    }
    
    /**
     * Clears all event data
     * @return Core\Debug\Profiler
     */
    public function reset()
    {
        $this->profileEventStack = array();
        return $this;
    }
}