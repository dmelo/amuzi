<?php

require_once 'bootstrap.php';

/**
 * PlaylistControllerTest Tests the PlaylistController class.
 *
 * @package amuzi
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class PlaylistControllerTest extends AbstractControllerTest
{
    /**
     * _postAddtrack Default post values for adding track.
     *
     * @var array
     */
    private $_postAddtrack = array(
        'id' => 8,
        'playlist' => 'Diogo Melo',
        'artist' => 'The Rolling Stones',
        'musicTitle' => 'Paint it Black'
    );

    /**
     * _postRmtrack Default post values for removing a track.
     *
     * @var array
     */
    private $_postRmtrack = array(
        'url' =>
            'http://amuzi.localhost/api/271/1MwjX4dG72s/Coldplay - Yellow.mp3',
        'playlist' => 'default'
    );

    /**
     * _postEditname Default values for editing a playlist's name.
     *
     * @var array
     */
    private $_postEditname = array(
        'name' => 'default',
        'newname' => 'New name'
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
        $this->assertAjaxLoginForm('/playlist/index');
    }

    /**
     * testLoggedOutSearch
     *
     * @return void
     */
    public function testLoggedOutSearch()
    {
        $this->assertAjaxLoginForm('/playlist/search');
    }

    public function testLoggedOutSave()
    {
        $this->assertAjaxLoginForm('/playlist/save');
    }

    public function testLoggedOutaddtrack()
    {
        $this->assertAjaxLoginForm('/playlist/addtrack');
    }

    public function testLoggedOutrmtrack()
    {
        $this->assertAjaxLoginForm('/playlist/rmtrack');
    }

    public function testLoggedOutload()
    {
        $this->assertAjaxLoginForm('/playlist/load');
    }

    public function testLoggedOutsetrepeat()
    {
        $this->assertAjaxLoginForm('/playlist/setrepeat');
    }

    public function testLoggedOutsetshuffle()
    {
        $this->assertAjaxLoginForm('/playlist/setshuffle');
    }

    public function testLoggedOutsetcurrent()
    {
        $this->assertAjaxLoginForm('/playlist/setcurrent');
    }

    public function testLoggedOutnew()
    {
        $this->assertAjaxLoginForm('/playlist/new');
    }

    /**
     * testIndexAction
     *
     * @return void
     */
    public function testIndexAction()
    {
        $this->testLogin();
        $this->assertAjaxWorks('/playlist');
        $this->assertBasics('index', 'playlist');
        $this->assertQuery('form#playlistsettings');
        $this->assertQuery('form#playlistsettings input#q');
        $this->assertQuery('form#playlistsettings input#submit');
    }

    public function testSearchAction()
    {
        $this->testLogin();
        $this->request->setMethod('POST');
        $this->assertAjaxWorks('/playlist/search');
        $this->assertQueryCount('tr', 3);
        $this->assertQueryCount('tr td img', 6);
        $this->assertQueryCount('tr td', 12);
    }

    public function testSearchAction2()
    {
        $this->testLogin();
        $this->request->setMethod('POST');
        $this->request->setPost(array('q' => 'newOne'));
        $this->assertAjaxWorks('/playlist/search');
        $this->assertQueryCount('tr', 1);
        $this->assertQueryCount('tr td img', 2);
        $this->assertQueryCount('tr td', 4);
    }

    public function testSearchAction3()
    {
        $this->testLogin();
        $this->request->setParams(array('q' => 'newOne'));
        $this->assertAjax500('/playlist/search');
        $this->assertEquals(
            $this->response->getBody(), "<span>Error: Invalid request</span>"
        );
    }

    public function testAddtrackAction()
    {
        $this->testLogin();
        $this->request->setPost($this->_postAddtrack);
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/playlist/addtrack');
        $ret = array('Track added', 'success', array(
            'id' => '8',
            'title' => 'The Rolling Stones- Paint it Black',
            'fid' => 'Q9DDpmyPZZA',
            'fcode' => 'y',
            'url' => '/api/255/Q9DDpmyPZZA/The+Rolling+Stones-+Paint+it+Black.mp3',
            'cover' => 'null',
            'duration' => '255',
            'youtubeUrl' => 'http://www.youtube.com/watch?v=Q9DDpmyPZZA'
        ));
        $this->assertJsonMessage($ret);
    }

    public function testAddtrackAction2()
    {
        $this->testLogin();
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
        $this->testLogin();
        $this->request->setPost(array('id' => 7, 'playlist' => 'default'));
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/playlist/addtrack');
        $obj = Zend_Json::decode($this->response->getBody());
        $this->assertJsonMessage(
            array(
            'Track added', 'success', array(
            'id' => '7',
            'title' => 'Angie - The Rolling Stones',
            'fid' => 'JMkFjYRWM4M',
            'fcode' => 'y',
            'url' => '/api/277/JMkFjYRWM4M/Angie+-+The+Rolling+Stones.mp3',
            'cover' => 'null',
            'duration' => '277',
            'youtubeUrl' => 'http://www.youtube.com/watch?v=JMkFjYRWM4M'
        )
        )
        );
    }

    /**
     * testAddtrackAction4 Tests adding a track by givin an invalid track id.
     *
     * @return void
     */
    public function testAddtrackAction4()
    {
        $this->testLogin();
        $this->request->setPost(array('id' => 9987, 'playlist' => 300));
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/playlist/addtrack');
        $obj = Zend_Json::decode($this->response->getBody());
        $this->assertEquals($obj[1], 'error');
        $this->assertEquals(
            substr($obj[0], 0, 25), 'Problems adding the track'
        );
    }


    public function testRmtrackAction()
    {
        $this->testLogin();
        $this->request->setPost($this->_postRmtrack);
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/playlist/rmtrack');
        $this->assertJsonMessage(array('Track removed', 'success'));
    }

    public function testEditnameAction()
    {
        $this->testLogin();
        $this->request->setPost($this->_postEditname);
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/playlist/editname');
        $this->assertJsonMessage(array('Saved', 'success'));
    }

    public function testEditname2Action()
    {
        $this->testLogin();
        $this->_postEditname['name'] = 'donotexists';
        $this->request->setPost($this->_postEditname);
        $this->request->setMethod('post');

        $this->assertAjaxWorks('/playlist/editname');
        $this->assertJsonMessage(array('Failed saving setting', 'error'));
    }
}
