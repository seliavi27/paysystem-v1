<?php
declare(strict_types=1);

namespace PaySystem\View;

use RuntimeException;

class TemplateEngine
{
    /** @var array<string,mixed> */
    private array $globals = [];

    public function __construct(
        private string $templatesPath
    ) {}

    public function addGlobal(string $key, mixed $value): void
    {
        $this->globals[$key] = $value;
    }

    public function render(string $template, array $data = []): string
    {
        $templateFile = $this->templatesPath . '/' . $template . '.php';

        if (!file_exists($templateFile))
        {
            throw new RuntimeException("Шаблон не найден: {$template}");
        }

        extract([...$this->globals, ...$data, 'view' => $this], EXTR_SKIP);

        ob_start();
        include $templateFile;

        return ob_get_clean();
    }

    public function renderWithLayout(
        string $template,
        array  $data = [],
        string $layout = 'layout'
    ): string
    {
        $content = $this->render($template, $data);

        return $this->render($layout, [...$data, 'content' => $content]);
    }

    public function include(string $template, array $data = []): string
    {
        return $this->render($template, $data);
    }

    public function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
