<?php
declare(strict_types=1);

function handleThemeGet($data): string
{
    if (isset($data['theme']))
    {
        setThemePreference($data['theme']);
        redirectToPage('profile');
    }

    return getThemePreference();
}

function handleUpdateProfilePost($data, $user): array
{
    $usersFile = USERS_FILE;
    $errors = [];
    $userInfo = null;
    $success = '';
    $users = json_decode(file_get_contents($usersFile), true);

    foreach ($users as &$u)
    {
        if ($u['id'] === $user['id'])
        {
            $userInfo = $u;

            if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            {
                return [
                        'success' => $success,
                        'errors' => $errors,
                        'userInfo' => $userInfo
                ];
            }

            if (isset($data['updateProfile']))
            {
                $full_name = trim($data['fullName']);
                $phone = trim($data['phone']);

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

                    $userInfo = $u;
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

                    $path = saveUploadedFile($_FILES['avatar'], AVATARS_PATH, $newName);

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

            break;
        }
    }

    return [
            'success' => $success,
            'errors' => $errors,
            'userInfo' => $userInfo
    ];
}

$user = requireLogin();
$theme = handleThemeGet($_GET);
$result = handleUpdateProfilePost($_POST, $user);

$success = $result['success'];
$errors = $result['errors'];
$userInfo = $result['userInfo'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <?php if ($theme == 'dark'): ?>
        <link rel="stylesheet" href=<?= LIGHT_CSS_PATH ?> >
    <?php else: ?>
        <link rel="stylesheet" href=<?= DARK_CSS_PATH ?>>
    <?php endif; ?>
</head>
<body>
<h2>Профиль</h2>

<div class="theme-buttons">
    <a href="/?page=profile&theme=light" style="background: #f0f0f0; color: #000;">Светлая</a>
    <a href="/?page=profile&theme=dark" style="background: #333; color: #fff;">Темная</a>
</div>

<p>Тема: <?= htmlspecialchars($theme) ?></p>

<p>Email: <?= htmlspecialchars($userInfo['email']) ?></p>

<?php if (!empty($userInfo['avatar'])): ?>
    <img src="<?= htmlspecialchars($userInfo['avatar']) ?>" width="150"><br><br>
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
    <input type="text" name="fullName" value="<?= htmlspecialchars($userInfo['fullName']) ?>"><br><br>
    <input type="text" name="phone" value="<?= htmlspecialchars($userInfo['phone']) ?>"><br><br>
    <button name="updateProfile">Сохранить</button>
</form>

<br><br>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar"><br><br>
    <button name="uploadAvatar">Загрузить аватар</button>
</form>

<br><br>
<a href="/?page=dashboard">Назад</a>

</body>
</html>