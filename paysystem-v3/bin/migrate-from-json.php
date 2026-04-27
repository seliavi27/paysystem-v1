<?php
declare(strict_types=1);

use Doctrine\DBAL\Connection;

/** @var array $container */
$container = require __DIR__ . '/../bootstrap.php';
/** @var Connection $db */
$db = $container[Connection::class];

echo 'DATABASE_URL: ' . ($_ENV['DATABASE_URL'] ?? 'not set') . PHP_EOL;

$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    echo "No data/ directory — JSON storage already removed. Nothing to migrate.\n";
    exit(0);
}

/**
 * Идемпотентный импорт: ON CONFLICT (id) DO NOTHING.
 * Повторный запуск не падает с unique violation.
 */
$importTable = function (Connection $db, string $table, string $jsonFile, callable $mapper): int {
    if (!is_file($jsonFile)) {
        echo "Skipped {$table}: {$jsonFile} not found.\n";
        return 0;
    }

    $data = json_decode((string)file_get_contents($jsonFile), true, flags: JSON_THROW_ON_ERROR);
    $count = 0;

    foreach ($data as $row) {
        if (!is_array($row) || !isset($row['id'])) {
            continue;
        }

        $payload = $mapper($row);
        $columns = array_keys($payload);
        $placeholders = array_map(fn(string $c) => ':' . $c, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s) ON CONFLICT (id) DO NOTHING',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders),
        );

        $affected = $db->executeStatement($sql, $payload);
        $count += $affected;
    }

    return $count;
};

$usersImported = $importTable($db, 'users', $dataDir . '/users.json', function (array $u): array {
    $createdAt = is_array($u['createdAt']) ? $u['createdAt']['date'] : $u['createdAt'];
    $updatedAt = is_array($u['updatedAt']) ? $u['updatedAt']['date'] : $u['updatedAt'];
    return [
        'id'         => $u['id'],
        'email'      => $u['email'],
        'password'   => $u['password'],
        'full_name'  => $u['fullName'],
        'phone'      => $u['phone'] ?? '',
        'balance'    => $u['balance'] ?? 0,
        'created_at' => $createdAt,
        'updated_at' => $updatedAt,
    ];
});

$paymentsImported = $importTable($db, 'payments', $dataDir . '/payments.json', function (array $p): array {
    $createdAt = is_array($p['createdAt']) ? $p['createdAt']['date'] : $p['createdAt'];
    $updatedAt = is_array($p['updatedAt']) ? $p['updatedAt']['date'] : $p['updatedAt'];
    return [
        'id'          => $p['id'],
        'user_id'     => $p['userId'],
        'amount'      => $p['amount'],
        'description' => $p['description'] ?? '',
        'currency'    => $p['currency'],
        'status'      => $p['status'],
        'method'      => $p['method'],
        'created_at'  => $createdAt,
        'updated_at'  => $updatedAt,
    ];
});

echo "Imported (new rows): users={$usersImported}, payments={$paymentsImported}\n";
