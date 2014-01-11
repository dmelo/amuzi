<?php

/**
 * ApiControllerTest
 *
 * @package Amuzi
 * @version 1.0
 * Amuzi - Online music
 * Copyright (C) 2010-2014  Diogo Oliveira de Melo
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require_once 'bootstrap.php';

class ApiControllerTest extends DZend_Test_PHPUnit_ControllerTestCase
{
    public function __construct()
    {
        $this->_databaseUsage = true;
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
        $this->request->setParams(
            array(
                'artist' => 'Coldplay', 'musicTitle' => 'Yellow'
            )
        );
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
            $this->_logger->debug(
                "ApiControllerTest::testSearchsimilarAction -> "
                . print_r($item, true)
            );
            $this->assertTrue(array_key_exists('artistMusicTitleId', $item));
            $artistMusicTitleId = $item['artistMusicTitleId'];
            $this->assertTrue(array_key_exists($artistMusicTitleId, $matrix));
            $this->assertInternalType('array', $matrix[$artistMusicTitleId]);
        }
    }
}
