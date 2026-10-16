<?php

namespace App\Entity;

use App\Attribute\Formable;
use App\Attribute\NotFormable;
use App\Enum\SauceServiceEnum;
use App\Repository\SauceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @var Collection<int, SauceCss>
     */
    #[ORM\OneToMany(targetEntity: SauceCss::class, mappedBy: 'Sauce', orphanRemoval: true)]
    private Collection $cssProperties;

    public function __construct()
    {
        $this->cssProperties = new ArrayCollection();
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

    /**
     * @return Collection<int, SauceCss>
     */
    public function getCssProperties(): Collection
    {
        return $this->cssProperties;
    }

    public function addCssProperty(SauceCss $cssProperty): static
    {
        if (!$this->cssProperties->contains($cssProperty)) {
            $this->cssProperties->add($cssProperty);
            $cssProperty->setSauce($this);
        }

        return $this;
    }

    public function removeCssProperty(SauceCss $cssProperty): static
    {
        if ($this->cssProperties->removeElement($cssProperty)) {
            // set the owning side to null (unless already changed)
            if ($cssProperty->getSauce() === $this) {
                $cssProperty->setSauce(null);
            }
        }

        return $this;
    }
}
