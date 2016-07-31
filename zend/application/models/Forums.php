<?php

class Application_Model_Forums extends Zend_Db_Table_Abstract
{
    protected $_name = "forums";
    
    function addForum($data){
        $row = $this->createRow();
        $row->name = $data['name'];
        $row->image = $data['image'];
        $row->is_locked = $data['is_locked'];
        $row->cat_id = $data['cat_id'];
               
        return $row->save();
    }
    
    function listForum(){
        
        return $this->fetchAll()->toArray();
    }
    
    function getForumById($id){
        return $this->find($id)->toArray();
    }
    
    function getForumsByCategoryId($cat_id)
    {
        $forums = $this->select()->where("cat_id = $cat_id");
        return $this->fetchAll($forums)->toArray();
    }
    
     function checkForums($array)
    {
        $forums = $this->select()
        ->from('forums')
             ->where("cat_id = $array[0]")
             ->where("name= '$array[1]'");
        return $this->fetchAll($forums)->toArray();
    }
    
    function lockForum($id,$lock){
      
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
            
    function editForum($data){
       
        if (empty($data['image'])) {
           unset($data['image']);
        }
        
        if (empty($data['is_locked'])) {
           unset($data['is_locked']);
        }
        
        unset($data['cat_id']);

        $this->update($data, "id=".$data['id']);
        return $this->fetchAll()->toArray();
    }
    
    function deleteForum($id){
        return $this->delete("id=$id");
    }


}

