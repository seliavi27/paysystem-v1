<?php
declare(strict_types=1);

function handlePostRequest(callable $handler): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $handler($_POST);
    }

//    $csrf_token = $_POST['csrf_token'] ?? '';
//
//    if (!hash_equals($_SESSION['csrf_token'], $csrf_token))
//    {
//        $errors['csrf'] = 'Invalid CSRF token';
//    }
//    else
//    {
//
//    }
//
//
//    if (!validateCsrfToken())
//    {
//        setFlashMessage('Ошибка безопасности. Попробуйте снова.', 'error');
//        redirectBack();
//        return;
//    }


//    switch ($handler)
//    {
//        case 'login':
//            handleLoginPost($handler);
//            break;
//
//        case 'register':
//            handleRegisterPost();
//            break;
//
//        case 'profile':
//            handleProfilePost();
//            break;
//
//        case 'settings':
//            handleSettingsPost();
//            break;
//
//        case 'upload_avatar':
//            handleUploadAvatarPost();
//            break;
//
//        case 'change_password':
//            handleChangePasswordPost();
//            break;
//
//        default:
//            http_response_code(404);
//            exit;
//    }
}

function validateCsrfToken(): bool
{
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function setFlashMessage(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function redirectBack(): void
{
    $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
    header("Location: $redirect");
    exit;
}

