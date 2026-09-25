<?php

namespace App\Entity;

use App\Attribute\Formable;
use App\Attribute\NotFormable;
use App\Enum\SauceServiceEnum;
use App\Repository\SauceRepository;
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
}
