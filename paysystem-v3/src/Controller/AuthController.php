<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Throwable;

use App\DTO\CreateUserRequest;
use App\Exception\ValidationException;
use App\Service\AuthenticationServiceInterface;
use App\Service\JwtTokenServiceInterface;
use App\Service\UserServiceInterface;
use Twig\Environment;

class AuthController extends AbstractController
{
    private const TOKEN_COOKIE = 'access_token';
    private const TOKEN_TTL = 3600;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly Environment $twig,
        private readonly AuthenticationServiceInterface $authenticationService,
        private readonly JwtTokenServiceInterface $jwtTokenService,
        private readonly UserServiceInterface $userService,
        private UrlGeneratorInterface $urlGenerator
    )
    {
        parent::__construct($requestStack, $twig);
    }

    #[Route('/login', name: 'login_form', methods: ['GET'])]
    public function loginForm(Request $request, Response $response): Response
    {
        return $this->view('auth/login', ['title' => 'Вход']);
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $email = (string)$request->request->get('email', '');
        $password = (string)$request->request->get('password', '');

        try
        {
            $user = $this->authenticationService->authenticate($email, $password);
            $token = $this->jwtTokenService->generate([
                'user_id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->fullName,
            ]);

            $session->getFlashBag()->add('success', "Добро пожаловать, {$user->fullName}!");

            return $this->setTokenCookie($token);
        }
        catch (Throwable $e)
        {
            return $this->view('auth/login', [
                'title' => 'Вход',
                'errors' => ['Неверный email или пароль'],
                'old'    => ['email' => $email],
            ]);
        }
    }

    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(Request $request, Response $response): Response
    {
        return $this->view('auth/register', ['title' => 'Регистрация']);
    }

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request, SessionInterface $session): Response
    {
        try {
            $user = $this->userService->create(
                new CreateUserRequest(
                    email: (string)$request->request->get('email', ''),
                    password: (string)$request->request->get('password', ''),
                    passwordConfirm: (string)$request->request->get('passwordConfirm', ''),
                    fullName: (string)$request->request->get('fullName', ''),
                    phone: (string)$request->request->get('phone', ''),
                )
            );

            $session->getFlashBag()->add('success', 'Аккаунт создан. Войдите в систему.');

            return $this->redirect('/login');
        }
        catch (ValidationException $e)
        {
            return $this->view('auth/register', [
                'title' => 'Регистрация',
                'errors' => [$e->getMessage()],
                'old' => $request->request->get('_', []) ?? [],
            ]);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(Request $request, Response $response): Response
    {
        $this->authenticationService->logout();
        return $this->clearTokenCookie();
    }

    private function setTokenCookie(string $token): Response
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

    private function clearTokenCookie(): Response
    {
        $response = $this->redirect($this->urlGenerator->generate('login_form'));

        $response->headers->setCookie(Cookie::create(
            self::TOKEN_COOKIE,
            null,
            1
        ));

        return $response;
    }
}
