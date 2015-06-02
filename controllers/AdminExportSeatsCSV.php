<?php

class AdminExportSeatsCSV extends Controller {
    private function preprocessAllSeats($seatsAndStudents) {
        $seatsOut = array();

        foreach ($seatsAndStudents as $seatAndStudent) {
            $seat = $seatAndStudent[0];

            $coordX = $seat->coordX();
            $coordY = $seat->coordY();

            $seatsOut[$seat->block()][$coordY][$coordX] = $seatAndStudent;
        }

        return $seatsOut;
    }

    public function render() {
        if(!$this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $req = $this->getRequest();

        $uid = $this->getSessionManager()->getSession()->getUserID();

        $stmt = db()->prepare('SELECT admin FROM `students` WHERE id=:id');
        $stmt->execute(array(':id' => $uid));

        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row || !$row['admin']) {
            throw new RedirectException(linkto('index.php'));
        }

        Response::header(Response::CONTENT_TYPE, 'text/csv');

        ob_start();

        $seats = $this->preprocessAllSeats(get_all_seats_and_students());

?>
Block,Seat,First name,Last name,Phonetic name
<?php
        foreach($seats as $block_num => $block) {
            $stmt = db()->prepare('SELECT `name` FROM `blocks` WHERE id=:id');
            $stmt->execute(array(':id' => $block_num));
            $block_details = $stmt->fetch();

            $block_label = $block_num;
            if ($block_details) $block_label = $block_details['name'];
            foreach($block as $block_num => $row) {
                foreach($row as $col_num => list($seat, $student)) {
                    if (!$student) continue;

                    $seat_coord = get_seat_label($seat);
                    echo "$block_label,$seat_coord,{$student->firstName()},{$student->lastName()},{$student->phoneticName()}\n";
                }
            }
        }

        return ob_get_clean();
    }
}