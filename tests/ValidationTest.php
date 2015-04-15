<?php

class ValidationTest extends PHPUnit_Framework_TestCase {
	
	protected function setMeUp () {
		load_api('validation');
		
		$config = config('tests/resources/validation');
		
		validation_init();
		validation_rules($config('rules'));
		validation_fields($config('fields'));
	}
	
	public function getValidData () {
		return array(
			'username' => 'cool_dood', // yeah, dude
			'password' => '123456',
			'confirm' => '123456',
			'mail' => 'vasya.pupkin@gmail.com'
		);
	}
	
	public function getInvalidData () {
		return array(
			'username' => 'modasdrfdd13123das',
			'password' => '123456123456dad asdasd asd asd as dasd12312 ',
			'confirm' => '123456~',
			'mail' => 'abc'
		);
	}
	
	public function testValidData () {
		$this->setMeUp();
		
		$data = $this->getValidData();
		
		$this->assertTrue(validate($data), validation_errors(true));
	}
	
	public function testInvalidData () {
		$data = $this->getInvalidData();
		
		$this->assertFalse(validate($data));
	}
	
	public function testNewValidator () {
		add_validator(
			'contains', 
			function ($value, $array, $content) {
				return strpos($value, $content) !== false;
			}, 
			'Field "%s" should contain string "%s"!'
		);
		
		validation_rules([
			'username' => 'required|contains:abc'
		]);
		
		$data = $this->getInvalidData();
		
		$this->assertFalse(validate($data));
	}
	
}