<?php

class AdminSeatController extends SiteController {
    public function getPageTitle() {
        return 'Admin: Edit Seat';
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

    public function renderPage() {
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

        ob_start();

        ?>
        <div class="seat-picker">
            <div class="sp-render-wrapper">
                <div class="sp-render-area">
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
        </div>
        <?php

        if ($req->hasval('seat')) {
            $seat_id = $req->getInt('seat');

            $stmt = db()->prepare('SELECT * FROM `seats` WHERE id=:id');
            $stmt->execute(array(':id' => $seat_id));

            $seat = $stmt->fetch();

            if (!$seat) {
                echo 'No such seat';
                return ob_get_clean();
            }

            $male_selected = $seat['gender'] ? '' : ' selected';
            $female_selected = $seat['gender'] ? ' selected' : '';

            $stmt = db()->prepare('SELECT * FROM `blocks` WHERE id=:id');
            $stmt->execute(array(':id' => $seat['block']));

            $row = $stmt->fetch();

            $block_post = '';

            if ($row) {
                $block_post = ', '.$row['name'];
            }

            $row_letter = chr(ord('A')+($seat['coord_y'] % 26));
            $seat_label = $row_letter.($seat['coord_x']+1).$block_post;

?>
        Editing <strong><?= $seat_label ?></strong><br />
        Coordinates: (<?= $seat['coord_x'] ?>, <?= $seat['coord_y'] ?>)<br />
        <form method="POST" action="<?= linkto('/admin/changeseat') ?>">
            Gender: <select name="gender"><option value="0"<?= $male_selected ?>>Male</option><option value="1"<?= $female_selected ?>>Female</option></select>
            <input type="hidden" name="seat" value="<?= $seat_id ?>" />
            <input type="submit" value="Update" />
        </form>
<?php
        }

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
                            ($takenSeatURI) :
                            ($seat->gender() ? $girlSeatURI : $boySeatURI);

                        ?>
                        <td title="<?= $student ? $student->fullName() : '' ?>">
                            <a href="?seat=<?= $seat->id() ?>"><img src="<?= $seatImg ?>" /></a>
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