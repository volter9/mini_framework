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
                array('GET', '6', '/cool/route')
            ),
            array(
                '/cool/route',
                array('*', '6', '/cool/route')
            )
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
            actions\controller(
                'index', '\resources\actions\friends',
                app\app_path('resources/actions/friends')
            )
        );
        
        router\map(
            'GET posts /posts/', 
            actions\controller(
                'index', '\posts\actions',
                app\app_path('resources/actions/posts')
            )
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
    
}