<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use App\DTO\CreateUserRequest;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Service\PaymentServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class UserController extends AbstractController
{
    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly Environment $twig,
        private readonly UserServiceInterface $userService,
        private readonly PaymentServiceInterface $paymentService,
    )
    {
        parent::__construct($requestStack, $twig);
    }

    public function profile(Request $request): Response
    {
        $userId = (string)$request->attributes->get('userId');
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

    #[Route('/users/register', methods: ['POST'])]
    public function create(Request $request): Response
    {
        try
        {
            $requestArray = $request->toArray();

            $user = $this->userService->create(new CreateUserRequest(
                email:           (string)($request->$requestArray['email'] ?? ''),
                password:        (string)($request->$requestArray['password'] ?? ''),
                passwordConfirm: (string)($request->$requestArray['passwordConfirm'] ?? ''),
                fullName:        (string)($request->$requestArray['fullName'] ?? ''),
                phone:           (string)($request->$requestArray['phone'] ?? ''),
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

    public function show(Request $request): Response
    {
        $user = $this->userService->findById(
            (string)$request->attributes->get('id'));

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
