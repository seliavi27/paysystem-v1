<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Платежи</h1>
    <a href="/payments/create" class="btn btn-primary">Создать платёж</a>
</div>

<form method="GET" action="/payments" class="mb-4">
    <div class="input-group" style="max-width: 360px;">
        <select name="status" class="form-select">
            <option value="">Все статусы</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= $view->e($status->value) ?>"
                    <?= $statusFilter === $status->value ? 'selected' : '' ?>>
                    <?= $view->e($status->getLabel()) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-secondary" type="submit">Фильтр</button>
    </div>
</form>

<?php if (empty($payments)): ?>
    <div class="alert alert-info">Платежей пока нет.</div>
<?php else: ?>
    <div class="card shadow-sm">
        <table class="table table-striped mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Сумма</th>
                    <th>Валюта</th>
                    <th>Статус</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><code><?= $view->e($payment->id) ?></code></td>
                        <td><?= number_format($payment->amount, 2) ?></td>
                        <td><?= $view->e($payment->currency->getLabel()) ?></td>
                        <td>
                            <?php
                            $badge = match ($payment->status->value) {
                                'completed'  => 'bg-success',
                                'pending'    => 'bg-warning text-dark',
                                'processing' => 'bg-info text-dark',
                                'failed'     => 'bg-danger',
                                'refunded'   => 'bg-secondary',
                                default      => 'bg-light text-dark',
                            };
                            ?>
                            <span class="badge <?= $badge ?>">
                                <?= $view->e($payment->status->getLabel()) ?>
                            </span>
                        </td>
                        <td><?= $view->e($payment->createdAt->format('d.m.Y H:i')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
