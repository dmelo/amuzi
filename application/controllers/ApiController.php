<?

/**
 * ApiController
 *
 * @version 1.0
 * @copyright Copyright (C) 2011 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL
 *
 */
class ApiController extends DZend_Controller_Action
{
    /**
     * searchAction API search call.
     *
     * @return void
     *
     */
    public function searchAction()
    {
        $q = $this->getRequest()->getParam('q');
        $list = array();
        if (null !== $q) {
            $cache = Zend_Registry::get('cache');
            if(($list = $cache->load(sha1($q))) === false) {
                $youtube = new Youtube();
                $resultSet = $youtube->search($q);
                $item = array();
                foreach ($resultSet as $result)
                    $list[] = $result->getArray();
                $cache->save($list, sha1($q));
            }

            $this->view->output = $list;
        }
    }

    public function autocompleteAction()
    {
        $q = $this->getRequest()->getParam('q');
        $list = array();
        if (null !== $q) {
            $lastfm = new Lastfm();
            $resultSet = $lastfm->search($q);
            foreach ($resultSet as $result)
                $list[] = $result->getArray();
            $this->view->output = $list;
        }
        else {
            $this->view->output = array('error' => 'Parameter "q" must be specified');
        }
    }

    /**
     * postDispatch Facilitates output using Json
     *
     * @return void
     *
     */
    public function postDispatch()
    {
        if (isset( $this->view->output ))
            echo Zend_Json::encode($this->view->output);
    }
}
