<?php

require_once 'bootstrap.php';

class PlaylistControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    private $_postAddtrack = array(
        'title' => 'Test Music',
        'mp3' => 'http://example.com/a.mp3',
        'cover' => 'http://example.com/a.jpg',
        'playlist' => 'default'
    );

    private $_postRmtrack = array(
        'url' =>
            'http://amuzi.localhost/api/271/1MwjX4dG72s/Coldplay - Yellow.mp3',
        'playlist' => 'default'
    );

    public function __construct()
    {
        $this->_databaseUsage = true;
    }

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
        $this->_databaseUsage = true;
        User::loginDummy();
        $this->assertAjaxWorks('/playlist');
        $this->assertBasics('index', 'playlist');
        $this->assertQuery('form#playlistsettings');
        $this->assertQuery('form#playlistsettings input#q');
        $this->assertQuery('form#playlistsettings input#submit');
    }

    public function testSearchAction()
    {
        $this->_databaseUsage = true;
        User::loginDummy();
        $this->request->setMethod('POST');
        $this->assertAjaxWorks('/playlist/search');
        $this->assertQueryCount('tr', 3);
        $this->assertQueryCount('tr td img', 3);
        $this->assertQueryCount('tr td', 12);
    }

    public function testSearchAction2()
    {
        $this->_databaseUsage = true;
        User::loginDummy();
        $this->request->setMethod('POST');
        $this->request->setPost(array('q' => 'newOne'));
        $this->assertAjaxWorks('/playlist/search');
        $this->assertQueryCount('tr', 1);
        $this->assertQueryCount('tr td img', 1);
        $this->assertQueryCount('tr td', 4);
    }

    public function testSearchAction3()
    {
        User::loginDummy();
        $this->request->setMethod('GET');
        $this->request->setParams(array('q' => 'newOne'));
        $this->assertAjax500('/playlist/search');
        $this->assertEquals(
            $this->response->getBody(), "<span>Error: Invalid request</span>"
        );
    }

    public function testAddtrackAction()
    {
        User::loginDummy();
        $this->request->setMethod('POST');
        $this->request->setPost($this->_postAddtrack);

        $this->assertAjaxWorks('/playlist/addtrack');
        $ret = array('Track added', 'success', array(
            'id' => '12',
            'title' => 'Test Music',
            'url' => 'http://example.com/a.mp3',
            'cover' => 'http://example.com/a.jpg',
            'duration' => '0'
        ));
        $this->assertJsonMessage($ret);
    }

    public function testAddtrackAction2()
    {
        User::loginDummy();
        $this->request->setMethod('GET');
        $this->request->setParams($this->_postAddtrack);

        $this->assertAjaxWorks('/playlist/addtrack');
        $this->assertJsonMessage(
            array('Problems adding track: Invalid request', 'error')
        );
    }

    /**
     * testAddtrackAction3 Tests adding a track by track id.
     *
     * @return void
     */
    public function testAddtrackAction3()
    {
        User::loginDummy();
        $this->request->setMethod('POST');
        $this->request->setPost(array('id' => 9, 'playlist' => 'default'));

        $this->assertAjaxWorks('/playlist/addtrack');
        $obj = Zend_Json::decode($this->response->getBody());
        $this->assertJsonMessage(array('Track added', 'success', array(
            'id' => '9',
            'title' => 'Motion City Soundtrack - My Dinosaur Life - 08 - Pulp Fiction',
            'url' => 'Motion City Soundtrack - My Dinosaur Life - 08 - Pulp Fiction',
            'cover' => 'http://i.ytimg.com/vi/BaTSyGfxh5w/3.jpg',
            'duration' => '0'
        )));


       array("id" => 9, "title" => "Motion City Soundtrack - My Dinosaur Life - 08 - Pulp Fiction", "url" => "Motion City Soundtrack - My Dinosaur Life - 08 - Pulp Fiction", "cover" => "http:\/\/i.ytimg.com\/vi\/BaTSyGfxh5w\/3.jpg", "duration" => 0);
    }

    /**
     * testAddtrackAction4 Tests adding a track by givin an invalid track id.
     *
     * @return void
     */
    public function testAddtrackAction4()
    {
        User::loginDummy();
        $this->request->setMethod('POST');
        $this->request->setPost(array('id' => 9987, 'playlist' => 300));

        $this->assertAjaxWorks('/playlist/addtrack');
        $obj = Zend_Json::decode($this->response->getBody());
        $this->assertEquals($obj[1], 'error');
        $this->assertEquals(substr($obj[0], 0, 25), 'Problems adding the track');
    }


    public function testRmtrackAction()
    {
        User::loginDummy();
        $this->request->setMethod('POST');
        $this->request->setPost($this->_postRmtrack);

        $this->assertAjaxWorks('/playlist/rmtrack');
        $this->assertJsonMessage(array('Track removed', 'success'));
    }
}
