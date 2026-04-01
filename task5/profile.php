<?php
declare(strict_types=1);

session_start();

require 'auth_functional.php';
require 'image_functional.php';
require 'validators_form.php';
require 'logger.php';
require 'cookies_handler.php';

if (isset($_GET['theme']))
{
    setThemePreference($_GET['theme']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$theme = getThemePreference();


$user = requireLogin();
$usersFile = 'data/users.json';
$errors = [];
$success = '';

$users = is_file($usersFile)
        ? json_decode(file_get_contents($usersFile), true)
        : [];

foreach ($users as &$u)
{
    if ($u['id'] === $user['id'])
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            if (isset($_POST['updateProfile']))
            {
                $full_name = trim($_POST['fullName']);
                $phone = trim($_POST['phone']);

                if ($full_name === '')
                {
                    $errors[] = 'Name is required';
                }

                if (!validatePhoneFormat($phone))
                {
                    $errors[] = 'Invalid phone format';
                }

                if (empty($errors))
                {
                    $u['fullName'] = $full_name;
                    $u['phone'] = $phone;

                    $_SESSION['user']['fullName'] = $full_name;
                    $_SESSION['user']['phone'] = $phone;

                    $success = 'Profile updated';
                }
            }

            if (isset($_POST['uploadAvatar']) && isset($_FILES['avatar']))
            {
                $validation = validateUploadedFile($_FILES['avatar']);

                if (!$validation['valid'])
                {
                    $errors[] = $validation['error'];
                }
                else
                {

                    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                    $newName = $user['id'] . '_' . time() . '.' . $ext;

                    $path = saveUploadedFile($_FILES['avatar'], 'uploads/avatars/', $newName);

                    if ($path === false)
                    {
                        $errors[] = 'Error saving file';
                    }
                    else
                    {
                        deleteOldAvatar($u['avatar'] ?? '');

                        $u['avatar'] = $path;
                        $success = 'Avatar uploaded';
                        log_operation('FILE_UPLOADED', 'Avatar uploaded');
                    }
                }
            }

            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        break;
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <?php if ($theme == 'dark'): ?>
        <link rel="stylesheet" href="css/dark.css">
    <?php else: ?>
        <link rel="stylesheet" href="css/light.css">
    <?php endif; ?>
</head>
<body>

<h2>Профиль</h2>

<div class="theme-buttons">
    <a href="?theme=light" style="background: #f0f0f0; color: #000;">Светлая</a>
    <a href="?theme=dark" style="background: #333; color: #fff;">Темная</a>
</div>

<p>Тема: <?= htmlspecialchars($theme) ?></p>

<p>Email: <?= htmlspecialchars($u['email']) ?></p>

<?php if (!empty($u['avatar'])): ?>
    <img src="<?= htmlspecialchars($u['avatar']) ?>" width="150"><br><br>
<?php endif; ?>

<?php if ($errors): ?>
    <div style="color:red;">
        <?php foreach ($errors as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="color:green;"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST">
    <input type="text" name="fullName" value="<?= htmlspecialchars($u['fullName']) ?>"><br><br>
    <input type="text" name="phone" value="<?= htmlspecialchars($u['phone']) ?>"><br><br>
    <button name="updateProfile">Сохранить</button>
</form>

<br><br>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar"><br><br>
    <button name="uploadAvatar">Загрузить аватар</button>
</form>

<br><br>
<a href="dashboard.php">Назад</a>

</body>
</html>