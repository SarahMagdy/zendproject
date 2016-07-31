<?php

class Application_Form_Forum extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod("post");
        $this->setAttrib("class","form-inline");
        
        $name = new Zend_Form_Element_Text("name");
        
        $name->setAttrib("class", "form-control");
        $name->setLabel("Title");
        $name->setRequired();
        $name->setAttrib("placeholder", "Forum Title");
        $name->addValidator(new Zend_Validate_Alnum(TRUE));
        $name->addFilter(new Zend_Filter_StripTags);     
        
       
         $image = new Zend_Form_Element_File("image");
         $image->addValidator(new Zend_Validate_File_Size(2048*1024));
         $image->addValidator(new Zend_Validate_File_IsImage());
         $image->setValueDisabled(true);
         $image->addValidator('Count', false, 1);
         $image->setLabel("Choose Image");
         $image->setRequired();
       
         $is_locked=new Zend_Form_Element_Radio("is_locked");
         $is_locked->setLabel("Forum Status ");
         $is_locked->setAttrib("name", "is_locked");
         $is_locked->setRequired(true);
         $is_locked->setMultiOptions(array('0' => 'Not Lock',
        '1' => 'Lock'));
         
         $cats=new Application_Model_Categories();
         $allCat=$cats->listCategory();
         $names[""]="Select Category";
         foreach ($allCat as $key => $value) 
         {
             $names[$value['id']] =  $value["name"];
         }
               
         $cat_id=new Zend_Form_Element_Select("cat_id");
         $cat_id->setAttrib("name", "cat_id");
         $cat_id->setLabel('Category ');
         $cat_id->setAttrib("class", "form-control");
         $cat_id->setMultiOptions($names);
         $cat_id->setRequired(true);
                         
        $id = new Zend_Form_Element_Hidden("id");
        
        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setAttrib("class", "btn btn-primary")
                 ->setLabel("Add")
                ->setAttrib("style", "margin-left: 180px; border-top-width: 4px; margin-top: 10px");
        
        $this->addElements(array($id,$name,$image,$cat_id,$is_locked,$submit));
    }


}

