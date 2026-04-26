<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;

interface AuthenticationServiceInterface
{
    public function authenticate(string $email, string $password): User;

    public function logout(): void;
}
