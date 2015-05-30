<?php

class SeatPickerComponent extends Component {
    private $_userId;
    private $_seats;
    private $_editMode;

    private $_renderUserRows;

    public function __construct($name, $userId, array $seatsAndStudents, $editMode) {
        parent::__construct($name);

        $this->_userId = $userId;
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

        foreach ($seatsAndStudents as $seatAndStudent) {
            $seat = $seatAndStudent[0];

            $coordX = $seat->coordX();
            $coordY = $seat->coordY();

            $seatsOut[$coordY][$coordX] = $seatAndStudent;
        }

        return $seatsOut;
    }

    public function renderView() {
        ob_start();

?>
<div id="<?= $this->p('seat_picker')?>" class="seat-picker">
<?php //        <input type="text" id="<?= $this->p('seat_picker_search') " placeholder="Search for a student..." /><br />?>
        <br />
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
                    echo '<div style="position: absolute; top: '.$block['coord_y'].'px; left: '.$block['coord_x'].'px; width: 66px; height: 80px; text-align: center; border: 1px solid #000; border-radius: 4px; background-color: #fff">Podium</div>';
                    break;
            }
        }
?>
        </div>
<?php if ($this->_renderUserRows) { ?>
<br />

<div class="sp-student-row-container" id="<?= $this->p('seat_picker_student_rows') ?>">
<?php foreach($this->_seats as $block) { ?>
<?php foreach($block as $row_num => $row) { ?>

            <?php
            foreach($row as $col_num => list($seat, $student)) {
                if (!$student) continue;

                $seat_coord = get_seat_label($seat);
            ?>
                <div class="sp-student-row">
                    <div class="seat-coord"><?= $seat_coord ?></div><div class="seat-student"><?= $student->fullName() ?></div><div class="seat-comment">Placeholder</div><div class="seat-end"></div>
                </div>
            <?php } ?>
<?php } ?>
<?php } ?>
<?php } ?>
</div>
</div>
<script type="text/javascript">seatpicker(document.getElementById('<?= $this->p('seat_picker')?>'), document.getElementById('<?= $this->p('seat_picker_search') ?>'), <?= $this->_editMode ? 'true' : 'false' ?>)</script>
<?php

        return ob_get_clean();
    }

    private function renderSeatBlock($block) {

        $takenSeatURI = linkto('/img/takenseat.png');
        $userTakenSeatURI = linkto('/img/usertakenseat.png');
        $boySeatURI = linkto('/img/boyemptyseat.png');
        $girlSeatURI = linkto('/img/girlemptyseat.png');

        $seats = $this->preprocessSeats(get_all_seats_and_students_in_block($block['id']));

        // possible optimization by calculating in preprocessSeats but it's 3:30am
        $maxY = max(array_keys($seats));
?>
        <table class="sp-table" style="position: absolute; top: <?= $block['coord_y'] ?>px; left: <?= $block['coord_x'] ?>px">
            <?php
            for ($row_num = 0; $row_num <= $maxY; $row_num++) {
                $row = $seats[$row_num];
                $maxX = max(array_keys($row));
                ?>
                <tr>
                    <th><?= $row_num+1 ?></th>
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
                        ?>
                        <td title="<?= $student ? $student->fullName() : '' ?>" data-id="<?= $seat->id() ?>" data-gender="<?= $seat->gender() ?>" data-taken="<?= $student ? 'true' : 'false' ?>" class="<?= $this->_editMode ? 'editable' : '' ?>">
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