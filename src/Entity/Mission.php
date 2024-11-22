<?php

namespace App\Entity;
use App\Entity\MissionStatus;

use Symfony\Component\Validator\Constraints as Assert; 

use App\Repository\MissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
class Mission
{
    public function __construct()
{
    $this->statut = MissionStatus::PENDING; // Initialisation par défaut du STATUT
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

    // #[ORM\Column(length: 255, nullable: true)]
    // private ?string $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255)]
    private ?string $lieu = null;


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

    #[ORM\ManyToOne(inversedBy: 'missions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipeAssignee = null;

    #[ORM\OneToOne(mappedBy: 'missionActuelle', cascade: ['persist', 'remove'])]
    private ?Equipe $equipe = null;

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

    public function getEquipeAssignee(): ?Equipe
    {
        return $this->equipeAssignee;
    }

    public function setEquipeAssignee(?Equipe $equipeAssignee): static
    {
        $this->equipeAssignee = $equipeAssignee;

        return $this;
    }

    public function getEquipe(): ?Equipe
    {
        return $this->equipe;
    }

    public function setEquipe(?Equipe $equipe): static
    {
        // unset the owning side of the relation if necessary
        if ($equipe === null && $this->equipe !== null) {
            $this->equipe->setMissionActuelle(null);
        }

        // set the owning side of the relation if necessary
        if ($equipe !== null && $equipe->getMissionActuelle() !== $this) {
            $equipe->setMissionActuelle($this);
        }

        $this->equipe = $equipe;

        return $this;
    }
}
