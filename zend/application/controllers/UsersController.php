
<?php

require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';
?>
    <?php

class UsersController extends Zend_Controller_Action
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
            $user = $user_model->getUserById($this->user_id);
            $this->is_admin = $user[0]["is_admin"];
       }
       
            $state_model = new Application_Model_SystemStatus();
            $sys_state=$state_model->getSystemState();
    
         //   file_put_contents(dirname(__FILE__) . "/text.html",$sys_state); 
            if($sys_state==1 && $this->is_admin==0 )
            {
                $this->redirect("error/sysoff");
            }
                  
//        if(!$authorization->hasIdentity()) 
//        {           
//           
//        }
            
       }  
  
    public function indexAction()
    {
        // action body
    }
    
    
    public function typeAction() {
        if($this->is_admin==1)
          {
        $user_model = new Application_Model_Users();
        $id = $this->_request->getParam("id");
        $type = $this->_request->getParam("mytype");
        $this->view->users = $user_model->typeUser($id, $type);
        $this->redirect("users/list");
          }
        
        else
            {
                $this->redirect("error/notauth");
            }
        
    }

    public function deleteAction() {
        if($this->is_admin==1)
          {
            
        $id = $this->_request->getParam("id");
        if (!empty($id)) {
            $img = $this->_request->getParam("img");
            $sigImg = $this->_request->getParam("sig");
            unlink(dirname(__FILE__) . "/../../public/profile_images/".$img);
            unlink(dirname(__FILE__) . "/../../public/signture_images/".$sigImg);
            $thread = new Application_Model_Threads();
                $delImgThread = $thread->getThreadsByUserId($id);
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
            $user_model = new Application_Model_Users();
            $user_model->deleteUser($id);
        }
        $this->redirect("users/list");
        
          }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }
    
    
     public function profileAction()
    {
        if($this->user_id != -1)
        {
            
        $view_model = new Application_Model_Users();
        $this->view->profile = $view_model->getUserById($this->user_id)[0];
        }
           else
            {
                $this->redirect("categories/main");
            }
        
    }
    
     public function userprofileAction()
    {
        if($this->user_id != -1)
        {
            
        $id = $this->_request->getParam("id");
        $view_model = new Application_Model_Users();
        $this->view->profile = $view_model->getUserById($id)[0];
        
        }
           else
            {
                $this->redirect("categories/main");
            }
        
    }
    
    public function listAction()
    {
        if($this->user_id!= -1)
          {
             if($this->is_admin==1)
          {
             $this->view->admin =1;
             $this->view->adminId =  $this->user_id;
          }
        $user_model = new Application_Model_Users();
        $this->view->users = $user_model->listUsers();
         }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }
    

    public function editAction() {
      
        if ($this->user_id != -1) 
        {

            $form = new Application_Form_Registration();
             $identity = $this->_request->getParam("admin"); 

            if ($this->user_id != -1 && $this->is_admin != 1) {
                $id = $this->user_id;
            } elseif ($this->is_admin == 1 && $identity == -1) {
                $id = $this->user_id;
            } elseif ($this->is_admin == 1 && $identity == -2) {
                $id = $this->_request->getParam("id");
            }
            $form->getElement("password")->setRequired(false);
        $form->getElement("image")->setRequired(false);
        $form->getElement("email")->removeValidator("Db_NoRecordExists");
        $form->getElement("name")->removeValidator("Db_NoRecordExists");
        $form->getElement("signature")->setRequired(false);
        $form->getElement("register")->setLabel("Done");
       $form->getElement("register")->setAttrib("style", "margin-left: 200px; border-top-width: 4px; margin-top: 10px");
        $form->removeElement("email");
        $user_model = new Application_Model_Users();
        $nam=$user_model->getUserById($id)[0]["name"];
         $this->view->nam = $nam;
         $this->view->us = 1;
        $this->view->form = $form;

        if ($this->_request->isPost()) {
            
            if ($form->isValid($this->_request->getParams())) {
                $user_info = $form->getValues();
                $user_model = new Application_Model_Users();
                
                 if($user_info["image"] !="" && $user_info["signature"] !="" )
               {
                    $user_model = new Application_Model_Users();
                    $users = $user_model->getUserById($id);
                    var_dump($users);
                    $imgName= $users[0]['image'];
                    unlink(dirname(__FILE__) . "/../../public/profile_images/".$imgName);
                    $signatureName= $users[0]['signature'];
                    unlink(dirname(__FILE__) . "/../../public/signture_images/".$signatureName);
                $ext = array(pathinfo($user_info["image"], PATHINFO_EXTENSION),pathinfo($user_info["signature"], PATHINFO_EXTENSION));
                $img = array($user_info["name"],"sign".$user_info["name"]);
                $des = array("profile_images","signture_images");
                $upload = new Zend_File_Transfer_Adapter_Http();
                
                
              $files  = $upload->getFileInfo();
              $i=0;
                foreach($files as $file => $fileInfo)
                {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $upload->setDestination(dirname(__FILE__) . "/../../public/".$des[$i]);
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $img[$i] . '.' . $ext[$i])));
                    $upload->receive($file);
                    $i++;
                }

                            $user_info["image"] = $img[0]. '.' . $ext[0];
                            $user_info["signature"] = $img[1]. '.' . $ext[1];
               }
                
                if($user_info["image"] !="" && $user_info["signature"] == "" )
               {
                    $user_model = new Application_Model_Users();
                    $users = $user_model->getUserById($id);
                    var_dump($users);
                    $imgName= $users[0]['image'];
                    unlink(dirname(__FILE__) . "/../../public/profile_images/".$imgName);
                    $ext = pathinfo($user_info["image"], PATHINFO_EXTENSION);
                    $upload = new Zend_File_Transfer_Adapter_Http();  
                    $upload->setDestination(dirname(__FILE__) . "/../../public/profile_images");
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $user_info["name"].'.'.$ext))); 
                    $files  = $upload->getFileInfo();
          
                foreach($files as $file => $fileInfo)
                {
                    $upload->receive($file);
                }
                    $user_info["image"]=$user_info["name"].'.'.$ext;
               }
               
               if($user_info["signature"] !="" && $user_info["image"] == "")
               {
                    $user_model = new Application_Model_Users();
                    $users = $user_model->getUserById($id);
                  
                    $signatureName= $users[0]['signature'];
                    unlink(dirname(__FILE__) . "/../../public/signture_images/".$signatureName);
                    $ext = pathinfo($user_info["signature"], PATHINFO_EXTENSION);
                    $upload = new Zend_File_Transfer_Adapter_Http();  
                    $upload->setDestination(dirname(__FILE__) . "/../../public/signture_images");
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => "sign".$user_info["name"].'.'.$ext))); 
                    $files  = $upload->getFileInfo();
                    foreach($files as $file => $fileInfo)
                {
                    $upload->receive($file);
                }
                    $user_info["signature"]="sign".$user_info["name"].'.'.$ext;
               }
             
                $user_model->editUser($user_info);
               
                 if ($this->user_id != -1 && $this->is_admin != 1) {
                $this->redirect("users/profile");
            } elseif ($this->is_admin == 1 && $identity == -1) {
                $this->redirect("users/profile");
            } elseif ($this->is_admin == 1 && $identity == -2) {
             $this->redirect("users/list");
            }
            }
        }
            if (!empty($id)) {
                $user_model = new Application_Model_Users();
                $user = $user_model->getUserById($id);
               
                $form->populate($user[0]);
            } else
            {
                $this->redirect("users/list");
            }
        
        $this->render('add');
    }
    else
            {
                $this->redirect("error/notauth");
            }
    }


    public function loginAction() {
         if($this->user_id == -1)
        {

        $login_form = new Application_Form_Login();
        
        $this->view->login = $login_form;

          if($this->_request->isPost()){
           if($login_form->isValid($this->_request->getParams())){
            $email = $this->_request->getParam('email');
            $password = $this->_request->getParam('password');
            
            $user_model = new Application_Model_Users();
            $user = $user_model->getUserByEmail($email);
            $is_confirmed = $user[0]["is_confirmed"];
            if($is_confirmed == 1)
            {          
            $db = Zend_Db_Table::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users', 'email', 'password');

            $authAdapter->setIdentity($email);
            $authAdapter->setCredential(md5($password));
            $result = $authAdapter->authenticate();
            if ($result->isValid()) {
                $auth = Zend_Auth::getInstance();
                $storage = $auth->getStorage();
                $storage->write($authAdapter->getResultRowObject(array('id')));
                $this->redirect("categories/main");
            }
        }
        else
        {
             $this->redirect("error/checkmail");
        }
        }
          }
           }
        
        else
            {
                $this->redirect("categories/main");
            }
    }
    
    
     public function banAction()
    {
         if($this->is_admin==1)
          {
        $users_model = new Application_Model_Users();
        $id = $this->_request->getParam("id");
        $ban = $this->_request->getParam("ban");
        $this->view->users = $users_model->banuser($id,$ban);
        $this->redirect("users/list");
         }
        
        else
            {
                $this->redirect("error/notauth");
            }
    }

    public function registerAction() {
        if($this->user_id == -1)
        {
        $register_model = new Application_Model_Users();
        $form = new Application_Form_Registration();
        
        $this->view->register = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {

                $data = $form->getValues();
                
                $data['password'] = md5($data['password']);
                
                $ext = array(pathinfo($data["image"], PATHINFO_EXTENSION),pathinfo($data["signature"], PATHINFO_EXTENSION));
                $img = array($data["name"],"sign".$data["name"]);
                $des = array("profile_images","signture_images");
                $upload = new Zend_File_Transfer_Adapter_Http();
                
                
              $files  = $upload->getFileInfo();
              $i=0;
                foreach($files as $file => $fileInfo)
                {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $upload->setDestination(dirname(__FILE__) . "/../../public/".$des[$i]);
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $img[$i] . '.' . $ext[$i])));
                    $upload->receive($file);
                    $i++;
                }

                            $data["image"] = $img[0]. '.' . $ext[0];
                            $data["signature"] = $img[1]. '.' . $ext[1];
                            
                              $register_model->insert($data);
                $email=$data['email'];
                $id=$register_model->getIdByEmail($email);
               

                $config = array('ssl' => 'ssl',
                    'port' => '465',
                    'auth' => 'login',
                    'username' => 'zendproject3@gmail.com',
                    'password' => 'emanmohamed');
                $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);


                $mail = new Zend_Mail();

                $mail->setType(Zend_Mime::MULTIPART_RELATED);

                $mail->setFrom('zendproject3@gmail.com', 'Sky');
                $mail->addTo($data['email'],$data['name']);
                $mail->setSubject('please confirm your registeration');

                $prefix = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                $server_name = $_SERVER['SERVER_NAME'];

                $url = $this->_helper->url->url(
                        array(
                            'controller' => 'users',
                            'action' => 'confirm-email',
                            'id' => $id
                ));

                $full_url = $prefix . "://" . $server_name . $url;
                

                $mail->setBodyText("Please, click the link to confirm your registration => " . "your name: " .
                        $data['name'] . " " . "your country :" . $data['country'] . " " . "your gender :" . $data['gender'] . " " .
                        //$this->getRequest()->getServer('HTTP_ORIGIN') .
                        $full_url
                );


                try {
                    $mail->send($transport);
                    $this->redirect("users/mailchecktrue");
                    echo "Message sent! Check your inbox to confirm your Registration<br />\n";
                    $flag = 0;
                } catch (Exception $ex) {
                    $flag = 1;
                    $register_model->deleteUser($id);
                    $this->redirect("users/mailcheckfalse");
                    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
                    
                }
                }        
}
}
 
        
        else
            {
                $this->redirect("categories/main");
            }

    }

    
     public function confirmEmailAction(){
        $users_model = new Application_Model_Users();
        $id = $this->_request->getParam("id");
        file_put_contents(dirname(__FILE__) . "/text.html",$id);
        $users_model->is_confirmed($id);
            
        }

    public function logoutAction() {
        $ath=Zend_Auth::getInstance();
        if($this->user_id != -1)
        {
        $ath->clearIdentity();
        $this->_redirect('users/login');
        }
        else 
            {
                $this->redirect("error/notauth");
            }
    }
    
    public function adduserAction() {
         if($this->is_admin == 1)
        {
        $form = new Application_Form_adduser();
        $adduser_model = new Application_Model_Users();
        $this->view->adduser = $form;
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {

                $data = $form->getValues();
//                var_dump($data);

                $data['password'] = md5($data['password']);

                $ext = array(pathinfo($data["image"], PATHINFO_EXTENSION), pathinfo($data["signature"], PATHINFO_EXTENSION));
                $img = array($data["name"], "sign" . $data["name"]);
                $des = array("profile_images", "signture_images");
                $upload = new Zend_File_Transfer_Adapter_Http();


                $files = $upload->getFileInfo();
                $i = 0;
                foreach ($files as $file => $fileInfo) {
                    $upload = new Zend_File_Transfer_Adapter_Http();
                    $upload->setDestination("/var/www/html/zend_project2/public/$des[$i]");
                    $upload->addFilter(new Zend_Filter_File_Rename(array('target' => $img[$i] . '.' . $ext[$i])));
                    $upload->receive($file);
                    $i++;
                }

                $data["image"] = $img[0] . '.' . $ext[0];
                $data["signature"] = $img[1] . '.' . $ext[1];

                 $adduser_model->insert($data);
                $email=$data['email'];
                $id=$adduser_model->getIdByEmail($email);
              

                $config = array('ssl' => 'ssl',
                    'port' => '465',
                    'auth' => 'login',
                    'username' => 'zendproject3@gmail.com',
                    'password' => 'emanmohamed');
                $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', $config);


                $mail = new Zend_Mail();

                $mail->setType(Zend_Mime::MULTIPART_RELATED);

                $mail->setFrom('zendproject3@gmail.com', 'Sky');
                $mail->addTo($data['email'],$data['name']);
                $mail->setSubject('please confirm your registeration');

                $prefix = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http';
                $server_name = $_SERVER['SERVER_NAME'];

                $url = $this->_helper->url->url(
                        array(
                            'controller' => 'users',
                            'action' => 'confirm-email',
                            'id' => $id
                ));

                $full_url = $prefix . "://" . $server_name . $url;

                $mail->setBodyText("Please, click the link to confirm your registration => " . "your name: " .
                        $data['name'] . " " . "your country :" . $data['country'] . " " . "your gender :" . $data['gender'] . " " .
                       // $this->getRequest()->getServer('HTTP_ORIGIN') .
                        $full_url
                );

                try {
                    $mail->send($transport);
                    echo "Message sent!  Check your inbox to confirm your Registration<br />\n";
                     $this->redirect("users/mailchecktrue");
                    $flag = 0;
                } catch (Exception $ex) {
                    $flag = 1;
                    $adduser_model->deleteUser($id);
                    $this->redirect("users/mailcheckfalse");
                    echo "Failed to send mail! " . $ex->getMessage() . "<br />\n";
                    
                }

            }
        }
         }
        else 
            {
                $this->redirect("error/notauth");
            }
    }
    
      public function mailchecktrueAction(){
         $this->view->msg="The Email Has Been Sent Check Your Email To Confirm Your Registration";
            
        }
        
          public function mailcheckfalseAction(){
              $this->redirect("users/mailcheckfalse");
              $this->view->msg="Faild To Send The Email ... Please Try Regostration Again";
        }
    
    
   


}

