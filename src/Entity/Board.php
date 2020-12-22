<?php

namespace App\Entity;

use App\Repository\BoardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=BoardRepository::class)
 */
class Board
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"default"})
     */
    private $name;

    /**
     * @Groups({"default"})
     * @ORM\Column(type="boolean")
     */
    private $isChef = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFamily = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfPersons;

    /**
     * @ORM\Column(type="integer")
     */
    private $minNumberOfPersons = 1;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="tableDetails")
     */
    private $reservations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tooltip;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIsChef(): ?bool
    {
        return $this->isChef;
    }

    public function setIsChef(bool $isChef): self
    {
        $this->isChef = $isChef;

        return $this;
    }

    public function getIsFamily(): ?bool
    {
        return $this->isFamily;
    }

    public function setIsFamily(bool $isFamily): self
    {
        $this->isFamily = $isFamily;

        return $this;
    }

    public function getNumberOfPersons(): ?int
    {
        return $this->numberOfPersons;
    }

    public function setNumberOfPersons(int $numberOfPersons): self
    {
        $this->numberOfPersons = $numberOfPersons;

        return $this;
    }

    public function getMinNumberOfPersons(): ?int
    {
        return $this->minNumberOfPersons;
    }

    public function setMinNumberOfPersons(int $minNumberOfPersons): self
    {
        $this->minNumberOfPersons = $minNumberOfPersons;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setTableDetails($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getTableDetails() === $this) {
                $reservation->setTableDetails(null);
            }
        }

        return $this;
    }

    public function getTooltip(): ?string
    {
        return $this->tooltip;
    }

    public function setTooltip(?string $tooltip): self
    {
        $this->tooltip = $tooltip;

        return $this;
    }
}
