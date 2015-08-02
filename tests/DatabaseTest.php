<?php

class DatabaseTest extends TestCase {
    
    public function data () {
        return array(
            array(
                array('title' => 'title', 'text' => 'text'),
                array('title' => 'hello', 'text' => 'world!')
            ),
            
            array(
                array('title' => 'Long title', 'text' => 'Test some text, yo!'),
                array('title' => 'Cool title', 'text' => 'wow...')
            )
        );
    }
    
    /**
     * @dataProvider data
     */
    public function testCRUD ($data, $new_data) {
        $id = $this->create($data);
        
        $this->assertTrue($id > 0);
        
        $item = $this->read($id);
        
        $this->assertEquals($item, $data);
        
        $this->assertTrue($this->update($id, $new_data) > 0);
        $this->assertTrue($this->delete($id));
    }
    
    /**
     * Create a data in database
     * 
     * @param array $data
     * @return int
     */
    public function create ($data) {
        return db\insert('posts', $data);
    }
    
    /**
     * Read item from database
     * 
     * @param array $data
     * @return int
     */
    public function read ($id) {
        return db\select('
            SELECT title, text
            FROM posts
            WHERE id = ?',
            array($id), true
        );
    }
    
    /**
     * Update item by id in database
     * 
     * @param array $data
     * @return int
     */
    public function update ($id, $data) {
        return db\update('posts', $data, array(
            'id[=]' => $id
        ));
    }
    
    /**
     * Remove an item from database
     * 
     * @param int $id 
     * @return bool
     */
    public function delete ($id) {
        return db\delete('posts', array(
            'id[=]' => $id
        ));
    }
    
}