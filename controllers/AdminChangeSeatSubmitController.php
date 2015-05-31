<?php
class AdminChangeSeatSubmitController extends SiteController {
    public function getPageTitle() {
        return '';
    }

    public function renderPage() {
        if(!$this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $uid = $this->getSessionManager()->getSession()->getUserID();

        $stmt = db()->prepare('SELECT admin FROM `students` WHERE id=:id');
        $stmt->execute(array(':id' => $uid));

        $stmt->execute();

        $row = $stmt->fetch();

        if (!$row || !$row['admin']) {
            throw new RedirectException(linkto('index.php'));
        }

        $req = $this->getRequest();

        if($req->getMethod() != Request::POST) {
            throw new RedirectException(linkto('/admin/viewseat'));
        }

        if (!$req->hasval('seat') || !$req->hasval('gender')) {
            throw new RedirectException(linkto('/admin/viewseat'));
        }

        $seat = $req->getInt('seat');
        $gender = $req->getInt('gender');

        $stmt = db()->prepare('UPDATE `seats` SET gender=:gender WHERE id=:id');
        $stmt->execute(array(':id' => $seat, ':gender' => $gender));

        throw new RedirectException(linkto('/admin/viewseat?seat='.$seat));
    }
}