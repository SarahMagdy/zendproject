<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initPlaceholders() {
    $this->bootstrap('View');
    $view = $this->getResource('View');
    $view->doctype('XHTML1_STRICT');
    $view->headMeta()->appendName('keywords', 'framework, PHP')->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
    $view->headTitle('Social Network')->setSeparator(' :: ');
}

}

