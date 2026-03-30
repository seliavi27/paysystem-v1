<?php
declare(strict_types=1);

require_once "json_storage.php";
require_once "csv_handler.php";
require_once "logger.php";
require_once "directory_manager.php";

class StorageManager
{
    private const FILE_NAME = 'payments.json';
    private string $storage_dir;

    public function __construct(string $storage_dir = 'data/')
    {
        $this->storage_dir = rtrim($storage_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($this->storage_dir))
        {
            mkdir($this->storage_dir, 0755, true);
        }
    }

    private function getFilePath(): string
    {
        return $this->storage_dir . self::FILE_NAME;
    }

    public function save_payments(array $payments): bool
    {
        $result = save_payments_to_json($payments, $this->getFilePath());
        log_operation('PAYMENT_CREATE', 'Payments created');
        return $result;
    }

    public function save_payment(array $payment): bool
    {
        $result = add_payment_to_storage($payment, $this->getFilePath());
        log_operation('PAYMENT_CREATE', 'Payment created');
        return $result;
    }

    public function get_payment(int $id): array|false
    {
        $payments = load_payments_from_json($this->getFilePath());
        $payment = array_find($payments, fn($payment) => $payment['id'] === $id);

        if (is_null($payment))
        {
            log_error("Payment with id=$id not found: " . self::FILE_NAME);
            return false;
        }

        return $payment;
    }

    public function get_all_payments(): array
    {
        return load_payments_from_json($this->getFilePath());
    }

    public function delete_payment(int $id): bool
    {
        $payments = load_payments_from_json($this->getFilePath());

        foreach ($payments as $key => $payment)
        {
            if ($payment['id'] === $id)
            {
                unset($payments[$key]);
                break;
            }
        }

        $result = save_payments_to_json($payments, $this->getFilePath());

        if (!$result)
        {
            log_error("Payment with id=$id not deleted: " . self::FILE_NAME);
        }

        return $result;
    }
}