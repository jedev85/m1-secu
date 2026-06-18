<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 180)]
    public string $title = '';

    #[ORM\Column(length: 255)]
    public string $filename = '';

    #[ORM\Column(length: 255)]
    public string $originalFilename = '';

    #[ORM\Column(length: 120)]
    public string $mimeType = '';

    #[ORM\Column(length: 255)]
    public string $path = '';

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $uploadedBy = null;

    #[ORM\Column(length: 40)]
    public string $visibility = 'private';

    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
