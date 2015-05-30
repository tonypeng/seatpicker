<?php

class Session
{
    private $_userid;
    private $_studentid;
    private $_firstname;
    private $_lastname;
    private $_phoneticname;

    public function __construct($user_id, $student_id, $first_name, $last_name, $phonetic_name)
    {
        $this->_userid = $user_id;
        $this->_studentid = $student_id;
        $this->_firstname = $first_name;
        $this->_lastname = $last_name;
        $this->_phoneticname = $phonetic_name;
    }

    public function isLoggedIn()
    {
        return $this->_userid > 0;
    }

    public function getUserID()
    {
        return $this->_userid;
    }

    public function getStudentID() {
        return $this->_studentid;
    }

    public function getFullName()
    {
        return $this->_firstname.' '.$this->_lastname;
    }

    public function getFirstName()
    {
        return $this->_firstname;
    }

    public function getLastName()
    {
        return $this->_lastname;
    }

    public function getPhoneticName()
    {
        return $this->_phoneticname;
    }
}