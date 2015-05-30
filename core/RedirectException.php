<?php

class RedirectException extends Exception {
    private $_url;

    public function __construct($url, $message='') {
        parent::__construct($message);

        $this->_url = $url;
    }

    public function getURL() {
        return $this->_url;
    }
}