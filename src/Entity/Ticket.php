<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 180)]
    public string $title = '';

    #[ORM\Column(type: 'text')]
    public string $description = '';

    #[ORM\Column(length: 40)]
    public string $status = 'open';

    #[ORM\Column(length: 40)]
    public string $priority = 'normal';

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?Client $customer = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $assignedTo = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Comment::class, cascade: ['remove'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    public Collection $comments;

    #[ORM\Column]
    public \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }
}
