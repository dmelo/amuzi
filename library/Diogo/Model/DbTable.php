<?php

class Diogo_Model_DbTable extends Zend_Db_Table_Abstract
{
    protected $_db;
    protected $_session;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_db = $this->getAdapter();
        $this->_session = Diogo_Session_Namespace::get('session');
    }

    protected function _transform($items)
    {
        $ret = array();
        foreach($items as $item) {
            $n = strtolower($item[0]);

            for($i = 1; $i < strlen($item); $i++) {
                if(preg_match('/[A-Z]/', $item[$i]))
                    $n .= '_' . strtolower($item[$i]);
                else
                    $n .= $item[$i];
            }

            $ret[] = $n;
        }

        return $ret;
    }

    protected function _funcToQuery($funcName, $args)
    {
        $funcName = preg_replace('/^find(|Row)By/', '', $funcName);
        $items = explode('And', $funcName);
        $items = $this->_transform($items);
        $where = '';
        foreach($items as $key => $item) {
            if($key)
                $where .= ' AND ';
            $where .= $this->_db->quoteInto($item . ' = ?', $args[$key]);
        }

        return $where;
    }

    public function __call($funcName, $args)
    {
        $where = $this->_funcToQuery($funcName, $args);
        if(preg_match('/^findBy.*/', $funcName)) {
            return $this->fetchAll($where);
        } elseif(preg_match('/^findRowBy.*/', $funcName)) {
            return $this->fetchRow($where);
        }
    }
}
