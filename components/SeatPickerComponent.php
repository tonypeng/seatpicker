<?php

class SeatPickerComponent extends Component {
    private $_userId;
    private $_userGender;
    private $_seats;
    private $_editMode;

    private $_renderUserRows;

    public function __construct($name, Session $session, array $seatsAndStudents, $editMode) {
        parent::__construct($name);

        $this->_userId = $session->getStudentID();
        $this->_userGender = $session->getGender();
        $this->_seats = $this->preprocessAllSeats($seatsAndStudents);
        $this->_editMode = $editMode;
    }

    public function setRenderUserRows($on) {
        $this->_renderUserRows = $on;
    }

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

    private function preprocessSeats($seatsAndStudents) {
        $seatsOut = array();

        $maxX = 0;
        $maxY = 0;

        foreach ($seatsAndStudents as $seatAndStudent) {
            $seat = $seatAndStudent[0];

            $coordX = $seat->coordX();
            $coordY = $seat->coordY();

            $maxX = max($coordX, $maxX);
            $maxY = max($coordY, $maxY);

            $seatsOut[$coordY][$coordX] = $seatAndStudent;
        }

        return array($seatsOut, $maxX, $maxY);
    }

    public function renderView() {
        ob_start();

?>
<div id="<?= $this->p('seat_picker')?>" class="seat-picker">
        <input type="text" id="<?= $this->p('seat_picker_search') ?>" placeholder="Student name" /> <button id="<?= $this->p('seat_picker_search_btn') ?>" class="sp-search-button">Search</button> <span class="caption">Results will be highlighted below.</span><br />
        <br />
        <div class="sp-render-wrapper">
        <div id="<?= $this->p('seat_picker_render_area') ?>" class="sp-render-area">
<?php
        $stmt = db()->prepare('SELECT * FROM `blocks`');
        $stmt->execute();

        while ($block = $stmt->fetch()) {
            $type = $block['type'];

            switch ($type) {
                case 0:
                    echo $this->renderSeatBlock($block);
                    break;
                case 1:
                    echo '<div style="position: absolute; top: '.$block['coord_y'].'px; left: '.$block['coord_x'].'px; width: 42px; font-size: 11px; height: 60px; text-align: center; border: 1px solid #000; border-radius: 4px; background-color: #fff"><br /><br />Podium</div>';
                    break;
            }
        }
?>
        </div>
        </div>
<?php if ($this->_renderUserRows) { ?>
<div class="sp-student-row-container" id="<?= $this->p('seat_picker_student_rows') ?>">
<?php foreach($this->_seats as $block_num => $block) {
        $stmt = db()->prepare('SELECT `name` FROM `blocks` WHERE id=:id');
        $stmt->execute(array(':id' => $block_num));
        $block_details = $stmt->fetch();

        $block_label = $block_num;
        if ($block_details) $block_label = $block_details['name'];
?>
    <br />
    <strong><?= $block_label ?></strong>
    <br />
    <br />
<?php foreach($block as $block_num => $row) { ?>
            <?php
            foreach($row as $col_num => list($seat, $student)) {
                if (!$student) continue;

                $seat_coord = get_seat_label($seat);
            ?>
                <div class="sp-student-row">
                    <div class="seat-coord"><?= $seat_coord ?></div><div class="seat-student"><?= $student->fullName() ?></div><div class="seat-comment"></div><div class="seat-end"></div>
                </div>
            <?php } ?>
<?php } ?>
<?php } ?>
<?php } ?>
</div>
</div>
<script type="text/javascript">seatpicker(document.getElementById('<?= $this->p('seat_picker')?>'), document.getElementById('<?= $this->p('seat_picker_search') ?>'), document.getElementById('<?= $this->p('seat_picker_search_btn') ?>'), <?= $this->_editMode ? 'true' : 'false' ?>)</script>
<?php

        return ob_get_clean();
    }

    private function renderSeatBlock($block) {

        $takenSeatURI = linkto('/img/takenseat.png');
        $userTakenSeatURI = linkto('/img/usertakenseat.png');
        $boySeatURI = linkto('/img/boyemptyseat.png');
        $girlSeatURI = linkto('/img/girlemptyseat.png');

        list($seats, $maxX, $maxY) = $this->preprocessSeats(get_all_seats_and_students_in_block($block['id']));

?>
        <table class="sp-table" style="position: absolute; top: <?= $block['coord_y'] ?>px; left: <?= $block['coord_x'] ?>px; width: <?= ($maxX+2)*14 ?>px">
            <?php
            for ($row_num = 0; $row_num <= $maxY; $row_num++) {
                $row = $seats[$row_num];
                $row_letter = chr(ord('A')+(($row_num) % 26));
                ?>
                <tr>
                    <th><?= $row_letter ?></th>
                    <?php
                    for ($x = 0; $x <= $maxX; $x++) {
                        if (!isset($row[$x])) {
                            echo '<td></td>';
                            continue;
                        }

                        list($seat, $student) = $row[$x];

                        $seatImg = $student ?
                            ($student->studentId() == $this->_userId ? $userTakenSeatURI : $takenSeatURI) :
                            ($seat->gender() ? $girlSeatURI : $boySeatURI);

                        $editable = ($this->_editMode && !$student && $seat->gender() == $this->_userGender) ? 'editable' : '';

                        ?>
                        <td title="<?= $student ? $student->fullName() : '' ?>" data-id="<?= $seat->id() ?>" data-gender="<?= $seat->gender() ?>" data-taken="<?= $student ? 'true' : 'false' ?>" class="<?= $editable?>">
                            <img src="<?= $seatImg ?>" />
                        </td>
                    <?php } ?>
                </tr>
            <?php
            }
            ?>
        </table>
<?php
    }
}