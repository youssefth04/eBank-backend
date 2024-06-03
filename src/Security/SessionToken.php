<?php

namespace App\Security;

use App\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class SessionToken extends AbstractToken
{
    private $sessionToken;

    public function __construct($sessionToken, array $roles = [])
    {
        parent::__construct($roles);

        $this->sessionToken = $sessionToken;

        // If the user has roles, consider it authenticated
    }

    public function getCredentials()
    {
        return '';
    }

    public function getSessionToken()
    {
        return $this->sessionToken;
    }
}