<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use PaySystem\DTO\CreateUserRequest;
use PaySystem\Exception\NotFoundException;
use PaySystem\Exception\ValidationException;
use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\PaymentServiceInterface;
use PaySystem\Service\UserServiceInterface;
use PaySystem\View\TemplateEngine;

class UserController extends AbstractController
{
    public function __construct(
        TemplateEngine                           $templateEngine,
        private readonly UserServiceInterface    $userService,
        private readonly PaymentServiceInterface $paymentService,
    )
    {
        parent::__construct($templateEngine);
    }

    // ===== HTML =====

    public function profile(Request $request, Response $response): Response
    {
        $userId = (string)$request->getAttribute('userId');
        $user   = $this->userService->findById($userId);

        if ($user === null)
        {
            throw new NotFoundException('User not found');
        }

        $payments = $this->paymentService->showAllByUserId($userId);

        return $this->view('users/profile', [
            'title'        => 'Профиль',
            'user'         => $user,
            'paymentsCount'=> count($payments),
            'paymentsSum'  => array_sum(array_map(fn($p) => $p->amount, $payments)),
        ]);
    }

    // ===== JSON API =====

    public function create(Request $request, Response $response): Response
    {
        try
        {
            $user = $this->userService->create(new CreateUserRequest(
                email:           (string)($request->getJson()['email'] ?? ''),
                password:        (string)($request->getJson()['password'] ?? ''),
                passwordConfirm: (string)($request->getJson()['passwordConfirm'] ?? ''),
                fullName:        (string)($request->getJson()['fullName'] ?? ''),
                phone:           (string)($request->getJson()['phone'] ?? ''),
            ));

            return $this->json([
                'id'       => $user->id,
                'email'    => $user->email,
                'fullName' => $user->fullName,
            ], 201);
        }
        catch (ValidationException $e)
        {
            return $this->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request, Response $response): Response
    {
        $user = $this->userService->findById((string)$request->getAttribute('id'));

        if ($user === null)
        {
            throw new NotFoundException('User not found');
        }

        return $this->json([
            'id'       => $user->id,
            'fullName' => $user->fullName,
            'email'    => $user->email,
            'balance'  => $user->balance,
        ]);
    }
}
