<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'client')]
    private Collection $devis;

    /**
     * @var Collection<int, DocumentsUtilisateur>
     */
    #[ORM\OneToMany(targetEntity: DocumentsUtilisateur::class, mappedBy: 'user', cascade: ["persist"])]
    private Collection $documents;

    /**
     * @var Collection<int, Dossier>
     */
    #[ORM\OneToMany(targetEntity: Dossier::class, mappedBy: 'user', cascade: ["persist"])]
    private Collection $dossiers;

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'user')]
    private Collection $events;

    /**
     * @var Collection<int, Facture>
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'client')]
    private Collection $factures;

    /**
     * @var Collection<int, Repertoire>
     */
    #[ORM\OneToMany(targetEntity: Repertoire::class, mappedBy: 'user')]
    private Collection $repertoires;

    /**
     * @var Collection<int, Services>
     */
    #[ORM\ManyToMany(targetEntity: Services::class, mappedBy: 'users')]
    private Collection $services;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'user')]
    private Collection $contacts;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastActivity = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomEntreprise = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwordResetToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $passwordResetExpiresAt = null;


    /**
     * @var Collection<int, Identifiants>
     */
    #[ORM\OneToMany(targetEntity: Identifiants::class, mappedBy: 'users', cascade: ["persist"])]
    private Collection $identifiants;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->dossiers = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->repertoires = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->identifiants = new ArrayCollection();
        
    }

    //Redis
    public function initializeRelations(): void
    {
        $relations = [
            $this->factures,
            $this->devis,
            $this->services,
            $this->dossiers,
            $this->repertoires,
            $this->documents,
            $this->events,
            $this->contacts,
            $this->identifiants,
        ];
    
        foreach ($relations as $relation) {
            if ($relation instanceof PersistentCollection && !$relation->isInitialized()) {
                $relation->initialize();
            }
        }
    
        // Gestion des relations imbriquées spécifiques
        foreach ($this->services as $service) {
            // Initialiser les dossiers liés aux services
            if ($service->getDossiers() instanceof PersistentCollection && !$service->getDossiers()->isInitialized()) {
                $service->getDossiers()->initialize();
            }
    
            // Initialiser les events liés aux services
            if ($service->getEvents() instanceof PersistentCollection && !$service->getEvents()->isInitialized()) {
                $service->getEvents()->initialize();
            }
    
            // Gérer les sous-relations des dossiers
            foreach ($service->getDossiers() as $dossier) {
                // Initialiser les documents liés aux dossiers
                if ($dossier->getDocuments() instanceof PersistentCollection && !$dossier->getDocuments()->isInitialized()) {
                    $dossier->getDocuments()->initialize();
                }
    
                // Initialiser les répertoires liés aux dossiers
                if ($dossier->getRepertoires() instanceof PersistentCollection && !$dossier->getRepertoires()->isInitialized()) {
                    $dossier->getRepertoires()->initialize();
                }
            }
        }

        // Gestion des relations imbriquées spécifiques
        foreach ($this->factures as $facture) {
            // Initialiser les lignes de factures
            if ($facture->getFactureLignes() instanceof PersistentCollection && !$facture->getFactureLignes()->isInitialized()) {
                $facture->getFactureLignes()->initialize();
            }
        }

        foreach ($this->devis as $devis) {
            // Initialiser les lignes de devis
            if ($devis->getDevisLignes() instanceof PersistentCollection && !$devis->getDevisLignes()->isInitialized()) {
                $devis->getDevisLignes()->initialize();
            }

            // Initialiser les versions de devis
            // if ($devis->getDevisVersions() instanceof PersistentCollection && !$devis->getDevisVersions()->isInitialized()) {
            //     $devis->getDevisVersions()->initialize();
            // }
        }

    }

    //Redis 
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'roles' => $this->roles,
            'nom' => $this->nom,
            'adresse' => $this->adresse,
            'codePostal' => $this->codePostal,
            'ville' => $this->ville,
            'pays' => $this->pays,
            'telephone' => $this->telephone,
            'mobile' => $this->mobile,
            'siret' => $this->siret,
            'nomEntreprise' => $this->nomEntreprise,
            'factures' => $this->factures, 
            "devis" => $this->devis ,
            "services" => $this->services ,
            "dossiers" => $this->dossiers ,
            "repertoires" => $this->repertoires ,
            "documents" => $this->documents ,
            "events" => $this->events ,
            "contacts" => $this->contacts ,
            "identifiants" => $this->identifiants,
        ];
    }

    // Redis
    public static function fromArray(array $data): self
    {
        $user = new self();
        $user->id = $data['id'] ?? null;
        $user->email = $data['email'] ?? '';
        $user->password = $data['password'] ?? '';
        $user->roles = $data['roles'] ?? [];
        $user->nom = $data['nom'] ?? '';
        $user->adresse = $data['adresse'] ?? '';
        $user->codePostal = $data['codePostal'] ?? '';
        $user->ville = $data['ville'] ?? '';
        $user->pays = $data['pays'] ?? '';
        $user->telephone = $data['telephone'] ?? '';
        $user->mobile = $data['mobile'] ?? '';
        $user->siret = $data['siret'] ?? '';
        $user->nomEntreprise = $data['nomEntreprise'] ?? '';
        $user->factures = $data['factures'] ?? []; 
        $user->devis = $data['devis'] ?? []; 
        $user->services = $data['services'] ?? []; 
        $user->dossiers = $data['dossiers'] ?? []; 
        $user->repertoires = $data['repertoires'] ?? []; 
        $user->documents = $data['documents'] ?? []; 
        $user->events = $data['events'] ?? []; 
        $user->contacts = $data['contacts'] ?? []; 
        $user->identifiants = $data['identifiants'] ?? []; 

        return $user;
    }

    public function __toString(): string {
        return $this->id.' '.$this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setClient($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getClient() === $this) {
                $devi->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DocumentsUtilisateur>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(DocumentsUtilisateur $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setUser($this);
        }

        return $this;
    }

    public function removeDocument(DocumentsUtilisateur $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getUser() === $this) {
                $document->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dossier>
     */
    public function getDossiers(): Collection
    {
        return $this->dossiers;
    }

    public function addDossier(Dossier $dossier): static
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers->add($dossier);
            $dossier->setUser($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): static
    {
        if ($this->dossiers->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getUser() === $this) {
                $dossier->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Events $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setClient($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getClient() === $this) {
                $facture->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Repertoire>
     */
    public function getRepertoires(): Collection
    {
        return $this->repertoires;
    }

    public function addRepertoire(Repertoire $repertoire): static
    {
        if (!$this->repertoires->contains($repertoire)) {
            $this->repertoires->add($repertoire);
            $repertoire->setUser($this);
        }

        return $this;
    }

    public function removeRepertoire(Repertoire $repertoire): static
    {
        if ($this->repertoires->removeElement($repertoire)) {
            // set the owning side to null (unless already changed)
            if ($repertoire->getUser() === $this) {
                $repertoire->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Services>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Services $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->addUser($this);
        }

        return $this;
    }

    public function removeService(Services $service): static
    {
        if ($this->services->removeElement($service)) {
            $service->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }

    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->lastActivity;
    }

    public function setLastActivity(?\DateTimeInterface $lastActivity): static
    {
        $this->lastActivity = $lastActivity;

        return $this;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(?string $nomEntreprise): static
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    public function setPasswordResetToken(?string $token): self
    {
        $this->passwordResetToken = $token;
        return $this;
    }

    public function getPasswordResetExpiresAt(): ?\DateTimeInterface
    {
        return $this->passwordResetExpiresAt;
    }

    public function setPasswordResetExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->passwordResetExpiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return Collection<int, Identifiants>
     */
    public function getIdentifiants(): Collection
    {
        return $this->identifiants;
    }

    public function addIdentifiant(Identifiants $identifiant): static
    {
        if (!$this->identifiants->contains($identifiant)) {
            $this->identifiants->add($identifiant);
            $identifiant->setUsers($this);
        }

        return $this;
    }

    public function removeIdentifiant(Identifiants $identifiant): static
    {
        if ($this->identifiants->removeElement($identifiant)) {
            // set the owning side to null (unless already changed)
            if ($identifiant->getUsers() === $this) {
                $identifiant->setUsers(null);
            }
        }

        return $this;
    }

   
}
