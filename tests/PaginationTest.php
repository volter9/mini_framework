<?php

class PaginationTest extends TestCase {
    
    public function paginations () {
        return array(
            array(
                16, 4, 2, array(
                    'offset' => 4,
                    'limit'  => 4,
                    'pages'  => 4,
                    'page'   => 2,
                    
                    'pagination' => array(1, 2, 3, 4),
                )
            ),
            
            array(
                40, 4, 1, array(
                    'offset' => 0,
                    'limit'  => 4,
                    'pages'  => 10,
                    'page'   => 1,
                    
                    'pagination' => array(1, 2, 3, 4, 5, 6, 7, 8, 10),
                )
            )
        );
    }
    
    /**
     * Testing generating pagination
     * 
     * @dataProvider paginations
     */
    public function testGeneratingPagination ($total, $per_page, $page, $expected) {
        $this->assertEquals(
            pagination\generate($total, $per_page, $page), 
            $expected
        );
    }
    
}