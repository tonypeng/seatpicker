<?php
require_once __DIR__.'/../components/SeatPickerComponent.php';

class OnboardingController extends SiteController {
    const KEY_STEP = 'step';

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

        if ($session->getOnboarded()) {
            throw new RedirectException(linkto('/dashboard'));
        }

        $step = 0;
        if ($request->hasval(self::KEY_STEP)) {
            $step = $request->getString(self::KEY_STEP);
        }

        $output = '';

        switch ($step) {
            case 2:
                $output = $this->renderAddPhoneticName();
                break;
            case 3:
                $output = $this->renderFinish();
                break;
            case 1:
                $output = $this->renderConfirmNameAndGender();
                break;
            default:
                $step = 0;
                $output = $this->renderWelcome();
                break;
        }

        ob_start();
        ?>
        <header class="bg-blue">
            <div id="header-container">
                <p id="header-title-large">
                    Welcome!
                </p>
                <?php if ($step) { ?><p class="subtitle">Step <?= $step ?></p><?php } ?>
            </div>
        </header>
        <div id="onboarding-content">
            <div id="onboarding-vertical-wrap">
                <div id="onboarding-vertical-center">
                    <div id="onboarding-island">
                        <?= $output ?>
                    </div>
                </div>
            </div>
        </div>
        <footer class="onboarding-footer">
            <div style="width: 50%; float: left; min-height: 1px; text-align: left;">
                <?php if ($step > 0) { ?>
                <a class="control" href="<?= linkto('/onboarding?step='.($step-1)) ?>">Back</a>
                <?php } ?>
            </div>
            <div style="width: 50%; float: left; text-align: right;">
                <?php if ($step < 3) { ?>
                <a class="control" href="<?= linkto('/onboarding?step='.($step+1)) ?>">Next</a>
                <?php } else { ?>
                    <a class="control" href="<?= linkto('/dashboard?page=edit') ?>">Finish</a>
                <?php } ?>
            </div>
            <div style="clear: both;"></div>
        </footer>
        <?php

        return ob_get_clean();
    }

    public function renderWelcome() {
        ob_start();

        $session = $this->getSessionManager()->getSession();

        ?>
            Hey, <?= $session->getFirstName() ?>!<br />
            <br />
            It looks like this is your first time signing in.<br />
            <br />Before we get started, we'll need you to confirm some information. Click "Next" below to get started.
        <?php

        return ob_get_clean();
    }

    public function renderConfirmNameAndGender() {
        ob_start();

        $session = $this->getSessionManager()->getSession();

        $male_selected = $session->getGender() ? '' : ' selected="selected"';
        $female_selected = $session->getGender() ? ' selected="selected"' : '';

        ?>
            Here's the information we have on file for you. Take a look and ensure it is correct.<br />
            <br />
            <form method="POST" action="<?= linkto('/onboarding/submit') ?>">
                <table style="width: 100%">
                    <tr>
                        <th class="login-label">First name</th><td class="login-input"><input type="text" value="<?= $session->getFirstName() ?>" name="first_name" /></td>
                    </tr>
                    <tr>
                        <th class="login-label">Last name</th><td class="login-input"><input type="text" value="<?= $session->getLastName() ?>" name="last_name" /></td>
                    </tr>
                </table>
                <input type="hidden" name="type" value="name_gender" />
                <div class="center-text"><input type="submit" value="Update" style="background: #eee; border: 1px solid #777; border-radius: 4px; padding: 8px;"/></div>
            </form>
        <?php

        return ob_get_clean();
    }

    public function renderAddPhoneticName() {
        ob_start();

        $session = $this->getSessionManager()->getSession();

        $male_selected = $session->getGender() ? '' : ' selected="selected"';
        $female_selected = $session->getGender() ? ' selected="selected"' : '';

        ?>
        A phonetic spelling of your full name helps announcers pronounce your name. This field is optional but recommended.<br />
        <br />
        <form method="POST" action="<?= linkto('/onboarding/submit') ?>">
            <table style="width: 100%">
                <tr>
                    <th class="login-label" style="width: 40%">Phonetic spelling</th><td class="login-input"><input type="text" value="<?= $session->getPhoneticName() ?>" name="phonetic_name" placeholder="Example: toe knee pen-g" /></td>
                </tr>
            </table>
            <input type="hidden" name="type" value="phonetic" />
            <div class="center-text"><input type="submit" value="Update" style="background: #eee; border: 1px solid #777; border-radius: 4px; padding: 8px;"/></div>
        </form>
        <?php

        return ob_get_clean();
    }

    public function renderFinish() {
        ob_start();

        // mark as onboarded
        try {
            $stmt = db()->prepare('UPDATE `students` SET onboarded=1 WHERE id=:id');
            $stmt->execute(array(':id' => $this->getSessionManager()->getSession()->getUserID()));
        } catch (PDOException $pdoe) {
            echo 'Oops! An error occurred. Please try again.';
            return ob_get_clean();
        }

        ?>
        ...and that's it! <br />
        <br />
        Click "Finish" below to choose your seat.
        <?php

        return ob_get_clean();
    }

    public function isSingleColumn() {
        return false;
    }
}