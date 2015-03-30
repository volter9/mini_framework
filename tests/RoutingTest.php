<?php

class RoutingTest extends PHPUnit_Framework_TestCase {
	
	public function setItUp () {
		router('supress', true);
		router('settings.root', '');
	}
	
	public function testRouting () {
		$this->setItUp();
		
		route('GET #index /', 'app/actions/index');
		route('GET #test /test', 'app/actions/test');
		route('GET #testParams /test/:any', 'app/actions/test:test_params');
		
		$this->assertCount(3, router('routes'));
	}
	
	public function testFetching () {
		$route = fetch_route('', 'GET');
		
		$this->assertEquals($route['route']['url'], '');
	}
	
	public function testUrl () {
		$this->assertEquals(url('#index'), '/');
	}
	
	public function testUrlWithParams () {
		$this->assertEquals(
			url('#testParams', ['test']),
			'/test/test'
		);
	}
	
}