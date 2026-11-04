<?php

namespace App\Dto\Question;

use App\Entity\Question\TextQuestion;
use IteratorAggregate;

/**
 * to manage a list of Question
 * @implements IteratorAggregate<int,TextQuestions>
 */
class TextQuestions extends QuestionsInterface {

    public function add(TextQuestion $question): void {
        $this->questions[] = $question;
    }

    /**
     * to merge two Questions array
     */
    public function merge(TextQuestions $questions) : void {
        foreach($questions as $question){
            $this->questions[] = $question;
        }
    }
}