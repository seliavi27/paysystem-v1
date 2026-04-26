<?php
declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

trait Timestampable
{
    #[ORM\Column(name: 'created_at', type: 'datetimetz_immutable')]
    public DateTime $createdAt
    {
        get => $this->createdAt;
    }

    #[ORM\Column(name: 'updated_at', type: 'datetimetz_immutable')]
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