<?php

class Application_Form_Reply extends Zend_Form
{

    public function init()
    {
      $this->setMethod("post");
        $this->setAttrib("class","form-horizontal");
        
        $reply = new Zend_Form_Element_Textarea("body");
        $reply->setAttrib('cols', '40');
        $reply->setAttrib('rows', '3');
        $reply->setRequired();
        $reply->addFilter(new Zend_Filter_StripTags);
        $reply->setAttrib("class", "form-control");
        
        $image = new Zend_Form_Element_File("image");
        $image->addValidator(new Zend_Validate_File_Size(2048 * 1024));
        $image->addValidator(new Zend_Validate_File_IsImage());
        $image->setValueDisabled(true);
        $image->addValidator('Count', false, 1);
        $image->setLabel("Choose Image");

                         
//        $id = new Zend_Form_Element_Hidden("id");
        
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Add Reply");
        $submit->setAttrib("class", "btn btn-primary");
        
        $this->addElements(array($reply, $image, $submit));
    }


}

