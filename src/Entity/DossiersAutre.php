<?php

namespace App\Entity;

use App\Repository\DossiersAutreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossiersAutreRepository::class)]
class DossiersAutre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $libelle;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbDossiers;

    #[ORM\OneToOne(targetEntity: Lieu::class, cascade: ['persist', 'remove'])]
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
}
