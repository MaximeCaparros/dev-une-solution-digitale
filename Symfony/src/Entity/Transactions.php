<?php

namespace App\Entity;


use App\Repository\TransactionsRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TransactionsRepository::class)]
class Transactions
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\Column(type: 'float')]
    private $quantity;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    private $solded;

    #[ORM\Column(type: 'float', nullable: true)]
    private $benefit;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $soldedAt;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSolded(): ?bool
    {
        return $this->solded;
    }

    public function setSolded(bool $solded): self
    {
        $this->solded = $solded;

        return $this;
    }

    public function getBenefit(): ?float
    {
        return $this->benefit;
    }

    public function setBenefit(?float $benefit): self
    {
        $this->benefit = $benefit;

        return $this;
    }

    public function getSoldedAt(): ?\DateTimeInterface
    {
        return $this->soldedAt;
    }

    public function setSoldedAt(?\DateTimeInterface $soldedAt): self
    {
        $this->soldedAt = $soldedAt;

        return $this;
    }
}
