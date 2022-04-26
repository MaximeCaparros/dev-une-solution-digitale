<?php

namespace App\Entity;

use App\Repository\RentabiliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentabiliteRepository::class)]
class Rentabilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\Column(type: 'float')]
    private $benefice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBenefice(): ?float
    {
        return $this->benefice;
    }

    public function setBenefice(float $benefice): self
    {
        $this->benefice = $benefice;

        return $this;
    }
}
