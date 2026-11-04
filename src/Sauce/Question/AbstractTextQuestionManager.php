<?php

namespace App\Sauce\Question;

use App\Dto\Question\TextQuestions;
use Override;

abstract class AbstractTextQuestionManager extends AbstractQuestionManager {
    #[Override]
    abstract public function handleQuestionsCreation() : TextQuestions;
}