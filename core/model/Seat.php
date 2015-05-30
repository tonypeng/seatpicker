<?php
if(!defined('GRAD_PAGE')) {
    die('Access Denied');
}

class Seat extends Model {
    const SEAT_MALE = 0;
    const SEAT_FEMALE = 1;

    public static function load($id) {
        return new Seat($id);
    }

    public static function loadFully($id, $coordX, $coordY, $gender) {
        $seat = new Seat(Model::NO_ID);
        $seat->setId($id);
        $seat->_coordX = $coordX;
        $seat->_coordY = $coordY;
        $seat->_gender = $gender;

        return $seat;
    }

    private $_coordX;
    private $_coordY;
    private $_gender;

    protected function __construct($id) {
        parent::__construct($id);
    }

    public function coordX() {
        return $this->_coordX;
    }

    public function coordY() {
        return $this->_coordY;
    }

    public function gender() {
        return $this->_gender;
    }

    public function fetch() {
        $stmt = db()->prepare('SELECT coord_x, coord_y, gender FROM `seats` WHERE id=:id');
        try {
            $stmt->execute(array(
                ':id' => $this->id(),
            ));
        } catch (PDOException $e) {
            return;
        }
        if ($row = $stmt->fetch()) {
            $this->_coordX = $row['coord_x'];
            $this->_coordY = $row['coord_y'];
            $this->_gender = $row['gender'];
        }
    }
}