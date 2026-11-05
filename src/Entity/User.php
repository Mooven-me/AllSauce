<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['uuid'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user', 'room'])]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    #[Ignore]
    #[Groups(['user'])]
    private Uuid $uuid;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['user'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Ignore]
    private array $roles = [];

    #[Ignore]
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(options: ['default' => true])]
    #[Groups(['user'])]
    private bool $anonymous = true;

    #[ORM\Column]
    #[Groups(['user'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['user', 'room'])]
    private ?int $score = null;

    /**
     * @var Collection<int, SauceRoom>
     */
    #[Ignore]
    #[ORM\ManyToMany(targetEntity: SauceRoom::class, inversedBy: 'users')]
    private Collection $sauceRooms;

    public function __construct()
    {
        $this->uuid = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->sauceRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[Ignore]
    public function getUserIdentifier(): string
    {
        return $this->uuid->toRfc4122();
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
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

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isAnonymous(): bool
    {
        return $this->anonymous;
    }

    public function setAnonymous(bool $anonymous): static
    {
        $this->anonymous = $anonymous;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    // public function getSauceRoom(): ?SauceRoom
    // {
    //     return $this->sauceRoom;
    // }

    // public function setSauceRoom(?SauceRoom $sauceRoom): static
    // {
    //     $this->sauceRoom = $sauceRoom;

    //     return $this;
    // }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection<int, SauceRoom>
     */
    public function getSauceRooms(): Collection
    {
        return $this->sauceRooms;
    }

    public function addSauceRoom(SauceRoom $sauceRoom): static
    {
        if (!$this->sauceRooms->contains($sauceRoom)) {
            $this->sauceRooms->add($sauceRoom);
        }

        return $this;
    }

    public function removeSauceRoom(SauceRoom $sauceRoom): static
    {
        $this->sauceRooms->removeElement($sauceRoom);

        return $this;
    }
}
