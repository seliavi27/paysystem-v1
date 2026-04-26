<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PaySystem\DTO\CreateUserRequest;
use PaySystem\Exception\NotFoundException;
use PaySystem\Exception\ValidationException;
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

    public function profile(Request $request): Response
    {
        $userId = (string)$request->attributes->get('userId');
        $user   = $this->userService->findById($userId);

        if ($user === null)
        {
            throw new NotFoundException('User not found');
        }

        $payments = $this->paymentService->showAllByUserId($userId);

        return $this->view($request, 'users/profile', [
            'title'        => 'Профиль',
            'user'         => $user,
            'paymentsCount'=> count($payments),
            'paymentsSum'  => array_sum(array_map(fn($p) => $p->amount, $payments)),
        ]);
    }

    // ===== JSON API =====

    public function create(Request $request): Response
    {
        try
        {
            $data = $request->toArray();

            $user = $this->userService->create(new CreateUserRequest(
                email:           (string)($data['email'] ?? ''),
                password:        (string)($data['password'] ?? ''),
                passwordConfirm: (string)($data['passwordConfirm'] ?? ''),
                fullName:        (string)($data['fullName'] ?? ''),
                phone:           (string)($data['phone'] ?? ''),
            ));

            return $this->json([
                'id'       => $user->id,
                'email'    => $user->email,
                'fullName' => $user->fullName,
            ], Response::HTTP_CREATED);
        }
        catch (ValidationException $e)
        {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(Request $request): Response
    {
        $user = $this->userService->findById((string)$request->attributes->get('id'));

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
