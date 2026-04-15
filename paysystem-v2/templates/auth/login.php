<?php
declare(strict_types=1);

$errors = $errors ?? [];
$old = $old ?? [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container">
    <div class="row vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-6 offset-md-3">

            <div class="card shadow">
                <div class="card-body">

                    <h3 class="card-title text-center mb-4">Вход</h3>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/auth/login">

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                    required
                            >
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Пароль</label>
                            <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Войти
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="/register">
                            Зарегистрироваться
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>