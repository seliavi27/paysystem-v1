<?php
declare(strict_types=1);

use Doctrine\DBAL\Connection;

/** @var array $container */
$container = require __DIR__ . '/../bootstrap.php';
/** @var Connection $db */
$db = $container[Connection::class];
echo 'DATABASE_URL: ' . ($_ENV['DATABASE_URL'] ?? 'not set') . PHP_EOL;
echo 'Connecting with user: paysystem_user' . PHP_EOL;
$importTable = function(Connection $db, string $table, string $jsonFile, callable $mapper): int
{
    $data = json_decode(file_get_contents($jsonFile), true, flags: JSON_THROW_ON_ERROR);
    $count = 0;
    foreach ($data as $row)
    {
        if (!is_array($row) || !isset($row['id'])) continue;
        $db->insert($table, $mapper($row));
        $count++;
    }
    return $count;
};

$usersImported = $importTable($db, 'users', __DIR__ . '/../data/users.json', function (array $u): array
{
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

$paymentsImported = $importTable($db, 'payments', __DIR__ . '/../data/payments.json', function (array $p): array
{
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

echo "Imported: users={$usersImported}, payments={$paymentsImported}\n";


//try {
//    $pdo = new PDO(
//        'pgsql:host=localhost;port=5432;dbname=paysystem_dev',
//        'paysystem_user',
//        'paysystem_pass',
//        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
//    );
//    echo "✅ Connected with PDO!\n";
//
//    // Тест запроса
//    $stmt = $pdo->query("SELECT 1");
//    var_dump($stmt->fetch());
//
//} catch (PDOException $e) {
//    echo "❌ PDO Error: " . $e->getMessage() . "\n";
//}