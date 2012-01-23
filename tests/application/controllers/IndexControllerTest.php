<?php

require_once 'bootstrap.php';

class IndexControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testSanity()
    {
        $this->assertTrue(true);
    }

    public function testIndexAction()
    {
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertQueryCount('form#search', 1);
        $this->assertQueryCount('form#search input#q', 1);
        $this->assertQueryCount('form#search input#submit', 1);
        $this->assertQueryCount('div#jp_container_1', 1);
        $this->assertQueryCount('div#result', 1);
        $this->assertQueryCount('html', 1);
    }


}

