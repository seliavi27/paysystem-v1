<?php
declare(strict_types=1);

namespace PaySystem\View;

use RuntimeException;

class TemplateEngine
{
    public function __construct(
        private string $templatesPath
    ) {}

    public function render(string $template, array $data = []): string
    {
        $templateFile = $this->templatesPath . '/' . $template . '.php';

        if (!file_exists($templateFile))
        {
            throw new RuntimeException("Шаблон не найден: {$template}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $templateFile;
        $content = ob_get_clean();

        return $content;
    }

    public function renderWithLayout(
        string $template,
        array $data = [],
        string $layout = 'layout'
    ): string
    {
        $content = $this->render($template, $data);

        if ($layout === null)
        {
            return $content;
        }

        return $this->render($layout, array_merge($data,[
            'content' => $content,
        ]));
    }
}