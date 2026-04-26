<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Вход</h3>

                <?= $view->include('components/errors', ['errors' => $errors]) ?>

                <form method="POST" action="/auth/login">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= $view->e($old['email'] ?? '') ?>"
                               required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Пароль</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Войти</button>
                </form>

                <div class="text-center mt-3">
                    <a href="/register">Нет аккаунта? Зарегистрироваться</a>
                </div>
            </div>
        </div>
    </div>
</div>
