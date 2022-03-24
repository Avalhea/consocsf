<?php

namespace App\Entity;

use App\Repository\AtelierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AtelierRepository::class)]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $themeAtelier;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbSeances;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbPersonnesTotal;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'atelier')]
    #[ORM\JoinColumn(nullable: false)]
    private $lieu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getThemeAtelier(): ?string
    {
        return $this->themeAtelier;
    }

    public function setThemeAtelier(?string $themeAtelier): self
    {
        $this->themeAtelier = $themeAtelier;

        return $this;
    }

    public function getNbSeances(): ?int
    {
        return $this->nbSeances;
    }

    public function setNbSeances(?int $nbSeances): self
    {
        $this->nbSeances = $nbSeances;

        return $this;
    }

    public function getNbPersonnesTotal(): ?int
    {
        return $this->nbPersonnesTotal;
    }

    public function setNbPersonnesTotal(?int $nbPersonnesTotal): self
    {
        $this->nbPersonnesTotal = $nbPersonnesTotal;

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
