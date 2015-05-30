<?php
class OnboardingUpdateController extends SiteController {
    public function getPageTitle() {
        return '';
    }

    public function renderPage() {
        if(!$this->getSessionManager()->isSignedIn()) {
            throw new RedirectException(linkto('index.php'));
        }

        $req = $this->getRequest();

        if($req->getMethod() != Request::POST) {
            throw new RedirectException(linkto('index.php'));
        }

        if(!$req->hasval('type')) {
            throw new RedirectException(linkto('index.php'));
        }

        $type = $req->getString('type');

        switch ($type) {
            case 'name_gender':
                if (!$req->hasval('first_name') || !$req->hasval('last_name') || !$req->hasval('gender')) {
                    throw new RedirectException(linkto('index.php'));
                }

                $first_name = $req->getString('first_name');
                $last_name = $req->getString('last_name');
                $gender = $req->getInt('gender');


                if ($gender != 0 && $gender != 1) {
                    throw new RedirectException(linkto('index.php'));
                }


                try {
                    $stmt = db()->prepare('UPDATE `students` SET first_name=:first_name, last_name=:last_name, gender=:gender WHERE id=:id');
                    $stmt->execute(array(
                        ':id' => $this->getSessionManager()->getSession()->getUserID(),
                        ':first_name' => $first_name,
                        ':last_name' => $last_name,
                        ':gender' => $gender,
                    ));
                } catch (PDOException $pdoe) {

                }

                throw new RedirectException(linkto('/onboarding?step=1'));
                break;
            case 'phonetic':
                if (!$req->hasval('phonetic_name')) {
                    throw new RedirectException(linkto('index.php'));
                }

                $phonetic_name = $req->getString('phonetic_name');


                try {
                    $stmt = db()->prepare('UPDATE `students` SET phonetic_name=:phonetic_name WHERE id=:id');
                    $stmt->execute(array(
                            ':id' => $this->getSessionManager()->getSession()->getUserID(),
                            ':phonetic_name' => $phonetic_name,
                        ));
                } catch (PDOException $pdoe) {

                }

                throw new RedirectException(linkto('/onboarding?step=2'));
                break;
        }

        throw new RedirectException(linkto('index.php'));
    }
}