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
                    'name' => 'index',
                    'namespace' => '\\actions\\abc'
                )
            ),
            array(
                'actions/abc:page',
                array(
                    'file' => 'actions/abc', 
                    'name' => 'page',
                    'namespace' => '\\actions\\abc'
                )
            ),
            array(
                'actions/abc:\cool\page',
                array(
                    'file' => 'actions/abc',
                    'name' => 'page',
                    'namespace' => '\cool'
                )
            ),
            array('is_int', 'is_int'),
            array($action, $action)
        );
    }
    
    public function routes () {
        return array(
            array('', 'GET'),
            array('home', 'GET'),
            array('page/1', 'GET'),
            array('friends', 'GET'),
            array('posts', 'GET')
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
        
        router\map(
            'GET friends /friends/', 
            app\app_path('resources/actions/friends:index')
        );
        
        router\map(
            'GET posts /posts/', 
            app\app_path('resources/actions/posts:\posts\actions\index')
        );
        
        $this->assertCount(5, router\storage('routes'));
    }
    
    /**
     * Test dispatching routes
     * 
     * @dataProvider routes
     */
    public function testDispatchingRoutes ($url, $method) {
        $this->assertTrue(router\dispatch(
            router\find($url, $method)
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