<?php

class Application_Form_Thread extends Zend_Form
{

    public function init()
    {
        $this->setMethod("post");

        $title = new Zend_Form_Element_Text("title");
        $title->setLabel("Title : ");
        $title->setRequired();
        $title->setAttrib("class", "form-control");
        $title->addFilter(new Zend_Filter_StripTags);

        $body = new Zend_Form_Element_Textarea("body");
        $body->setLabel("Body : ");
        $body->setRequired();
        $body->setAttrib('cols', '40');
        $body->setAttrib('rows', '10');
        $body->setAttrib("class", "form-control");
        $body->addFilter(new Zend_Filter_StripTags);

        $image = new Zend_Form_Element_File("image");
        $image->addValidator(new Zend_Validate_File_Size(2048 * 1024));
        $image->addValidator(new Zend_Validate_File_IsImage());
        $image->setValueDisabled(true);
        $image->addValidator('Count', false, 1);
        $image->setLabel("Choose Image");


        $id = new Zend_Form_Element_Hidden("id");
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setLabel("Post");
        $submit->setAttrib("class", "btn btn-primary ")
                ->setAttrib("style", "margin-left: 350px; border-top-width: 4px; margin-top: 10px");
        $this->addElements(array($id, $title, $body, $image, $submit));
    }


}

