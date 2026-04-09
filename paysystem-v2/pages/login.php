<?php
declare(strict_types=1);

use PaySystem\Entity\User;

$errors = [];

function handleLoginPost($data): array
{
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    {
        return $errors;
    }

    try
    {
        $email = trim($data['email'] ?? '');
        log_operation('LOGIN_ATTEMPT', "Email: $email");
        $password = $data['password'] ?? '';
        $remember = isset($data['remember_me']);

        if ($email === '' || $password === '')
        {
            $errors[] = 'Please fill in all fields';
            return $errors;
        }

        $usersArray = is_file(USERS_FILE) ? json_decode(file_get_contents(USERS_FILE), true) : [];
        $users = array_map(
                fn($u) => User::fromArray($u),
                $usersArray ?? []
        );

        foreach ($users as $user)
        {
            if (strtolower($user->email) === strtolower($email))
            {
                if (password_verify($password, $user->password))
                {
                    startUserSession($user->toArray());

                    if ($remember)
                    {
                        setRememberMeCookie($user->email);
                    }

                    log_operation('LOGIN_SUCCESS', "Email: $email");
                    redirectWithMessage('dashboard', 'Вход выполнен');
                }
                else
                {
                    $errors[] = 'Incorrect password';
                }

                break;
            }
        }

        if (empty($errors))
        {
            $errors[] = 'User not found';
            log_error("User not found: " . basename(__FILE__));
        }

    }
    catch (Exception $e)
    {
        log_error($e->getMessage());
        $errors = $e->getMessage();
    }

    return $errors;
}

$errors = handleLoginPost($_POST);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $field => $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

</head>
<body>
<form method="POST">
    <h2>Вход</h2>

    <label>
        <input type="email" name="email" placeholder="Email" required>
    </label><br><br>
    <label>
        <input type="password" name="password" placeholder="Пароль" required>
    </label><br><br>

    <label>
        <input type="checkbox" name="remember_me">
        Запомнить меня
    </label>

    <br><br>

    <button type="submit">Войти</button>

    <br><br>

    <a href="/?page=register" class="register-link">Зарегистрироваться</a>
</form>
</body>
</html>
