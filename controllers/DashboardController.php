<?php
require_once __DIR__.'/../components/SeatPickerComponent.php';

class DashboardController extends SiteController {
    const KEY_PAGE = 'page';

    const PAGE_HOME = 'home';
    const PAGE_EDIT = 'edit';
    const PAGE_SETTINGS = 'settings';

    public function getPageTitle() {
        return 'Graduation';
    }

    public function renderPage() {
        if (!$this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $session = $this->getSessionManager()->getSession();

        $request = $this->getRequest();

        $student_seat = get_student_seat($session->getUserID());

        if (!$session->getOnboarded()) {
            throw new RedirectException(linkto('/onboarding'));
        }

        $page = self::PAGE_HOME;
        if ($request->hasval(self::KEY_PAGE)) {
            $page = $request->getString(self::KEY_PAGE);
        }

        $output = '';

        $home_selected = '';
        $edit_selected = '';
        $settings_selected = '';

        $page_title = '';

        switch ($page) {
            case self::PAGE_EDIT:
                $output = $this->renderEdit();
                $edit_selected = ' selected';
                $page_title = 'Change seat';
                break;
            case self::PAGE_SETTINGS:
                $output = $this->renderSettings();
                $settings_selected = ' selected';
                $page_title = 'Settings';
                break;
            case self::PAGE_HOME:
            default:
                $output = $this->renderHome($student_seat);
                $home_selected = ' selected';
                $page_title = 'Home';
                break;
        }

        ob_start();
        ?>
        <header class="bg-blue">
            <div id="header-container">
                <button class="nav-drawer-icon"></button>
                <span id="header-title">
                    <?= $page_title ?>
                </span>
                <script type="text/javascript">
                    $('.nav-drawer-icon').click(function(event) {
                        var navDrawer = $('#side-nav');

                        if (navDrawer.is(':visible')) {
                            navDrawer.hide();
                        } else {
                            navDrawer.show();
                        }

                        event.stopPropagation();
                    });
                </script>
            </div>
        </header>
        <div id="side-nav">
            <div id="side-nav-container">
                <div id="nav-welcome">
                    Hey there, <?php echo $this->getSessionManager()->getSession()->getFirstName() ?>!<br />
                    <form action="<?= linkto('/logout') ?>" method="POST">
                        <button type="submit" class="logout-btn">Log out</button>
                    </form>
                </div>
                <nav>
                    <div class="nav-entry<?= $home_selected ?>">
                        <?= makelink('dashboard', 'Home') ?>
                    </div>
                    <div class="nav-entry<?= $edit_selected ?>">
                        <?= makelink('dashboard?page=edit', 'Change seat') ?>
                    </div>
                    <div class="nav-entry<?= $settings_selected ?>">
                        <?= makelink('dashboard?page=settings', 'Settings') ?>
                    </div>
                </nav>
                <div class="footer">
                    &copy; 2015 <a href="http://tonypeng.com/">Tony Peng</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $('#side-nav').click(function(event) {
                event.stopPropagation();
            });
        </script>
        <div id="content">
            <?= $output ?>
        </div>
        <script type="text/javascript">
            $('body').click(function() {
                if ($(window).width() >= 1555) {
                    return;
                }

                var navDrawer = $('#side-nav');

                if (navDrawer.is(':visible')) {
                    navDrawer.hide();
                }
            })

            $(window).resize(function () {
                console.log('resize');
                if ($(window).width() >= 1555) {
                    var navDrawer = $('#side-nav');

                    navDrawer.show();
                }
            });
        </script>
        <?php

        return ob_get_clean();
    }

    public function renderHome($studentSeat) {
        ob_start();

        $seats = get_all_seats_and_students();

        $seatPicker = new SeatPickerComponent('dashboard_seat_picker', $this->getSessionManager()->getSession(), $seats, false);
        $seatPicker->setRenderUserRows(true);

        $message = $studentSeat ? 'You are currently seated at: <strong>'.get_seat_label_with_block($studentSeat).'</strong>.'
            : 'You haven\'t picked a seat yet! Click '.makelink('/dashboard?page=edit', 'here').' to do so.';

?>
        <div class="center-text">
            <div class="center-wrap">
                <p class="title"><?= $message ?></p>
                <?= $seatPicker->renderView() ?>
            </div>
        </div>
<?php

        return ob_get_clean();
    }

    public function renderEdit() {
        ob_start();

        $seats = get_all_seats_and_students();

        $seatPicker = new SeatPickerComponent('dashboard_seat_picker', $this->getSessionManager()->getSession(), $seats, true);
        $seatPicker->setRenderUserRows(true);
        ?>
        <div class="center-text">
            <div class="center-wrap">
                <p class="title">Click on an available seat to pick it.</p>
                <span>Blue seats are reserved for boys and gray seats are reserved for girls.</span><br />
                <br />
                <?= $seatPicker->renderView() ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    public function renderSettings() {
        ob_start();

?>
        <div id="onboarding-island">
            <form method="POST" action="<?= linkto('/settings/submit') ?>">
                <table style="width: 100%">
                    <tr>
                        <th class="login-label">First name</th><td class="login-input" style="width: 40%"><input type="text" value="<?= $this->getSessionManager()->getSession()->getFirstName() ?>" name="first_name" /></td>
                    </tr>
                    <tr>
                        <th class="login-label">Last name</th><td class="login-input" style="width: 40%"><input type="text" value="<?= $this->getSessionManager()->getSession()->getLastName() ?>" name="last_name" /></td>
                    </tr>
                    <tr>
                        <th class="login-label">Phonetic name</th><td class="login-input" style="width: 40%"><input type="text" value="<?= $this->getSessionManager()->getSession()->getPhoneticName() ?>" name="phonetic_name" /></td>
                    </tr>
                </table>
                <div class="center-text"><input type="submit" value="Update" style="background: #eee; border: 1px solid #777; border-radius: 4px; padding: 8px; cursor: pointer"/></div>
            </form>
        </div>
<?php

        return ob_get_clean();
    }

    public function isSingleColumn() {
        return false;
    }
}