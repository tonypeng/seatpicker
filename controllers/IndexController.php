<?php
class IndexController extends SiteController {
    public function getPageTitle() {
        return 'Graduation: Home';
    }

    public function renderPage() {
        if($this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('dashboard'));
        }

        ob_start();

?>
        <header class="bg-dark-red">
            <div id="header-container">
                <span id="header-title">
                    Lynbrook High School Class of 2015 Graduation Seating
                </span>
            </div>
        </header>
        <div id="login-content" class="bg-red">
            <div id="login-vertical-wrap">
                <div id="login-vertical-center">
                    <div id="login-island">
                        <form method="POST" action="login/submit">
                            <div class="content">
                                <div class="headline">Login</div>
                                <div class="error">
                                <?php if ($this->getRequest()->has('failed')) { ?>
                                    Invalid student ID/password combination.
                                <?php } else if($this->getRequest()->has('error')) { ?>
                                    Student ID and password are required.
                                <?php } ?>
                                </div>
                                <table>
                                    <tr>
                                        <th class="login-label">Student ID</th><td class="login-input"><input type="text" value="" name="student_id" /></td>
                                    </tr>
                                    <tr>
                                        <th class="login-label">Password</th><td class="login-input"><input type="password" value="" name="password" /></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="copyright">&copy; 2015 <a href="http://tonypeng.com/">Tony Peng</a></div>
                            <button type="submit">
                                <img src="<?= linkto('/img/Arrow-right_24x24px.svg') ?>" style="height: 44px;" />
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<?php

        return ob_get_clean();
    }

    public function isSingleColumn() {
        return false;
    }
}