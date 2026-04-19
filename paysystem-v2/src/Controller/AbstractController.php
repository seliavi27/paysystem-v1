<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use PaySystem\Response;
use PaySystem\View\TemplateEngine;

abstract class AbstractController
{
    public function __construct(
        private readonly TemplateEngine $templateEngine
    ) {}

    protected function json(array $data, int $status = 200): Response
    {
        return (new Response())
            ->setStatusCode($status)
            ->setJson($data);
    }

    protected function view(string $template, array $data = []): Response
    {
        $html = $this->templateEngine->renderWithLayout($template, [
            ...$this->sharedContext(),
            ...$data,
        ]);

        return (new Response())
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody($html);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        return (new Response())
            ->setStatusCode($status)
            ->setHeader('Location', $url);
    }

    /**
     * @return array<string,mixed>
     */
    private function sharedContext(): array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return [
            'flash'           => $flash,
            'isAuthenticated' => !empty($_COOKIE['access_token']),
            'errors'          => [],
            'old'             => [],
        ];
    }
}
