<?php

namespace App\Security;

use App\Model\UserFacade;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class AppAuthenticator implements Authenticator
{

    protected UserFacade $userFacade;

    protected Passwords $passwords;

    public function __construct(UserFacade $userFacade, Passwords $passwords)
    {
        $this->userFacade = $userFacade;
        $this->passwords = $passwords;
    }

    function authenticate(string $user, string $password): IIdentity
    {
        $user = $this->userFacade->findOneByUsername($user);

        if(!$user) {
            throw new AuthenticationException('User not found');
        }

        if(!$this->passwords->verify($password,$user->password)) {
            throw new AuthenticationException('Incorrect password');
        }

        return new SimpleIdentity($user->id, 'user', ['username' => $user->username]);
    }
}