<?php

/**
 * Routing test
 */
class RouterTest extends TestCase {
    
    public function urls () {
        return array(
            array(
                'GET id /cool/route',
                array('GET', 'id', '/cool/route')
            ),
            array(
                'GET /cool/route',
                array('GET', '-1', '/cool/route')
            ),
            array(
                '/cool/route',
                array('*', '-1', '/cool/route')
            )
        );
    }
    
    public function actions () {
        $action = function () {};
        
        return array(
            array(
                'actions/abc',
                array(
                    'file' => 'actions/abc', 
                    'name' => 'index'
                )
            ),
            array(
                'actions/abc:page',
                array(
                    'file' => 'actions/abc', 
                    'name' => 'page'
                )
            ),
            array('is_int', 'is_int'),
            array($action, $action)
        );
    }
    
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
    
    /**
     * @dataProvider urls
     */
    public function testParsingURL ($url, $expected) {
        $this->assertEquals(router\parse_url($url), $expected);
    }
    
    /**
     * @dataProvider actions
     */
    public function testParsingActions ($action, $expected) {
        $this->assertEquals(router\parse_action($action), $expected);
    }
    
}