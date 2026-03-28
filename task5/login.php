<?php
declare(strict_types=1);

require 'login_functional.php';

session_start();

$users_file = 'data/users.json';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember_me']);

    if ($email === '' || $password === '')
    {
        $errors[] = 'Please fill in all fields';
    }
    else
    {
        $users = is_file($users_file) ? json_decode(file_get_contents($users_file), true) : [];

        foreach ($users as $user)
        {
            if (strtolower($user['email']) === strtolower($email))
            {
                if (password_verify($password, $user['password']))
                {
                    start_user_session($user);

                    if ($remember) {
                        set_remember_me_cookie($user['email']);
                    }

                    //header('Location: dashboard.php');
                    exit;
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
