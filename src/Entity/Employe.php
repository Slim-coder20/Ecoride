<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'employe', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?self $User = null;

    #[ORM\OneToOne(targetEntity: self::class, mappedBy: 'User', cascade: ['persist', 'remove'])]
    private ?self $employe = null;

    #[ORM\Column(length: 255)]
    private ?string $poste = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?self
    {
        return $this->User;
    }

    public function setUser(self $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getEmploye(): ?self
    {
        return $this->employe;
    }

    public function setEmploye(self $employe): static
    {
        // set the owning side of the relation if necessary
        if ($employe->getUser() !== $this) {
            $employe->setUser($this);
        }

        $this->employe = $employe;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): static
    {
        $this->poste = $poste;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
}
