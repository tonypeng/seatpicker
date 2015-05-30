<?php

abstract class Component
{
    private $_name;

    public function __construct($name) {
        $this->_name = $name;
    }

    public abstract function renderView();

    public function __toString() {
        return $this->renderView();
    }

    public function getName() {
        return $this->_name;
    }

    protected function p($t) {
        return $this->_name.'_'.$t;
    }
}