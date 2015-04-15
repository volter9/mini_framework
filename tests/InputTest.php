<?php

class InputTest extends PHPUnit_Framework_TestCase {
	
	protected function setUpMethod ($method) {
		$_SERVER['REQUEST_METHOD'] = strtoupper($method);
	}
	
	protected function setUpPost () {
		$this->setUpMethod('post');
		
		$_POST = array(
			'some' => 'test',
			'data' => implode(',', range(1, 10))
		);
	}
	
	protected function setUpGet () {
		$this->setUpMethod('get');
		
		$_GET = array(
			'another' => 'test',
			'data' => implode(',', range(20, 30))
		);
	}
	
	public function testPostInput () {
		$this->setUpPost();
		
		$this->assertTrue(input('some') !== false);
		$this->assertFalse(input('something_else'));
	}
	
	public function testGetInput () {
		$this->setUpGet();
		
		$this->assertTrue(input('another') !== false);
		$this->assertFalse(input('something_else'));
	}
	
	public function sessionData () {
		return array(
			array('username', 'peter'),
			array('password', '123456')
		);
	}
	
	/**
	 * @dataProvider sessionData
	 */
	public function testSessionGetSet ($key, $value) {
		session($key, $value);
		
		$this->assertEquals(session($key), $value);
	}
	
}