<?php

class SeatPickerComponent extends Component {
    private $_userId;
    private $_seats;
    private $_editMode;

    private $_renderUserRows;

    public function __construct($name, $userId, array $seatsAndStudents, $editMode) {
        parent::__construct($name);

        $this->_userId = $userId;
        $this->preprocessSeats($seatsAndStudents);
        $this->_editMode = $editMode;
    }

    public function setRenderUserRows($on) {
        $this->_renderUserRows = $on;
    }

    private function preprocessSeats($seatsAndStudents) {
        foreach ($seatsAndStudents as $seatAndStudent) {
            $seat = $seatAndStudent[0];

            $coordX = $seat->coordX();
            $coordY = $seat->coordY();

            $this->_seats[$coordY][$coordX] = $seatAndStudent;
        }
    }

    public function renderView() {
        ob_start();

        $takenSeatURI = linkto('/img/takenseat.png');
        $userTakenSeatURI = linkto('/img/usertakenseat.png');
        $boySeatURI = linkto('/img/boyemptyseat.png');
        $girlSeatURI = linkto('/img/girlemptyseat.png');
?>
<div class="seat-picker">
        <input type="text" id="<?= $this->p('seat_picker_search') ?>" placeholder="Search for a student..." /><br />
        <br />
        <div class="sp-table-wrapper">
            <table class="sp-table" id="<?= $this->p('seat_picker_table') ?>">
<?php
        foreach($this->_seats as $row_num => $row) {
?>
                <tr>
                <th><?= $row_num+1 ?></th>
<?php
            foreach($row as list($seat, $student)) {
                $seatImg = $student ?
                    ($student->studentId() == $this->_userId ? $userTakenSeatURI : $takenSeatURI) :
                    ($seat->gender() ? $girlSeatURI : $boySeatURI);
?>
                    <td title="<?= $student ? $student->fullName() : '' ?>" data-id="<?= $seat->id() ?>" data-gender="<?= $seat->gender() ?>" data-taken="<?= $student ? 'true' : 'false' ?>">
                       <img src="<?= $seatImg ?>" />
                    </td>
            <?php } ?>
                </tr>
<?php
        }
?>
            </table>
        </div>
<?php if ($this->_renderUserRows) { ?>
<?php foreach($this->_seats as $row_num => $row) { ?>
        <br />

        <div class="sp-student-row-container" id="<?= $this->p('seat_picker_student_rows') ?>">
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
        </div>
<?php } ?>
</div>
<script type="text/javascript">seatpicker(document.getElementById('<?= $this->p('seat_picker_table') ?>'), document.getElementById('<?= $this->p('seat_picker_search') ?>'), <?= $this->_editMode ? 'true' : 'false' ?>)</script>
<?php

        return ob_get_clean();
    }
}