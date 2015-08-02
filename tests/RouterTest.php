<?php

/**
 * Routing test
 */
class RouterTest extends TestCase {
    
    /**
     * Test adding routes
     */
    public function testAddingRoutes () {
        $callback = function () {
            return true;
        };
        
        router\map('GET index /', $callback);
        router\map('GET home /home/', $callback);
        router\map('GET page /page/:num', $callback);
        
        $this->assertCount(3, router\storage('routes'));
    }
    
    /**
     * Test dispatching routes
     */
    public function testDispatchingRoutes () {
        $this->assertTrue(router\dispatch(
            router\find('home', 'GET')
        ));
        
        $this->assertTrue(router\dispatch(
            router\find('', 'GET')
        ));
        
        $this->assertTrue(router\dispatch(
            router\find('page/1', 'GET')
        ));
    }
    
    /**
     * Test generating URL's
     */
    public function testGeneratingRoutesURLs () {
        $this->assertEquals(router\url('home') ,'/home');
        $this->assertEquals(router\url('index') ,'/');
        $this->assertEquals(router\url('page', array(1)) ,'/page/1');
    }
    
    /**
     * Test generating routes with different base path which 
     * is set in router\storage('settings.root')
     */
    public function testGeneratingRoutesWithDifferentBasePath () {
        router\storage('settings.root', 'mini');
        
        $this->assertEquals(router\url('home') ,'/mini/home');
        $this->assertEquals(router\url('index') ,'/mini/');
        $this->assertEquals(router\url('page', array(1)) ,'/mini/page/1');
    }
    
}