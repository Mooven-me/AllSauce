<?php

namespace App\Entity;

use App\Repository\SauceCssRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SauceCssRepository::class)]
class SauceCss
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $property = null;

    #[ORM\Column(length: 255)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'cssProperties')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sauce $Sauce = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(string $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getSauce(): ?Sauce
    {
        return $this->Sauce;
    }

    public function setSauce(?Sauce $Sauce): static
    {
        $this->Sauce = $Sauce;

        return $this;
    }
}
