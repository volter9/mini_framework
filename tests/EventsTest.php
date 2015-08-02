<?php

/**
 * Events test
 */
class EventsTest extends TestCase {
    
    /**
     * Test binding events
     */
    public function testBinding () {
        events\bind('foo', function () {
            $this->assertTrue(true);
        });
        
        events\bind('bar', function ($bool) {
            $this->assertTrue($bool);
        });
        
        $this->assertCount(1, events\storage('foo'));
    }
    
    /**
     * Test emitting events
     */
    public function testEmitting () {
        events\emit('foo');
        events\emit('bar', true);
    }
    
}