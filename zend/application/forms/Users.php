<?php

class Application_Form_Users extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod("post");
        $username = new Zend_Form_Element_Text("name");
        $username->setAttrib("class", "form-control");
        $username->setLabel("Username: ");
        $username->setRequired();
//        $username->addFilter(new Zend_Filter_StripTags);
        $username->setAttrib("class", "form-control");

       
        
        $email = new Zend_Form_Element_Text("email");
         $email->setRequired()
                ->setLabel("Email:")
                 ->addValidator(new Zend_Validate_EmailAddress())
                 ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
        'table' => 'users',
        'field' => 'email'
    )
));
         
         $password = new Zend_Form_Element_Password("password");
         $password->setRequired()
                 ->setLabel("Password");
         
       
         $id = new Zend_Form_Element_Hidden("id");
         $submit = new Zend_Form_Element_Submit("submit");
         $this->addElements(array($id,$username,$email,$password,$submit));
    }


}

