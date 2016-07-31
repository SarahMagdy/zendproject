<?php

class ForumsController extends Zend_Controller_Action
{
    private $user_id = -1;
    private $is_admin=0;


    public function init()
    {
        /* Initialize action controller here */
        $authorization = Zend_Auth::getInstance();
       if($authorization->hasIdentity()) 
       {           
            $storage = $authorization->getStorage();
           $this->user_id =  $storage->read()->id;
            $user_model = new Application_Model_Users();
            $user = $user_model->getUserById($this->user_id );
            $this->is_banned = $user[0]["is_banned"];
            $this->is_admin = $user[0]["is_admin"];
       }
       
            $state_model = new Application_Model_SystemStatus();
            $sys_state=$state_model->getSystemState();

            if($sys_state==1 && $this->is_admin==0 )
            {
                $this->redirect("error/sysoff");
            }
                  
    }

    public function indexAction()
    {
        // action body
    }
    
    public function addAction()
    {
        if($this->is_admin==1)
          {
        $form  = new Application_Form_Forum();
       
       if($this->_request->isPost()){
           if($form->isValid($this->_request->getParams())){
               $forum_info = $form->getValues();
             
               $name=$forum_info["name"];
               $catgId=$forum_info["cat_id"];
               $forum_model = new Application_Model_Forums();
               $result=$forum_model->checkForums(array($catgId,$name));
               if($result)
               {
                   echo "There is a forum with the same name in this category";
               }
               else
               {
                  
                    $ext = pathinfo($forum_info["image"], PATHINFO_EXTENSION);
                    $upload = new Zend_File_Transfer_Adapter_Http();  
                    $upload->setDestination(dirname(__FILE__) . "/../../public/forum_images");
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $forum_info["name"].$forum_info["cat_id"].'.'.$ext)));                  
                    $upload->receive();
                    $forum_info["image"]=$forum_info["name"].$forum_info["cat_id"].'.'.$ext;
                    $forum_model->addForum($forum_info);
                    $this->redirect("forums/list/id/$catgId");

               }       
           }
       }
        $this->view->edt = 0;
	$this->view->form = $form;
          }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }

    public function deleteAction()
    {
        if($this->is_admin==1)
          {
        $id = $this->_request->getParam("id");
        if(!empty($id)){
            $img = $this->_request->getParam("img");
            $catId = $this->_request->getParam("catgId");
            $deside= $this->_request->getParam("c");
            unlink(dirname(__FILE__) . "/../../public/forum_images/".$img);
             $thread = new Application_Model_Threads();
                $delImgThread = $thread->getThreadsByForumId($id);
                for ($i = 0; $i < count($delImgThread); $i++) 
                {
                    $imgth = $delImgThread[$i]['image'];
                    $thrId = $delImgThread[$i]['id'];
                    unlink(dirname(__FILE__) . "/../../public/thread_images/".$imgth);
                    $reply = new Application_Model_Replies();
                    $delImgReply = $reply->getRepliesByThreadId($thrId);
                    for ($j = 0; $j < count($delImgReply); $j++) 
                    {
                        $imgrep = $delImgReply[$j]['image'];

                        unlink(dirname(__FILE__) . "/../../public/reply_images/".$imgrep);
                    }
                }
            $forum_model = new Application_Model_Forums();
            $forum_model->deleteForum($id);
        }
         if($deside == 1)
         {
             $this->redirect("categories/main");
         }
         if($deside == 2)
         {
           $this->redirect("forums/list/id/$catId");  
         }
          
            }
        
        else
            {
                $this->redirect("error/notauth");
            }
      
    }

    public function editAction()
    {
        if($this->is_admin==1)
          {
        $id = $this->_request->getParam("id");
        $deside= $this->_request->getParam("c");
        $form  = new Application_Form_Forum(); 
        $form->getElement("image")->setRequired(false);
        $form->removeElement("cat_id");
        $form->removeElement("is_locked");
        $this->view->edt =1;
        $this->view->form = $form;

        if($this->_request->isPost())
        {
           if($form->isValid($this->_request->getParams()))
            {
               $forum_info = $form->getValues();
               $forum_model = new Application_Model_Forums();
               if($forum_info["image"] !="")
               {
                    $forum_model = new Application_Model_Forums();
                    $forum = $forum_model->getForumById($id);
                  
                    $imgName= $forum[0]['image'];
                    unlink(dirname(__FILE__) . "/../../public/forum_images/".$imgName);
                    $ext = pathinfo($forum_info["image"], PATHINFO_EXTENSION);
                    $upload = new Zend_File_Transfer_Adapter_Http();  
                    $upload->setDestination(dirname(__FILE__) . "/../../public/forum_images");
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $forum_info["name"].$forum[0]["cat_id"].'.'.$ext)));                  
                    $upload->receive();
                    $forum_info["image"]=$forum_info["name"].$forum[0]["cat_id"].'.'.$ext;
               }
               $forum_model->editForum ($forum_info); 
                $catId = $this->_request->getParam("catgId");
             if($deside == 1)
         {
             $this->redirect("categories/main");
         }
         if($deside == 2)
         {
           $this->redirect("forums/list/id/$catId");  
         }
           }
        }
        if (!empty($id)) 
        {
            $forum_model = new Application_Model_Forums();
            $forum = $forum_model->getForumById($id);
            $form->populate($forum[0]);
        } 
        else 
        {
            $catId = $this->_request->getParam("catgId");
             if($deside == 1)
         {
             $this->redirect("categories/main");
         }
         if($deside == 2)
         {
           $this->redirect("forums/list/id/$catId");  
         }
        }  
        
    
	$this->render('add');
          }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }

    public function listAction()
    {
        if($this->is_admin==1)
          {
             $this->view->admin =1;
          }
        $forum_model = new Application_Model_Forums();
        $id = $this->_request->getParam("id");
        $this->view->forums = $forum_model->getForumsByCategoryId($id);
         $catg_model = new Application_Model_Categories();
        $this->view->cat = $catg_model->getCategoryById($id);
        
    }
    
    public function lockAction()
    {
        if($this->is_admin==1)
          {
        $forum_model = new Application_Model_Forums();
        $id = $this->_request->getParam("id");
        $lock = $this->_request->getParam("lock");
        $catId = $this->_request->getParam("catgId");
        $this->view->forums = $forum_model->lockForum($id,$lock);
        $this->redirect("forums/list/id/$catId");
          }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }


}

