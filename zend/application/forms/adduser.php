<?php

class Application_Form_adduser extends Zend_Form {

    public function init() {
        /* Form Elements & Other Definitions Here ... */
        $this->setMethod("post");
        $this->setAttrib("class", "form-inline");

        $id = $this->createElement('hidden', 'id');

        $name = $this->createElement('text', 'name');
        $name->setLabel('Name: *')
                ->setAttrib("class", "col-sm-2 control-label")
                ->setRequired(true)
                ->addFilter('StripTags');
        $name->setAttrib("class", "form-control");
        $name->setAttrib("placeholder", "Name");
        $name->addValidator(new Zend_Validate_Alnum(TRUE))
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'users',
                    'field' => 'name'
                        )
        ));

        $email = $this->createElement('text', 'email');
        $email->setLabel('Email: *')
                ->setRequired(true);
        $email->setAttrib("class", "form-control");
        $email->setAttrib("placeholder", "Email")
                ->addFilter(new Zend_Filter_StringTrim)
                ->addValidator(new Zend_Validate_EmailAddress())
                ->addValidator(new Zend_Validate_Db_NoRecordExists(array(
                    'table' => 'users',
                    'field' => 'email'
                        )
        ));




        $password = $this->createElement('password', 'password');
        $password->setLabel('Password: *')
                ->setRequired(true);
        $password->setAttrib("class", "form-control");
        $password->setAttrib("placeholder", "Password");


        $gender = new Zend_Form_Element_Radio("gender");
        $gender->setLabel("Gender *")
                ->setMultiOptions(array('male' => 'Male', 'female' => 'Female'))
                ->setAttrib("name", "gender")
                ->setRequired(true);

        $image = new Zend_Form_Element_File('image');
        $image->setLabel("Upload Profile Image *")
                ->setRequired(true)
                ->addValidator('Extension', false, 'jpg,png,gif,jpeg')
                ->addValidator('Count', false, 1)
                ->addValidator('Size', false, 2048 * 1024)
                ->setValueDisabled(true)
                ->getValidator('Extension')->setMessage('This file type is not supportted.');

        $country = new Zend_Form_Element_Select("country");
        $country->setLabel("Country *");
        $country->setAttrib("class", "form-control");
        $locale = new Zend_Locale("en_US");
        $countries = $locale->getTranslationList('Territory', 'en_US', 2);
        asort($countries, SORT_LOCALE_STRING);
        $arr[""] = "Select Country";
        foreach ($countries as $key => $value) {
            $arr[$value] = $value;
        }

        $country->setMultiOptions($arr);
        $country->setRequired(true);


        $signature = new Zend_Form_Element_File('signature');
        $signature->setLabel("Upload signature Image *")
                ->setRequired(true)
                ->setValueDisabled(true)
                ->addValidator('Extension', false, 'jpeg,png,gif,jpg')
                ->addValidator('Count', false, 1)
                ->addValidator('Size', false, 2048 * 1024)
                ->getValidator('Extension')->setMessage('This file type is not supportted.');


        $useradmin = new Zend_Form_Element_Radio("is_admin");
        $useradmin->setLabel("Add As *")
                ->setMultiOptions(array('1' => 'Admin', '0' => 'User'))
                ->setAttrib("name", "is_admin")
                ->setRequired(true);



        $register = $this->createElement('submit', 'register');
        $register->setLabel('Sign up');
        $register->setAttrib("class", "btn btn-primary")
                ->setAttrib("style", "margin-left: 180px; border-top-width: 4px; margin-top: 20px")
                ->setIgnore(true);


        $this->addElements(array(
            $name,
            $email,
            $password,
            $gender,            
            $country,
            $useradmin,
            $image,
            $signature,
            $register,
            $id
        ));
    }

}
