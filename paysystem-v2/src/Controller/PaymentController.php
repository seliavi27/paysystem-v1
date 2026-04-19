<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\DTO\PaymentResponse;
use PaySystem\Entity\Payment;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Exception\NotFoundException;
use PaySystem\Exception\ValidationException;
use PaySystem\Request;
use PaySystem\Response;
use PaySystem\Service\PaymentServiceInterface;
use PaySystem\View\TemplateEngine;

class PaymentController extends AbstractController
{
    public function __construct(
        TemplateEngine $templateEngine,
        private readonly PaymentServiceInterface $paymentService
    )
    {
        parent::__construct($templateEngine);
    }

    // ===== HTML =====

    public function index(Request $request, Response $response): Response
    {
        $userId = (string)$request->getAttribute('userId');
        $statusFilter = $request->getQuery('status');

        $payments = $statusFilter
            ? $this->paymentService->showAllByStatus($userId, (string)$statusFilter)
            : $this->paymentService->showAllByUserId($userId);

        return $this->view('payments/list', [
            'title' => 'Платежи',
            'payments' => $payments,
            'statusFilter' => $statusFilter,
            'statuses' => PaymentStatus::cases(),
        ]);
    }

    public function createForm(Request $request, Response $response): Response
    {
        return $this->view('payments/create', [
            'title' => 'Новый платёж',
            'currencies' => CurrencyType::cases(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $userId = (string)$request->getAttribute('userId');

        try {
            $this->paymentService->create(
                new CreatePaymentRequest(
                    userId: $userId,
                    amount: (float)$request->getPost('amount', 0),
                    description: (string)$request->getPost('description', ''),
                    currency: CurrencyType::from((string)$request->getPost('currency', 'RUB')),
                    method: PaymentMethod::from((string)$request->getPost('method', 'credit_card')),
                )
            );

            $_SESSION['flash'] = ['success' => 'Платёж успешно создан'];

            return $this->redirect('/payments');
        } catch (ValidationException $e) {
            return $this->view('payments/create', [
                'title' => 'Новый платёж',
                'currencies' => CurrencyType::cases(),
                'methods' => PaymentMethod::cases(),
                'errors' => [$e->getMessage()],
                'old' => [
                    'amount' => $request->getPost('amount'),
                    'description' => $request->getPost('description'),
                    'currency' => $request->getPost('currency'),
                    'method' => $request->getPost('method'),
                ],
            ]);
        }
    }

    // ===== JSON API =====

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getJson();

        $payment = $this->paymentService->create(
            new CreatePaymentRequest(
                userId: (string)($data['userId'] ?? $request->getAttribute('userId')),
                amount: (float)($data['amount'] ?? 0),
                description: (string)($data['description'] ?? ''),
                currency: CurrencyType::from($data['currency'] ?? 'RUB'),
                method: PaymentMethod::from($data['method'] ?? 'credit_card'),
            )
        );

        return $this->json(PaymentResponse::fromEntity($payment)->toArray(), 201);
    }

    public function show(Request $request, Response $response): Response
    {
        $payment = $this->paymentService->show((string)$request->getAttribute('id'));

        if (!$payment instanceof Payment)
        {
            throw new NotFoundException('Payment not found');
        }

        return $this->json(PaymentResponse::fromEntity($payment)->toArray());
    }

    public function showAllByUserId(Request $request, Response $response): Response
    {
        $userId = (string)$request->getAttribute('userId');
        $payments = $this->paymentService->showAllByUserId($userId);

        return $this->json([
            'success' => true,
            'count' => count($payments),
            'data' => array_map(
                fn(Payment $p) => PaymentResponse::fromEntity($p)->toArray(),
                $payments
            ),
        ]);
    }

    public function showAllByStatus(Request $request, Response $response): Response
    {
        $userId = (string)$request->getAttribute('userId');
        $status = (string)$request->getAttribute('status');
        $payments = $this->paymentService->showAllByStatus($userId, $status);

        return $this->json([
            'success' => true,
            'count' => count($payments),
            'data' => array_map(
                fn(Payment $p) => PaymentResponse::fromEntity($p)->toArray(),
                $payments
            ),
        ]);
    }

    public function refund(Request $request, Response $response): Response
    {
        $this->paymentService->refund((string)$request->getAttribute('id'));

        return $this->json(['status' => 'refunded']);
    }
}
