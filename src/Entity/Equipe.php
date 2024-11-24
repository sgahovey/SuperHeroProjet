<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?bool $estActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creeLe = null;

    #[ORM\ManyToOne(inversedBy: 'equipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SuperHero $chef = null;


    // Pas de doublons dans les membres
    #[Assert\Count(
     min: 2,
        max: 5,
    minMessage: 'Une équipe doit avoir au moins 2 membres.',
    maxMessage: 'Une équipe ne peut pas avoir plus de 5 membres.'
    )]
    #[ORM\ManyToMany(targetEntity: SuperHero::class)]
private Collection $membres;
    // /**
    //  * @var Collection<int, SuperHero>
    //  */
    // #[ORM\ManyToMany(targetEntity: SuperHero::class)]
    // private Collection $membres;

    /**
     * @var Collection<int, Mission>
     */
    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'equipeAssignee')]
    private Collection $missions;

    #[ORM\OneToOne(inversedBy: 'equipe', cascade: ['persist', 'remove'])]
    private ?Mission $missionActuelle = null;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->missions = new ArrayCollection();
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

    public function isEstActive(): ?bool
    {
        return $this->estActive;
    }

    public function setEstActive(bool $estActive): static
    {
        $this->estActive = $estActive;

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

    public function getChef(): ?SuperHero
    {
        return $this->chef;
    }
    
    
    public function setChef(?SuperHero $chef): static
    {
        $this->chef = $chef;

        return $this;
    }

    /**
     * @return Collection<int, SuperHero>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(SuperHero $membre): static
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
        }

        return $this;
    }

    public function removeMembre(SuperHero $membre): static
    {
        $this->membres->removeElement($membre);

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): static
    {
        if (!$this->missions->contains($mission)) {
            $this->missions->add($mission);
            $mission->setEquipeAssignee($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): static
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getEquipeAssignee() === $this) {
                $mission->setEquipeAssignee(null);
            }
        }

        return $this;
    }

    public function getMissionActuelle(): ?Mission
    {
        return $this->missionActuelle;
    }

    public function setMissionActuelle(?Mission $missionActuelle): static
    {
        $this->missionActuelle = $missionActuelle;

        return $this;
    }
}
