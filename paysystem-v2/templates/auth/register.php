<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Регистрация</h3>

                <?= $view->include('components/errors', ['errors' => $errors]) ?>

                <form method="POST" action="/auth/register">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= $view->e($old['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Полное имя</label>
                        <input type="text" name="fullName" class="form-control"
                               value="<?= $view->e($old['fullName'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Телефон</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= $view->e($old['phone'] ?? '') ?>"
                               placeholder="+79991234567">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Повтор пароля</label>
                            <input type="password" name="passwordConfirm" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
                </form>

                <div class="text-center mt-3">
                    <a href="/login">Уже есть аккаунт? Войти</a>
                </div>
            </div>
        </div>
    </div>
</div>
