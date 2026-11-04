<?php

namespace App\Sauce\Question\lolsauce;

use App\Dto\Question\TextQuestions;
use App\Entity\Question\TextQuestion;
use App\Enum\SauceServiceEnum;
use App\Sauce\Question\AbstractTextQuestionManager;
use App\Service\Question\JsonFileReader;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoreQuestion extends AbstractTextQuestionManager {

    public function __construct(
        private LoLSauceUtilManager $lolSauceUM,
        private JsonFileReader $json,
    ){
        $json->setFolderName('lolsauce/'.$this->lolSauceUM->getVersion());
    }

    public function getSauceServiceEnum() : SauceServiceEnum {
        return SauceServiceEnum::LoLSauce;
    }

    public function handleQuestionsCreation() : TextQuestions {
        $textQuestions = new TextQuestions();

        $champions = $this->lolSauceUM->getChampions();

        foreach($champions as $champion){
            $lore = str_replace($champion['name'],str_repeat('*',6), $champion['blurb']);
            $answers = $this->normalizeArray($this->lolSauceUM->getChampionNames($champion['id']));

            // create a new Questions object to the data base
            $question = new TextQuestion();
            $question->setContent($lore);
            $question->setTitle('Quel champion à ce lore ?');
            $question->setAnswers($answers);

            $textQuestions->add($question);
        }
        
        return $textQuestions;
    }
}