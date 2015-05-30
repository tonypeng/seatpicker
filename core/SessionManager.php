<?php

class SessionManager
{
    const SESSION_COOKIE_NAME = 'lhsgrad__session';

    private $_driver;
    /** @var Session $_session */
    private $_session;

    public function __construct(Request $req, ISessionDriver $driver)
    {
        $this->_driver = $driver;

        $this->_session = new Session(-1, '', '', '', '', false, 0, false);

        if($req->hasCookie(self::SESSION_COOKIE_NAME)) {
            $val = $req->cookie(self::SESSION_COOKIE_NAME);

            try {
                $this->_session = $driver->lookup($val);
            } catch (SessionInvalidKeyException $e) { }
        }
    }

    public function isSignedIn()
    {
        return $this->getSession()->isLoggedIn();
    }

    public function signOut()
    {
        if(!$this->getSession()->isLoggedIn()) {
            throw new SessionNotLoggedInException('Cannot sign out: there is no existing session.');
        }

        Response::setCookie(self::SESSION_COOKIE_NAME, '', time() - 3600);

        $this->_driver->signout($this->getSession()->getUserID());

        $this->_session = new Session(-1, '', '', '', '', false, 0, false);
    }

    public function signIn($username, $password, $duration=-1)
    {
        if($this->getSession()->isLoggedIn()) {
            throw new SessionAlreadyLoggedInException('Cannot sign in: there is an existing session.');
        }

        $stmt = db()->prepare('SELECT * FROM `students` WHERE student_id=:username');
        $stmt->bindValue(':username', $username);

        try {
            $stmt->execute();
        } catch (PDOException $pde) {
            throw new DatabaseException();
        }

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();

            if(password_verify($password, $row['password'])) {
                $duration = $duration < 0 ? (365 * 24 * 60 * 60) : $duration;
                $expire_time = time() + $duration;

                $session_key = $this->_driver->write($row['id'], $username, $expire_time);

                Response::setCookie(self::SESSION_COOKIE_NAME, $session_key, $expire_time);

                return ($this->_session =
                    new Session($row['id'], $row['student_id'], $row['first_name'], $row['last_name'], $row['phonetic_name'], $row['gender'], $row['onboarded'])
                );
            }
        }

        return false;
    }

    public function getSession()
    {
        return $this->_session;
    }
}

class SessionAlreadyLoggedInException extends Exception { }
class SessionNotLoggedInException extends Exception { }