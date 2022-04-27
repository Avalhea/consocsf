<?php

namespace App\Entity;

use App\Repository\PermanenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermanenceRepository::class)]
class Permanence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbJours;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbHeures;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbDossierSimple;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbDossierDifficile;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbJours(): ?int
    {
        return $this->nbJours;
    }

    public function setNbJours(?int $nbJours): self
    {
        $this->nbJours = $nbJours;

        return $this;
    }

    public function getNbHeures(): ?int
    {
        return $this->nbHeures;
    }

    public function setNbHeures(?int $nbHeures): self
    {
        $this->nbHeures = $nbHeures;

        return $this;
    }

    public function getNbDossierSimple(): ?int
    {
        return $this->nbDossierSimple;
    }

    public function setNbDossierSimple(?int $nbDossierSimple): self
    {
        $this->nbDossierSimple = $nbDossierSimple;

        return $this;
    }

    public function getNbDossierDifficile(): ?int
    {
        return $this->nbDossierDifficile;
    }

    public function setNbDossierDifficile(?int $nbDossierDifficile): self
    {
        $this->nbDossierDifficile = $nbDossierDifficile;

        return $this;
    }


}
