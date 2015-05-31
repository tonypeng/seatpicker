<?php
class CreateSeatsController extends SiteController {
    public function getPageTitle() {
        return 'Graduation: Sign in';
    }

    public function renderPage() {
        for($y = 1; $y < 6; $y++) {
            for($x = 0; $x < 42; $x++) {
                $realX = $x;
                if ($x >= 21) {
                    $realX += 1;
                }
                try {
                $stmt = db()->prepare('INSERT INTO `seats` (coord_x, coord_y, gender, block) VALUES (:x, :y, :g, 1)');
                $stmt->execute(array(
                    ':x' => $realX,
                    ':y' => $y,
                    ':g' => ($x+($y % 2)) % 2,
                ));
                } catch (PDOException $e) {
                    var_dump($e);
                    die();
                }
            }
        }

        echo 'done with left';

        for($y = 1; $y < 6; $y++) {
            for($x = 0; $x < 42; $x++) {
                $realX = $x;
                if ($x >= 21) {
                    $realX += 1;
                }
                try {
                $stmt = db()->prepare('INSERT INTO `seats` (coord_x, coord_y, gender, block) VALUES (:x, :y, :g, 2)');
                $stmt->execute(array(
                        ':x' => $realX,
                        ':y' => $y,
                        ':g' => ($x+($y % 2)) % 2,
                    ));
                } catch (PDOException $e) {
                    var_dump ($e);
                    die();
                }
            }
        }

        echo 'done with right';

        for($x = 0; $x < 17; $x++) {
            $realX = 42 - $x;

            try {
                $stmt = db()->prepare('INSERT INTO `seats` (coord_x, coord_y, gender, block) VALUES (:x, 0, 0, 1)');
                $stmt->execute(array(
                    ':x' => $realX,
                ));
            } catch (PDOException $e) {
                var_dump($e);
                die();
            }
        }

        for($x = 0; $x < 42; $x++) {

            try {
                $stmt = db()->prepare('INSERT INTO `seats` (coord_x, coord_y, gender, block) VALUES (:x, 0, 0, 2)');
                $stmt->execute(array(
                        ':x' => $x,
                    ));
            } catch (PDOException $e) {
                var_dump($e);
                die();
            }
        }

        for($y = 1; $y < 6; $y++) {
            try {
            $stmt = db()->prepare('INSERT INTO `seats` (coord_x, coord_y, gender, block) VALUES (21, :y, 0, 1)');
            $stmt->execute(array(
                ':y' => $y,
            ));
            } catch (PDOException $e) {
                var_dump($e);
                die();
            }
        }

        for($y = 1; $y < 6; $y++) {
            try {
                $stmt = db()->prepare('INSERT INTO `seats` (coord_x, coord_y, gender, block) VALUES (21, :y, 0, 2)');
                $stmt->execute(array(
                        ':y' => $y,
                    ));
            } catch (PDOException $e) {
                var_dump($e);
                die();
            }
        }
    }
}