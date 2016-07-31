<?php

class Application_Form_Category extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        
        $this->setMethod("post");
        $this->setAttrib("class","form-inline");
        
        $name = new Zend_Form_Element_Text("name");
        
        $name->setAttrib("class", "form-control");
        $name->setLabel("Name  ");
        $name->setRequired();
        $name->setAttrib("placeholder", "Category Name ");
        $name->addValidator(new Zend_Validate_Alnum(TRUE));
        $name->addFilter(new Zend_Filter_StripTags); 
        $name->addValidator(new Zend_Validate_Db_NoRecordExists(array(
        'table' => 'categories',
        'field' => 'name'
    )
));
        
       
        $image = new Zend_Form_Element_File("image");
        $image->addValidator(new Zend_Validate_File_Size(2048*1024));
        $image->addValidator(new Zend_Validate_File_IsImage());
        $image->setValueDisabled(true);
        $image->addValidator('Count', false, 1);
        $image->setLabel("Choose Image");
        $image->setRequired();
                                
        $id = new Zend_Form_Element_Hidden("id");
        
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setAttrib("class", "btn btn-primary")
                ->setLabel("Add")
                ->setAttrib("style", "margin-left: 180px; border-top-width: 4px; margin-top: 10px");
        
        $this->addElements(array($id,$name,$image,$submit));
    }


}

