<?php

class ValidationTest extends TestCase {
    
    public function data () {
        $rules = array(
            'name' => 'required|alpha_dash',
            'age'  => 'required|is_numeric',
            'date' => 'required'
        );
        
        return array(
            array(
                $rules,
                array(
                    'name' => 'peter_abc',
                    'age'  => '25',
                    'date' => 'Some bullshit data'
                ),
                true
            ),
            array(
                $rules,
                array(
                    'name' => 'Peter Parker',
                    'age'  => 'adsa',
                    'date' => ''
                ),
                false
            ),
            array(
                $rules,
                array(
                    'name' => 'Peter_Parker',
                    'age'  => 'adsa',
                    'date' => 'abc'
                ),
                false
            )
        );
    }
    
    /**
     * Test validating set of data
     * 
     * @dataProvider data
     */
    public function testValidating ($rules, $data, $expected) {
        $this->assertEquals(validation\validate($data, $rules), $expected);
    }
    
    /**
     * Test validating a field
     */
    public function testValidatingAField () {
        $result = validation\validate_field('required', '');
        
        $this->assertTrue($result !== true);
    }
    
}