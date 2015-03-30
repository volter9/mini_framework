<?php

class EventsTest extends PHPUnit_Framework_TestCase {
	
	public function testBinding () {
		$instance = $this;
		
		bind('test', function () use ($instance) {
			$instance->assertTrue(true);
		});
		
		bind('test:2', function () use ($instance) {
			$instance->assertFalse(false);
		});
	}
	
	public function testEmitting () {
		emit('test');
		emit('test:2');
	}
	
	public function testBindingWithArguments () {
		$instance = $this;
		
		bind('test:3', function ($param) use ($instance) {
			$instance->assertEquals($param, 'abc');
		});
		
		bind('test:4', function ($param) use ($instance) {
			$instance->assertEquals($param, 'def');
		});
	}
	
	public function testEmittingWithArguments () {
		emit('test:3', 'abc');
		emit('test:4', 'def');
	}
	
}