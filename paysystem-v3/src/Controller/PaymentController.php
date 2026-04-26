<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

use App\DTO\CreatePaymentRequest;
use App\DTO\PaymentResponse;
use App\Entity\Payment;
use App\Enum\CurrencyType;
use App\Enum\PaymentMethod;
use App\Enum\PaymentStatus;
use App\Exception\NotFoundException;
use App\Exception\ValidationException;
use App\Service\PaymentServiceInterface;

final class PaymentController extends AbstractController
{
    private const string UUID_REGEX   = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';
    private const string STATUS_REGEX = 'pending|processing|completed|failed|refunded';

    public function __construct(
        private readonly PaymentServiceInterface $paymentService
    )
    {

    }

    #[Route('/payments', name: 'payments_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $statusFilter = $request->query->get('status');

        $payments = $statusFilter
            ? $this->paymentService->showAllByStatus($user->getUserIdentifier(),
                (string)$statusFilter)
            : $this->paymentService->showAllByUserId($user->getUserIdentifier());

        return $this->render('payments/list.html.twig', [
            'title' => 'Платежи',
            'payments' => $payments,
            'statusFilter' => $statusFilter,
            'statuses' => PaymentStatus::cases(),
        ]);
    }

    #[Route('/payments/create', name: 'payments_create_form', methods: ['GET'])]
    public function createPaymentsForm(): Response
    {
        return $this->render('payments/create.html.twig', [
            'title' => 'Новый платёж',
            'currencies' => CurrencyType::cases(),
            'methods' => PaymentMethod::cases(),
        ]);
    }

    #[Route('/payments/store', name: 'payments_store', methods: ['POST'])]
    public function store(Request $request): Response
    {
        $user = $this->getUser();

        try
        {
            $this->paymentService->create(
                new CreatePaymentRequest(
                    userId: $user->getUserIdentifier(),
                    amount: (float)$request->request->get('amount', 0),
                    description: (string)$request->request->get('description', ''),
                    currency: CurrencyType::from((string)$request->request->get('currency', 'RUB')),
                    method: PaymentMethod::from((string)$request->request->get('method', 'credit_card')),
                )
            );

            $this->addFlash('success', 'Платёж успешно создан');

            return $this->redirectToRoute('payments_index');
        }
        catch (Exception $e)
        {
            return $this->render('payments/create.html.twig', [
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

// --- API Section ---

    #[Route('/api/payments', name: 'api_payments_list', methods: ['GET'])]
    public function showAllByUserId(): JsonResponse
    {
        $user = $this->getUser();
        $payments = $this->paymentService->showAllByUserId($user->getUserIdentifier());

        return $this->json([
            'success' => true,
            'count' => count($payments),
            'data' => array_map(
                fn(Payment $p) => PaymentResponse::fromEntity($p),
                $payments),
        ]);
    }

    #[Route(
        '/api/payments/status/{status}',
        name: 'api_payments_by_status',
        requirements: ['status' => self::STATUS_REGEX],
        methods: ['GET']
    )]
    public function showAllByStatus(#[MapQueryParameter] ?string $status = null): JsonResponse
    {
        $user = $this->getUser();
        $payments = $this->paymentService->showAllByStatus($user->getUserIdentifier(), $status);

        return $this->json([
            'success' => true,
            'count' => count($payments),
            'data' => array_map(
                fn(Payment $p) => PaymentResponse::fromEntity($p),
                $payments
            ),
        ]);
    }

    #[Route(
        '/api/payments/{id}',
        name: 'api_payments_show',
        requirements: ['id' => self::UUID_REGEX],
        methods: ['GET']
    )]
    public function show(string $id): JsonResponse
    {
        $payment = $this->paymentService->show($id);
        return $this->json(PaymentResponse::fromEntity($payment));
    }

    #[Route('/api/payments', name: 'api_payments_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $user = $this->getUser();

        $payment = $this->paymentService->create(
            new CreatePaymentRequest(
                userId: $user->getUserIdentifier(),
                amount: (float)($data['amount'] ?? 0),
                description: (string)($data['description'] ?? ''),
                currency: CurrencyType::from($data['currency'] ?? 'RUB'),
                method: PaymentMethod::from($data['method'] ?? 'credit_card'),
            )
        );

        return $this->json(PaymentResponse::fromEntity($payment), 201);
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
}
