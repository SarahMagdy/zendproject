<?php

class Application_Model_Threads extends Zend_Db_Table_Abstract
{
    protected $_name = "threads";
    
    function addThread($data){
        $row = $this->createRow();
        $row->title = $data['title'];
        $row->body = $data['body'];
        $row->image = $data['image'];
        $row->user_id = $data['user_id'];
        $row->forum_id = $data['forum_id'];
        return $row->save();
    }
    
    function listThreads(){
        
        return $this->fetchAll()->toArray();
    }
            
    function editThread($data){
        $this->update($data, "id=".$data['id']);
        return $this->fetchAll()->toArray();
    }
    
    function deleteThread($id){
        return $this->delete("id=$id");
    }
    
    function getThreadsByForumId($forum_id) {
        
        $threads = $this->select()->where("forum_id = $forum_id")->order("date DESC");
        return $this->fetchAll($threads)->toArray();
    }
    
    function getThreadsByUserId($user_id) {
        
        $threads = $this->select()->where("user_id = $user_id");
        return $this->fetchAll($threads)->toArray();
    }
    
     function lockthread($id,$lock){
      
        if($lock==0)
        {
            $locked = array(
           'is_locked'      => '1');  
        }
        
        if($lock==1)
        {
            $locked = array(
           'is_locked'      => '0');  
        }
        return $this->update($locked, "id=".$id);
      
    }
    
    function stickthread($id){    
        
        $stiky = array('is_sticky'      => '0');  
        
        $this->update($stiky);
         
        $stiky = array('is_sticky'      => '1'); 
             
         return $this->update($stiky, "id=".$id);
    }
    
    function getThreadById($id){
        return $this->find($id)->toArray();
    }


}

