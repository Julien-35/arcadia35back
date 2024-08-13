<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
class Horaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(length: 250)]
    private ?string $message = null;

    #[ORM\Column(length: 5)]
    private ?string $heure_debut = null;

    #[ORM\Column(length: 5)]
    private ?string $heure_fin = null;

    #[ORM\Column(length: 50)]
    private ?string $jour = null;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getHeureDebut(): ?string
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(string $heure_debut): static
    {
        $this->heure_debut = $heure_debut;

        return $this;
    }

    public function getHeureFin(): ?string
    {
        return $this->heure_fin;
    }

    public function setHeureFin(string $heure_fin): static
    {
        $this->heure_fin = $heure_fin;

        return $this;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): static
    {
        $this->jour = $jour;

        return $this;
    }
}
