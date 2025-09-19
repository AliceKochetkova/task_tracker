<?php


namespace App\Entity;

use App\Enum\ProjectStatus;
use App\Repository\ProjectRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: "project")]
#[ORM\HasLifecycleCallbacks]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['project:read'])]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['project:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['project:read'])]
    private ProjectStatus $status = ProjectStatus::ACTIVE;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "id", nullable: true)]
    private ?User $createdBy = null;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['project:read'])]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(ProjectStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $user): static
    {
        $this->createdBy = $user;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
