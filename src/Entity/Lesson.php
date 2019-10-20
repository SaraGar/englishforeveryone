<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LessonRepository")
 */
class Lesson
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
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $attendants;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $priceNet;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Schedule", mappedBy="lesson", orphanRemoval=true)
     */
    private $schedules;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getLevel().' '.$this->getDescription();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttendants(): ?int
    {
        return $this->attendants;
    }

    public function setAttendants(int $attendants): self
    {
        $this->attendants = $attendants;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceNet(): ?float
    {
        return $this->priceNet;
    }

    public function setPriceNet(float $priceNet): self
    {
        $this->priceNet = $priceNet;

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
            $schedule->setLesson($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->contains($schedule)) {
            $this->schedules->removeElement($schedule);
            // set the owning side to null (unless already changed)
            if ($schedule->getLesson() === $this) {
                $schedule->setLesson(null);
            }
        }

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }
}
