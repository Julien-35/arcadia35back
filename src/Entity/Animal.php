<?php

namespace App\Entity;

use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
class Animal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $etat = null;

    #[ORM\Column(length: 50)]
    private ?string $grammage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $feeding_time = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, length: 500000)]
    private ?string $image_data = null;

    #[ORM\ManyToOne(inversedBy: 'animal')]
    private ?Habitat $habitat = null;

    #[ORM\ManyToOne(inversedBy: 'animal')]
    private ?Race $race = null;


    #[ORM\OneToMany(targetEntity: RapportVeterinaire::class, mappedBy: 'animal')]
    private Collection $rapport_veterinaire;

    #[ORM\Column(length: 250)]
    private ?string $nourriture = null;

    public function __construct()
    {
        $this->rapport_veterinaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    
    public function getNourriture(): ?string
    {
        return $this->nourriture;
    }

    public function setNourriture(string $nourriture): static
    {
        $this->nourriture = $nourriture;

        return $this;
    }
    

    public function getGrammage(): ?string
    {
        return $this->grammage;
    }

    public function setGrammage(string $grammage): static
    {
        $this->grammage = $grammage;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getFeedingTime(): ?\DateTimeInterface
    {
        return $this->feeding_time;
    }

    public function setFeedingTime(\DateTimeInterface $feeding_time): static
    {
        $this->feeding_time = $feeding_time;

        return $this;
    }

    public function getImageData(): ?string
    {
        return $this->image_data;
    }

    public function setImageData(string $image_data): static
    {
        $this->image_data = $image_data;

        return $this;
    }

    public function getHabitat(): ?Habitat
    {
        return $this->habitat;
    }

    public function setHabitat(?Habitat $habitat): static
    {
        $this->habitat = $habitat;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }

    /**
     * @return Collection<int, RapportVeterinaire>
     */
    public function getRapportVeterinaire(): Collection
    {
        return $this->rapport_veterinaire;
    }

    public function addRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    {
        if (!$this->rapport_veterinaire->contains($rapportVeterinaire)) {
            $this->rapport_veterinaire->add($rapportVeterinaire);
            $rapportVeterinaire->setAnimal($this);
        }

        return $this;
    }

    public function removeRapportVeterinaire(RapportVeterinaire $rapportVeterinaire): static
    {
        if ($this->rapport_veterinaire->removeElement($rapportVeterinaire)) {
            if ($rapportVeterinaire->getAnimal() === $this) {
                $rapportVeterinaire->setAnimal(null);
            }
        }

        return $this;
    }

}
