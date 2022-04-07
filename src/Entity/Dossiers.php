<?php

namespace App\Entity;

use App\Repository\DossiersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DossiersRepository::class)]
class Dossiers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $CommunicationsElectroniques;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Banque;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Surendettement;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Assurance;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Sante;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Services;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Energie;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Eau;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Automobile;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $LogementLocSocial;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $LogementLocPriv;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $LogementPropri;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $Travaux;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $PratiquesComDeloyales;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $DefenseDroitsAcces;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $NbAutre;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $AutreLibelle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommunicationsElectroniques(): ?int
    {
        return $this->CommunicationsElectroniques;
    }

    public function setCommunicationsElectroniques(?int $CommunicationsElectroniques): self
    {
        $this->CommunicationsElectroniques = $CommunicationsElectroniques;

        return $this;
    }

    public function getBanque(): ?int
    {
        return $this->Banque;
    }

    public function setBanque(?int $Banque): self
    {
        $this->Banque = $Banque;

        return $this;
    }

    public function getSurendettement(): ?int
    {
        return $this->Surendettement;
    }

    public function setSurendettement(?int $Surendettement): self
    {
        $this->Surendettement = $Surendettement;

        return $this;
    }

    public function getAssurance(): ?int
    {
        return $this->Assurance;
    }

    public function setAssurance(?int $Assurance): self
    {
        $this->Assurance = $Assurance;

        return $this;
    }

    public function getSante(): ?int
    {
        return $this->Sante;
    }

    public function setSante(?int $Sante): self
    {
        $this->Sante = $Sante;

        return $this;
    }

    public function getServices(): ?int
    {
        return $this->Services;
    }

    public function setServices(?int $Services): self
    {
        $this->Services = $Services;

        return $this;
    }

    public function getEnergie(): ?int
    {
        return $this->Energie;
    }

    public function setEnergie(?int $Energie): self
    {
        $this->Energie = $Energie;

        return $this;
    }

    public function getEau(): ?int
    {
        return $this->Eau;
    }

    public function setEau(?int $Eau): self
    {
        $this->Eau = $Eau;

        return $this;
    }

    public function getAutomobile(): ?int
    {
        return $this->Automobile;
    }

    public function setAutomobile(?int $Automobile): self
    {
        $this->Automobile = $Automobile;

        return $this;
    }

    public function getLogementLocSocial(): ?int
    {
        return $this->LogementLocSocial;
    }

    public function setLogementLocSocial(?int $LogementLocSocial): self
    {
        $this->LogementLocSocial = $LogementLocSocial;

        return $this;
    }

    public function getLogementLocPriv(): ?int
    {
        return $this->LogementLocPriv;
    }

    public function setLogementLocPriv(?int $LogementLocPriv): self
    {
        $this->LogementLocPriv = $LogementLocPriv;

        return $this;
    }

    public function getLogementPropri(): ?int
    {
        return $this->LogementPropri;
    }

    public function setLogementPropri(?int $LogementPropri): self
    {
        $this->LogementPropri = $LogementPropri;

        return $this;
    }

    public function getTravaux(): ?int
    {
        return $this->Travaux;
    }

    public function setTravaux(?int $Travaux): self
    {
        $this->Travaux = $Travaux;

        return $this;
    }

    public function getPratiquesComDeloyales(): ?int
    {
        return $this->PratiquesComDeloyales;
    }

    public function setPratiquesComDeloyales(?int $PratiquesComDeloyales): self
    {
        $this->PratiquesComDeloyales = $PratiquesComDeloyales;

        return $this;
    }

    public function getDefenseDroitsAcces(): ?int
    {
        return $this->DefenseDroitsAcces;
    }

    public function setDefenseDroitsAcces(?int $DefenseDroitsAcces): self
    {
        $this->DefenseDroitsAcces = $DefenseDroitsAcces;

        return $this;
    }

    public function getNbAutre(): ?int
    {
        return $this->NbAutre;
    }

    public function setNbAutre(?int $NbAutre): self
    {
        $this->NbAutre = $NbAutre;

        return $this;
    }

    public function getAutreLibelle(): ?string
    {
        return $this->AutreLibelle;
    }

    public function setAutreLibelle(?string $AutreLibelle): self
    {
        $this->AutreLibelle = $AutreLibelle;

        return $this;
    }
}
