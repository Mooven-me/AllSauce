<?php

namespace App\Entity\Question;

use App\Entity\Question;
use App\Repository\Question\TextQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TextQuestionRepository::class)]
class TextQuestion extends Question
{
    #[ORM\Column(length: 1200, nullable: true)]
    private ?string $content = null;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }
}
