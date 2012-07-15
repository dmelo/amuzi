<?php

class DbTable_Track extends DZend_Model_DbTable
{
    public function insert($data)
    {
        // filtering fields.
        $data['title'] = str_replace(
            array('"', '\'', '/'), array('', '', ''), strip_tags($data['title'])
        );

        $trackRow = $this->findRowByFidAndFcode($data['fid'], $data['fcode']);
        if (!$trackRow) {
            parent::insert($data);
            $trackRow = $this->findRowByFidAndFcode(
                $data['fid'], $data['fcode']
            );
        }

        return $trackRow;
    }
}
