<?php

require_once 'bootstrap.php';

class ApiControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    public function assertValidSearch()
    {
        $this->assertAjaxWorks('/api/search');
        $obj = Zend_Json::decode($this->getResponse()->getBody());
        $this->assertInternalType('array', $obj);
        $this->assertEquals(count($obj), 9);
        foreach ($obj as $key => $value) {
            $this->assertInternalType('int', $key);
            $this->assertInternalType('array', $value);
            $this->assertTrue(array_key_exists('id', $value));
            $this->assertTrue(array_key_exists('title', $value));
            $this->assertTrue(array_key_exists('content', $value));
            $this->assertTrue(array_key_exists('pic', $value));
            $this->assertTrue(array_key_exists('duration', $value));
            $this->assertTrue(array_key_exists('you2better', $value));
        }
    }

    public function testSearch1Action()
    {
        $this->request->setMethod('GET');
        $this->request->setParams(array('q' => 'Coldplay'));
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
            $this->assertTrue(array_key_exists('pic', $value));
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
}
