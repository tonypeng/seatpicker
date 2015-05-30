<?php
if(!defined('GRAD_PAGE')) {
    die('Access Denied');
}

class Student extends Model {
    public static function load($id) {
        return new Student($id);
    }

    public static function loadFully($id, $studentId, $firstName, $lastName, $phoneticName) {
        $student = new Student(Model::NO_ID);
        $student->setId($id);
        $student->_studentId = $studentId;
        $student->_firstName = $firstName;
        $student->_lastName = $lastName;
        $student->_phoneticName = $phoneticName;

        return $student;
    }

    public static function with($studentId, $firstName, $lastName, $phoneticName) {
        $student = new Student(Model::NO_ID);
        $student->_studentId = $studentId;
        $student->_firstName = $firstName;
        $student->_lastName = $lastName;
        $student->_phoneticName = $phoneticName;

        return $student;
    }

    private $_studentId;
    private $_firstName;
    private $_lastName;
    private $_phoneticName;

    protected function __construct($id) {
        parent::__construct($id);
    }

    public function studentId() {
        return $this->_studentId;
    }

    public function firstName() {
        return $this->_firstName;
    }

    public function lastName() {
        return $this->_lastName;
    }

    public function fullName() {
        return $this->firstName().' '.$this->lastName();
    }

    public function phoneticName() {
        return $this->_phoneticName;
    }

    public function fetch() {
        $stmt = db()->prepare('SELECT student_id, first_name, last_name, phonetic_name FROM `students` WHERE id=:id');
        try {
            $stmt->execute(array(
                ':id' => $this->id(),
            ));
        } catch (PDOException $e) {
            return;
        }
        if ($row = $stmt->fetch()) {
            $this->_studentId = $row['student_id'];
            $this->_firstName = $row['first_name'];
            $this->_lastName = $row['last_name'];
            $this->_phoneticName = $row['phonetic_name'];
        }
    }
}