<?php

class LoaderTest extends PHPUnit_Framework_TestCase {
	
	public function testLoadingPHPFile () {
		load_php('tests/resources/functions');
		
		$this->assertEquals(unit_testing(), FFF_VERSION);
	}
	
}