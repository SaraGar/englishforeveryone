<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlanRepository")
 */
class Plan
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
    private $lessonsNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priceNet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $priceTotal;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disabled;

    public function __toString()
    {
        return (string) $this->getLessonsNumber();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLessonsNumber(): ?string
    {
        return $this->lessonsNumber;
    }

    public function setLessonsNumber(string $lessonsNumber): self
    {
        $this->lessonsNumber = $lessonsNumber;

        return $this;
    }

    public function getPriceNet(): ?string
    {
        return $this->priceNet;
    }

    public function setPriceNet(string $priceNet): self
    {
        $this->priceNet = $priceNet;

        return $this;
    }

    public function getPriceTotal(): ?string
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(string $priceTotal): self
    {
        $this->priceTotal = $priceTotal;

        return $this;
    }

    public function getDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }
}
