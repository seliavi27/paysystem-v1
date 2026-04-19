<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4">Новый платёж</h3>

                <?= $view->include('components/errors', ['errors' => $errors]) ?>

                <form method="POST" action="/payments/store">
                    <div class="mb-3">
                        <label class="form-label">Сумма</label>
                        <input type="number" name="amount" step="0.01" min="0"
                               class="form-control"
                               value="<?= $view->e($old['amount'] ?? '') ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Валюта</label>
                        <select name="currency" class="form-select">
                            <?php foreach ($currencies as $currency): ?>
                                <option value="<?= $view->e($currency->value) ?>"
                                    <?= ($old['currency'] ?? 'RUB') === $currency->value ? 'selected' : '' ?>>
                                    <?= $view->e($currency->getLabel()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Метод оплаты</label>
                        <select name="method" class="form-select">
                            <?php foreach ($methods as $method): ?>
                                <option value="<?= $view->e($method->value) ?>"
                                    <?= ($old['method'] ?? 'credit_card') === $method->value ? 'selected' : '' ?>>
                                    <?= $view->e($method->getLabel()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea name="description" class="form-control" rows="3"><?= $view->e($old['description'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="/payments" class="btn btn-outline-secondary">Отмена</a>
                        <button type="submit" class="btn btn-success">Создать</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
