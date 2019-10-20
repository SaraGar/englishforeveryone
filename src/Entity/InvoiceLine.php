<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceLineRepository")
 */
class InvoiceLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Invoice", inversedBy="invoiceLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $concept;

    /**
     * @ORM\Column(type="float")
     */
    private $totalNet;

    /**
     * @ORM\Column(type="float")
     */
    private $totalVAT;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    public function __toString()
    {
        return (string) $this->getConcept();
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getConcept(): ?string
    {
        return $this->concept;
    }

    public function setConcept(string $concept): self
    {
        $this->concept = $concept;

        return $this;
    }

    public function getTotalNet(): ?float
    {
        return $this->totalNet;
    }

    public function setTotalNet(float $totalNet): self
    {
        $this->totalNet = $totalNet;

        return $this;
    }

    public function getTotalVAT(): ?float
    {
        return $this->totalVAT;
    }

    public function setTotalVAT(float $totalVAT): self
    {
        $this->totalVAT = $totalVAT;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }
}
