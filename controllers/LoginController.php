<?php
class LoginController extends SiteController {
    public function getPageTitle() {
        return 'Graduation: Sign in';
    }

    public function renderPage() {
        if($this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('dashboard'));
        }

        ob_start();
        ?>
            <?php if ($this->getRequest()->has('failed')) { ?>
                Invalid student ID/password combination.
            <?php } else if($this->getRequest()->has('error')) { ?>
                Student ID and password are required.
            <?php } ?>
            <form method="POST" action="login/submit">
                Student ID: <input type="text" value="" name="student_id" /><br />
                Password: <input type="password" value="" name="password" /><br />
                <input type="submit" value="Sign in" />
            </form>
        <?php
        return ob_get_clean();
    }
}