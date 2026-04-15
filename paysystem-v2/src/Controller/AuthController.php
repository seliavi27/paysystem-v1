<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use Exception;
use PaySystem\DTO\UserResponse;
use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\AuthenticationServiceInterface;
use PaySystem\Service\JwtTokenServiceInterface;
use PaySystem\Service\UserService;
use PaySystem\View\TemplateEngine;

class AuthController extends AbstractController
{
    private readonly AuthenticationServiceInterface $authenticationService;
    private readonly JwtTokenServiceInterface $jwtTokenService;

    public function __construct(
        TemplateEngine $templateEngine,
        AuthenticationServiceInterface $authenticationService,
        JwtTokenServiceInterface $jwtTokenService
    )
    {
        parent::__construct($templateEngine);
        $this->authenticationService = $authenticationService;
        $this->jwtTokenService = $jwtTokenService;
    }

    public function loginForm(Request $request, Response $response): Response
    {
        return $this->view('auth/login', [
            'title' => 'Вход в систему',
        ]);
    }

    public function login(Request $request, Response $response): Response
    {
//        $data = $request->getJson();
        $email = $request->getPost('email', '');
        $password = $request->getPost('password', '');

        if (empty($email) || empty($password))
        {
//                return $response->setStatusCode(400)
//                    ->setJson([
//                        'error' => 'Bad Request',
//                        'message' => 'Email and password are required'
//                    ]);
            return $this->view('auth/login', [
                'title'  => 'Вход в систему',
                'errors' => ['Неверный email или пароль'],
                'old'    => ['email' => $email],
            ]);
        }

        try
        {

            $user = $this->authenticationService->authenticate(
                $email,
                $password
            );

//            $token = $this->jwtTokenService->generate([
//                'user_id' => $user->id,
//                'email' => $user->email,
//                'full_name' => $user->fullName
//            ]);

            $_SESSION['user_id'] = $user->id;

//            $refreshToken = $this->jwtTokenService->generate([
//                'user_id' => $user->id,
//                'type' => 'refresh'
//            ]);
//
//            return $response->setJson([
//                'success' => true,
//                'message' => 'Login successful',
//                'data' => [
//                    'access_token' => $token,
//                    'refresh_token' => $refreshToken,
//                    'token_type' => 'Bearer',
//                    'user' => [
//                        'id' => $user->id,
//                        'email' => $user->email,
//                        'full_name' => $user->fullName
//                    ]
//                ]
//            ]);

            return $this->redirect('/dashboard', 201);

        }
        catch (Exception $e)
        {
//            return $response->setStatusCode(401)
//                ->setJson([
//                    'error' => 'Unauthorized',
//                    'message' => $e->getMessage()
//                ]);
            return $this->view('auth/login', [
                'title'  => 'Вход в систему',
                'errors' => ['Неверный email или пароль'],
                'old'    => ['email' => $email],
            ]);
        }
    }

    public function profile(Request $request, Response $response): Response
    {
        $user = $this->authenticationService->getCurrentUser();

        if (!$user)
        {
            return $response->setStatusCode(401)
                ->setJson(['error' => 'Unauthorized']);
        }

        $userResponse = UserResponse::fromEntity($user);

        return $response->setJson([
            'success' => true,
            'data' => $userResponse->toArray()
        ]);
    }

    public function logout(Request $request, Response $response): Response
    {
        $this->authenticationService->logout();

        return $response->setJson([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }
}