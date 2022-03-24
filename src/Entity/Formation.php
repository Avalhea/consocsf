<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbParticipants;

    #[ORM\Column(type: 'string', length: 60)]
    private $themeFormation;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'formation')]
    #[ORM\JoinColumn(nullable: false)]
    private $lieu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbParticipants(): ?int
    {
        return $this->nbParticipants;
    }

    public function setNbParticipants(?int $nbParticipants): self
    {
        $this->nbParticipants = $nbParticipants;

        return $this;
    }

    public function getThemeFormation(): ?string
    {
        return $this->themeFormation;
    }

    public function setThemeFormation(string $themeFormation): self
    {
        $this->themeFormation = $themeFormation;

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
