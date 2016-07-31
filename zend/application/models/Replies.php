<?php

class Application_Model_Replies extends Zend_Db_Table_Abstract
{
    protected $_name = "replies";
    
    function addReply($data){
        $row = $this->createRow();
        $row->image = $data['image'];
        $row->body = $data['body'];
        $row->user_id = $data['user_id'];
        $row->thread_id = $data['thread_id'];
        return $row->save();
    }
    
    function listReply(){
        
        return $this->fetchAll()->toArray();
    }
    
    function getReplyById($id){
        return $this->find($id)->toArray();
    }
            
    function editReply($data){
    
        $this->update($data, "id=".$data['id']);
        return $this->fetchAll()->toArray();
    }
    
    function deleteReply($id){
        return $this->delete("id=$id");
    }
    
    function getRepliesByThreadId($thread_id) {
        
        $replies = $this->select()->where("thread_id = $thread_id");
        return $this->fetchAll($replies)->toArray();
    }



}

