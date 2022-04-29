<?php

namespace App\Entity;

use App\Repository\RepresentationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RepresentationRepository::class)]
class Representation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $frequence;

    #[ORM\ManyToOne(targetEntity: CategorieRep::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $categorie;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'representation')]
    private $lieu;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrequence(): ?int
    {
        return $this->frequence;
    }

    public function setFrequence(?int $frequence): self
    {
        $this->frequence = $frequence;

        return $this;
    }

    public function getCategorie(): ?CategorieRep
    {
        return $this->categorie;
    }

    public function setCategorie(?CategorieRep $categorie): self
    {
        $this->categorie = $categorie;

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
