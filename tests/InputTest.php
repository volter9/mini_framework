<?php

/**
 * Input module test
 */
class InputTest extends TestCase {
    
    /**
     * Testing if the request is AJAX
     */
    public function testIsAJAX () {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        
        $this->assertTrue(input\is_ajax());
    }
    
    /**
     * Testing if request method is POST
     */
    public function testIsPost () {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $this->assertTrue(input\is_post());
    }
    
    /**
     * Test session helper
     */
    public function testSession () {
        input\session('foo', 'bar');
        
        $this->assertEquals(input\session('foo'), 'bar');
        $this->assertEquals(input\session(), array(
            'foo' => 'bar'
        ));
        
        input\session('foo', false);
        
        $this->assertFalse(input\session('foo'));
    }
    
    /**
     * Testing input helper
     */
    public function testInput () {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = array(
            'a' => 10,
            'b' => 20,
            'c' => 'foo'
        );
        
        $this->assertEquals(input\get(), $_POST);
        $this->assertEquals(input\get('c'), 'foo');
    }
    
}