<?php 
App::import('Model', 'Wildflower.WildUser');

class UserTestCase extends CakeTestCase {
    public $fixtures = array(
        'plugin.wildflower.wild_user'
    );
    private $User;
    
    function startTest() {
    	$this->User = ClassRegistry::init('Wildflower.WildUser');
    }
    
    function endTest() {
    	unset($this->User);
    }
    
    function testValidSave() {
    	$data[$this->User->name] = array('name' => 'Pajtas', 'login' => 'pajtas',
    	   'email' => 'pajtas@klevo.sk', 
    	   'password' => 'hgfgha', 'confirm_password' => 'hgfgha');
        $this->User->create($data);
        $result = $this->User->save();
        
        unset($result[$this->User->name]['created'], $result[$this->User->name]['updated']);
        $data[$this->User->name]['password'] = sha1($data[$this->User->name]['password']);
        
        $this->assertEqual($data, $result);
    }
    
    function testInvalidSave() {
    	$data[$this->User->name] = array('name' => 'Pajtas', 'login' => '_pajtas',
    	   'email' => 'pajtas@klevo.sk', 
    	   'password' => 'hgfgha', 'confirm_password' => 'hgfgha');
        $this->User->create($data);
        $result = $this->User->save();
        $this->assertFalse($result);
        
        $data[$this->User->name] = array('name' => 'Pajtas', 'login' => 'pajtas',
           'email' => 'pajtas@klevo.sk', 
           'password' => 'hgfgha123', 'confirm_password' => 'hgfgha');
        $this->User->create($data);
        $result = $this->User->save();
        $this->assertFalse($result);
        
        $data[$this->User->name] = array('name' => 'Pajtas', 'login' => '<>',
           'email' => 'pajtas@klevo.sk', 
           'password' => 'hgfgha123', 'confirm_password' => 'hgfgha');
        $this->User->create($data);
        $result = $this->User->save();
        $this->assertFalse($result);
    }
    
    function testSuccefulAuthenticate() {
    	$result = $this->User->authenticate('klevo', 'article123');
    	$this->assertTrue(!empty($result));
    	
    	$result = $this->User->authenticate('admin', 'čýáíľššýíáč');
    	$this->assertTrue(!empty($result));
    }
    
    function testUnsuccefulAuthenticate() {
    	$result = $this->User->authenticate('klevo_', 'article123');
    	$this->assertTrue(empty($result));
    	
        $result = $this->User->authenticate('admin', ' čýáíľššýíáč');
        $this->assertTrue(empty($result));
    }
    
    function testSaveAndAuthenticate() {
    	// Create a new user
    	$data[$this->User->name] = array('name' => 'Uriah Heep', 'login' => 'uriah',
           'email' => 'uriah@heep.tk', 
           'password' => 'never2late', 'confirm_password' => 'never2late');
        $result = $this->User->save($data);
        
    	// Try to authenticate
    	$result = $this->User->authenticate('uriah', 'never2late');
        $this->assertTrue(!empty($result));
    }
}
