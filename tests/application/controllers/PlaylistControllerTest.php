<?php

require_once 'bootstrap.php';

class PlaylistControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    /**
     * testLoggedOutIndex
     *
     * @return void
     */
    public function testLoggedOutIndex()
    {
        $this->assertAjax500('/playlist/index');
    }

    /**
     * testLoggedOutSearch
     *
     * @return void
     */
    public function testLoggedOutSearch()
    {
        $this->assertAjax500('/playlist/search');
    }

    public function testLoggedOutSave()
    {
        $this->assertAjax500('/playlist/save');
    }

    public function testLoggedOutaddtrack()
    {
        $this->assertAjax500('/playlist/addtrack');
    }

    public function testLoggedOutrmtrack()
    {
        $this->assertAjax500('/playlist/rmtrack');
    }

    public function testLoggedOutload()
    {
        $this->assertAjax500('/playlist/load');
    }

    public function testLoggedOutsetrepeat()
    {
        $this->assertAjax500('/playlist/setrepeat');
    }

    public function testLoggedOutsetshuffle()
    {
        $this->assertAjax500('/playlist/setshuffle');
    }

    public function testLoggedOutsetcurrent()
    {
        $this->assertAjax500('/playlist/setcurrent');
    }

    public function testLoggedOutnew()
    {
        $this->assertAjax500('/playlist/new');
    }

    /**
     * testIndexAction
     *
     * @return void
     */
    public function testIndexAction()
    {
        User::loginDummy();
        $this->assertAjaxWorks('/playlist');
        $this->assertBasics('index', 'playlist');
        $this->assertQuery('form#playlistsettings');
        $this->assertQuery('form#playlistsettings input#q');
        $this->assertQuery('form#playlistsettings input#submit');
    }
}
