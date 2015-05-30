<?php
abstract class Controller
{
    private $_request;
    private $_sessionManager;

    public function __construct(Request $request, SessionManager $session_manager)
    {
        $this->_request = $request;
        $this->_sessionManager = $session_manager;
    }

    public abstract function render();

    protected function getRequest()
    {
        return $this->_request;
    }

    protected function getSessionManager()
    {
        return $this->_sessionManager;
    }
}