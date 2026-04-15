<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use Exception;
use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\DTO\PaymentResponse;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Exception\ValidationException;
use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\PaymentServiceInterface;
use PaySystem\View\TemplateEngine;

class PaymentController extends AbstractController
{
    private PaymentServiceInterface $paymentService;

    public function __construct(
        TemplateEngine $templateEngine,
        PaymentServiceInterface $paymentService
    )
    {
        parent::__construct($templateEngine);
        $this->paymentService = $paymentService;
    }

    public function index(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $payments = $this->paymentService->showAllByUserId($userId);

        return $this->view('payments/list', [
            'title'    => 'Платежи',
            'payments' => $payments,
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        try
        {
            $data = $request->getJson();

            $paymentRequest = new CreatePaymentRequest(
                userId: $data['userId'],
                amount: (float)$data['amount'],
                description: $data['description'] ?? '',
                currency: CurrencyType::from($data['currency'] ?? 'RUB'),
                method: PaymentMethod::from($data['method'] ?? 'credit_card')
            );

            $payment = $this->paymentService->create($paymentRequest);

            return $this->json([
                'id' => $payment->id,
                'status' => $payment->status->value,
                'amount' => $payment->amount,
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
        $id = $request->getAttribute('id');
        $payment = $this->paymentService->show($id);

        if (!$payment)
        {
            return $this->json(['error' => 'Payment not found'], 404);
        }

        return $this->json([
            'id' => $payment->id,
            'status' => $payment->status->value,
            'amount' => $payment->amount,
        ]);
    }

    public function showAllByUserId(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');

        if (!$userId)
        {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        $payments = $this->paymentService->showAllByUserId($userId);


        if (empty($payments))
        {
            return $this->json([
                'data' => [],
                'message' => 'No payments found'
            ], 200);
        }

        $paymentsArray = array_map(function($payment) {
            return [
                'id' => $payment->id,
                'status' => $payment->status->value,
                'amount' => $payment->amount,
                'currency' => $payment->currency->value,
                'description' => $payment->description,
                'createdAt' => $payment->createdAt->format('Y-m-d H:i:s'),
            ];
        }, $payments);

        return $this->json([
            'success' => true,
            'data' => $paymentsArray,
            'count' => count($paymentsArray)
        ]);
    }

    public function showAllByStatus(Request $request, Response $response): Response
    {
        $userId = $request->getAttribute('userId');
        $status = $request->getAttribute('status');

        if (!$userId)
        {
            return $this->json(['error' => 'User not authenticated'], 401);
        }

        if (!$status)
        {
            return $this->json(['error' => 'Status invalid'], 403);
        }

        $payments = $this->paymentService->showAllByStatus($userId, $status);

        if (empty($payments))
        {
            return $this->json([
                'data' => [],
                'message' => 'No payments found'
            ], 200);
        }

        $paymentsArray = array_map(function($payment) {
            return new PaymentResponse(
                $payment->id,
                $payment->userId,
                $payment->amount,
                $payment->description,
                $payment->currency->value,
                $payment->status->value,
                $payment->createdAt)->toArray();
        }, $payments);

        return $this->json([
            'success' => true,
            'data' => $paymentsArray,
            'count' => count($paymentsArray)
        ]);
    }

    public function refund(Request $request, Response $response): Response
    {
        $id = $request->getAttribute('id');
        $this->paymentService->refund($id);

        return $this->json(['status' => 'refunded']);
    }
}