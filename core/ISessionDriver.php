<?php

interface ISessionDriver
{
    function lookup($session_key);
    function write($user_id, $username, $expire_time);
    function signout($user_id);
}

class SessionInvalidKeyException extends Exception { }