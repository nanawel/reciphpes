<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class AccessManager
{
    private const SESSION_LOGGED_IN_FLAG = 'is-logged-in';

    private const SESSION_WRITE_ACCESS_FLAG = 'has-write-access';

    public function __construct(
        private readonly RequestStack $requestStack,
        private string                $writeAccessPassword = ''
    )
    {
    }

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

    public function hasWriteAccess(): bool
    {
        return !$this->isRestrictedAccessEnabled()
            || $this->requestStack->getSession()->get(self::SESSION_WRITE_ACCESS_FLAG);
    }

    public function signIn(string $password): bool
    {
        if ($password === $this->writeAccessPassword) {
            $this->requestStack->getSession()->set(self::SESSION_LOGGED_IN_FLAG, true);
            $this->requestStack->getSession()->set(self::SESSION_WRITE_ACCESS_FLAG, true);

            return true;
        }

        return false;
    }

    public function signOut(\Symfony\Component\HttpFoundation\Response $response): void
    {
        $this->requestStack->getSession()->clear();
        $response->headers->clearCookie(ini_get('session.name'));
    }
}
