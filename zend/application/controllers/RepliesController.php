<?php

class RepliesController extends Zend_Controller_Action
{   
    private $user_id = -1;
    private $is_banned;
    private $is_admin = 0;

    public function init()
    {
        /* Initialize action controller here */
        $authorization = Zend_Auth::getInstance();
       if($authorization->hasIdentity()) 
       {           
            $storage = $authorization->getStorage();
            $this->user_id =  $storage->read()->id;
            $user_model = new Application_Model_Users();
            $user = $user_model->getUserById($this->user_id);
            $this->is_banned = $user[0]["is_banned"];
            $this->is_admin = $user[0]["is_admin"];          
        }
        $state_model = new Application_Model_SystemStatus();
        $sys_state = $state_model->getSystemState();
        if ($sys_state == 1 && $is_admin == 0) {
            $this->redirect("error/sysoff");
        }
    }

    public function indexAction()
    {
        // action body
    }

    public function addAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->is_banned != 1 && $this->user_id != -1) {
            $threadId = $this->getRequest()->getParam("id");
            
            $form = new Application_Form_Reply();

            $reply_model = new Application_Model_Replies();


            if ($this->getRequest()->isPost() && $threadId) {
                if ($form->isValid($this->getRequest()->getParams())) {
                    $reply_info = $form->getValues();
    //                unset($reply_info["image"]);

                    $reply_info["thread_id"] = $threadId;
                    $reply_info["user_id"] = $this->user_id;

                    if ($reply_info["image"] != NULL) {
                        $ext = pathinfo($reply_info["image"], PATHINFO_EXTENSION);
                                        file_put_contents(dirname(__FILE__) . "/text.html", $ext);
                        $string = rand();
                        $arr = range('a', 'z');
                        $x = rand(0, sizeof($arr) - 1);
                        $y = rand(0, sizeof($arr) - 1);
                        $z = rand(0, sizeof($arr) - 1);
                        $string .= $arr[$x] . $arr[$y] . $arr[$z] . '.' . $ext;
                        $ext = pathinfo($reply_info["image"], PATHINFO_EXTENSION);
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination(dirname(__FILE__) . "/../../public/reply_images");
                        $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $string)));
                        $upload->receive();
                        $reply_info["image"] = $string;
                    } 
                    if($reply_model->addReply($reply_info)){
                        //$this->forward("add", "Replies", null, array("id" => $id)); // redirect($this->getHelper()->baseUrl() . "/id/$id", array("method" => "get"));
                        $form = new Application_Form_Reply();                

                        $db = $reply_model->getAdapter();
                        $reply_id = $db->lastInsertId();

                        $this->view->reply = $reply_model->getReplyById($reply_id);
                    }
                }
            }


            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }

            $this->view->form = $form;
        }
        else{
            $this->redirect("error/notauth");
        }
    }

    public function editAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $this->getRequest()->getParam("id");
            $form = new Application_Form_Reply();
            $form->getElement("submit")->setLabel("Edit Reply");
            $reply_model = new Application_Model_Replies();
            $reply = $reply_model->getReplyById($id);

            if ($this->getRequest()->isPost() && $id) {

                if ($form->isValid($this->getRequest()->getParams())) {

                    $reply_info = $form->getValues();
                    $reply[0]['body'] = $reply_info['body'];
                    if ($reply_info["image"] != NULL) {
                        $ext = pathinfo($reply_info["image"], PATHINFO_EXTENSION);
                        $string = rand();
                        $arr = range('a', 'z');
                        $x = rand(0, sizeof($arr) - 1);
                        $y = rand(0, sizeof($arr) - 1);
                        $z = rand(0, sizeof($arr) - 1);
                        $string .= $arr[$x] . $arr[$y] . $arr[$z] . '.' . $ext;
                        $ext = pathinfo($reply_info["image"], PATHINFO_EXTENSION);
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination(dirname(__FILE__) . "/../../public/reply_images");
                        $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $string)));
                        $upload->receive();
                        if($reply[0]["image"] != NULL){
                            unlink(dirname(__FILE__) . "/../../public/reply_images/".$reply[0]['image']);
                        }

                        $reply[0]["image"] = $string;
                    }

                    if($reply_model->editReply($reply[0])){
                        $this->view->data = array("success" => 1, "message"=> "updated successfully");
                    }  else {
                        $this->view->data = array("success" => 0, "message"=> "unable to edit reply");
                    }
                }
            }  else {
                $form->populate($reply[0]);

            }
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }

            $this->view->reply = $reply;
            $this->view->form = $form;
        }
        else{
            $this->redirect("error/notauth");
        }
    }

    public function deleteAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $this->getRequest()->getParam("id");
            $reply_model = new Application_Model_Replies();
            if ($this->getRequest()->isXmlHttpRequest()) {
                $this->_helper->layout->disableLayout();
            }
            $reply = $reply_model->getReplyById($id);
            if($reply[0]["image"] != NULL){
                unlink(dirname(__FILE__) . "/../../public/reply_images/".$reply[0]['image']);
            }
            if($reply_model->deleteReply($id)){
                $this->view->data = array("success" => 1, "message" => "Deleted Successfully");
            }  else {            
                $this->view->data = array("success" => 0, "message" => "unable to Delete");
            }
        }
        else{
            $this->redirect("error/notauth");
        }
    }


}







