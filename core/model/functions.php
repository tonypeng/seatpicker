<?php
if(!defined('GRAD_PAGE')) {
    die('Access Denied');
}

require_once __DIR__ . '/Seat.php';
require_once __DIR__ . '/Student.php';

function get_seat_label(Seat $seat) {
    $row_letter = chr(ord('A')+($seat->coordY() % 26));
    return $row_letter.($seat->coordX()+1);
}

function get_student_seat($id) {
    $stmt = db()->prepare('SELECT s.* FROM `student_seat_assoc` t JOIN `seats` s ON t.seat=s.id AND t.student=:id');
    $stmt->execute(array(':id' => $id));

    if ($row = $stmt->fetch()) {
        return Seat::loadFully($row['id'], $row['coord_x'], $row['coord_y'], $row['gender']);
    }

    return null;
}

function get_all_seats() {
    $stmt = db()->prepare('SELECT * FROM `seats`');
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        return null;
    }
    $rows = $stmt->fetchAll();
    return array_map(function($v) { return Seat::loadFully($v['id'], $v['coord_x'], $v['coord_y'], $v['gender']); }, $rows);
}

function get_all_seats_and_students() {
    $stmt = db()->prepare('SELECT st.id as st_id, st.first_name, st.last_name, st.phonetic_name, st.student_id,
        se.id as se_id, se.coord_x, se.coord_y, se.gender FROM `seats` se
        LEFT JOIN `student_seat_assoc` t
            JOIN `students` st ON st.id=t.student
        ON se.id=t.seat');
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        return null;
    }
    $rows = $stmt->fetchAll();

    $ret = array();

    foreach($rows as $row) {
        $seat = Seat::loadFully($row['se_id'], $row['coord_x'], $row['coord_y'], $row['gender']);
        $student = null;

        if($row['st_id']) {
            $student = Student::loadFully($row['st_id'], $row['student_id'], $row['first_name'],
                $row['last_name'], $row['phonetic_name']);
        }

        $ret[] = array($seat, $student);
    }

    return $ret;
}