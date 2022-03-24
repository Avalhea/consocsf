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

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private $libelle;

    #[ORM\Column(type: 'text', nullable: true)]
    private $detailEvenement;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'evenement')]
    #[ORM\JoinColumn(nullable: false)]
    private $lieu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
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
