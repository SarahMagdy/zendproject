<?php

class ThreadsController extends Zend_Controller_Action
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
        if ($sys_state == 1 && $this->is_admin == 0) {
            $this->redirect("error/sysoff");
        }
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {
        $thread_model = new Application_Model_Threads();
        $id = $this->_request->getParam("id"); 
        $this->view->threads = $thread_model->getThreadsByForumId($id);
        $forum_model=new Application_Model_Forums();
        $this->view->forum = $forum_model->getForumById($id);
        $this->view->user_id = $this->user_id;
        $this->view->is_admin = $this->is_admin;
        $this->view->is_banned = $this->is_banned;
        $this->view->forum_id = $id;
    } 

    public function deleteAction()
    {
        $id = $this->_request->getParam("id");
        $thread_model = new Application_Model_Threads();
        $thread = $thread_model->getThreadById($id);
        if($this->is_admin == 1 || $this->user_id == $thread[0]["user_id"]){
            if(!empty($id)){
                $forumId = $this->_request->getParam("forumId");
                $img=$this->_request->getParam("img");
                 unlink(dirname(__FILE__) . "/../../public/thread_images/$img");
                  $reply = new Application_Model_Replies();
                    $delImgReply = $reply->getRepliesByThreadId($id);
                    for ($j = 0; $j < count($delImgReply); $j++) 
                    {
                        $imgrep = $delImgReply[$j]['image'];

                        unlink(dirname(__FILE__) . "/../../public/reply_images/".$imgrep);
                    }
                $thread_model = new Application_Model_Threads();
                $thread_model->deleteThread($id);
            }

              $this->redirect("threads/list/id/$forumId");
        }
        else{
            $this->redirect("error/notauth");
        }
    }

    public function lockAction()
    {
        if($this->is_admin == 1){
            $thread_model = new Application_Model_Threads();
            $id = $this->_request->getParam("id");
            $lock = $this->_request->getParam("lock");
            $formId = $this->_request->getParam("formId");
            $this->view->forums = $thread_model->lockthread($id,$lock);
            $this->redirect("threads/list/id/$formId");
        }
        else{
            $this->redirect("error/notauth");
        }
    }

    public function stickyAction()
    {
        if($this->is_admin == 1){
            $thread_model = new Application_Model_Threads();
            $id = $this->_request->getParam("id");
            $formId = $this->_request->getParam("formId");
            $this->view->forums = $thread_model->stickthread($id);
            $this->redirect("threads/list/id/$formId");
        }
        else{
            $this->redirect("error/notauth");
        }
    }

    public function viewThreadAction()
    {
        $id = $this->_request->getParam("id");
        if (!empty($id)) {
            $thread_model = new Application_Model_Threads();
            $thread = $thread_model->getThreadById($id);
            $reply_model = new Application_Model_Replies();
            $replies = $reply_model->getRepliesByThreadId($id);
        }
        $this->view->thread = $thread[0];
        $this->view->replies = $replies;
        $this->view->user_id = $this->user_id;
        $this->view->is_admin = $this->is_admin;
        $this->view->is_banned = $this->is_banned;
    }

   public function addThreadAction()
    {
       if($this->user_id != -1 && $this->is_banned != 1){
            $forum_id = $this->getRequest()->getParam("forum_id");
            $form = new Application_Form_Thread();
            if ($this->_request->isPost() && $forum_id) {
                if ($form->isValid($this->_request->getParams())) {
                    $thread_info = $form->getValues();
                    $thread_info["forum_id"] = $forum_id;
                    $thread_info["user_id"] = $this->user_id;
                    if ($thread_info["image"] != NULL) {
                        $ext = pathinfo($thread_info["image"], PATHINFO_EXTENSION);
                        $string = rand();
                        $arr = range('a', 'z');
                        $x = rand(0, sizeof($arr) - 1);
                        $y = rand(0, sizeof($arr) - 1);
                        $z = rand(0, sizeof($arr) - 1);
                        $string .= $arr[$x] . $arr[$y] . $arr[$z] . '.' . $ext;
                        $ext = pathinfo($thread_info["image"], PATHINFO_EXTENSION);
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination(dirname(__FILE__) . "/../../public/thread_images");
                        $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $string)));
                        $upload->receive();


                        $thread_info["image"] = $string;
                    }
    //                var_dump($thread_info);
                    $thread_model = new Application_Model_Threads();
                   $thread_model->addThread($thread_info);
                   $this->redirect("threads/list/id/$forum_id");
                }
            }

            $this->view->form = $form;
       }else{
            $this->redirect("error/notauth");
       }
 
    }

    public function editAction()
    {
        $id = $this->_request->getParam("id");
        $thread_model = new Application_Model_Threads();
        $thread = $thread_model->getThreadById($id);
        if($this->is_admin == 1 || $this->user_id == $thread[0]["user_id"]){
            $form = new Application_Form_Thread();
            $form->getElement("submit")->setLabel("Done");
            if ($this->_request->isPost()) {
                if ($form->isValid($this->_request->getParams())) {
                    $thread_info = $form->getValues();
                    if ($thread_info["image"] != NULL) {
                        $ext = pathinfo($thread_info["image"], PATHINFO_EXTENSION);
                        $string = rand();
                        $arr = range('a', 'z');
                        $x = rand(0, sizeof($arr) - 1);
                        $y = rand(0, sizeof($arr) - 1);
                        $z = rand(0, sizeof($arr) - 1);
                        $string .= $arr[$x] . $arr[$y] . $arr[$z] . '.' . $ext;
                        $ext = pathinfo($thread_info["image"], PATHINFO_EXTENSION);
                        $upload = new Zend_File_Transfer_Adapter_Http();
                        $upload->setDestination(dirname(__FILE__) . "/../../public/thread_images");
                        $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $string)));
                        $upload->receive();
                        if($thread[0]["image"] != NULL){
                             unlink(dirname(__FILE__) . "/../../public/thread_images/".$thread[0]['image']);
                        }

                        $thread_info["image"] = $string;
                    }else{
                        $thread_info["image"] = $thread[0]["image"];
                    }
                    var_dump($thread_info);
                    $thread_model = new Application_Model_Threads();
                    $thread_model->editThread($thread_info);
                    $this->redirect("threads/view-thread/id/$id");
                }
                if (!empty($id)) {
                    $thread_info = $form->getValues();
                    $form->populate($thread_info);
                } else
                    $this->redirect("threads/view-thread/id/$id");
            }else {
                $thread_model = new Application_Model_Threads();
                $thread = $thread_model->getThreadById($id);
                $form->populate($thread[0]);
            }
            $this->view->form = $form;
        }
        else{
            $this->redirect("error/notauth");
        }
    }
}







