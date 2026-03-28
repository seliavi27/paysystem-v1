<?php
declare(strict_types=1);

session_start();

require 'validators_form.php';

$users_file = 'data/users.json';
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
        $form_data = [
                'email' => trim($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'password_confirm' => $_POST['password_confirm'] ?? '',
                'full_name' => trim($_POST['full_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? '')
        ];

        $validation = validate_registration_form($form_data);

        if (!$validation['valid'])
        {
            $errors = $validation['errors'];
        }
        else
        {
            $new_user = [
                    'email' => $form_data['email'],
                    'password' => hash_password($form_data['password']),
                    'full_name' => $form_data['full_name'],
                    'phone' => $form_data['phone'],
                    'created_at' => date('Y-m-d H:i:s')
            ];

            $users = is_file($users_file) ?
                json_decode(file_get_contents($users_file), true) :
                ensure_users_storage($users_file);

            if (!is_array($users))
            {
                $users = [];
            }

            $users[] = $new_user;

            file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

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
    <input type="password" name="password_confirm" placeholder="Подтверждение пароля" required><br><br>
    <input type="text" name="full_name" placeholder="Имя и фамилия"><br><br>
    <input type="tel" name="phone" placeholder="Телефон" required><br><br>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>" class="csrf-token">

    <button type="submit">Зарегистрироваться</button>
</form>
</body>
</html>