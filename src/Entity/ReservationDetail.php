<?php

namespace App\Entity;

use App\Repository\ReservationDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReservationDetailRepository::class)
 */
class ReservationDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Menu::class, inversedBy="reservationDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Reservation::class, inversedBy="reservationDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $reservationId;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $taxValue;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?menu
    {
        return $this->name;
    }

    public function setName(?menu $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReservationId(): ?reservation
    {
        return $this->reservationId;
    }

    public function setReservationId(?reservation $reservationId): self
    {
        $this->reservationId = $reservationId;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getTaxValue(): ?string
    {
        return $this->taxValue;
    }

    public function setTaxValue(string $taxValue): self
    {
        $this->taxValue = $taxValue;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
