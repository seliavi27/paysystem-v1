<?php if (!empty($flash)): ?>
    <?php foreach ($flash as $type => $message): ?>
        <div class="alert alert-<?= $view->e($type) ?> alert-dismissible fade show">
            <?= $view->e($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
