<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityRepository")
 */
class Activity
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant", inversedBy="creations")
     */
    private $creators;

    /**
     * @ORM\Column(type="float")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer")
     */
    private $minimumGroupParticipants;

    /**
     * @ORM\Column(type="integer")
     */
    private $maximumGroupParticipants;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfGroupHostsRequired;

    /**
     * @ORM\Column(type="text")
     */
    private $note;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAreaExclusive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isScheduleExclusive;

    /**
     * @ORM\Column(type="array")
     */
    private $languages = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $canParticipateMoreThanOnce;

    /**
     * @ORM\Column(type="integer")
     */
    private $maximumSignups;

    /**
     * @ORM\Column(type="integer")
     */
    private $maximumSignupsPerSchedule;

    /**
     * @ORM\Column(type="integer")
     */
    private $karmaType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ActivityType", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="activity", orphanRemoval=true)
     */
    private $schedules;

    public function __construct()
    {
        $this->creators = new ArrayCollection();
        $this->schedules = new ArrayCollection();
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

    /**
     * @return Collection|Participant[]
     */
    public function getCreators(): Collection
    {
        return $this->creators;
    }

    public function addCreator(Participant $creator): self
    {
        if (!$this->creators->contains($creator)) {
            $this->creators[] = $creator;
        }

        return $this;
    }

    public function removeCreator(Participant $creator): self
    {
        if ($this->creators->contains($creator)) {
            $this->creators->removeElement($creator);
        }

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMinimumGroupParticipants(): ?int
    {
        return $this->minimumGroupParticipants;
    }

    public function setMinimumGroupParticipants(int $minimumGroupParticipants): self
    {
        $this->minimumGroupParticipants = $minimumGroupParticipants;

        return $this;
    }

    public function getMaximumGroupParticipants(): ?int
    {
        return $this->maximumGroupParticipants;
    }

    public function setMaximumGroupParticipants(int $maximumGroupParticipants): self
    {
        $this->maximumGroupParticipants = $maximumGroupParticipants;

        return $this;
    }

    public function getNumberOfGroupHostsRequired(): ?int
    {
        return $this->numberOfGroupHostsRequired;
    }

    public function setNumberOfGroupHostsRequired(int $numberOfGroupHostsRequired): self
    {
        $this->numberOfGroupHostsRequired = $numberOfGroupHostsRequired;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIsAreaExclusive(): ?bool
    {
        return $this->isAreaExclusive;
    }

    public function setIsAreaExclusive(bool $isAreaExclusive): self
    {
        $this->isAreaExclusive = $isAreaExclusive;

        return $this;
    }

    public function getIsScheduleExclusive(): ?bool
    {
        return $this->isScheduleExclusive;
    }

    public function setIsScheduleExclusive(bool $isScheduleExclusive): self
    {
        $this->isScheduleExclusive = $isScheduleExclusive;

        return $this;
    }

    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    public function setLanguages(array $languages): self
    {
        $this->languages = $languages;

        return $this;
    }

    public function getCanParticipateMoreThanOnce(): ?bool
    {
        return $this->canParticipateMoreThanOnce;
    }

    public function setCanParticipateMoreThanOnce(bool $canParticipateMoreThanOnce): self
    {
        $this->canParticipateMoreThanOnce = $canParticipateMoreThanOnce;

        return $this;
    }

    public function getMaximumSignups(): ?int
    {
        return $this->maximumSignups;
    }

    public function setMaximumSignups(int $maximumSignups): self
    {
        $this->maximumSignups = $maximumSignups;

        return $this;
    }

    public function getMaximumSignupsPerSchedule(): ?int
    {
        return $this->maximumSignupsPerSchedule;
    }

    public function setMaximumSignupsPerSchedule(int $maximumSignupsPerSchedule): self
    {
        $this->maximumSignupsPerSchedule = $maximumSignupsPerSchedule;

        return $this;
    }

    public function getKarmaType(): ?int
    {
        return $this->karmaType;
    }

    public function setKarmaType(int $karmaType): self
    {
        $this->karmaType = $karmaType;

        return $this;
    }

    public function getType(): ?ActivityType
    {
        return $this->type;
    }

    public function setType(?ActivityType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Schedule[]
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setActivity($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getActivity() === $this) {
                $schedule->setActivity(null);
            }
        }

        return $this;
    }
}
