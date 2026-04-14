<?php
declare(strict_types=1);

namespace PaySystem\Controller;

use PaySystem\Response;

abstract class AbstractController
{
    protected function json(array $data, int $status = 200): Response
    {
        $response = new Response();
        return $response
            ->setStatusCode($status)
            ->setJson($data);
    }

    protected function view(string $view, array $data = []): Response
    {
        $response = new Response();
        return $response->setBody(json_encode($data));
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        $response = new Response();
        $response->setHeader('Location', $url);
        return $response->setStatusCode($status);
    }
}