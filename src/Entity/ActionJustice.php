<?php

namespace App\Entity;

use App\Repository\ActionJusticeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActionJusticeRepository::class)]
class ActionJustice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @Assert\PositiveOrZero
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbActionConjointe;

    /**
     * @Assert\PositiveOrZero
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $nbAccompagnement;



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
