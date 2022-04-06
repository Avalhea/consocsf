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

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Evenement::class)]
    private $evenement;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Dossier::class)]
    private $dossier;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'lieux')]
    private $user;

    #[ORM\ManyToOne(targetEntity: Statut::class, inversedBy: 'lieux')]
    private $statut;

    #[ORM\ManyToOne(targetEntity: UD::class, inversedBy: 'lieux')]
    private $UD;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Permanence::class)]
    private $permanence;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Representation::class)]
    private $representation;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Atelier::class)]
    private $atelier;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Communication::class)]
    private $communication;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Formation::class)]
    private $formation;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: ActionJustice::class)]
    private $actionJustice;

    public function __construct()
    {
        $this->evenement = new ArrayCollection();
        $this->dossier = new ArrayCollection();
        $this->permanence = new ArrayCollection();
        $this->representation = new ArrayCollection();
        $this->atelier = new ArrayCollection();
        $this->communication = new ArrayCollection();
        $this->formation = new ArrayCollection();
        $this->actionJustice = new ArrayCollection();
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

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenement(): Collection
    {
        return $this->evenement;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenement->contains($evenement)) {
            $this->evenement[] = $evenement;
            $evenement->setLieu($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenement->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getLieu() === $this) {
                $evenement->setLieu(null);
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
            $dossier->setUser($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): self
    {
        if ($this->dossier->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getUser() === $this) {
                $dossier->setUser(null);
            }
        }

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
     * @return Collection<int, Permanence>
     */
    public function getPermanence(): Collection
    {
        return $this->permanence;
    }

    public function addPermanence(Permanence $permanence): self
    {
        if (!$this->permanence->contains($permanence)) {
            $this->permanence[] = $permanence;
            $permanence->setLieu($this);
        }

        return $this;
    }

    public function removePermanence(Permanence $permanence): self
    {
        if ($this->permanence->removeElement($permanence)) {
            // set the owning side to null (unless already changed)
            if ($permanence->getLieu() === $this) {
                $permanence->setLieu(null);
            }
        }

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

    /**
     * @return Collection<int, Formation>
     */
    public function getFormation(): Collection
    {
        return $this->formation;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formation->contains($formation)) {
            $this->formation[] = $formation;
            $formation->setLieu($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formation->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getLieu() === $this) {
                $formation->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActionJustice>
     */
    public function getActionJustice(): Collection
    {
        return $this->actionJustice;
    }

    public function addActionJustice(ActionJustice $actionJustice): self
    {
        if (!$this->actionJustice->contains($actionJustice)) {
            $this->actionJustice[] = $actionJustice;
            $actionJustice->setLieu($this);
        }

        return $this;
    }

    public function removeActionJustice(ActionJustice $actionJustice): self
    {
        if ($this->actionJustice->removeElement($actionJustice)) {
            // set the owning side to null (unless already changed)
            if ($actionJustice->getLieu() === $this) {
                $actionJustice->setLieu(null);
            }
        }

        return $this;
    }
}
