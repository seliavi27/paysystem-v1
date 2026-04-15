<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Платежи</h1>
<!--    <a href="/payments/create" class="btn btn-primary">Создать платёж</a>-->
</div>

<?php if (empty($payments)): ?>
    <div class="alert alert-info">Платежей пока нет.</div>
<?php else: ?>
    <table class="table table-striped">
        <thead>
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
                <td><?= htmlspecialchars((string)$payment->id) ?></td>
                <td><?= number_format($payment->amount, 2) ?></td>
                <td><?= htmlspecialchars($payment->currency->getLabel()) ?></td>
                <td>
                    <?php
                    $statusClass = match($payment->status->value) {
                        'completed' => 'bg-success',
                        'pending'   => 'bg-warning',
                        'failed'    => 'bg-danger',
                        'refunded'  => 'bg-secondary',
                        default     => 'bg-info',
                    };
                    ?>
                    <span class="badge <?= $statusClass ?>">
                        <?= htmlspecialchars($payment->status->getLabel()) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($payment->createdAt->format('d.m.Y H:i')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>