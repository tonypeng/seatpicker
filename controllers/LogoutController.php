<?php
class LogoutController extends SiteController {
    public function getPageTitle() {
        return 'Lynbrook DECA: Sign out';
    }

    public function renderPage() {
        if(!$this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $this->getSessionManager()->signOut();

        throw new RedirectException(linkto('index.php'));
    }
}