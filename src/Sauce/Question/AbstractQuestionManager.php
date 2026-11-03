<?php

namespace App\Sauce\Question;

use App\Entity\Question;
use App\Entity\Sauce;
use App\Enum\SauceServiceEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Contracts\Service\Attribute\Required;

#[AutoconfigureTag]
abstract class AbstractQuestionManager {

    protected EntityManagerInterface $em;

    #[Required]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }
    
    /**
     * to set the related SauceService of the question 
     */
    abstract public function getSauceServiceEnum(): SauceServiceEnum;

    /**
     * to generate the question
     */
    abstract public function handleQuestionCreation(): Question;

    /**
     * to create the question (only persist it for now)
     */
    protected function createQuestion(Question $question){
        $question->setSauce($this->getSauce());
        $this->em->persist($question);
    }

    protected function getSauce() : Sauce {
        return $this->em->getRepository(Sauce::class)->findOneBySauceServiceEnum($this->getSauceServiceEnum());
    }

    /**
     * to remove accents, and set the word in UPPER
     */
    public function normalize(string $string) {
        // Convert accented characters to ASCII equivalents
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

        // Convert to uppercase in a multibyte-safe way
        $string = mb_strtoupper($string, 'UTF-8');

        return $string;
    }
}