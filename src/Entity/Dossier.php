<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
class Dossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Assert\PositiveOrZero
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbDossiers;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'dossier')]
    private $lieu;

    #[ORM\ManyToOne(targetEntity: TypologieDossier::class, inversedBy: 'dossiers')]
    private $typologieDossier;

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

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

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
}
