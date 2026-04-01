<?php
declare(strict_types=1);

//
function getCurrentPage(): string
{
    return $_GET['page'] ?? 'home';
}

function redirectToPage(string $page, array $params = []): void
{
    $url = BASE_URL . '/?page=' . urlencode($page);

    if (!empty($params))
    {
        $url .= '&' . http_build_query($params);
    }

    header('Location: ' . $url);
    exit;
}

function renderPage(string $page): void
{
    $page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

    $page_file = PAGES_PATH . '/' . $page . '.php';

    if (!file_exists($page_file))
    {
        $page_file = PAGES_PATH . '/error.php';
    }

    include $page_file;
}
