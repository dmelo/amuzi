<?php

abstract class AbstractControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    public function testLogin()
    {
        $params = array(
            'email' => 'dmelo87@gmail.com',
            'password' => 'cafess123',
            'submit' => 'Login'
        );

        $this->request->setMethod('post')
            ->setPost($params)->setParams($params);

        $this->dispatch('/Auth/index/login');
        $this->assertRedirectTo('/');

        $this->resetRequest()->resetResponse();
        $this->request->setMethod('get')->setPost(array());
    }

    public function assertLoginForm()
    {
        $this->assertQuery('form input#email');
        $this->assertQuery('form input#password');
        $this->assertQuery('form input#submit');
    }

    public function assertAjaxLoginForm($uri)
    {
        $this->assertIsAjax($uri);
        $this->assertLoginForm();
    }
}
