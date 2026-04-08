<?php
declare(strict_types=1);

namespace PaySystem\Storage;

class JsonStorage implements StorageInterface
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;

        if (!file_exists($this->file))
        {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function save(object $object): bool
    {
        $data = $this->read();

        $array = $this->normalize($object);

        $found = false;

        foreach ($data as &$item)
        {
            if (($item['id'] ?? null) === ($array['id'] ?? null))
            {
                $item = $array;
                $found = true;
                break;
            }
        }

        if (!$found)
        {
            $data[] = $array;
        }

        return $this->write($data);
    }

    public function delete(string $id): bool
    {
        $data = $this->read();

        $data = array_filter($data, fn($item) => ($item['id'] ?? null) !== $id);

        return $this->write(array_values($data));
    }

    public function find(string $id): ?object
    {
        $data = $this->read();

        foreach ($data as $item) {
            if (($item['id'] ?? null) === $id) {
                return (object)$item;
            }
        }

        return null;
    }

    public function findAll(): array
    {
        return array_map(fn($item) => (object)$item, $this->read());
    }

    public function all(): array
    {
        return $this->read();
    }

    private function read(): array
    {
        $content = file_get_contents($this->file);

        $data = json_decode($content, true);

        return is_array($data) ? $data : [];
    }

    private function write(array $data): bool
    {
        return (bool)file_put_contents(
            $this->file,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    private function normalize(object $object): array
    {
        if (method_exists($object, 'toArray'))
        {
            return $object->toArray();
        }

        return (array)$object;
    }
}