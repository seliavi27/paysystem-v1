<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use PaySystem\Response;
use PaySystem\View\TemplateEngine;

abstract class AbstractController
{
    public function __construct(
        private readonly TemplateEngine $templateEngine
    ) { }

    protected function json(array $data, int $status = 200): Response
    {
        $response = new Response();
        return $response
            ->setStatusCode($status)
            ->setJson($data);
    }

    protected function view(string $template, array $data = []): Response
    {
        $html = $this->templateEngine->renderWithLayout($template, $data);
        $response = new Response();
        return $response->setBody($html);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        $response = new Response();
        $response->setHeader('Location', $url);
        return $response->setStatusCode($status);
    }
}