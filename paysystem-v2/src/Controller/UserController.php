<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use Exception;
use PaySystem\DTO\CreateUserRequest;
use PaySystem\Exception\ValidationException;
use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\UserService;

class UserController extends AbstractController
{
    private readonly UserService $userService;

    public function __construct(
        UserService $userService
    )
    {
        $this->userService = $userService;
    }

    public function create(Request $request, Response $response): Response
    {
        try
        {
            $data = $request->getJson();

            $userRequest = new CreateUserRequest(
                email: $data['email'],
                password: $data['password'],
                passwordConfirm: $data['passwordConfirm'],
                fullName: $data['fullName'],
                phone: $data['phone']
            );

            $user = $this->userService->create($userRequest);

            return $this->json([
                'id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->fullName
            ], 201);

        }
        catch (ValidationException $e)
        {
            return $this->json(['error' => $e->getMessage()], 422);
        }
        catch (Exception $e)
        {
            return $this->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function show(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('id');
        $user = $this->userService->findById($userId);

        if (is_null($user))
        {
            return $this->json(['error' => 'User not found'], 404);
        }

        return $this->json([
            'id' => $user->id,
            'fullName' => $user->fullName,
            'email' => $user->email,
            'balance' => $user->balance,
        ]);
    }
}