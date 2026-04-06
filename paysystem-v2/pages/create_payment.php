<?php
declare(strict_types=1);

function handleCreatePaymentPost($data, $user): array
{
    $errors = [];

    $paymentsFile = PAYMENTS_FILE;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    {
        log_error("Payment was not created by {$user['email']}");
        $errors[] = "Invalid request method";
        return $errors;
    }

    $amount = $data['amount'] ?? '';
    $type = $data['type'] ?? '';
    $description = trim($data['description'] ?? '');

    if (!is_numeric($amount) || $amount <= 0)
    {
        $errors['amount'] = 'The amount must be a positive number';
    }

    $typeEnum = PaymentType::tryFrom($type);

    if ($typeEnum === null)
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

    if (!empty($errors))
    {
        return $errors;
    }

    $payments = json_decode(file_get_contents($paymentsFile), true);

    if (!is_array($payments))
    {
        $payments = [];
    }

    $newPayment = Payment::create(
            $user['id'],
            (float)$amount,
            CurrencyType::USD,
            $typeEnum);

    $payments[] = $newPayment;

    file_put_contents(
            $paymentsFile,
            json_encode($payments, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    log_operation(
            'PAYMENT_CREATE',
            (string)$newPayment
    );

    redirectToPage('dashboard');

    return $errors;
}

$user = requireLogin();
$errors = handleCreatePaymentPost($_POST, $user);
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
        <?php foreach (PaymentType::cases() as $type): ?>
            <option value="<?= $type->value ?>"
                    <?= (($_POST['type'] ?? '') === $type->value) ? 'selected' : '' ?>
            >
                <?= htmlspecialchars($type->getLabel()) ?>
            </option>
        <?php endforeach; ?>
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

<br>
<a href="/?page=dashboard">Назад</a>

</body>
</html>