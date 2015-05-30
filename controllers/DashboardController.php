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

        switch ($page) {
            case self::PAGE_EDIT:
                $output = $this->renderEdit();
                $edit_selected = ' selected';
                break;
            case self::PAGE_SETTINGS:
                $output = '';
                $settings_selected = ' selected';
                break;
            case self::PAGE_HOME:
            default:
                $output = $this->renderHome($student_seat);
                $home_selected = ' selected';
                break;
        }

        ob_start();
        ?>
        <header class="bg-blue">
            <div id="header-container">
                <span id="header-title">
                    Home
                </span>
            </div>
        </header>
        <div id="side-nav">
            <div id="nav-welcome">
                Hey there, <?php echo $this->getSessionManager()->getSession()->getFirstName() ?>!<br />
                <?= makelink('logout', 'Log out') ?>
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
        </div>
        <div id="content">
            <?= $output ?>
        </div>
        <?php

        return ob_get_clean();
    }

    public function renderHome($studentSeat) {
        ob_start();

        $seats = get_all_seats_and_students();
        $userId = $this->getSessionManager()->getSession()->getStudentID();

        $seatPicker = new SeatPickerComponent('dashboard_seat_picker', $userId, $seats, false);
        $seatPicker->setRenderUserRows(true);

        $message = $studentSeat ? 'You are currently seated at: <strong>'.get_seat_label($studentSeat).'</strong>.'
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
        $userId = $this->getSessionManager()->getSession()->getStudentID();

        $seatPicker = new SeatPickerComponent('dashboard_seat_picker', $userId, $seats, true);

        ?>
        <div class="center-text">
            <div class="center-wrap">
                <p class="title">Click on an available seat to pick it.</p>
                <span>Blue seats are reserved for boys and gray seats are reserved for girls.</span>
                <?= $seatPicker->renderView() ?>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    public function isSingleColumn() {
        return false;
    }
}