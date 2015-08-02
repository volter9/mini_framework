<?php

class ViewTest extends TestCase {
    
    /**
     * Templates data
     */
    public function templates () {
        view\storage('settings.template', 'default');
        
        return array(
            array('index', array('default', 'index')),
            array(
                'hello:is/it/me/you/looking/for', 
                array('hello', 'is/it/me/you/looking/for')
            ),
            array(
                app\base_path('hey/there'),
                array('default', app\base_path('hey/there'))
            ),
            array(
                'hello:' . app\base_path('hey/there'),
                array('hello', app\base_path('hey/there'))
            )
        );
    }
    
    /**
     * Views data
     */
    public function views () {
        return array(
            array(
                'default', 'abc', 
                app\base_path('resources/views/default/html/abc.php')
            ),
            array(
                'default', 'def/abc', 
                app\base_path('resources/views/default/html/def/abc.php')
            ),
            array(
                'default', 'html:abc', 
                app\base_path('resources/views/html/html/abc.php')
            ),
            array(
                '', 'abc', 
                app\base_path('resources/views/abc.php')
            ),
            array(
                '', 'html:abc', 
                app\base_path('resources/views/html/html/abc.php')
            )
        );
    }
    
    /**
     * Views and data
     */
    public function actualViews () {
        return array(
            array(
                'hello_world', array(), 'Hello, world!'
            ),
            array(
                'hello', array('name' => 'Bob'), 'Hello, Bob!'
            )
        );
    }
    
    /**
     * Test for correct parsing template strings
     * 
     * @dataProvider templates
     */
    public function testParsingTemplate ($input, $expected) {
        $this->assertEquals(view\parse_template($input), $expected);
    }
    
    /**
     * Test for correct building of view path
     * 
     * @dataProvider views
     */
    public function testBuildingViewPath ($template, $input, $expected) {
        view\storage('settings.template', $template);
        
        $this->assertEquals(view\path($input), $expected);
    }
    
    /**
     * Viewing actual views
     * 
     * @dataProvider actualViews
     */
    public function testViewingStuff ($view, array $data, $expected) {
        $output = view\capture(function () use ($view, $data) {
            view\view($view, $data);
        });
        
        $this->assertEquals($output, $expected);
    }
    
    /**
     * Viewing actual views
     * 
     * @dataProvider actualViews
     */
    public function testViewingLayout ($view, array $data, $expected) {
        $output = view\capture(function () use ($view, $data) {
            view\layout($view, $data);
        });
        
        $this->assertEquals($output, "<sarcasm>$expected</sarcasm>");
    }
    
}