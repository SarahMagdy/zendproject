<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
         $this->setMethod('post');

        $email = $this->createElement('text', 'email');
        $email->setLabel('Email: *')
                ->setRequired(true);

        $email->setAttrib("class", "form-control");
        $email->setAttrib("placeholder", "Email");

        $password = $this->createElement('password', 'password');
        $password->setLabel('Password: *')
                ->setRequired(true);
        $password->setAttrib("class", "form-control");
        $password->setAttrib("placeholder", "Password");


        $submit = new Zend_Form_Element_Submit("submit");
        $submit->setAttrib("class", "btn btn-primary")
                ->setLabel("Login")
                ->setAttrib("style", "margin-left: 200px; border-top-width: 4px; margin-top: 20px");


        $this->addElements(array(
            $email,
            $password,
            $submit,
        ));
    }

}

