<?php

namespace App\Dto\Question;

use App\Entity\Question;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Question>
 */
abstract class QuestionsInterface implements IteratorAggregate
{
    /**
     * @var array<int, Question>
     */
    public array $questions = [];

    /**
     * @return Traversable<int, Question>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->questions);
    }
}
