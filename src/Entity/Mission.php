<?php

namespace App\Entity;
use App\Entity\MissionStatus;

use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert; 
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
class Mission
{
    public function __construct()
{
    $this->statut = MissionStatus::PENDING; // Initialisation par défaut du STATUT
    $this->pouvoirsRequis = new ArrayCollection();
}

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(enumType: MissionStatus::class)]
    private MissionStatus $statut;

    #[ORM\ManyToOne(targetEntity: Equipe::class, inversedBy: 'missionsPassees')]
    private ?Equipe $equipeHistorique = null;

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[Assert\GreaterThan(propertyPath: 'dateDebut', message: "La date de fin doit être supérieure à la date de début.")]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;


    #[ORM\Column(length: 255)]
    private ?string $lieu = null;

    #[Assert\NotBlank(message: "Vous devez sélectionner au moins un pouvoir requis.")]
    #[ORM\ManyToMany(targetEntity: Pouvoir::class)]
    private Collection $pouvoirsRequis;



    #[ORM\Column]
    #[Assert\NotNull(message: "Le niveau de danger est requis.")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Le niveau de danger doit être compris entre {{ min }} et {{ max }}."
    )]
    private ?int $niveauDanger = null;
    


    // #[ORM\Column]
    // private ?int $niveauDanger = null;
    #[Assert\NotBlank(message: "Une équipe assignée est obligatoire.")]
    #[ORM\OneToOne(targetEntity: Equipe::class, inversedBy: 'missionActuelle', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipeAssignee = null;


    // #[ORM\OneToOne(mappedBy: 'missionActuelle', cascade: ['persist', 'remove'])]
    // private ?Equipe $equipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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


    public function getStatut(): MissionStatus
{
    return $this->statut;
}

public function setStatut(MissionStatus $statut): self
{
    $this->statut = $statut;

    return $this;
}

    /**
     * @return Collection<int, Pouvoir>
     */
    public function getPouvoirsRequis(): Collection
    {
        return $this->pouvoirsRequis;
    }

    public function addPouvoirRequis(Pouvoir $pouvoir): self
    {
        if (!$this->pouvoirsRequis->contains($pouvoir)) {
            $this->pouvoirsRequis->add($pouvoir);
        }

        return $this;
    }

    public function removePouvoirRequis(Pouvoir $pouvoir): self
    {
        $this->pouvoirsRequis->removeElement($pouvoir);

        return $this;
    }

    // public function getStatut(): ?string
    // {
    //     return $this->statut;
    // }

    // public function setStatut(?string $statut): static
    // {
    //     $this->statut = $statut;

    //     return $this;
    // }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getNiveauDanger(): ?int
    {
        return $this->niveauDanger;
    }

    public function setNiveauDanger(int $niveauDanger): static
    {
        $this->niveauDanger = $niveauDanger;

        return $this;
    }

    public function getEquipeHistorique(): ?Equipe
    {
        return $this->equipeHistorique;
    }

    public function setEquipeHistorique(?Equipe $equipe): self
    {
        $this->equipeHistorique = $equipe;

        return $this;
    }
    
    public function getEquipeAssignee(): ?Equipe
    {
        return $this->equipeAssignee;
    }

    public function setEquipeAssignee(?Equipe $equipe): self
    {
        $this->equipeAssignee = $equipe;

        return $this;
    }
}
