<?php
declare(strict_types=1);

namespace PaySystem\Service;

use PaySystem\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface AuthenticationServiceInterface
{
    public function authenticate(string $email, string $password): User;

    public function logout(SessionInterface $session): void;
}
