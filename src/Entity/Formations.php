<?php

namespace App\Entity;

use App\Repository\FormationsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationsRepository::class)]
class Formations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $NbFormationsAnnee;

    #[ORM\Column(type: 'text', nullable: true)]
    private $ThemeFormationEtParticipants;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbFormationsAnnee(): ?int
    {
        return $this->NbFormationsAnnee;
    }

    public function setNbFormationsAnnee(?int $NbFormationsAnnee): self
    {
        $this->NbFormationsAnnee = $NbFormationsAnnee;

        return $this;
    }

    public function getThemeFormationEtParticipants(): ?string
    {
        return $this->ThemeFormationEtParticipants;
    }

    public function setThemeFormationEtParticipants(?string $ThemeFormationEtParticipants): self
    {
        $this->ThemeFormationEtParticipants = $ThemeFormationEtParticipants;

        return $this;
    }
}
