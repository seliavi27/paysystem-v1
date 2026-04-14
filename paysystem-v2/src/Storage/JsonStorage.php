<?php
declare(strict_types=1);

namespace PaySystem\Storage;

class JsonStorage implements StorageInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->ensureFileExists();
    }

    public function load(): array
    {
        $content = file_get_contents($this->filePath);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public function save(array $data): bool
    {
        return (bool)file_put_contents(
            $this->filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function ensureFileExists(): void
    {
        if (!file_exists($this->filePath))
        {
            file_put_contents($this->filePath, json_encode([]));
        }
    }
}