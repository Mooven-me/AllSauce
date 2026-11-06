<?php

namespace App\Sauce\Question\lolsauce;

use App\Dto\Question\TextQuestions;
use App\Entity\Question\TextQuestion;
use App\Enum\LanguageEnum;
use App\Enum\SauceServiceEnum;
use App\Sauce\Question\AbstractTextQuestionManager;
use App\Service\Question\JsonFileReader;

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

            // create a new Questions object to the data base
            $question = new TextQuestion();
            $question->setContent($lore);
            $question->setTitle('Quel champion à ce lore ?');
            foreach(LanguageEnum::cases() as $language){
                $question->setAnswer($language, $this->lolSauceUM->getChampionNameByLanguage($champion['id'], $language));
            }

            $textQuestions->add($question);
        }
        
        return $textQuestions;
    }
}