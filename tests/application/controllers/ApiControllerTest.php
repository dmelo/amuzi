<?php

require_once 'bootstrap.php';

class ApiControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    public function __construct()
    {
        $this->_databaseUsage = true;
    }

    public function assertValidSearch()
    {
        try {
        $this->assertAjaxWorks('/api/search');
        $obj = Zend_Json::decode($this->response->getBody());
        $this->assertInternalType('array', $obj);
        $this->assertEquals(count($obj), 9);
        foreach ($obj as $key => $value) {
            $this->assertInternalType('int', $key);
            $this->assertInternalType('array', $value);
            $this->assertTrue(array_key_exists('id', $value));
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue(array_key_exists('content', $value));
            $this->assertTrue(array_key_exists('cover', $value));
            $this->assertTrue(array_key_exists('duration', $value));
            $this->assertTrue(array_key_exists('you2better', $value));
        }
        } catch(Exception $e) {
        }
    }

    public function testSearch1Action()
    {
        $this->request->setMethod('GET');
        $this->request->setParams(
            array(
                'q' => 'Coldplay - Yellow',
                'artist' => 'Coldplay',
                'musicTitle' => 'Yellow'
            )
        );
        $this->assertValidSearch();
    }

    public function testSearch2Action()
    {
        $this->request->setMethod('POST');
        $this->request->setParams(array('q' => 'Rolling Stones'));
        $this->assertValidSearch();
    }

    public function testSearch3Action()
    {
        $this->request->setMethod('POST');
        $this->request->setParams(array('qq' => 'Rolling Stones'));
        $this->assertAjaxWorks('/api/search');
        $obj = Zend_Json::decode($this->getResponse()->getBody());
        $this->assertInternalType('array', $obj);
        $this->assertTrue(array_key_exists('error', $obj));
        $this->assertInternalType('string', $obj['error']);
    }

    /**
     * Tests the "more" button feature. This requests page 2.
     */
    public function testSearch4Action()
    {
        $this->request->setMethod('POST');
        $this->request->setParams(
            array('q' => 'Rolling Stones', 'offset' => 10, 'limit' => 9)
        );
        $this->assertValidSearch();
    }


    public function assertValidAutocomplete()
    {
        $this->assertAjaxWorks('/api/autocomplete');
        $obj = Zend_Json::decode($this->getResponse()->getBody());
        $this->assertInternalType('array', $obj);
        $this->assertTrue(count($obj) >= 10);
        foreach ($obj as $key => $value) {
            $this->assertInternalType('int', $key);
            $this->assertInternalType('array', $value);
            $this->assertTrue(array_key_exists('name', $value));
            $this->assertTrue(array_key_exists('cover', $value));
        }
    }

    public function testAutocompleteAction()
    {
        $this->request->setMethod('GET');
        $this->request->setParams(array('q' => 'Coldplay'));
        $this->assertValidAutocomplete();
    }

    public function testAutocomplete2Action()
    {
        $this->request->setMethod('POST');
        $this->request->setParams(array('q' => 'Rolling Stones'));
        $this->assertValidAutocomplete();
    }

    public function testAutocomplete3Action()
    {
        $this->request->setMethod('GET');
        $this->setAjaxHeader();
        $this->request->setParams(array('qq' => 'U2')); // Wrong parameter
        $this->dispatch('/api/autocomplete');
        $this->assertResponseCode(200);
        $obj = Zend_Json::decode($this->getResponse()->getBody());
        $this->assertInternalType('array', $obj);
        $this->assertTrue(array_key_exists('error', $obj));
        $this->assertInternalType('string', $obj['error']);
    }

    public function testGettrackAction()
    {
        $this->request->setMethod('GET');
        $this->setAjaxHeader();
        $this->request->setParams(array('id' => 4));
        $this->dispatch('/api/gettrack');
        $this->assertResponseCode(200);
        $obj = Zend_Json::decode($this->getResponse()->getBody());
        $this->assertInternalType('array', $obj);
        $this->assertTrue(array_key_exists('id', $obj));
        $this->assertTrue(array_key_exists('title', $obj));
        $this->assertTrue(array_key_exists('fcode', $obj));
        $this->assertTrue(array_key_exists('fid', $obj));
        $this->assertTrue(array_key_exists('cover', $obj));
        $this->assertTrue(array_key_exists('duration', $obj));

        $title = "we will rock you by QUEEN with lyrics";
        $this->assertEquals($obj['id'], 4);
        $this->assertEquals($obj['title'], $title);
        $this->assertEquals($obj['fcode'], 'y');
        $this->assertEquals($obj['fid'], 'qGaOlfmX8rQ');
        $this->assertEquals($obj['duration'], 125);
        $this->assertEquals($obj['cover'], 'null');
    }

    public function testSearchsimilarAction()
    {
        $this->request->setMethod('GET');
        $this->setAjaxHeader();
        $this->request->setParams(array(
            'artist' => 'Coldplay', 'musicTitle' => 'Yellow'
        ));
        $this->dispatch('/api/searchsimilar');
        $this->assertResponseCode(200);
        $obj = Zend_Json::decode($this->getResponse()->getBody());

        $this->assertTrue(array_key_exists(0, $obj));
        $this->assertTrue(array_key_exists(1, $obj));
        $list = $obj[0];
        $matrix = $obj[1];

        $this->assertInternalType('array', $list);
        $this->assertInternalType('array', $matrix);

        foreach ($list as $item) {
            $this->assertInternalType('array', $item);
            $this->_logger->debug("ApiControllerTest::testSearchsimilarAction -> " . print_r($item, true));
            $this->assertTrue(array_key_exists('artistMusicTitleId', $item));
            $artistMusicTitleId = $item['artistMusicTitleId'];
            $this->assertTrue(array_key_exists($artistMusicTitleId, $matrix));
            $this->assertInternalType('array', $matrix[$artistMusicTitleId]);
        }
    }
}
