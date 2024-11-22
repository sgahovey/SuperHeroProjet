<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\PouvoirRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PouvoirRepository::class)]
class Pouvoir
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du pouvoir est requis.")]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est requise.")]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le niveau est requis.")]
    #[Assert\Range(
                    min: 1,
                    max: 5,
    notInRangeMessage: "Le niveau doit être compris entre {{ min }} et {{ max }}.")]
    private ?int $niveau = null;

    // #[ORM\Column]
    // private ?int $niveau = null;

    /**
     * @var Collection<int, SuperHero>
     */
    #[ORM\ManyToMany(targetEntity: SuperHero::class, mappedBy: 'pouvoirs')]
    private Collection $superHeroes;

    public function __construct()
    {
        $this->superHeroes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getNiveau(): ?int
    {
        return $this->niveau;
    }

    public function setNiveau(int $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * @return Collection<int, SuperHero>
     */
    public function getSuperHeroes(): Collection
    {
        return $this->superHeroes;
    }

    public function addSuperHero(SuperHero $superHero): static
    {
        if (!$this->superHeroes->contains($superHero)) {
            $this->superHeroes->add($superHero);
            $superHero->addPouvoir($this);
        }

        return $this;
    }

    public function removeSuperHero(SuperHero $superHero): static
    {
        if ($this->superHeroes->removeElement($superHero)) {
            $superHero->removePouvoir($this);
        }

        return $this;
    }
}
