<?php
declare(strict_types=1);

function validateUploadedFile(array $file, string $type = 'image'): array
{
    if ($file['error'] !== UPLOAD_ERR_OK)
    {
        return ['valid' => false, 'error' => 'File upload error'];
    }

    if ($file['size'] > 2 * 1024 * 1024)
    {
        return ['valid' => false, 'error' => 'File is too large (max 2MB)'];
    }

    if ($type === 'image')
    {
        $allowed = ['image/jpeg', 'image/png', 'image/gif'];
        $mime = mime_content_type($file['tmp_name']);

        if (!in_array($mime, $allowed, true))
        {
            return ['valid' => false, 'error' => 'Only images (jpg, png, gif)'];
        }
    }

    return ['valid' => true, 'error' => ''];
}

function saveUploadedFile(array $file, string $upload_dir, string $new_name): string|false
{
    if (!is_dir($upload_dir))
    {
        mkdir($upload_dir, 0755, true);
    }

    $destination = rtrim($upload_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $new_name;

    if (move_uploaded_file($file['tmp_name'], $destination))
    {
        return $destination;
    }

    return false;
}

function deleteOldAvatar(string $avatar_path): void
{
    if ($avatar_path && file_exists($avatar_path))
    {
        unlink($avatar_path);
    }
}