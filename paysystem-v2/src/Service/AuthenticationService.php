<?php
declare(strict_types=1);

class AuthenticationService
{
    private const SESSION_KEY = 'userId';
    private UserServiceInterface $userService;

    public function __construct(
        UserServiceInterface $userService
    )
    {
        $this->userService = $userService;
    }

    public function authenticate(string $email, string $password): User
    {
        $user = $this->userService->findByEmail($email);

        if (!$user)
        {
            throw new RuntimeException('User not found');
        }

        if (!password_verify($password, $user->password))
        {
            throw new RuntimeException('Invalid password');
        }

        $_SESSION[self::SESSION_KEY] = $user->id;

        return $user;
    }

    public function isAuthenticated(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    public function getCurrentUser(): ?User
    {
        if (!$this->isAuthenticated())
        {
            return null;
        }

        return $this->userService->findById($_SESSION[self::SESSION_KEY]);
    }

    public function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        session_destroy();
    }
}