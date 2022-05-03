<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nom;

    /**
     * @Assert\PositiveOrZero
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbSalaries;

    /**
     * @Assert\PositiveOrZero
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbBenevole;

    #[ORM\Column(type: 'string', length: 254, nullable: true)]
    private $adresse;

    #[ORM\Column(type: 'text', nullable: true)]
    private $joursEtHorairesOuverture;

    /**
     * @Assert\PositiveOrZero
     */
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


    #[ORM\OneToOne(targetEntity: Permanence::class, cascade: ['persist', 'remove'])]
    private $permanence;

    #[ORM\OneToOne(targetEntity: Evenement::class, cascade: ['persist', 'remove'])]
    private $evenement;

    #[ORM\OneToOne(targetEntity: Formations::class, cascade: ['persist', 'remove'])]
    private $formations;

    #[ORM\OneToOne(targetEntity: ActionJustice::class, cascade: ['persist', 'remove'])]
    private $actionJustice;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Representation::class)]
    private $representation;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Dossier::class)]
    private $dossier;

    #[ORM\ManyToOne(targetEntity: Echelle::class, inversedBy: 'lieux')]
    private $echelle;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbAteliers;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbPartiAteliers;

    public function __construct()
    {
        $this->atelier = new ArrayCollection();
        $this->communication = new ArrayCollection();
        $this->formation = new ArrayCollection();
        $this->representation = new ArrayCollection();
        $this->dossier = new ArrayCollection();
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

    public function getActionJustice(): ?ActionJustice
    {
        return $this->actionJustice;
    }

    public function setActionJustice(?ActionJustice $actionJustice): self
    {
        $this->actionJustice = $actionJustice;

        return $this;
    }

    /**
     * @return Collection<int, Representation>
     */
    public function getRepresentation(): Collection
    {
        return $this->representation;
    }

    public function addRepresentation(Representation $representation): self
    {
        if (!$this->representation->contains($representation)) {
            $this->representation[] = $representation;
            $representation->setLieu($this);
        }

        return $this;
    }

    public function removeRepresentation(Representation $representation): self
    {
        if ($this->representation->removeElement($representation)) {
            // set the owning side to null (unless already changed)
            if ($representation->getLieu() === $this) {
                $representation->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dossier>
     */
    public function getDossier(): Collection
    {
        return $this->dossier;
    }

    public function addDossier(Dossier $dossier): self
    {
        if (!$this->dossier->contains($dossier)) {
            $this->dossier[] = $dossier;
            $dossier->setLieu($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): self
    {
        if ($this->dossier->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getLieu() === $this) {
                $dossier->setLieu(null);
            }
        }

        return $this;
    }

    public function getEchelle(): ?Echelle
    {
        return $this->echelle;
    }

    public function setEchelle(?Echelle $echelle): self
    {
        $this->echelle = $echelle;

        return $this;
    }

    public function getNbAteliers(): ?int
    {
        return $this->nbAteliers;
    }

    public function setNbAteliers(?int $nbAteliers): self
    {
        $this->nbAteliers = $nbAteliers;

        return $this;
    }

    public function getNbPartiAteliers(): ?int
    {
        return $this->nbPartiAteliers;
    }

    public function setNbPartiAteliers(?int $nbPartiAteliers): self
    {
        $this->nbPartiAteliers = $nbPartiAteliers;

        return $this;
    }

}
