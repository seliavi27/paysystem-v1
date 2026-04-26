<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

use PaySystem\DTO\CreateUserRequest;
use PaySystem\Exception\ValidationException;
use PaySystem\Service\AuthenticationServiceInterface;
use PaySystem\Service\JwtTokenServiceInterface;
use PaySystem\Service\UserServiceInterface;
use PaySystem\View\TemplateEngine;

class AuthController extends AbstractController
{
    private const string TOKEN_COOKIE = 'access_token';
    private const int TOKEN_TTL = 3600;

    public function __construct(
        TemplateEngine $templateEngine,
        private readonly AuthenticationServiceInterface $authenticationService,
        private readonly JwtTokenServiceInterface $jwtTokenService,
        private readonly UserServiceInterface $userService,
        private readonly SessionInterface $session,
        private readonly UrlGeneratorInterface $urlGenerator,
    )
    {
        parent::__construct($templateEngine);
    }

    #[Route('/login', name: 'login_form', methods: ['GET'])]
    #[Route('/', name: 'home', methods: ['GET'])]
    public function loginForm(Request $request): Response
    {
        return $this->view($request, 'auth/login', ['title' => 'Вход']);
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $email = (string)$request->request->get('email', '');
        $password = (string)$request->request->get('password', '');

        try
        {
            $user = $this->authenticationService->authenticate($email, $password);
            $token = $this->jwtTokenService->generate([
                'user_id'  => $user->id,
                'email'    => $user->email,
                'fullName' => $user->fullName,
            ]);

            $this->session->getFlashBag()->add('success', "Добро пожаловать, {$user->fullName}!");

            return $this->setTokenCookie($token);
        }
        catch (Throwable $e)
        {
            return $this->view($request, 'auth/login', [
                'title'  => 'Вход',
                'errors' => ['Неверный email или пароль'],
                'old'    => ['email' => $email],
            ]);
        }
    }

    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(Request $request): Response
    {
        return $this->view($request, 'auth/register', ['title' => 'Регистрация']);
    }

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        try {
            $this->userService->create(
                new CreateUserRequest(
                    email: (string)$request->request->get('email', ''),
                    password: (string)$request->request->get('password', ''),
                    passwordConfirm: (string)$request->request->get('passwordConfirm', ''),
                    fullName: (string)$request->request->get('fullName', ''),
                    phone: (string)$request->request->get('phone', ''),
                )
            );

            $this->session->getFlashBag()->add('success', 'Аккаунт создан. Войдите в систему.');

            return $this->redirect($this->urlGenerator->generate('login_form'));
        }
        catch (ValidationException $e)
        {
            return $this->view($request, 'auth/register', [
                'title'  => 'Регистрация',
                'errors' => [$e->getMessage()],
                'old'    => [
                    'email'    => (string)$request->request->get('email', ''),
                    'fullName' => (string)$request->request->get('fullName', ''),
                    'phone'    => (string)$request->request->get('phone', ''),
                ],
            ]);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(Request $request): Response
    {
        $this->authenticationService->logout($this->session);
        return $this->clearTokenCookie();
    }

    private function setTokenCookie(string $token): RedirectResponse
    {
        $response = $this->redirect($this->urlGenerator->generate('payments_index'));

        $response->headers->setCookie(Cookie::create(
            name: self::TOKEN_COOKIE,
            value: $token,
            expire: time() + self::TOKEN_TTL,
            path: '/',
            secure: false,
            httpOnly: true,
            sameSite: Cookie::SAMESITE_LAX,
        ));

        return $response;
    }

    private function clearTokenCookie(): RedirectResponse
    {
        $response = $this->redirect($this->urlGenerator->generate('login_form'));

        $response->headers->setCookie(Cookie::create(
            name: self::TOKEN_COOKIE,
            value: '',
            expire: 1,
            path: '/',
            httpOnly: true,
        ));

        return $response;
    }
}
