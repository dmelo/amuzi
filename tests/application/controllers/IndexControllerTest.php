<?php

require_once 'bootstrap.php';

class IndexControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    public function testSanity()
    {
        $this->assertTrue(true);
    }

    public function testIndexAction()
    {
        $this->dispatch('/');
        $this->assertBasics('index', 'index', 'default');
        $this->assertQuery('form#search');
        $this->assertQuery('form#search input#q');
        $this->assertQuery('form#search input#submit');
        $this->assertQuery('div#jp_container_1');
        $this->assertQuery('div#result');
        $this->assertQuery('div#result');
        $this->assertQuery('div.topbar');
    }

    public function testAboutAction()
    {
        $this->assertAjaxWorks('/index/about');
        $this->assertBasics('about', 'index');
    }
}

