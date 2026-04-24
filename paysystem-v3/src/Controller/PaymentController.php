<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

use App\DTO\CreatePaymentRequest;
use App\DTO\PaymentResponse;
use App\Entity\Payment;
use App\Enum\CurrencyType;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Service\PaymentServiceInterface;
use Twig\Environment;

final class PaymentController extends AbstractController
{
    private const string UUID_REGEX   = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
    private const string STATUS_REGEX = 'pending|processing|completed|failed|refunded';

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly Environment $twig,
        private readonly PaymentServiceInterface $paymentService
    )
    {
        parent::__construct($requestStack, $twig);
    }

    #[Route('/payments/create', name: 'payments_create_form', methods: ['GET'])]
    public function createForm(Request $request): Response
    {
        return $this->view('payments/create', [
            'title' => 'Новый платёж',
            'currencies' => CurrencyType::cases(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    #[Route('/payments/store', name: 'payments_store', methods: ['POST'])]
    public function store(Request $request): Response
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

    #[Route(
        '/api/payments/{id}/refund',
        name: 'api_payments_refund',
        requirements: ['id' => self::UUID_REGEX],
        methods: ['POST']
    )]
    public function refund(string $id): Response
    {
        $this->paymentService->refund($id);

        return $this->json(['status' => 'refunded']);
    }

    #[Route(
        '/api/payments/status/{status}',
        name: 'api_payments_by_status',
        requirements: ['status' => self::STATUS_REGEX],
        methods: ['GET']
    )]
    public function showAllByStatus(Request $request, #[MapQueryParameter] ?string $status = null): Response
    {
        $userId = (string)$request->attributes->get('userId');
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

    /**
     * @throws NotFoundException
     */
    #[Route(
        '/api/payments/{id}',
        name: 'api_payments_show',
        requirements: ['id' => self::UUID_REGEX],
        methods: ['GET']
    )]
    public function show(string $id): Response
    {
        $payment = $this->paymentService->show($id);

        if (!$payment instanceof Payment)
        {
            throw new NotFoundException('Payment not found');
        }

        return $this->json(PaymentResponse::fromEntity($payment)->toArray());
    }

    #[Route('/api/payments', name: 'api_payments_create', methods: ['POST'])]
    public function create(Request $request): Response
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

    #[Route('/api/payments', name: 'api_payments_list', methods: ['GET'])]
    public function showAllByUserId(Request $request): Response
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

    #[Route('/payments', name: 'payments_index', methods: ['GET'])]
    public function index(Request $request): Response
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
}
