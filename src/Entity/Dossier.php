<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
class Dossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbDossiers;

    #[ORM\ManyToOne(targetEntity: TypologieDossier::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $typologieDossier;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'dossier')]
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbDossiers(): ?int
    {
        return $this->nbDossiers;
    }

    public function setNbDossiers(?int $nbDossiers): self
    {
        $this->nbDossiers = $nbDossiers;

        return $this;
    }

    public function getTypologieDossier(): ?TypologieDossier
    {
        return $this->typologieDossier;
    }

    public function setTypologieDossier(?TypologieDossier $typologieDossier): self
    {
        $this->typologieDossier = $typologieDossier;

        return $this;
    }

    public function getUser(): ?Lieu
    {
        return $this->user;
    }

    public function setUser(?Lieu $user): self
    {
        $this->user = $user;

        return $this;
    }

}
