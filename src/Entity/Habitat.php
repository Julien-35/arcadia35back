<?php

namespace App\Entity;

use App\Repository\HabitatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HabitatRepository::class)]
class Habitat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 500)]
    private ?string $description = null;

    #[ORM\Column(length: 500)]
    private ?string $commentaire_habitat = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, length: 500000)]
    private ?string $image_data = null;

    #[ORM\OneToMany(targetEntity: Animal::class, mappedBy: 'habitat')]
    private Collection $habitat;

    public function __construct()
    {
        $this->habitat = new ArrayCollection();
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

    public function getCommentaireHabitat(): ?string
    {
        return $this->commentaire_habitat;
    }

    public function setCommentaireHabitat(string $commentaire_habitat): static
    {
        $this->commentaire_habitat = $commentaire_habitat;

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

    /**
     * @return Collection<int, Animal>
     */
    public function getHabitat(): Collection
    {
        return $this->habitat;
    }

    public function addHabitat(Animal $habitat): static
    {
        if (!$this->habitat->contains($habitat)) {
            $this->habitat->add($habitat);
            $habitat->setHabitat($this);
        }

        return $this;
    }

    public function removeHabitat(Animal $habitat): static
    {
        if ($this->habitat->removeElement($habitat)) {
            // set the owning side to null (unless already changed)
            if ($habitat->getHabitat() === $this) {
                $habitat->setHabitat(null);
            }
        }

        return $this;
    }
}
