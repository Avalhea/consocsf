<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nom;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbSalaries;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbBenevole;

    #[ORM\Column(type: 'string', length: 254, nullable: true)]
    private $adresse;

    #[ORM\Column(type: 'text', nullable: true)]
    private $joursEtHorairesOuverture;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $NbConsomRensTel;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'lieux')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Statut::class, inversedBy: 'lieux')]
    private $statut;

    #[ORM\ManyToOne(targetEntity: UD::class, inversedBy: 'lieux')]
    private $UD;


    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Atelier::class)]
    private $atelier;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Communication::class)]
    private $communication;

    #[ORM\OneToOne(targetEntity: Dossiers::class, cascade: ['persist', 'remove'])]
    private $dossiers;

    #[ORM\OneToOne(targetEntity: Permanence::class, cascade: ['persist', 'remove'])]
    private $permanence;

    #[ORM\OneToOne(targetEntity: Evenement::class, cascade: ['persist', 'remove'])]
    private $evenement;

    #[ORM\OneToOne(targetEntity: Formations::class, cascade: ['persist', 'remove'])]
    private $formations;

    #[ORM\OneToOne(targetEntity: Representation::class, cascade: ['persist', 'remove'])]
    private $representations;

    #[ORM\OneToOne(targetEntity: ActionJustice::class, cascade: ['persist', 'remove'])]
    private $actionJustice;

    public function __construct()
    {
        $this->atelier = new ArrayCollection();
        $this->communication = new ArrayCollection();
        $this->formation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNbSalaries(): ?int
    {
        return $this->nbSalaries;
    }

    public function setNbSalaries(?int $nbSalaries): self
    {
        $this->nbSalaries = $nbSalaries;

        return $this;
    }

    public function getNbBenevole(): ?int
    {
        return $this->nbBenevole;
    }

    public function setNbBenevole(?int $nbBenevole): self
    {
        $this->nbBenevole = $nbBenevole;

        return $this;
    }


    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getJoursEtHorairesOuverture(): ?string
    {
        return $this->joursEtHorairesOuverture;
    }

    public function setJoursEtHorairesOuverture(?string $joursEtHorairesOuverture): self
    {
        $this->joursEtHorairesOuverture = $joursEtHorairesOuverture;

        return $this;
    }


    public function getNbConsomRensTel(): ?int
    {
        return $this->NbConsomRensTel;
    }

    public function setNbConsomRensTel(?int $NbConsomRensTel): self
    {
        $this->NbConsomRensTel = $NbConsomRensTel;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUD(): ?UD
    {
        return $this->UD;
    }

    public function setUD(?UD $UD): self
    {
        $this->UD = $UD;

        return $this;
    }


    /**
     * @return Collection<int, Atelier>
     */
    public function getAtelier(): Collection
    {
        return $this->atelier;
    }

    public function addAtelier(Atelier $atelier): self
    {
        if (!$this->atelier->contains($atelier)) {
            $this->atelier[] = $atelier;
            $atelier->setLieu($this);
        }

        return $this;
    }

    public function removeAtelier(Atelier $atelier): self
    {
        if ($this->atelier->removeElement($atelier)) {
            // set the owning side to null (unless already changed)
            if ($atelier->getLieu() === $this) {
                $atelier->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Communication>
     */
    public function getCommunication(): Collection
    {
        return $this->communication;
    }

    public function addCommunication(Communication $communication): self
    {
        if (!$this->communication->contains($communication)) {
            $this->communication[] = $communication;
            $communication->setLieu($this);
        }

        return $this;
    }

    public function removeCommunication(Communication $communication): self
    {
        if ($this->communication->removeElement($communication)) {
            // set the owning side to null (unless already changed)
            if ($communication->getLieu() === $this) {
                $communication->setLieu(null);
            }
        }
        return $this;
    }


    public function getDossiers(): ?Dossiers
    {
        return $this->dossiers;
    }

    public function setDossiers(?Dossiers $dossiers): self
    {
        $this->dossiers = $dossiers;

        return $this;
    }

    public function getPermanence(): ?Permanence
    {
        return $this->permanence;
    }

    public function setPermanence(?Permanence $permanence): self
    {
        $this->permanence = $permanence;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): self
    {
        $this->evenement = $evenement;

        return $this;
    }

    public function getFormations(): ?Formations
    {
        return $this->formations;
    }

    public function setFormations(?Formations $formations): self
    {
        $this->formations = $formations;

        return $this;
    }

    public function getRepresentations(): ?Representation
    {
        return $this->representations;
    }

    public function setRepresentations(?Representation $representations): self
    {
        $this->representations = $representations;

        return $this;
    }

    public function getActionJustice(): ?ActionJustice
    {
        return $this->actionJustice;
    }

    public function setActionJustice(?ActionJustice $actionJustice): self
    {
        $this->actionJustice = $actionJustice;

        return $this;
    }
}
