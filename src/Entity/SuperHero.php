<?php

namespace App\Entity;

use App\Repository\SuperHeroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuperHeroRepository::class)]
class SuperHero
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $alterEgo = null;

    #[ORM\Column]
    private ?bool $estDisponible = null;

    #[ORM\Column]
    private ?int $niveauEnergie = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $biographie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomImage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creeLe = null;

    /**
     * @var Collection<int, Equipe>
     */
    #[ORM\OneToMany(targetEntity: Equipe::class, mappedBy: 'chef')]
    private Collection $equipes;

    /**
     * @var Collection<int, Pouvoir>
     */
    #[ORM\ManyToMany(targetEntity: Pouvoir::class, inversedBy: 'superHeroes')]
    private Collection $pouvoirs;

    public function __construct()
    {
        $this->equipes = new ArrayCollection();
        $this->pouvoirs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAlterEgo(): ?string
    {
        return $this->alterEgo;
    }

    public function setAlterEgo(?string $alterEgo): static
    {
        $this->alterEgo = $alterEgo;

        return $this;
    }

    public function isEstDisponible(): ?bool
    {
        return $this->estDisponible;
    }

    public function setEstDisponible(bool $estDisponible): static
    {
        $this->estDisponible = $estDisponible;

        return $this;
    }

    public function getNiveauEnergie(): ?int
    {
        return $this->niveauEnergie;
    }

    public function setNiveauEnergie(int $niveauEnergie): static
    {
        $this->niveauEnergie = $niveauEnergie;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(string $biographie): static
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getNomImage(): ?string
    {
        return $this->nomImage;
    }

    public function setNomImage(?string $nomImage): static
    {
        $this->nomImage = $nomImage;

        return $this;
    }

    public function getCreeLe(): ?\DateTimeImmutable
    {
        return $this->creeLe;
    }

    public function setCreeLe(\DateTimeImmutable $creeLe): static
    {
        $this->creeLe = $creeLe;

        return $this;
    }

    /**
     * @return Collection<int, Equipe>
     */
    public function getEquipes(): Collection
    {
        return $this->equipes;
    }

    public function addEquipe(Equipe $equipe): static
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes->add($equipe);
            $equipe->setChef($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): static
    {
        if ($this->equipes->removeElement($equipe)) {
            // set the owning side to null (unless already changed)
            if ($equipe->getChef() === $this) {
                $equipe->setChef(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pouvoir>
     */
    public function getPouvoirs(): Collection
    {
        return $this->pouvoirs;
    }

    public function addPouvoir(Pouvoir $pouvoir): static
    {
        if (!$this->pouvoirs->contains($pouvoir)) {
            $this->pouvoirs->add($pouvoir);
        }

        return $this;
    }

    public function removePouvoir(Pouvoir $pouvoir): static
    {
        $this->pouvoirs->removeElement($pouvoir);

        return $this;
    }
}
