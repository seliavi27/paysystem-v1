<?php
declare(strict_types=1);

trait Timestampable
{
    public DateTime $createdAt
    {
        get => $this->createdAt;
    }
    public DateTime $updatedAt
    {
        get => $this->updatedAt;
    }

    protected function initializeTimestamps(): void
    {
        $now = new DateTime();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTime();
    }
}