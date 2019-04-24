<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    const VALID_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $passwordResetHash;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $passwordResetTime;

    public function __construct()
    {
        $this->password = 'invalid';
        $this->status = self::STATUS_INACTIVE;
        $this->passwordResetHash = '';
        $this->passwordResetTime = new \DateTimeImmutable('1000-01-01 00:00:00');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException('Invalid status set');
        }

        $this->status = $status;

        return $this;
    }

    public function getPasswordResetHash(): string
    {
        return $this->passwordResetHash;
    }

    public function setPasswordResetHash(string $passwordResetHash): self
    {
        $this->passwordResetHash = $passwordResetHash;

        return $this;
    }

    public function getPasswordResetTime(): \DateTimeImmutable
    {
        return $this->passwordResetTime;
    }

    public function setPasswordResetTime(\DateTimeImmutable $passwordResetTime): self
    {
        $this->passwordResetTime = $passwordResetTime;

        return $this;
    }
}
