<?php

class Application_Model_SystemStatus extends Zend_Db_Table_Abstract
{
     protected $_name = "sys_status";
    
    function editSystemStatus($data){
        $this->update($data, "id=1");
        return $this->fetchAll()->toArray();
    }
    
      function getSystemState(){
        
        return $this->fetchAll()->toArray()[0]['is_closed'];
    }


}

