<?php
declare(strict_types=1);

session_start();

require 'validators_form.php';

$usersFile = 'data/users.json';
$errors = [];
$success = false;

if (empty($_SESSION['csrf_token']))
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf_token))
    {
        $errors['csrf'] = 'Invalid CSRF token';
    }
    else
    {
        $formData = [
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'passwordConfirm' => $_POST['passwordConfirm'] ?? '',
                'fullName' => trim($_POST['fullName'] ?? ''),
                'phone' => trim($_POST['phone'] ?? '')
        ];

        $validation = validateRegistrationForm($formData);

        if (!$validation['valid'])
        {
            $errors = $validation['errors'];
            log_error("Registration was failed: " . basename(__FILE__));
        }
        else
        {
            $newUser = [
                    'email' => $formData['email'],
                    'password' => hashPassword($formData['password']),
                    'fullName' => $formData['fullName'],
                    'phone' => $formData['phone'],
                    'createdAt' => date('Y-m-d H:i:s')
            ];

            $users = is_file($usersFile) ?
                json_decode(file_get_contents($usersFile), true) :
                ensure_users_storage($usersFile);

            if (!is_array($users))
            {
                $users = [];
            }

            $listId = array_column($users, 'id');
            $newUser['id'] = max($listId) + 1;

            $users[] = $newUser;

            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $success = true;

            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <style>
        .csrf-token {
            display: none;
        }
    </style>
</head>
<body>
<form method="POST" action="register.php">
    <h2>Регистрация</h2>

    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Пароль" required><br><br>
    <input type="password" name="passwordConfirm" placeholder="Подтверждение пароля" required><br><br>
    <input type="text" name="fullName" placeholder="Имя и фамилия" required><br><br>
    <input type="tel" name="phone" placeholder="Телефон" required><br><br>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>" class="csrf-token">

    <button type="submit">Зарегистрироваться</button>
</form>
</body>
</html>