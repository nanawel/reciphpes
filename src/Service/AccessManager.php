<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class AccessManager
{
    private const SESSION_LOGGED_IN_FLAG = 'is-logged-in';
    private const SESSION_WRITE_ACCESS_FLAG = 'has-write-access';

    /** @var Session */
    private $session;

    /** @var string */
    private $writeAccessPassword;

    /**
     * @param Session $session
     * @param string $writeAccessPassword
     */
    public function __construct(
        Session $session,
        $writeAccessPassword
    ) {
        $this->session = $session;
        $this->writeAccessPassword = $writeAccessPassword;
    }

    /**
     * @return bool
     */
    public function isRestrictedAccessEnabled() {
        return strlen($this->writeAccessPassword) > 0;
    }

    /**
     * @return bool
     */
    public function isSignedIn() {
        return $this->session->get(self::SESSION_LOGGED_IN_FLAG);
    }

    /**
     * @return bool
     */
    public function hasWriteAccess() {
        return ! $this->isRestrictedAccessEnabled()
            || $this->session->get(self::SESSION_WRITE_ACCESS_FLAG);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function signIn($password) {
        if ($password === $this->writeAccessPassword) {
            $this->session->set(self::SESSION_LOGGED_IN_FLAG, true);
            $this->session->set(self::SESSION_WRITE_ACCESS_FLAG, true);

            return true;
        }

        return false;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response
     * @return void
     */
    public function signOut(\Symfony\Component\HttpFoundation\Response $response) {
        $this->session->clear();
        $response->headers->clearCookie(ini_get('session.name'));
    }
}
