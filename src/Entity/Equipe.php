<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'équipe est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom de l'équipe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column]
    private ?bool $estActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $creeLe = null;

    #[ORM\ManyToOne(inversedBy: 'equipes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'équipe doit avoir un chef.")]
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
    
    #[ORM\OneToOne(targetEntity: Mission::class, mappedBy: 'equipeAssignee', cascade: ['persist', 'remove'])]
    private ?Mission $missionActuelle = null;

    #[ORM\OneToMany(mappedBy: 'equipeAssignee', targetEntity: Mission::class)]
    private Collection $missionsHistorique;



    public function __construct()
    {
        $this->missionsHistorique = new ArrayCollection();
        $this->membres = new ArrayCollection();
        $this->creeLe = new \DateTimeImmutable(); // Définit la date de création par défaut
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
    
    public function setChef(?SuperHero $chef): static
    {
    $this->chef = $chef;
    return $this;
    }

    public function getChef(): ?SuperHero
    {
        return $this->chef;
    }
    
    #[Assert\Callback]
    public function validateChefEnergy(ExecutionContextInterface $context): void
    {
        if ($this->chef && $this->chef->getNiveauEnergie() <= 80) {
            $context->buildViolation('Le chef doit avoir un niveau d\'énergie supérieur à 80.')
                ->atPath('chef')
                ->addViolation();
        }
    }
    
    #[Assert\Callback]
    public function validateChefNotInMembers(ExecutionContextInterface $context): void
    {
        if ($this->chef && $this->membres->contains($this->chef)) {
            $context->buildViolation('Le chef ne peut pas être un membre de l\'équipe.')
                ->atPath('membres')
                ->addViolation();
        }
    }

    #[Assert\Callback]
    public function validateMembersNotInOtherTeams(ExecutionContextInterface $context): void
    {
        foreach ($this->membres as $membre) {
            foreach ($membre->getEquipes() as $equipe) {
                if ($equipe !== $this) {
                    $context->buildViolation(sprintf(
                        'Le héros %s appartient déjà à une autre équipe.',
                        $membre->getNom()
                    ))
                    ->atPath('membres')
                    ->addViolation();
                }
            }}}

    /**
     * @return Collection<int, SuperHero>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(SuperHero $membre): static
    {   
    // Vérifier si le membre est déjà chef
    if ($this->chef && $this->chef === $membre) {
        throw new \InvalidArgumentException("Le chef ne peut pas être un membre de l'équipe.");
    }

    if (!$this->membres->contains($membre)) {
        // Vérifier si le membre est déjà dans une autre équipe
        foreach ($membre->getEquipes() as $equipe) {
            if ($equipe !== $this) {
                throw new \InvalidArgumentException(sprintf("Le héros %s appartient déjà à une autre équipe.", $membre->getNom()));
            }
        }

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
    public function getMissionsHistorique(): Collection
    {
        return $this->missionsHistorique;
    }

    public function addMissionHistorique(Mission $mission): self
    {
        if (!$this->missionsHistorique->contains($mission)) {
            $this->missionsHistorique->add($mission);
            $mission->setEquipeAssignee($this); // Lier la mission à l'équipe
        }
        return $this;
    }

    public function removeMissionHistorique(Mission $mission): self
    {
        if ($this->missionsHistorique->removeElement($mission)) {
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

    public function setMissionActuelle(?Mission $mission): self
    {
        $this->missionActuelle = $mission;

        return $this;
    }
}
