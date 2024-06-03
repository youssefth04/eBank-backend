<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;

#[ORM\Entity(repositoryClass: "App\Repository\SessionRepository")]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $sessionToken = null;

    #[ORM\Column(type: "datetime")]
    private $expirationDate;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getSessionToken(): ?string
    {
        return $this->sessionToken;
    }

    public function setSessionToken(string $sessionToken): self
    {
        $this->sessionToken = $sessionToken;
        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }
}