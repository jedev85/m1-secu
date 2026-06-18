<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class InternalNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 180)]
    public string $title = '';

    #[ORM\Column(type: 'text')]
    public string $content = '';

    #[ORM\Column(length: 40)]
    public string $visibility = 'private';

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    public ?Client $relatedClient = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    public ?Project $relatedProject = null;

    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
