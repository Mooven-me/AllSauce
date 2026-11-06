<?php

namespace App\Entity;

use App\Repository\SauceRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: SauceRoomRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_sauce_room_code', columns: ['sauce_id', 'room_code'])]
class SauceRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    #[Groups(['room'])]
    private ?string $roomCode = null;

    #[ORM\ManyToOne(inversedBy: 'sauceRooms')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['room'])]
    private ?Sauce $sauce = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Ignore]
    private ?Question $currentQuestion = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'sauceRooms')]
    #[Groups(['room'])]
    private Collection $users;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['room'])]
    private ?User $leader = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId() : ?int {
        return $this->id;
    }

    public function getRoomCode(): ?string
    {
        return $this->roomCode;
    }

    public function setRoomCode(string $roomCode): static
    {
        $this->roomCode = $roomCode;

        return $this;
    }

    public function getSauce(): ?Sauce
    {
        return $this->sauce;
    }

    public function setSauce(?Sauce $sauce): static
    {
        $this->sauce = $sauce;

        return $this;
    }

    public function getCurrentQuestion(): ?Question
    {
        return $this->currentQuestion;
    }

    public function setCurrentQuestion(?Question $currentQuestion): static
    {
        $this->currentQuestion = $currentQuestion;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addSauceRoom($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeSauceRoom($this);
        }

        return $this;
    }

    public function getLeader(): ?User
    {
        return $this->leader;
    }

    public function setLeader(?User $leader): static
    {
        $this->leader = $leader;

        return $this;
    }
}
