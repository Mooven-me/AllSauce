<?php

namespace App\Command;

use App\Sauce\Question\AbstractQuestionManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'allsauce:generate',
    description: 'Add a short description for your command',
)]
class AllsauceGenerateCommand{
    public function __invoke(
        SymfonyStyle $io,
        #[AutowireIterator(AbstractQuestionManager::class)]
        iterable $saucesQuestionManager
    ): int {
        set_time_limit(-1);
        ini_set('memory_limit', '-1');

        $io->progressStart(count($saucesQuestionManager));

        foreach($saucesQuestionManager as $sauceQuestionManager){
            $sauceQuestionManager->run();
            $io->progressAdvance();
        }

        $io->progressFinish();
        $io->success('end of every sauces !');

        return Command::SUCCESS;
    }
}
