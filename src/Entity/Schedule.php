<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScheduleRepository")
 */
class Schedule
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="schedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $end;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location", inversedBy="schedules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $meetingLocation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Schedule", inversedBy="childSchedules")
     */
    private $parentSchedule;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="parentSchedule")
     */
    private $childSchedules;

    public function __construct()
    {
        $this->childSchedules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(\DateTimeImmutable $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(\DateTimeImmutable $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getMeetingLocation(): ?Location
    {
        return $this->meetingLocation;
    }

    public function setMeetingLocation(?Location $meetingLocation): self
    {
        $this->meetingLocation = $meetingLocation;

        return $this;
    }

    public function getParentSchedule(): ?self
    {
        return $this->parentSchedule;
    }

    public function setParentSchedule(?self $parentSchedule): self
    {
        $this->parentSchedule = $parentSchedule;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildSchedules(): Collection
    {
        return $this->childSchedules;
    }

    public function addChildSchedule(self $childSchedule): self
    {
        if (!$this->childSchedules->contains($childSchedule)) {
            $this->childSchedules[] = $childSchedule;
            $childSchedule->setParentSchedule($this);
        }

        return $this;
    }

    public function removeChildSchedule(self $childSchedule): self
    {
        if ($this->childSchedules->contains($childSchedule)) {
            $this->childSchedules->removeElement($childSchedule);
            // set the owning side to null (unless already changed)
            if ($childSchedule->getParentSchedule() === $this) {
                $childSchedule->setParentSchedule(null);
            }
        }

        return $this;
    }
}
