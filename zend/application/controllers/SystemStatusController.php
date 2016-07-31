<?php

class SystemStatusController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
     public function switchAction()
    {
        // action body
        $system_model = new Application_Model_SystemStatus();
        $status = $system_model->getSystemState();
        if($status == 1){
             $system_model->editSystemStatus(array("is_closed"=>0));
             $this->view->data = 0;
        }else{
            $system_model->editSystemStatus(array("is_closed"=>1));
            $this->view->data = 1;
        }
         $this->_helper->layout->disableLayout();
    }


}

