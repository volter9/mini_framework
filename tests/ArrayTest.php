<?php

class ArrayTest extends TestCase {
    
    public function getData () {
        return array(
            array(
                array('name' => 'cool'),
                'name', 'cool'
            ),
            
            array(
                array('name' => array('cool' => 'man')),
                'name.cool', 'man'
            ),
            
            array(
                array('name' => 'cool'),
                'foo', false
            )
        );
    }
    
    public function setData () {
        return array(
            array(
                array('name' => 'cool'),
                'name', 'cool'
            ),
            
            array(
                array('name' => 'cool'),
                'foo', 'bar'
            ),
            
            array(
                array('name' => 'cool'),
                'root.beer.in', 'bar'
            )
        );
    }
    
    public function extractData () {
        $data = array('foo' => 1, 'bar' => 2, 'baz' => 3);
        
        return array(
            array($data, array('foo'),          array('foo' => 1)),
            array($data, array('bar', 'zilla'), array('bar' => 2)),
            array($data, array('fooz', 'bard'), array())
        );
    }
    
    public function excludeData () {
        $data = array('foo' => 1, 'bar' => 2, 'baz' => 3);
        
        return array(
            array($data, array('foo'),               array('bar' => 2, 'baz' => 3)),
            array($data, array('bar', 'zilla'),      array('foo' => 1, 'baz' => 3)),
            array($data, array('foo', 'bar', 'baz'), array())
        );
    }
    
    /**
     * @dataProvider getData
     */
    public function testGet ($data, $key, $expected) {
        $this->assertEquals(array_get($data, $key), $expected);
    }
    
    /**
     * @dataProvider setData
     */
    public function testSet ($data, $key, $expected) {
        array_set($data, $key, $expected);
        
        $this->testGet($data, $key, $expected);
    }
    
    /**
     * @dataProvider extractData
     */
    public function testExtract ($data, $keys, $expected) {
        $this->assertEquals(array_extract($data, $keys), $expected);
    }
    
    /**
     * @dataProvider excludeData
     */
    public function testExclude ($data, $keys, $expected) {
        $this->assertEquals(array_exclude($data, $keys), $expected);
    }
    
}