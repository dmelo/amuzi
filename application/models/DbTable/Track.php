<?php

class DbTable_Track extends DZend_Model_DbTable
{
    public function insert($data)
    {
        if (array_key_exists('mp3', $data)) {
            $data['url'] = $data['mp3'];
            unset($data['mp3']);
        }

        $trackRow = $this->findRowByUrl($data['url']);
        if (!$trackRow) {
            parent::insert($data);
            $trackRow = $this->findRowByUrl($data['url']);
        }

        return $trackRow;
    }
}
