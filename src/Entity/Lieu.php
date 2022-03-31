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


    #[ORM\ManyToOne(targetEntity: Statut::class, inversedBy: 'lieus')]
    private $statut;

    #[ORM\ManyToOne(targetEntity: Echelle::class, inversedBy: 'lieus')]
    private $echelle;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Permanence::class, orphanRemoval: true)]
    private $permanence;

    #[ORM\ManyToOne(targetEntity: UD::class, inversedBy: 'lieus')]
    private $ud;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Representation::class, orphanRemoval: true)]
    private $representation;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Atelier::class, orphanRemoval: true)]
    private $atelier;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Communication::class, orphanRemoval: true)]
    private $communication;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Formation::class, orphanRemoval: true)]
    private $formation;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Evenement::class, orphanRemoval: true)]
    private $actionJustice;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Evenement::class, orphanRemoval: true)]
    private $evenement;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Dossier::class, orphanRemoval: true)]
    private $dossier;

    #[ORM\Column(type: 'string', length: 254, nullable: true)]
    private $adresse;

    #[ORM\Column(type: 'text', nullable: true)]
    private $joursEtHorairesOuverture;

    public function __construct()
    {
        $this->permanence = new ArrayCollection();
        $this->ud = new ArrayCollection();
        $this->representation = new ArrayCollection();
        $this->atelier = new ArrayCollection();
        $this->communication = new ArrayCollection();
        $this->formation = new ArrayCollection();
        $this->evenement = new ArrayCollection();
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


    public function getStatut(): ?Statut
    {
        return $this->statut;
    }

    public function setStatut(?Statut $statut): self
    {
        $this->statut = $statut;

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
     * @return Collection<int, UD>
     */
    public function getUd(): Collection
    {
        return $this->ud;
    }

    public function addUd(UD $ud): self
    {
        if (!$this->ud->contains($ud)) {
            $this->ud[] = $ud;
        }

        return $this;
    }

    public function removeUd(UD $ud): self
    {
        $this->ud->removeElement($ud);

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

    public function getActionJustice(): ?ActionJustice
    {
        return $this->ActionJustice;
    }

    public function setActionJustice(?ActionJustice $ActionJustice): self
    {
        $this->ActionJustice = $ActionJustice;

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
}
