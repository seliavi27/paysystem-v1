<?php
declare(strict_types=1);

session_start();

require 'auth_functional.php';
require 'logger.php';

$user = requireLogin();

$paymentsFile = 'data/payments.json';
$allowedTypes = ['card', 'wallet', 'bankTransfer'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $amount = $_POST['amount'] ?? '';
    $type = $_POST['type'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (!is_numeric($amount) || $amount <= 0)
    {
        $errors['amount'] = 'The amount must be a positive number';
    }

    if (!in_array($type, $allowedTypes, true))
    {
        $errors['type'] = 'Invalid payment type';
    }

    if ($description === '')
    {
        $errors['description'] = 'Description is required';
    }
    elseif (mb_strlen($description) > 500)
    {
        $errors['description'] = 'Maximum 500 characters';
    }

    if (empty($errors))
    {
        $payments = is_file($paymentsFile)
                ? json_decode(file_get_contents($paymentsFile), true)
                : [];

        if (!is_array($payments))
        {
            $payments = [];
        }

        $newId = empty($payments) ? 1 : max(array_column($payments, 'id')) + 1;

        $payment = [
                'id' => $newId,
                'userId' => $user['id'],
                'date' => date('Y-m-d H:i:s'),
                'amount' => (float)$amount,
                'type' => $type,
                'description' => $description,
                'status' => 'pending'
        ];

        $payments[] = $payment;

        file_put_contents(
                $paymentsFile,
                json_encode($payments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        log_operation(
                'PAYMENT_CREATE',
                "Payment #{$newId} created by {$user['email']} amount {$amount}"
        );

        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создать платёж</title>
</head>
<body>

<form method="POST">
    <h2>Создать платёж</h2>

    <input type="text" name="amount" placeholder="Сумма"
           value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>">

    <?php if (!empty($errors['amount'])): ?>
        <div class="error">
            <?= $errors['amount'] ?>
        </div>
    <?php endif; ?>

    <br>
    <br>

    <select name="type">
        <option value="">Выберите тип платежа</option>
        <option value="card" <?= (($_POST['type'] ?? '') === 'card') ? 'selected' : '' ?>>Card</option>
        <option value="wallet" <?= (($_POST['type'] ?? '') === 'wallet') ? 'selected' : '' ?>>Wallet</option>
        <option value="bankTransfer" <?= (($_POST['type'] ?? '') === 'bankTransfer') ? 'selected' : '' ?>>Bank Transfer</option>
    </select>

    <?php if (!empty($errors['type'])): ?>
        <div class="error"><?= $errors['type'] ?></div>
    <?php endif; ?>

    <br>
    <br>

    <textarea name="description" placeholder="Описание"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

    <?php if (!empty($errors['description'])): ?>
        <div class="error"><?= $errors['description'] ?></div>
    <?php endif; ?>

    <br>
    <br>

    <button type="submit">Создать</button>
</form>

</body>
</html>