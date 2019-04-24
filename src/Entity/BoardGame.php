<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BoardGameRepository")
 */
class BoardGame
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $owner;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="integer")
     */
    private $boardgamegeekId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isOriginConvention;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BoardGameEvent", mappedBy="boardGame")
     */
    private $boardGameEvents;

    public function __construct()
    {
        $this->boardGameEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getBoardgamegeekId(): ?int
    {
        return $this->boardgamegeekId;
    }

    public function setBoardgamegeekId(int $boardgamegeekId): self
    {
        $this->boardgamegeekId = $boardgamegeekId;

        return $this;
    }

    public function getIsOriginConvention(): ?bool
    {
        return $this->isOriginConvention;
    }

    public function setIsOriginConvention(bool $isOriginConvention): self
    {
        $this->isOriginConvention = $isOriginConvention;

        return $this;
    }

    /**
     * @return Collection|BoardGameEvent[]
     */
    public function getBoardGameEvents(): Collection
    {
        return $this->boardGameEvents;
    }

    public function addBoardGameEvent(BoardGameEvent $boardGameEvent): self
    {
        if (!$this->boardGameEvents->contains($boardGameEvent)) {
            $this->boardGameEvents[] = $boardGameEvent;
            $boardGameEvent->setBoardGame($this);
        }

        return $this;
    }

    public function removeBoardGameEvent(BoardGameEvent $boardGameEvent): self
    {
        if ($this->boardGameEvents->contains($boardGameEvent)) {
            $this->boardGameEvents->removeElement($boardGameEvent);
            // set the owning side to null (unless already changed)
            if ($boardGameEvent->getBoardGame() === $this) {
                $boardGameEvent->setBoardGame(null);
            }
        }

        return $this;
    }
}
