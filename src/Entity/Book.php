<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 50)]
    private ?string $genre = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getAuthor(): ?string { return $this->author; }
    public function setAuthor(string $author): self { $this->author = $author; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getGenre(): ?string { return $this->genre; }
    public function setGenre(string $genre): self { $this->genre = $genre; return $this; }
    public function getCoverImage(): ?string { return $this->coverImage; }
    public function setCoverImage(?string $coverImage): self { $this->coverImage = $coverImage; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    #[ORM\PrePersist]
    public function prePersist(): void {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
