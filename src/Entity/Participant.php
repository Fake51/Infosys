<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=380)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $postalcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

    /**
     * @ORM\Column(type="boolean")
     */
    private $messaging;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ParticipantType", mappedBy="participants")
     */
    private $participantTypes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="participant", orphanRemoval=true)
     */
    private $notes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $superGamemaster;

    /**
     * @ORM\Column(type="array")
     */
    private $workAreas = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Activity", mappedBy="creators")
     */
    private $creations;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="integer")
     */
    private $wantedNumberOfActivities;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $birthDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $extraVouchers;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $googleCloudMessagingId;

    /**
     * @ORM\Column(type="text")
     */
    private $offeredSkills;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $applePushId;

    /**
     * @ORM\Column(type="integer")
     */
    private $wantedNumberOfTasks;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Language")
     * @ORM\JoinColumn(nullable=false)
     */
    private $languages;

    public function __construct()
    {
        $this->participantTypes = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->creations = new ArrayCollection();
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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getMessaging(): ?bool
    {
        return $this->messaging;
    }

    public function setMessaging(bool $messaging): self
    {
        $this->messaging = $messaging;

        return $this;
    }

    /**
     * @return Collection|ParticipantType[]
     */
    public function getParticipantTypes(): Collection
    {
        return $this->participantTypes;
    }

    public function addParticipantType(ParticipantType $participantType): self
    {
        if (!$this->participantTypes->contains($participantType)) {
            $this->participantTypes[] = $participantType;
            $participantType->addParticipant($this);
        }

        return $this;
    }

    public function removeParticipantType(ParticipantType $participantType): self
    {
        if ($this->participantTypes->contains($participantType)) {
            $this->participantTypes->removeElement($participantType);
            $participantType->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setParticipant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getParticipant() === $this) {
                $note->setParticipant(null);
            }
        }

        return $this;
    }

    public function getSuperGamemaster(): ?bool
    {
        return $this->superGamemaster;
    }

    public function setSuperGamemaster(bool $superGamemaster): self
    {
        $this->superGamemaster = $superGamemaster;

        return $this;
    }

    public function getWorkAreas(): ?array
    {
        return $this->workAreas;
    }

    public function setWorkAreas(array $workAreas): self
    {
        $this->workAreas = $workAreas;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getCreations(): Collection
    {
        return $this->creations;
    }

    public function addCreation(Activity $creation): self
    {
        if (!$this->creations->contains($creation)) {
            $this->creations[] = $creation;
            $creation->addCreator($this);
        }

        return $this;
    }

    public function removeCreation(Activity $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            $creation->removeCreator($this);
        }

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getWantedNumberOfActivities(): ?int
    {
        return $this->wantedNumberOfActivities;
    }

    public function setWantedNumberOfActivities(int $wantedNumberOfActivities): self
    {
        $this->wantedNumberOfActivities = $wantedNumberOfActivities;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getExtraVouchers(): ?int
    {
        return $this->extraVouchers;
    }

    public function setExtraVouchers(int $extraVouchers): self
    {
        $this->extraVouchers = $extraVouchers;

        return $this;
    }

    public function getGoogleCloudMessagingId(): ?string
    {
        return $this->googleCloudMessagingId;
    }

    public function setGoogleCloudMessagingId(string $googleCloudMessagingId): self
    {
        $this->googleCloudMessagingId = $googleCloudMessagingId;

        return $this;
    }

    public function getOfferedSkills(): ?string
    {
        return $this->offeredSkills;
    }

    public function setOfferedSkills(string $offeredSkills): self
    {
        $this->offeredSkills = $offeredSkills;

        return $this;
    }

    public function getApplePushId(): ?string
    {
        return $this->applePushId;
    }

    public function setApplePushId(string $applePushId): self
    {
        $this->applePushId = $applePushId;

        return $this;
    }

    public function getWantedNumberOfTasks(): ?int
    {
        return $this->wantedNumberOfTasks;
    }

    public function setWantedNumberOfTasks(int $wantedNumberOfTasks): self
    {
        $this->wantedNumberOfTasks = $wantedNumberOfTasks;

        return $this;
    }

    public function getLanguages(): ?Language
    {
        return $this->languages;
    }

    public function setLanguages(?Language $languages): self
    {
        $this->languages = $languages;

        return $this;
    }
}
