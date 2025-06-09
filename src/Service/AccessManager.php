<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class AccessManager
{
    private const SESSION_LOGGED_IN_FLAG = 'is-logged-in';
    private const SESSION_WRITE_ACCESS_FLAG = 'has-write-access';

    /**
     * @param RequestStack $requestStack
     * @param string $writeAccessPassword
     */
    public function __construct(
        private readonly RequestStack $requestStack,
        private                       $writeAccessPassword
    )
    {
    }

    /**
     * @return bool
     */
    public function isRestrictedAccessEnabled(): bool
    {
        return strlen($this->writeAccessPassword) > 0;
    }

    /**
     * @return bool
     */
    public function isSignedIn(): mixed
    {
        return $this->requestStack->getSession()->get(self::SESSION_LOGGED_IN_FLAG);
    }

    /**
     * @return bool
     */
    public function hasWriteAccess(): bool
    {
        return !$this->isRestrictedAccessEnabled()
            || $this->requestStack->getSession()->get(self::SESSION_WRITE_ACCESS_FLAG);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function signIn($password): bool
    {
        if ($password === $this->writeAccessPassword) {
            $this->requestStack->getSession()->set(self::SESSION_LOGGED_IN_FLAG, true);
            $this->requestStack->getSession()->set(self::SESSION_WRITE_ACCESS_FLAG, true);

            return true;
        }

        return false;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response
     * @return void
     */
    public function signOut(\Symfony\Component\HttpFoundation\Response $response): void
    {
        $this->requestStack->getSession()->clear();
        $response->headers->clearCookie(ini_get('session.name'));
    }
}
