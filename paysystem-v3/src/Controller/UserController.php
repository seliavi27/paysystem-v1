<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

use App\DTO\CreateUserRequest;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Service\PaymentServiceInterface;
use App\Service\UserServiceInterface;

class UserController extends AbstractController
{
    private const string UUID_REGEX   = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly PaymentServiceInterface $paymentService,
    )
    {

    }

    /**
     * @throws NotFoundException
     */
    #[Route('/profile', name: 'user_profile', methods: ['GET'])]
    public function profile(): Response
    {
        $user = $this->getUser();

        if ($user === null)
        {
            throw new NotFoundException('User not found');
        }

        $payments = $this->paymentService->showAllByUserId($user->getUserIdentifier());

        return $this->render('users/profile.html.twig', [
            'title'         => 'Профиль',
            'user'          => $user,
            'paymentsCount' => count($payments),
            'paymentsSum'   => array_sum(array_map(fn($p) => $p->getAmount(), $payments)),
        ]);
    }

    #[Route('/api/users/register', name: 'api_user_register', methods: ['POST'])]
    public function create(Request $request): JsonResponse
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
        catch (Exception $e)
        {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    #[Route(
        '/api/users/{id}',
        name: 'api_user_show',
        requirements: ['id' => self::UUID_REGEX],
        methods: ['GET'])]
    public function show(string $id): Response
    {
        $user = $this->userService->findById($id);

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
