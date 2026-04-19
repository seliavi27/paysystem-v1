<?php
declare(strict_types=1);

namespace PaySystem\Middleware;

use PaySystem\Request;
use PaySystem\Response;;

interface MiddlewareInterface
{
    public function handle(Request $request, Response $response): void;
}