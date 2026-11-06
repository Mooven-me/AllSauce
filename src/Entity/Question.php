<?php

namespace App\Entity;

use App\Entity\Question\TextQuestion;
use App\Enum\LanguageEnum;
use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\DiscriminatorMap;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'textQuestion' => TextQuestion::class,
])]
#[DiscriminatorMap(typeProperty: 'type', mapping: [
    'textQuestion' => TextQuestion::class,
])]
abstract class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Ignore]
    private ?Sauce $sauce = null;

    #[ORM\Column]
    #[Ignore]
    private array $answers = [];

    #[ORM\Column(length: 255)]
    private ?string $managerClass = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): static
    {
        $this->answers = $answers;

        return $this;
    }

    public function setAnswer(LanguageEnum $language, string $answer): static
    {
        $this->answers[$language->value] = $answer;
    
        return $this;
    }

    public function getAnswer(LanguageEnum $language, LanguageEnum $fallback = LanguageEnum::EN): ?string
    {
        return $this->answers[$language->value]
            ?? $this->answers[$fallback->value] 
            ?? null;
    }

    public function getManagerClass(): ?string
    {
        return $this->managerClass;
    }

    public function setManagerClass(string $managerClass): static
    {
        $this->managerClass = $managerClass;

        return $this;
    }
}
