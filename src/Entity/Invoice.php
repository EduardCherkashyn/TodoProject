<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvoiceRepository")
 */
class Invoice implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=3000)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=3000)
     */
    private $description;
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    private $price;
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Currency()
     */
    private $currency;
    /**
     * @var
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeChargeId;
    /**
     * @ORM\Column(type="string", length=255, options={"default":"new"})
     */
    private $status = 'new';

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeInvoiceId;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getPrice(): ?int
    {
        return $this->price;
    }
    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }
    public function getCurrency(): ?string
    {
        return $this->currency;
    }
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }
    public function setStripeChargeId(string $stripeChargeId): self
    {
        $this->stripeChargeId = $stripeChargeId;
        return $this;
    }
    public function getStripeChargeId(): ?string
    {
        return $this->stripeChargeId;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'price' => $this->getPrice(),
            'currency' => $this->getCurrency(),
            'stripeChargeId' => $this->getStripeChargeId(),
            'stripeInvoiceId' => $this->getStripeInvoiceId()
        ];
    }

    public function getStripeInvoiceId(): ?string
    {
        return $this->stripeInvoiceId;
    }

    public function setStripeInvoiceId(?string $stripeInvoiceId): self
    {
        $this->stripeInvoiceId = $stripeInvoiceId;

        return $this;
    }
}
