<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`task`')]
class Task
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
    public string $status = 'todo';

    #[ORM\Column(length: 40)]
    public string $priority = 'normal';

    #[ORM\Column(nullable: true)]
    public ?\DateTimeImmutable $dueDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    public ?User $assignedTo = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?Project $project = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $createdBy = null;
}
