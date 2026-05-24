<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(length: 40)]
    private string $number = '';

    #[ORM\Column]
    private int $amountCents = 0;

    #[ORM\Column(length: 255)]
    private string $filePath = '';

    #[ORM\Column]
    private \DateTimeImmutable $issuedAt;

    public function __construct() { $this->issuedAt = new \DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getUser(): User { return $this->user; }
    public function setUser(User $user): self { $this->user = $user; return $this; }
    public function getNumber(): string { return $this->number; }
    public function setNumber(string $number): self { $this->number = $number; return $this; }
    public function getAmountCents(): int { return $this->amountCents; }
    public function setAmountCents(int $amountCents): self { $this->amountCents = $amountCents; return $this; }
    public function getFilePath(): string { return $this->filePath; }
    public function setFilePath(string $filePath): self { $this->filePath = $filePath; return $this; }
    public function getIssuedAt(): \DateTimeImmutable { return $this->issuedAt; }
}
