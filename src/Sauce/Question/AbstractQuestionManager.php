<?php

namespace App\Sauce\Question;

use App\Dto\Question\Questions;
use App\Dto\Question\QuestionsInterface;
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
    abstract public function handleQuestionsCreation(): QuestionsInterface;

    /**
     * to set the type of the current question in the database, 
     * so you can know from wich service it has been craeted
     * and also select question about its type
     */
    public function getManagerClass() : string { return self::class; }

    protected function getSauce() : Sauce {
        static $sauce = null;
        if(!$sauce){
            $sauce = $this->em->getRepository(Sauce::class)->findOneByService($this->getSauceServiceEnum());
        }
        return $sauce;
    }

    /**
     * to remove accents, and set the word in UPPER
     */
    public function normalize(string $string): string 
    {
        // Decompose characters, strip Nonspacing Marks (accents/diacritics), re-compose
        $transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
        $string = $transliterator->transliterate($string);
    
        return mb_strtoupper($string, 'UTF-8');
    }

    /**
     * to remove accents, and set the words in UPPER from an array
     */
    public function normalizeArray(array $strings) : array {
        $result=[];
        foreach($strings as $string){
            $result[] = $this->normalize($string);
        }
    
        return $result;
    }

    public function run() {
        /** @var QuestionsInterface */
        $questions = $this->handleQuestionsCreation(); 
        foreach($questions as $question){
            $question->setSauce($this->getSauce());
            $question->setManagerClass($this->getManagerClass());
            $this->em->persist($question);
        }
        $this->em->getRepository(Question::class)->deleteByManagerClass($this->getManagerClass());
        $this->em->flush();
    }

    /**
     * to get the downloaded file path automatically
     * @param $filename to force a file name
     */
    protected function getDownloadPath(?string $filename = null) : string {
        if(!$filename) $filename = $this->getSauce()->getSlug(); 
        return AbstractDownloaderManager::$DOWNLOAD_PATH.'/'.$filename;
    }
}