<?php

namespace App\Entity;

use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['task:read'])]
    private ?string $title;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['task:read'])]
    private ?string $description = null;

    #[ORM\Column(enumType: TaskStatus::class, length: 50)]
    #[Groups(['task:read'])]
    private TaskStatus $status = TaskStatus::ACTIVE;

    #[ORM\Column(length: 50)]
    #[Groups(['task:read'])]
    private string $priority = 'medium'; // low, medium, high

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: "id", nullable: true)]
    private ?Project $project = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'assignedTasks')]
    #[ORM\JoinColumn(name: "assigned_to", referencedColumnName: "id", nullable: true)]
    private ?User $assignedTo = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdTasks')]
    #[ORM\JoinColumn(name: "created_by", referencedColumnName: "id", nullable: true)]
    private ?User $createdBy = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['task:read'])]
    private ?DateTimeImmutable $dueDate = null;

    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['task:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setStatus(TaskStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function setDueDate(?DateTimeImmutable $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): void
    {
        $this->assignedTo = $assignedTo;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

}
