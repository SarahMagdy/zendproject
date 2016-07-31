<?php

class CategoriesController extends Zend_Controller_Action
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
            $this->is_admin = $user[0]["is_admin"];
       }
       
            $state_model = new Application_Model_SystemStatus();
            $sys_state=$state_model->getSystemState();
    
         //   file_put_contents(dirname(__FILE__) . "/text.html",$sys_state); 
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
        $form  = new Application_Form_Category();
       
       if($this->_request->isPost()){
            if($form->isValid($this->_request->getParams()))
            {
                $category_info = $form->getValues();
             
                $category_model = new Application_Model_Categories();
   
                $ext = pathinfo($category_info["image"], PATHINFO_EXTENSION);
                $upload = new Zend_File_Transfer_Adapter_Http();  
                $upload->setDestination(dirname(__FILE__) . "/../../public/category_images/");
                $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $category_info["name"].'.'.$ext)));                  
                $upload->receive();
                $category_info["image"]=$category_info["name"].'.'.$ext;
                $category_model->addCategory($category_info);
                $this->redirect("categories/list");

               }       
           }
       
         $this->view->ed = 0;
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

        $d = $this->_request->getParam("d");
        if (!empty($id)) {
            $imgCat = $this->_request->getParam("catImg");
             unlink(dirname(__FILE__) . "/../../public/category_images/".$imgCat);
            $forum_model = new Application_Model_Forums();
            $delImg = $forum_model->getForumsByCategoryId($id);
            for ($index = 0; $index < count($delImg); $index++) 
            {
                $imgForum = $delImg[$index]['image'];
                $formId = $delImg[$index]['id'];
                unlink(dirname(__FILE__) . "/../../public/forum_images/".$imgForum);
                $thread = new Application_Model_Threads();
                $delImgThread = $thread->getThreadsByForumId($formId);
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
            }

            $Category_model = new Application_Model_Categories();
            $Category_model->deleteCategory($id);
        }
       
        if($d ==-1)
        {
        
           $this->redirect("categories/main");
        }
        if($d ==-2)
        {
         $this->redirect("categories/list");
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
        $e= $this->_request->getParam("e");
        $form  = new Application_Form_Category(); 
        $form->getElement("image")->setRequired(false);
        $form->getElement("name")->removeValidator("Db_NoRecordExists");
        $this->view->ed = 1;
        $this->view->form = $form;

        if($this->_request->isPost())
        {
           if($form->isValid($this->_request->getParams()))
            {
               $category_info = $form->getValues();
               $category_model = new Application_Model_Categories();
               if($category_info["image"] !="")
               {
                    $category_model = new Application_Model_Categories();
                    $forum = $category_model->getCategoryById($id);
                  
                    $imgName= $forum[0]['image'];
                    unlink(dirname(__FILE__) . "/../../public/category_images/".$imgName);
                    $ext = pathinfo($category_info["image"], PATHINFO_EXTENSION);
                    $upload = new Zend_File_Transfer_Adapter_Http();  
                    $upload->setDestination(dirname(__FILE__) . "/../../public/category_images");
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $category_info["name"].'.'.$ext)));                  
                    $upload->receive();
                    $category_info["image"]=$category_info["name"].'.'.$ext;
               }
               $category_model->editCategory($category_info); 
               if($e == -1)
               {
               $this->redirect("categories/main");
               }
               if($e == -2)
               {
               $this->redirect("categories/list");
               }
               
           }
        }
        if (!empty($id)) 
        {
            $category_model = new Application_Model_Categories();
            $forum = $category_model->getCategoryById($id);
            $form->populate($forum[0]);
        } 
        else 
        {
            if($e ==-1)
               {
               $this->redirect("categories/main");
               }
               if($e ==-2)
               {
               $this->redirect("categories/list");
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
        $category_model = new Application_Model_Categories();
        $this->view->categories = $category_model->listCategory();
         }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }
    
      public function mainAction()
    {
         if($this->is_admin==1)
          {
             $this->view->admin =1;
          }
         
       $category_model = new Application_Model_Categories();
        $this->view->categories = $category_model->listCategory();
        $forum_model = new Application_Model_Forums();
        $this->view->forums = $forum_model->listForum();
    }
    
    public function lockforumAction()
    {
         if($this->is_admin==1)
          {
        $forum_model = new Application_Model_Forums();
        $id = $this->_request->getParam("id");
        $lock = $this->_request->getParam("lock");
        $forum_model->lockForum($id,$lock);
         $this->redirect("categories/main");
          }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }
    
}

