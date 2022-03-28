<?php

namespace App\Entity;

use App\Repository\ActionJusticeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionJusticeRepository::class)]
class ActionJustice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbActionConjointe;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbAccompagnement;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'atelier')]
    #[ORM\JoinColumn(nullable: false)]
    private $lieu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbActionConjointe(): ?int
    {
        return $this->nbActionConjointe;
    }

    public function setNbActionConjointe(?int $nbActionConjointe): self
    {
        $this->nbActionConjointe = $nbActionConjointe;

        return $this;
    }

    public function getNbAccompagnement(): ?int
    {
        return $this->nbAccompagnement;
    }

    public function setNbAccompagnement(?int $nbAccompagnement): self
    {
        $this->nbAccompagnement = $nbAccompagnement;

        return $this;
    }
}
