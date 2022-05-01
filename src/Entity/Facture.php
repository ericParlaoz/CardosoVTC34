<?php

namespace App\Entity;

use App\Repository\ClientsRepository;
use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $nom;

    #[ORM\Column(type: 'string', length: 100)]
    private $prenom;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    private $entreprise;

    #[ORM\Column(type: 'string', length: 200)]
    private $rue;

    #[ORM\Column(type: 'string',length: 20)]
    private $codepostal;

    #[ORM\Column(type: 'string', length: 100)]
    private $ville;

    #[ORM\Column(type: 'string',length: 20)]
    private $telephone;

    #[ORM\Column(type: 'string', length: 100)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $adresse_depart;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $adresse_arrivee;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $date;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $unique_id;

    #[ORM\Column(type: 'string', length: 100)]
    private $date_reservation;

    #[ORM\Column(type: 'integer')]
    private $prix;

    #[ORM\Column(type: 'string', length: 10)]
    private $date_compta;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(?string $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getCodepostal(): ?string
    {
        return $this->codepostal;
    }

    public function setCodepostal(string $codepostal): self
    {
        $this->codepostal = $codepostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getAdresseDepart(): ?string
    {
        return $this->adresse_depart;
    }

    public function setAdresseDepart(?string $adresse_depart): self
    {
        $this->adresse_depart = $adresse_depart;

        return $this;
    }

    public function getAdresseArrivee(): ?string
    {
        return $this->adresse_arrivee;
    }

    public function setAdresseArrivee(?string $adresse_arrivee): self
    {
        $this->adresse_arrivee = $adresse_arrivee;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->unique_id;
    }

    public function setUniqueId(string $unique_id): self
    {
        $this->unique_id = $unique_id;

        return $this;
    }

    public function getDateReservation(): ?string
    {
        return $this->date_reservation;
    }

    public function setDateReservation(string $date_reservation): self
    {
        $this->date_reservation = $date_reservation;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCompta(): ?string
    {
        return $this->date_compta;
    }

    public function setDateCompta(string $date_compta): self
    {
        $this->date_compta = $date_compta;

        return $this;
    }
}
