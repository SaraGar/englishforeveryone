<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoCodeRepository")
 */
class PromoCode
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $code;

    /**
     * @ORM\Column(type="float")
     */
    private $percentDiscount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxTimesUsed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $timesUsed;

    public function __toString()
    {
        return (string) $this->getCode();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPercentDiscount(): ?float
    {
        return $this->percentDiscount;
    }

    public function setPercentDiscount(float $percentDiscount): self
    {
        $this->percentDiscount = $percentDiscount;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getMaxTimesUsed(): ?int
    {
        return $this->maxTimesUsed;
    }

    public function setMaxTimesUsed(?int $maxTimesUsed): self
    {
        $this->maxTimesUsed = $maxTimesUsed;

        return $this;
    }

    public function getTimesUsed(): ?int
    {
        return $this->timesUsed;
    }

    public function setTimesUsed(?int $timesUsed): self
    {
        $this->timesUsed = $timesUsed;

        return $this;
    }
}
