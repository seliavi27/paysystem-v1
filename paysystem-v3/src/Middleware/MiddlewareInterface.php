<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response): ?Response;
}