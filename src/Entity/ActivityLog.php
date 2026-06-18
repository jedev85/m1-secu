<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ActivityLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $actor = null;

    #[ORM\Column(length: 120)]
    public string $action = '';

    #[ORM\Column(length: 80)]
    public string $targetType = '';

    #[ORM\Column(nullable: true)]
    public ?int $targetId = null;

    #[ORM\Column(length: 60, nullable: true)]
    public ?string $ipAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $userAgent = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $details = null;

    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
