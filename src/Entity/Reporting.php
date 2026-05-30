<?php

namespace App\Entity;

use App\Repository\ReportingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportingRepository::class)]
class Reporting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $reason;

    #[ORM\Column]
    private bool $treated;

    #[ORM\Column(length: 50)]
    private string $type;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'reportingsSent')]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    #[ORM\ManyToOne(inversedBy: 'reportingsReceived')]
    #[ORM\JoinColumn(nullable: false)]
    private User $target;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function isTreated(): bool
    {
        return $this->treated;
    }

    public function setTreated(bool $treated): static
    {
        $this->treated = $treated;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getTarget(): User
    {
        return $this->target;
    }

    public function setTarget(User $target): static
    {
        $this->target = $target;

        return $this;
    }
}
