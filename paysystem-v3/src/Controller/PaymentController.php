<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PaySystem\DTO\CreatePaymentRequest;
use PaySystem\DTO\PaymentResponse;
use PaySystem\Entity\Payment;
use PaySystem\Enum\CurrencyType;
use PaySystem\Enum\PaymentMethod;
use PaySystem\Enum\PaymentStatus;
use PaySystem\Exception\NotFoundException;
use PaySystem\Exception\ValidationException;
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

    public function index(Request $request, Response $response): Response
    {
        $userId = (string)$request->attributes->get('userId');
        $statusFilter = $request->query->get('status');

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
        $userId = (string)$request->attributes->get('userId');

        try
        {
            $this->paymentService->create(
                new CreatePaymentRequest(
                    userId: $userId,
                    amount: (float)$request->request->get('amount', 0),
                    description: (string)$request->request->get('description', ''),
                    currency: CurrencyType::from((string)$request->request->get('currency', 'RUB')),
                    method: PaymentMethod::from((string)$request->request->get('method', 'credit_card')),
                )
            );

            $_SESSION['flash'] = ['success' => 'Платёж успешно создан'];

            return $this->redirect('/payments');
        }
        catch (ValidationException $e)
        {
            return $this->view('payments/create', [
                'title' => 'Новый платёж',
                'currencies' => CurrencyType::cases(),
                'methods' => PaymentMethod::cases(),
                'errors' => [$e->getMessage()],
                'old' => [
                    'amount' => $request->request->get('amount'),
                    'description' => $request->request->get('description'),
                    'currency' => $request->request->get('currency'),
                    'method' => $request->request->get('method'),
                ],
            ]);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->toArray();

        $payment = $this->paymentService->create(
            new CreatePaymentRequest(
                userId: (string)($data['userId'] ?? $request->attributes->get('userId')),
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
        $payment = $this->paymentService->show((string)$request->attributes->get('id'));

        if (!$payment instanceof Payment)
        {
            throw new NotFoundException('Payment not found');
        }

        return $this->json(PaymentResponse::fromEntity($payment)->toArray());
    }

    public function showAllByUserId(Request $request, Response $response): Response
    {
        $userId = (string)$request->attributes->get('userId');
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
        $userId = (string)$request->attributes->get('userId');
        $status = (string)$request->attributes->get('status');
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
        $this->paymentService->refund((string)$request->attributes->get('id'));

        return $this->json(['status' => 'refunded']);
    }
}
