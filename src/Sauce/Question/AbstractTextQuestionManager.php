<?php

namespace App\Sauce\Question;

use App\Entity\Question\TextQuestion;
use Override;

abstract class AbstractTextQuestionManager extends AbstractQuestionManager {
    /**
     * to generate the question
     */
    #[Override]
    abstract public function handleQuestionCreation(): TextQuestion;
}