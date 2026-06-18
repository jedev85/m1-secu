<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Setting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 100)]
    public string $keyName = '';

    #[ORM\Column(type: 'text')]
    public string $value = '';

    #[ORM\Column]
    public bool $isSensitive = false;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $updatedBy = null;

    #[ORM\Column]
    public \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
