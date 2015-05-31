<?php
class SettingsSubmitController extends SiteController {
    public function getPageTitle() {
        return '';
    }

    public function renderPage() {
        if(!$this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $req = $this->getRequest();

        if($req->getMethod() != Request::POST) {
            throw new RedirectException(linkto('/dashboard?page=settings'));
        }

        if (!$req->hasval('first_name') || !$req->hasval('last_name') || !$req->hasval('phonetic_name')) {
            throw new RedirectException(linkto('/dashboard?page=settings'));
        }

        $first_name = $req->getString('first_name');
        $last_name = $req->getString('last_name');
        $phonetic_name = $req->getString('phonetic_name');

        try {
            $stmt = db()->prepare('UPDATE `students` SET first_name=:first_name, last_name=:last_name, phonetic_name=:phonetic_name WHERE id=:id');
            $stmt->execute(array(
                    ':id' => $this->getSessionManager()->getSession()->getUserID(),
                    ':first_name' => $first_name,
                    ':last_name' => $last_name,
                    ':phonetic_name' => $phonetic_name,
                ));
        } catch (PDOException $pdoe) {

        }

        throw new RedirectException(linkto('/dashboard?page=settings'));
    }
}