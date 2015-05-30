<?php
class PostSeatController extends Controller {
    const KEY_SEAT_ID = 'seat';

    public function getPageTitle() {
        return '';
    }

    public function render() {
        Response::header(Response::CONTENT_TYPE, 'application/json');

        $req = $this->getRequest();

        if($req->getMethod() != Request::POST) {
            return json_encode(array('error' => 'Invalid request method.'));
        }

        if(!$req->hasval(self::KEY_SEAT_ID)) {
            return json_encode(array('error' => 'No seat specified.'));
        }

        $seat = $req->getInt(self::KEY_SEAT_ID, -1);

        if(!$this->getSessionManager()->isSignedIn()) {
            return json_encode(array('error' => 'Not signed in. Try refreshing the page.'));
        }

        $session = $this->getSessionManager()->getSession();

        // db constraints should ensure unique and valid values
        try {
            db()->beginTransaction();

            $stmt = db()->prepare('DELETE FROM `student_seat_assoc` WHERE student=:id');
            $stmt->execute(array(':id' => $session->getUserID()));

            $stmt = db()->prepare('INSERT INTO `student_seat_assoc` (student, seat) VALUES (:id, :seat_id)');
            $stmt->execute(array(':id' => $session->getUserID(), ':seat_id' => $seat));

            db()->commit();
        } catch (PDOException $pdoe) {
            db()->rollBack();

            if ($pdoe->getCode() == 23000) {
                return json_encode(array('error' => 'The chosen seat is already occupied.'));
            }

            return json_encode(array('error' => 'Oops! An error occurred. Try refreshing the page.'));
        }

        return json_encode(array('error' => null));
    }
}