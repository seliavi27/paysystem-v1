<?php
declare(strict_types=1);

require 'auth_functional.php';
require 'logger.php';
require 'security.php';

session_start();

$usersFile = 'data/users.json';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $email = trim($_POST['email'] ?? '');
    log_operation('LOGIN_ATTEMPT', "Email: $email");
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    if ($email === '' || $password === '')
    {
        $errors[] = 'Please fill in all fields';
    }
    else
    {
        $users = is_file($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];

        foreach ($users as $user)
        {
            if (strtolower($user['email']) === strtolower($email))
            {
                if (password_verify($password, $user['password']))
                {
                    startUserSession($user);

                    if ($remember)
                    {
                        setRememberMeCookie($user['email']);
                    }

                    log_operation('LOGIN_SUCCESS', "Email: $email");
                    redirectWithMessage('dashboard.php', 'Вход выполнен');
                    //header('Location: dashboard.php');
                    //exit;
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
}
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
<form method="POST" action="login.php">
    <h2>Вход</h2>

    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Пароль" required><br><br>

    <label>
        <input type="checkbox" name="remember_me">
        Запомнить меня
    </label>

    <br><br>

    <button type="submit">Войти</button>

    <br><br>

    <a href="register.php" class="register-link">Зарегистрироваться</a>
</form>
</body>
</html>
