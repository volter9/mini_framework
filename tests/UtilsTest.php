<?php

class UtilsTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * Multidimensional stuff
	 */
	
	public function getData () {
		return array(
			array(
				array(
					'foo' => array(
						'bar' => array(
							'def' => 'abc'
						),
						'baz' => array(
							'abc' => 'cool'
						)
					)
				),
				'foo.bar.def',
				'abc'
			)
		);
	}
	
	/**
	 * @dataProvider getData
	 */
	public function testMultidimensionalGet ($data, $key, $expected) {
		$this->assertEquals(md_get($data, $key), $expected);
	}
	
	/**
	 * @dataProvider getData
	 */
	public function testMultidimensionalGetWhichIsNotExistent ($data, $key, $expected) {
		$this->assertFalse(md_get($data, 'foo.foo.foo.foo.foo.foo'));
	}
	
	/**
	 * @dataProvider getData
	 */
	public function testMultidimensionalSet ($data, $key) {
		md_set($data, $key, 'cba');
		
		$this->assertEquals(md_get($data, $key), 'cba');
	}
	
	/**
	 * @dataProvider getData
	 */
	public function testMultidimensionalSetWithNotExistentKey ($data) {
		md_set($data, 'foo.baz.def.abc', 'foo');
		
		$this->assertEquals(md_get($data, 'foo.baz.def.abc'), 'foo');
	}
	
	public function testMultidimensionalSetWithEmptyArray () {
		$data = [];
		
		md_set($data, 'foo.bar.def.abc', 'ghj');
		
		$this->assertEquals(md_get($data, 'foo.bar.def.abc'), 'ghj');
	}
	
	/**
	 * Repo testing
	 */
	
	public function testAaaa () {
		$repo = repo();
		
		$repo('abc', 'def');
		
		$this->assertEquals($repo('abc'), 'def');
	}
	
	public function getKeysValues () {
		return array(
			array('multi.foo.bar.baz', 'foo'),
			array('multi.foo.baz.bar', 'foo')
		);
	}
	
	/**
	 * @dataProvider getKeysValues
	 */
	public function testMutlidimensionalRepo ($key, $value) {
		$repo = repo();
		$repo($key, $value);
		
		$this->assertEquals($repo($key), $value);
	}
	
}