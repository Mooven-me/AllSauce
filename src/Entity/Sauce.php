<?php

namespace App\Entity;

use App\Attribute\Formable;
use App\Attribute\NotFormable;
use App\Enum\SauceCssEnum;
use App\Enum\SauceServiceEnum;
use App\Interface\Sauce\SauceServiceInterface;
use App\Repository\SauceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Psr\Container\ContainerInterface;

#[ORM\Entity(repositoryClass: SauceRepository::class)]
#[Formable(path: 'sauce')]
class Sauce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[NotFormable]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(enumType: SauceServiceEnum::class)]
    private ?SauceServiceEnum $service = null;

    /**
     * @var Collection<int, SauceRoom>
     */
    #[ORM\OneToMany(targetEntity: SauceRoom::class, mappedBy: 'sauce', orphanRemoval: true)]
    private Collection $sauceRooms;

    public function __construct()
    {
        $this->sauceRooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getService(): ?SauceServiceEnum
    {
        return $this->service;
    }

    public function setService(SauceServiceEnum $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getSauceService(ContainerInterface $sauceServiceLocator): ?SauceServiceInterface
    {
        if (null === $this->service) {
            return null;
        }

        return $sauceServiceLocator->get($this->service->value);
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
            $sauceRoom->setSauce($this);
        }

        return $this;
    }

    public function removeSauceRoom(SauceRoom $sauceRoom): static
    {
        if ($this->sauceRooms->removeElement($sauceRoom)) {
            // set the owning side to null (unless already changed)
            if ($sauceRoom->getSauce() === $this) {
                $sauceRoom->setSauce(null);
            }
        }

        return $this;
    }
}
