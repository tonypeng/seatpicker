<?php
if(!defined('GRAD_PAGE')) {
    die('Access Denied');
}

abstract class Model {
    const NO_ID = -1;

    private $_id;

    protected function __construct($id) {
        $this->_id = $id;
        if ($id != self::NO_ID) {
            $this->fetch();
        }
    }

    public function id() {
        return $this->_id;
    }

    protected function setId($id) {
        $this->_id = $id;
    }

    public abstract function fetch();
}