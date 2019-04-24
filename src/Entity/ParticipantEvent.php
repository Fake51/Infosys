<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantEventRepository")
 */
class ParticipantEvent
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="string", length=1024)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $participantId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParticipantId(): ?int
    {
        return $this->participantId;
    }

    public function setParticipantId(int $participantId): self
    {
        $this->participantId = $participantId;

        return $this;
    }
}
