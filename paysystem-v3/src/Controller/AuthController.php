<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;
use Throwable;

use App\DTO\CreateUserRequest;
use App\Exception\ValidationException;
use App\Service\AuthenticationServiceInterface;
use App\Service\JwtTokenServiceInterface;
use App\Service\UserServiceInterface;

class AuthController extends AbstractController
{
    private const string TOKEN_COOKIE = 'access_token';
    private const int TOKEN_TTL = 3600;

    public function __construct(
        private readonly AuthenticationServiceInterface $authenticationService,
        private readonly JwtTokenServiceInterface $jwtTokenService,
        private readonly UserServiceInterface $userService
    )
    {

    }

    #[Route('/login', name: 'login_form', methods: ['GET'])]
    public function loginForm(): Response
    {
        return $this->render('auth/login.html.twig', [
            'title' => 'Вход'
        ]);
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
                'user_id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->fullName,
            ]);

            $this->addFlash('success', "Добро пожаловать, {$user->fullName}!");

            return $this->setTokenCookie($token);
        }
        catch (Throwable $e)
        {
            return $this->render('auth/login.html.twig', [
                'title' => 'Вход',
                'errors' => ['Неверный email или пароль'],
                'old'    => ['email' => $email],
            ]);
        }
    }

    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(): Response
    {
        return $this->render('auth/register.html.twig', [
            'title' => 'Регистрация'
        ]);
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

            $this->addFlash('success', 'Аккаунт создан. Войдите в систему.');

            return $this->redirectToRoute('login_form');
        }
        catch (Exception $e)
        {
            return $this->render('auth/register.html.twig', [
            'title' => 'Регистрация',
            'errors' => [$e->getMessage()],
            'old' => $request->request->all(),
        ]);
        }
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): Response
    {
        $this->authenticationService->logout();
        return $this->clearTokenCookie();
    }

    private function setTokenCookie(string $token): Response
    {
        $response = $this->redirectToRoute('payments_index');

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
        $response = $this->redirectToRoute('login_form');

        $response->headers->setCookie(Cookie::create(
            self::TOKEN_COOKIE,
            null,
            1
        ));

        return $response;
    }
}
