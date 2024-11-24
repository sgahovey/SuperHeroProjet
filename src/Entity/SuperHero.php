<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SuperHeroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SuperHeroRepository::class)]
#[Vich\Uploadable] // Annotation pour VichUploader
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
    #[Assert\NotNull(message: 'Le niveau d\'énergie est obligatoire.')]
    #[Assert\Range(
                    min: 0,
                    max: 100,
    notInRangeMessage: 'Le niveau d\'énergie doit être compris entre {{ min }} et {{ max }}.',
    )]
private ?int $niveauEnergie = null;


    // #[ORM\Column]
    // private ?int $niveauEnergie = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $biographie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomImage = null; // Nom du fichier dans la base de données

    #[Vich\UploadableField(mapping: 'super_hero_image', fileNameProperty: 'nomImage')]
    private ?File $imageFile = null; // Fichier uploadé

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateImageModif = null; // Date de dernière modification de l'image

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

    // Getters et setters

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

    public function getAlterEgo(): ?string
    {
        return $this->alterEgo;
    }

    public function setAlterEgo(?string $alterEgo): self
    {
        $this->alterEgo = $alterEgo;

        return $this;
    }

    public function isEstDisponible(): ?bool
    {
        return $this->estDisponible;
    }

    public function setEstDisponible(bool $estDisponible): self
    {
        $this->estDisponible = $estDisponible;

        return $this;
    }

    public function getNiveauEnergie(): ?int
    {
        return $this->niveauEnergie;
    }

    public function setNiveauEnergie(int $niveauEnergie): self
    {
        $this->niveauEnergie = $niveauEnergie;

        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;

        return $this;
    }

    public function getNomImage(): ?string
    {
        return $this->nomImage;
    }

    public function setNomImage(?string $nomImage): self
    {
        $this->nomImage = $nomImage;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            // Met à jour la date de modification si une nouvelle image est uploadée
            $this->dateImageModif = new \DateTimeImmutable();
        }
    }

    public function getDateImageModif(): ?\DateTimeImmutable
    {
        return $this->dateImageModif;
    }

    public function setDateImageModif(?\DateTimeImmutable $dateImageModif): self
    {
        $this->dateImageModif = $dateImageModif;

        return $this;
    }

    public function getCreeLe(): ?\DateTimeImmutable
    {
        return $this->creeLe;
    }

    public function setCreeLe(\DateTimeImmutable $creeLe): self
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

    public function addEquipe(Equipe $equipe): self
    {
        if (!$this->equipes->contains($equipe)) {
            $this->equipes->add($equipe);
            $equipe->setChef($this);
        }

        return $this;
    }

    public function removeEquipe(Equipe $equipe): self
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

    public function addPouvoir(Pouvoir $pouvoir): self
    {
        if (!$this->pouvoirs->contains($pouvoir)) {
            $this->pouvoirs->add($pouvoir);
        }

        return $this;
    }

    public function removePouvoir(Pouvoir $pouvoir): self
    {
        $this->pouvoirs->removeElement($pouvoir);

        return $this;
    }
}
