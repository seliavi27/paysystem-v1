<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h3 class="card-title mb-1"><?= $view->e($user->fullName) ?></h3>
                        <div class="text-muted"><?= $view->e($user->email) ?></div>
                    </div>
                    <span class="badge bg-light text-dark">ID: <code><?= $view->e($user->id) ?></code></span>
                </div>

                <hr>

                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Телефон</dt>
                    <dd class="col-sm-8"><?= $view->e($user->phone ?: '—') ?></dd>

                    <dt class="col-sm-4 text-muted">Баланс</dt>
                    <dd class="col-sm-8"><?= number_format($user->balance, 2) ?> ₽</dd>

                    <dt class="col-sm-4 text-muted">Зарегистрирован</dt>
                    <dd class="col-sm-8"><?= $view->e($user->createdAt->format('d.m.Y H:i')) ?></dd>

                    <dt class="col-sm-4 text-muted">Обновлён</dt>
                    <dd class="col-sm-8"><?= $view->e($user->updatedAt->format('d.m.Y H:i')) ?></dd>
                </dl>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-muted small text-uppercase">Всего платежей</div>
                        <div class="display-6"><?= $view->e($paymentsCount) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="text-muted small text-uppercase">Сумма платежей</div>
                        <div class="display-6"><?= number_format($paymentsSum, 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <a href="/payments" class="btn btn-outline-primary">К платежам</a>
            <a href="/logout" class="btn btn-outline-danger ms-auto">Выйти</a>
        </div>
    </div>
</div>
