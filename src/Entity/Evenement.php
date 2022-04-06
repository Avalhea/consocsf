<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $detailEvenement;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'evenement')]
    private $lieu;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetailEvenement(): ?string
    {
        return $this->detailEvenement;
    }

    public function setDetailEvenement(?string $detailEvenement): self
    {
        $this->detailEvenement = $detailEvenement;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

}
