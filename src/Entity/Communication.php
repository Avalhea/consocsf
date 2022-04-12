<?php

namespace App\Entity;

use App\Repository\CommunicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommunicationRepository::class)]
class Communication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nombre;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private $Sujets;

    #[ORM\ManyToOne(targetEntity: TypeCommunication::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $typeCommunication;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: 'communication')]
    private $lieu;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(?int $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getSujets(): ?string
    {
        return $this->Sujets;
    }

    public function setSujets(string $Sujets): self
    {
        $this->Sujets = $Sujets;

        return $this;
    }

    public function getTypeCommunication(): ?TypeCommunication
    {
        return $this->typeCommunication;
    }

    public function setTypeCommunication(?TypeCommunication $typeCommunication): self
    {
        $this->typeCommunication = $typeCommunication;

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
