<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/payments">PaySystem</a>

        <div class="d-flex">
            <?php if ($isAuthenticated): ?>
                <a href="/payments" class="btn btn-outline-light me-2">Платежи</a>
                <a href="/payments/create" class="btn btn-light me-2">Новый платёж</a>
                <a href="/profile" class="btn btn-outline-light me-2">Профиль</a>
                <a href="/logout" class="btn btn-outline-light">Выйти</a>
            <?php else: ?>
                <a href="/login" class="btn btn-outline-light me-2">Вход</a>
                <a href="/register" class="btn btn-light">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
