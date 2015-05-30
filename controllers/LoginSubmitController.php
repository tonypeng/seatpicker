<?php
class LoginSubmitController extends SiteController {
    public function getPageTitle() {
        return 'Graduation: Sign in';
    }

    public function renderPage() {
        if($this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $req = $this->getRequest();

        if($req->getMethod() != Request::POST) {
            throw new RedirectException(linkto('index.php'));
        }

        if(!$req->hasval('student_id') || !$req->hasval('password')) {
            throw new RedirectException(linkto('index.php?error'));
        }

        $username = $req->getString('student_id');
        $password = $req->getString('password');

        $res = $this->getSessionManager()->signIn($username, $password);

        if(!$res) {
            throw new RedirectException(linkto('index.php?failed'));
        }

        throw new RedirectException(linkto('dashboard'));
    }
}