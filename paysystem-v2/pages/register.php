<?php
declare(strict_types=1);

use PaySystem\Entity\User;
use PaySystem\Validator\UserValidator;

function handleRegisterPost($data): array
{
    $errors = [];

    try
    {
        $usersFile = USERS_FILE;

        preventCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            return $errors;
        }

        $csrf_token = $data['csrf_token'] ?? '';

        if (!verifyCsrfToken($csrf_token))
        {
            $errors['csrf'] = 'Invalid CSRF token';
            return $errors;
        }

        $formData = [
                'email' => trim($data['email'] ?? ''),
                'password' => $data['password'] ?? '',
                'passwordConfirm' => $data['passwordConfirm'] ?? '',
                'fullName' => trim($data['fullName'] ?? ''),
                'phone' => trim($data['phone'] ?? '')
        ];

        $validation = UserValidator::validateRegistrationForm($formData);

        if (!$validation['valid'])
        {
            $errors = $validation['errors'];
            log_error("Registration was failed: " . basename(__FILE__));
            return $errors;
        }
//
//        $newUser = [
//                'email' => $formData['email'],
//                'password' => hashPassword($formData['password']),
//                'fullName' => $formData['fullName'],
//                'phone' => $formData['phone'],
//                'createdAt' => date('Y-m-d H:i:s')
//        ];

        $newUser = User::create(
                $formData['email'],
                UserValidator::hashPassword($formData['password']),
                $formData['fullName'],
                $formData['phone']);

        //$newUserData = $newUser->toArray();
        $users = json_decode(file_get_contents($usersFile), true);

        if (!is_array($users))
        {
            $users = [];
        }

        $users[] = $newUser;

        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        redirectToPage('login');
    }
    catch (Exception $e)
    {
        log_error($e->getMessage());
        $errors = $e->getMessage();
    }

    return $errors;
}

$errors = handleRegisterPost($_POST);
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
<form method="POST">
    <h2>Регистрация</h2>

    <label>
        <input type="email" name="email" placeholder="Email" required>
    </label><br><br>
    <label>
        <input type="password" name="password" placeholder="Пароль" required>
    </label><br><br>
    <label>
        <input type="password" name="passwordConfirm" placeholder="Подтверждение пароля" required>
    </label><br><br>
    <label>
        <input type="text" name="fullName" placeholder="Имя и фамилия" required>
    </label><br><br>
    <label>
        <input type="tel" name="phone" placeholder="Телефон" required>
    </label><br><br>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>" class="csrf-token">

    <button type="submit">Зарегистрироваться</button>
</form>
</body>
</html>