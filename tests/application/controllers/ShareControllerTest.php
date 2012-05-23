<?php

require_once 'bootstrap.php';

class ShareControllerTest extends AbstractControllerTest
{
    public function __construct()
    {
        $this->_databaseUsage = true;
    }

    public function testLoggedOutIndex()
    {
        $this->assertAjaxLoginForm('/share/index');
    }

    public function testIndexAction()
    {
        $this->testLogin();
        $this->request->setPost(array('url' => 'http://amuzi.net/#!t32'));
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/share/index');
        $this->assertQuery('p.share-link a');
    }
}
