<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 160)]
    public string $name = '';

    #[ORM\Column(length: 180)]
    public string $email = '';

    #[ORM\Column(length: 40, nullable: true)]
    public ?string $phone = null;

    #[ORM\Column(length: 160)]
    public string $company = '';

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $notes = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $owner = null;

    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
