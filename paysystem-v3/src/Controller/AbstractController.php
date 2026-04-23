<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

abstract class AbstractController
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Environment $twig
    ) {}

    protected function json(array $data, int $status = 200): Response
    {
        return new JsonResponse($data, $status);
    }

    protected function view(string $template, array $data = []): Response
    {
        $html = $this->twig->render($template, [
            ...$this->sharedContext(),
            ...$data]);

        return new Response(
            $html,
            Response::HTTP_OK,
            ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    protected function redirect(string $url, int $status = Response::HTTP_FOUND): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @return array<string,mixed>
     */
    private function sharedContext(): array
    {
        $flash = [];
        $session = $this->requestStack->getCurrentRequest()?->getSession();

        if ($session)
        {
            foreach ($session->getFlashBag()->all() as $type => $messages)
            {
                $flash[$type] = $messages[0] ?? null;
            }
        }

        $request = $this->requestStack->getCurrentRequest();
        $cookies = $request?->cookies->has('access_token') ?? false;

        return [
            'flash'           => $flash ? : null,
            'isAuthenticated' => $cookies,
            'errors'          => [],
            'old'             => [],
        ];
    }
}
