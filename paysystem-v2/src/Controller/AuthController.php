<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use PaySystem\DTO\CreateUserRequest;
use PaySystem\Exception\ValidationException;
use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\AuthenticationServiceInterface;
use PaySystem\Service\JwtTokenServiceInterface;
use PaySystem\Service\UserServiceInterface;
use PaySystem\View\TemplateEngine;
use Throwable;

class AuthController extends AbstractController
{
    private const TOKEN_COOKIE = 'access_token';
    private const TOKEN_TTL = 3600;

    public function __construct(
        protected readonly TemplateEngine $templateEngine,
        private readonly AuthenticationServiceInterface $authenticationService,
        private readonly JwtTokenServiceInterface $jwtTokenService,
        private readonly UserServiceInterface $userService
    )
    {
        parent::__construct($templateEngine);
    }

    public function loginForm(Request $request, Response $response): Response
    {
        return $this->view('auth/login', ['title' => 'Вход']);
    }

    public function login(Request $request, Response $response): Response
    {
        $email = (string)$request->getPost('email', '');
        $password = (string)$request->getPost('password', '');

        try
        {
            $user = $this->authenticationService->authenticate($email, $password);
            $token = $this->jwtTokenService->generate([
                'user_id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->fullName,
            ]);

            $this->setTokenCookie($token);
            $_SESSION['flash'] = ['success' => "Добро пожаловать, {$user->fullName}!"];

            return $this->redirect('/payments');
        } catch (Throwable $e)
        {
            return $this->view('auth/login', [
                'title' => 'Вход',
                'errors' => ['Неверный email или пароль'],
                'old'    => ['email' => $email],
            ]);
        }
    }

    public function registerForm(Request $request, Response $response): Response
    {
        return $this->view('auth/register', ['title' => 'Регистрация']);
    }

    public function register(Request $request, Response $response): Response
    {
        try {
            $user = $this->userService->create(
                new CreateUserRequest(
                    email: (string)$request->getPost('email', ''),
                    password: (string)$request->getPost('password', ''),
                    passwordConfirm: (string)$request->getPost('passwordConfirm', ''),
                    fullName: (string)$request->getPost('fullName', ''),
                    phone: (string)$request->getPost('phone', ''),
                )
            );

            $_SESSION['flash'] = ['success' => 'Аккаунт создан. Войдите в систему.'];

            return $this->redirect('/login');
        } catch (ValidationException $e) {
            return $this->view('auth/register', [
                'title' => 'Регистрация',
                'errors' => [$e->getMessage()],
                'old' => $request->getPost('_', []) ?? [],
            ]);
        }
    }

    public function logout(Request $request, Response $response): Response
    {
        $this->authenticationService->logout();
        $this->clearTokenCookie();

        return $this->redirect('/login');
    }

    private function setTokenCookie(string $token): void
    {
        setcookie(self::TOKEN_COOKIE, $token, [
            'expires' => time() + self::TOKEN_TTL,
            'path' => '/',
            'httponly' => true,
            'secure' => false,
            'samesite' => 'Lax',
        ]);
    }

    private function clearTokenCookie(): void
    {
        setcookie(self::TOKEN_COOKIE, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
