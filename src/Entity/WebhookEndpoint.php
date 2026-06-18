<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WebhookEndpoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 120)]
    public string $name = '';

    #[ORM\Column(length: 255)]
    public string $url = '';

    #[ORM\Column(length: 80)]
    public string $eventType = '';

    #[ORM\Column(length: 160)]
    public string $secret = '';

    #[ORM\Column]
    public bool $active = true;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $createdBy = null;
}
